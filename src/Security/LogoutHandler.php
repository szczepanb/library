<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use App\Security\AuthenticationUtility;

class LogoutHandler implements LogoutSuccessHandlerInterface
{
    private $router; 

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * Creates a Response object to send upon a successful logout.
     *
     * @return Response never null
     */
    public function onLogoutSuccess(Request $request)
    {
        setcookie(AuthenticationUtility::LOGOUT_MESSAGE, 'Logout success');

        return new RedirectResponse($this->router->generate('app_login'));
    }
}
