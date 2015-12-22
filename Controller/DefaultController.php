<?php

namespace AtlanteGroup\Bundle\CorporateVCardsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('CorporateVCardsBundle:Default:index.html.twig');
    }
}
