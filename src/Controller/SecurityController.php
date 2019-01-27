<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Security\LoginFormAuthenticator;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use App\Security\AuthenticationUtility;
use App\Security\RegisterForm;
use App\Entity\User;


class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(Request $request, AuthenticationUtility $authenticationUtils, SessionInterface $session): Response
    {
        if($authenticationUtils->isUserLogged())
        {
            return $this->redirectToRoute('app_home');
        }

        $logoutMessage = $authenticationUtils->getLogoutMessage();
        $messages = $session->getFlashBag()->get('messages', []);

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error, 'logout_message' => $logoutMessage, 'messages' => $messages]);
    }
    
    /**
     * @Route("/register", name="app_register")
     */
    public function register(LoginFormAuthenticator $authenticator, GuardAuthenticatorHandler $guardHandler, Request $request, AuthenticationUtility $authenticationUtils, RegisterForm $registerForm): Response
    {
        if($authenticationUtils->isUserLogged())
        {
            return $this->redirectToRoute('app_home');
        }

        $errors = null;

        $user = new User();
        $user->setRoles(['ROLE_USER']);

        if(
            'app_register' === $request->attributes->get('_route') && 
            $request->isMethod('POST')
        )
        {
            $registerForm->checkCsrfToken($request->get('_csrf_token'));

            $user->setEmail((string) $request->request->get('email'));
            $user->setPassword((string) $request->request->get('password'));
            $user->setAutologin((bool) $request->request->get('autologin'));

            if($registerForm->validateUser($user))
            {
                $registerForm->registerUser($user);

                if($user->getAutologin())
                {
                    return $guardHandler->authenticateUserAndHandleSuccess(
                        $user, $request, $authenticator, 'main'
                    );
                }
                else
                {
                    return $this->redirectToRoute('app_login');
                }
            }
            else
            {
                $errors = $registerForm->getErrorsValidation();
            }
        }

        return $this->render('security/register.html.twig', [
            'user' => $user,
            'errors' => $errors,
        ]);
    }
    
    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(Request $request): Response
    {

    }
}