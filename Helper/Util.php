<?php

namespace AtlanteGroup\CorporateVCardsBundle\Helper;

abstract class Util
{
    /**
     * Recursively merge an array
     * @param array $array1
     * @param null $array2
     * @return array
     * Source: http://danielsmedegaardbuus.dk/2009-03-19/phps-array_merge_recursive-as-it-should-be/
     */
    public static function &array_merge_recursive_distinct(array &$array1, &$array2 = null)
    {
        $merged = $array1;

        if (is_array($array2))
            foreach ($array2 as $key => $val)
                if (is_array($array2[$key]))
                    $merged[$key] = is_array($merged[$key]) ? Util::array_merge_recursive_distinct($merged[$key], $array2[$key]) : $array2[$key];
                else
                    $merged[$key] = $val;

        return $merged;
    }

    /**
     * "@AppBundle/Resources/public/img/1.jpg" => "bundles/app/img/1.jpg"
     * @param $logicalPath
     * @return string
     */
    public static function getPublicDir($logicalPath)
    {
        preg_match('/@(.*)Bundle\/(?:.*)\/public\/(.*)/', $logicalPath, $matches);

        $bundleName = strtolower($matches[1]);
        $publicPath = $matches[2];

        return 'bundles/' . $bundleName . '/' . $publicPath;
    }

    /**
     * Tries to guess contact information from his email address
     * @param $email
     * @return array (email, firstName, lastName, business)
     */
    public static function getContactInformationFromEmailAddress($email)
    {
        // Get "jdoe" & "evil-corp" from jdoe@evil-corp.com
        preg_match("/(.*)@(.*)\.(?:.*)/", $email, $matches);

        $namePart = $matches[1];
        $domainPart = $matches[2];

        $firstName = $lastName = $business = null;

        // Try to get first name & last name from $namePart
        if (preg_match("/(\.|-|_)/", $namePart)) {
            preg_match("/(.*)(?:\.|-|_)(.*)/", $namePart, $nameMatches);

            $firstName = ucwords($nameMatches[1]);
            $lastName = ucwords($nameMatches[2]);
        } else {
            $firstName = ucwords($namePart);
        }

        // Prettify business name
        // Exclude common mail providers
        $excludedProviders = [
            "gmail", "google-mail", "live", "outlook", "wanadoo",
            "yahoo", "orange", "aol", "laposte", "sfr", "free"
        ];
        $business = in_array($domainPart, $excludedProviders) ? null : ucwords(preg_replace("/(\.|-|_)/", " ", $domainPart));

        return [
            'email' => $email,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'business' => $business
        ];
    }
}
