<?php

namespace App\Library;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use App\Library\Exception\AuthorNotExistException;
use App\Entity\Author;
use App\Repository\AuthorRepository;
use App\Library\CountriesFetcher;

trait AuthorForm
{
    use CountriesFetcher;

    private $validator;
    private $repository;
    private $csrfTokenManager;
    private $errorsValidation;

    /**
     * @required
     */
    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @required
     */
    public function setRepository(AuthorRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @required
     */
    public function setCsrfTokenManager(CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->csrfTokenManager = $csrfTokenManager;
    }

    public function buildForm()
    {
        $countries = $this->getCountries();

        return [
            'countries' => $countries,
        ];
    }

    public function checkCsrfToken($token)
    {
        $token = new CsrfToken('author', $token);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }
    }

    /**
     * 
     */
    public function validateAuthor(Author $author)
    {
        $errors = $this->validator->validate($author);

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

    public function saveAuthor(Author $author)
    {
        return $this->repository->insertAuthor($author);
    }

    public function updateAuthor(Author $author)
    {
        return $this->repository->updateAuthor($author);
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
