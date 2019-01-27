<?php

namespace App\Security;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use App\Entity\User;
use App\Repository\UserRepository;

class RegisterForm
{
    private $passwordEncoder;
    private $csrfTokenManager;
    private $validator;
    private $repository;
    private $errorsValidation;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, CsrfTokenManagerInterface $csrfTokenManager, ValidatorInterface $validator, UserRepository $repository)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->repository = $repository;
        $this->validator = $validator;
    }

    public function validateUser(User $user)
    {
        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            $iterator = $errors->getIterator();
            while($iterator->valid()) {
                $current = $iterator->current();
                $this->addErrorValidation($current->getPropertyPath() , $current->getMessage());
                $iterator->next();
            }
    
            return false;
        }

        return true;
    }

    public function registerUser(User $user)
    {
        $password = $this->passwordEncoder->encodePassword($user, $user->getPassword());
        $user->setPassword($password);

        $this->repository->insertUser($user);
    }

    public function checkCsrfToken($token)
    {
        $token = new CsrfToken('register', $token);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }
    }

    private function addErrorValidation($key, $errorValidation)
    {
        $this->errorsValidation[$key][] = $errorValidation;
    }

    public function getErrorsValidation()
    {
        return $this->errorsValidation;
    }
}
