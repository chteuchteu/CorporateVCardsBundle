<?php

namespace Chteuchteu\CorporateVCardsBundle\Service;

interface MailsServiceInterface
{
    function sendVcard($toMail, $profile, $person);
}
