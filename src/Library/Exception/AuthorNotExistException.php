<?php

namespace App\Library\Exception;

use App\Library\Exception\ExceptionInterface;
use \RuntimeException;

class AuthorNotExistException extends RuntimeException implements ExceptionInterface
{
    private $messageKey;

    public function __construct(string $message = '', array $messageData = array(), int $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->setSafeMessage($message);
    }

    /**
     * Set a message that will be shown to the user.
     *
     * @param string $messageKey  The message or message key
     */
    public function setSafeMessage($messageKey)
    {
        $this->messageKey = $messageKey;
    }

    public function getMessageKey()
    {
        return $this->messageKey;
    }
}
