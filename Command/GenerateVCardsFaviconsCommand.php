<?php

namespace AtlanteGroup\CorporateVCardsBundle\Command;

use AtlanteGroup\CorporateVCardsBundle\CorporateVCardsBundle;
use AtlanteGroup\CorporateVCardsBundle\Helper\Util;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateVCardsFaviconsCommand extends ContainerAwareCommand
{
    private $relFaviconGeneratorApiKey;

    protected function configure()
    {
        $this
            ->setName('cvc:generate-favicons')
            ->setDescription('Generates favicons for each vcard profile');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $config = $this->getContainer()->getParameter(CorporateVCardsBundle::$conf_prefix . '.config');

        if (!$config['favicons']['enabled']) {
            $output->writeln('Favicons are disabled in app config.');
            return;
        }

        // Check API key
        $this->relFaviconGeneratorApiKey = $config['favicons']['real_favicon_generator_api_key'];
        if (!$this->relFaviconGeneratorApiKey) {
            $output->writeln('Missing RealFaviconGenerator API key, aborting.');
            $output->writeln('Read documentation at https://github.com/AtlanteGroup/CorporateVCardsBundle to learn how to generate one.');
            return;
        }

        // Generate destination path
        $destination_logicalPath = $config['favicons']['dir'];
        $destination = $container->get('file_locator')->locate($destination_logicalPath);

        // Get profiles
        $vCardService = $this->getContainer()->get('corporate_v_cards.vcard');
        $profiles = $vCardService->getProfiles();

        // Let's go!
        $output->writeln('Generating favicons for ' . implode(', ', array_keys($profiles)) . ':');

        foreach ($profiles as $person => $profile)
        {
            // Don't generate it if it doesn't have any photo
            if (!$profile['photo']) {
                $output->writeln('    [INFO] ' . $person . ' does not have any photo, skipping');
                continue;
            }

            $dir = $destination . $person;

            // Create a directory for this person if doesn't exists
            if (!file_exists($dir))
                mkdir($dir);

            // Check if there are favicons
            $files = array_diff(scandir($dir), ['..', '.']);
            if (!empty($files)) {
                $output->writeln('    [INFO] Favicons found for ' . $person . ', skipping');
                continue;
            }

            // Call API to generate favicons
            $output->writeln(' -> Calling API for ' . $person);
            $publicPath = '/' . Util::getPublicDir($destination_logicalPath . $person);
            $res = $this->getFavicons($profile, $dir, $publicPath);

            if (!$res)
                $output->writeln(' -> Failed while generating favicons for ' . $person);
            else
                $output->writeln(' -> Finished downloading favicons for ' . $person);
        }
    }

    private function getFavicons($profile, $destination, $publicPath)
    {
        // Build JSON structure for API call
        $struct = [
            'favicon_generation' => [
                'api_key' => $this->relFaviconGeneratorApiKey,
                'master_picture' => [
                    'type' => 'inline',
                    'content' => $this->base64EncodePicture($profile)
                ],
                'files_location' => [
                    'type' => 'path',
                    'path' => $publicPath
                ],
                'favicon_design' => [
                    'desktop_browser' => [],
                    'ios' => [],
                    'android_chrome' => [
                        'picture_aspect' => 'shadow',
                        'manifest' => [
                            'name' => $profile['firstName'] . ' ' . $profile['lastName']
                        ]
                    ],
                    'windows' => [],
                    'firefox_app' => [],
                    'safari_pinned_tab' => []
                ],
                'settings' => [
                    'compression' => '3',
                    'scaling_algorithm' => 'Lanczos'
                ]
            ]
        ];

        // Call API
        // Build request
        $apiUrl = 'http://realfavicongenerator.net/api/favicon';
        $opts = [
            'http' => [
                'method' => 'POST',
                'header' => 'Content-type: application/json',
                'content' => json_encode($struct)
            ]
        ];
        $context = stream_context_create($opts);
        $result = file_get_contents($apiUrl, false, $context);

        // Parse result
        $json = json_decode($result, true);
        $result = $json['favicon_generation_result'];

        if ($result['result']['status'] != 'success')
            return false;

        // Download each version
        $urls = $result['favicon']['files_urls'];
        foreach ($urls as $url) {
            $fileName = basename($url);
            file_put_contents($destination . '/' . $fileName, fopen($url, 'r'));
        }

        return true;
    }

    private function base64EncodePicture($profile)
    {
        // Generate filesystem URI for profile photo
        $webDir = $this->getContainer()->get('kernel')->getRootDir() . '/../web/';
        $data = file_get_contents($webDir . $profile['photo']);

        return base64_encode($data);
    }
}
