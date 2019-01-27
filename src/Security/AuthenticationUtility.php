<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use \Symfony\Component\HttpFoundation\Cookie;

class AuthenticationUtility extends AuthenticationUtils
{
    const LOGOUT_MESSAGE = '_logout_message';
    private $security;

    public function __construct(RequestStack $requestStack, Security $security)
    {
        parent::__construct($requestStack);

        $this->security = $security;
    }

    /**
     * @param bool $clearCookie
     *
     * @return LogoutMessage|null
     */
    public function getLogoutMessage($clearCookie = true)
    {
        $logoutMessage = null;
        if (null !== $_COOKIE && isset($_COOKIE[AuthenticationUtility::LOGOUT_MESSAGE])) {
            $logoutMessage = $_COOKIE[AuthenticationUtility::LOGOUT_MESSAGE];

            if ($clearCookie) {
                unset($_COOKIE[AuthenticationUtility::LOGOUT_MESSAGE]);
                setcookie(AuthenticationUtility::LOGOUT_MESSAGE, '', -1, '/');
            }
        }

        return $logoutMessage;
    }

    public function isUserLogged()
    {
        if($this->security->getUser() !== null)
            return true;
        
        return false;
    }
}
