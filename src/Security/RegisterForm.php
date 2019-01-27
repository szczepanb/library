<?php

namespace App\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\User;
use App\Repository\UserRepository;

class RegisterForm extends AbstractType
{
    private $passwordEncoder;
    private $validator;
    private $repository;
    private $errorsValidation;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, ValidatorInterface $validator, UserRepository $repository)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->repository = $repository;
        $this->validator = $validator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setAttribute('class', 'user-account-form')
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class)
            ->add('autologin', CheckboxType::class, ['required' => false])
            ->add('save', SubmitType::class)
        ;
    }

    /**
     * 
     */
    public function validateUser(User $user)
    {
        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            $this->errorsValidation = (string) $errors;
    
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

    private function setErrorsValidation(string $errorsValidation)
    {
        $this->errorsValidation = $errorsValidation;
    }

    public function getErrorsValidation():string
    {
        return $this->errorsValidation;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'attr' => array(
                'class' => 'user-account-form'
            )
        ]);
    }
}
