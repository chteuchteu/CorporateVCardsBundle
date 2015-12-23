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
}
