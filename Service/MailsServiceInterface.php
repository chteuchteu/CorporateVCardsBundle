<?php

namespace AtlanteGroup\CorporateVCardsBundle\Service;

interface MailsServiceInterface
{
    function sendVcard($toMail, $profile, $person);
}
