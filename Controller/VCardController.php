<?php

namespace AtlanteGroup\CorporateVCardsBundle\Controller;

use AtlanteGroup\CorporateVCardsBundle\CorporateVCardsBundle;
use AtlanteGroup\CorporateVCardsBundle\Helper\Util;
use AtlanteGroup\CorporateVCardsBundle\Service\MailsServiceInterface;
use AtlanteGroup\CorporateVCardsBundle\Service\VCardService;
use Endroid\QrCode\QrCode;
use JeroenDesloovere\VCard\VCard;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VCardController extends Controller
{
    public function vCardAction($person, Request $request)
    {
        /** @var VCardService $vCardService */
        $vCardService = $this->get('corporate_v_cards.vcard');

        // Get person profile
        $profile = $vCardService->getProfile($person);

        if (!$profile)
            throw new NotFoundHttpException();

        $config = $this->getParameter(CorporateVCardsBundle::$conf_prefix . '.config');

        // Get random background
        $backgrounds = $config['backgrounds'];
        $background = $backgrounds[rand(0, count($backgrounds)-1)];

        // Check if mails are enabled
        $formView = null;
        $mailSent = false;
        $recipientVcardLink = null;
        $mailsServiceName = $config['mails_service'];
        if (!!$mailsServiceName) {
            // Send by mail form
            $formBuilder = $this->createFormBuilder()
                ->add('email', EmailType::class, [
                    'attr' => ['placeholder' => 'Adresse e-mail'],
                    'required' => true
                ])
                ->add('submit', SubmitType::class, [
                    'label' => 'Envoyer'
                ]);

            $form = $formBuilder->getForm();
            $form->handleRequest($request);
            $formView = $form->createView();

            if ($form->isSubmitted() && $form->isValid()) {
                // Send e-mail
                /** @var MailsServiceInterface $mailsService */
                $mailsService = $this->get(substr($mailsServiceName, 1));
                $email = $form->get('email')->getData();
                $mailsService->sendVcard($email, $profile, $person);

                // Generate vcard download link from user e-mail address
                $infos = Util::getContactInformationFromEmailAddress($email);
                $recipientVcardLink = $this->get('router')->generate('vcard_download_frominfos', array_merge(['person' => $person], $infos));

                $mailSent = true;
            }
        }

        // Generate favicons public path base
        $faviconsPath = $config['favicons']['enabled']
            ? Util::getPublicDir($config['favicons']['dir'])
            : null;

        return $this->render('@CorporateVCards/vcard.html.twig', [
            'person' => $person,
            'profile' => $profile,
            'background' => $background,
            'mailsEnabled' => $mailsServiceName != null,
            'mailSent' => $mailSent,
            'recipientVcardLink' => $recipientVcardLink,
            'form' => $formView,
            'config' => $config,
            'favicons_path_base' => $faviconsPath
        ]);
    }

    public function downloadVcardAction($person)
    {
        /** @var VCardService $vCardService */
        $vCardService = $this->get('corporate_v_cards.vcard');

        // Get profile
        $profile = $vCardService->getProfile($person);

        if (!$profile)
            throw new NotFoundHttpException();

        // Get vCard
        $vcard = $vCardService->getVcard($profile, true);

        return $this->buildVcardResponse($vcard);
    }

    public function downloadVcardFromInfosAction(Request $request)
    {
        $params = $request->query;

        /** @var VCardService $vCardService */
        $vCardService = $this->get('corporate_v_cards.vcard');

        $infos = $vCardService->buildProfile([
            'firstName' => $params->get('firstName'),
            'lastName' => $params->get('lastName'),
            'email' => $params->get('email'),
            'company' => $params->get('company')
        ], false);

        // Get vCard
        $vcard = $vCardService->getVcard($infos, true);

        return $this->buildVcardResponse($vcard);
    }

    private function buildVcardResponse(VCard $vcard)
    {
        // Build response
        $response = new Response($vcard->getOutput());
        foreach ($vcard->getHeaders(true) as $key => $val)
            $response->headers->set($key, $val);

        return $response;
    }

    public function qrCodeAction($person)
    {
        /** @var VCardService $vCardService */
        $vCardService = $this->get('corporate_v_cards.vcard');

        // Get profile
        $profile = $vCardService->getProfile($person);

        if (!$profile)
            throw new NotFoundHttpException();

        // Get vCard
        $vcard = $vCardService->getVcard($profile, false);

        // Build qrCode
        $qrCode = new QrCode();
        $qrCode
            ->setText($vcard->getOutput())
            ->create();

        // Build response
        $response = new StreamedResponse(function() use ($qrCode) {
            $base64_uri = $qrCode->getDataUri();
            echo base64_decode(substr($base64_uri, strlen("data:image/png;base64,")));
        });
        $response->headers->set('Content-Type', 'image/png');

        return $response;
    }
}
