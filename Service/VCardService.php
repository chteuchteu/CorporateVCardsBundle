<?php

namespace Chteuchteu\CorporateVCardsBundle\Service;

use Chteuchteu\CorporateVCardsBundle\Helper\Util;
use JeroenDesloovere\VCard\VCard;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class VCardService
{
    /** @var array */
    private $profileSkeleton;
    /** @var array */
    private $profiles;
    /** @var array */
    private $defaultProfile;
    /** @var string */
    private $kernelRootDir;
    /** @var Request */
    private $request;

    public function __construct($profiles, $defaultProfile, $kernelRootDir, RequestStack $requestStack)
    {
        $this->profiles = $profiles;
        $this->defaultProfile = $defaultProfile;
        $this->kernelRootDir = $kernelRootDir;
        $this->request = $requestStack->getCurrentRequest();
        $this->profileSkeleton = [
            'firstName' => null, 'lastName' => null,
            'email' => null, 'jobTitle' => null,
            'company' => null, 'photo' => null,
            'phone' => [
                'work' => null, 'mobile' => null
            ],
            'address' => [
                'street' => null, 'city' => null, 'region' => null, 'zip' => null, 'country' => null
            ],
            'url' => null
        ];
    }

    /**
     * Get a profile array for a person
     * @param $person
     * @return array|null
     */
    public function getProfile($person)
    {
        if (!array_key_exists($person, $this->profiles))
            return null;

        return $this->buildProfile($this->profiles[$person]);
    }

    /**
     * Builds a profile array from person information
     * @param $infos
     * @return array
     */
    public function buildProfile($infos, $mergeWithDefaults=true)
    {
        $struct = $this->profileSkeleton;

        if ($mergeWithDefaults)
            $struct = Util::array_merge_recursive_distinct($struct, $this->defaultProfile);

        return Util::array_merge_recursive_distinct($struct, $infos);
    }

    /**
     * Returns all profiles as an associative array
     * @return array
     */
    public function getProfiles()
    {
        $profiles = [];

        foreach (array_keys($this->profiles) as $person)
            $profiles[$person] = $this->getProfile($person);

        return $profiles;
    }

    /**
     * Returns a vcard for a profile
     * @param $profile
     * @param $includePhoto
     * @return VCard
     */
    public function getVCard($profile, $includePhoto)
    {
        $vcard = new VCard();
        $vcard
            ->addName($profile['lastName'], $profile['firstName'])
            ->addCompany($profile['company'])
            ->addAddress(
                '',
                '',
                $profile['address']['street'],
                $profile['address']['city'],
                $profile['address']['region'],
                $profile['address']['zip'],
                $profile['address']['country']
            )
            ->addEmail($profile['email'])
            ->addURL($profile['url'])
            ->addPhoneNumber($profile['phone']['work'], 'WORK')
            ->addPhoneNumber($profile['phone']['mobile'], 'CELL')
            ->addJobtitle($profile['jobTitle']);

        // Add photo
        if ($profile['photo'] != null)
        {
            $photoUri = null;
            if ($includePhoto) {
                // Generate filesystem URI
                $webDir = $this->kernelRootDir . '/../web/';
                $photoUri = $webDir . $profile['photo'];
            } else {
                // Generate absolute public URI
                $photoUri = $this->request->getUriForPath('/' . $profile['photo']);
            }

            $vcard->addPhoto($photoUri, $includePhoto);
        }

        return $vcard;
    }
}
