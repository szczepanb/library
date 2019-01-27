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

        $user = new User();
        $user->setRoles(['ROLE_USER']);

        $form = $this->createForm(RegisterForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if($registerForm->validateUser($user))
            {
                $user = $form->getData();
                $registerForm->registerUser($user);
            }
            else
            {
                return new Response($registerForm->getErrorsValidation());
            }

            if($user->getAutologin())
            {
                return $guardHandler->authenticateUserAndHandleSuccess(
                    $user,          // the User object you just created
                    $request,
                    $authenticator, // authenticator whose onAuthenticationSuccess you want to use
                    'main'          // the name of your firewall in security.yaml
                );
            }
            else
            {
                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(Request $request): Response
    {

    }
}