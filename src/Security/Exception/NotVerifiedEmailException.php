<?php

namespace App\Security\Exception;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class NotVerifiedEmailException extends CustomUserMessageAuthenticationException
{
    public function __construct(
        string $message = 'Ce compte ne semble pas posséder d\'email vérifié',
        array $messageData = ['link' => 'https://support.google.com/accounts/answer/63950?hl=fr'],
        int $code = 0,
        \Throwable $previous = null)
    {
        parent::__construct($message, $messageData, $code, $previous);
    }
}
