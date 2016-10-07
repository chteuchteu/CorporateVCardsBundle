<?php

namespace Chteuchteu\CorporateVCardsBundle\Twig;

class AppExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('rot13', [$this, 'rot13Filter'])
        ];
    }

    public function rot13Filter($str)
    {
        return str_rot13($str);
    }

    public function getName()
    {
        return 'app_extension';
    }
}
