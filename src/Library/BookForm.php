<?php

namespace App\Library;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Library\Utilities\AuthorUtility;
use App\Library\Exception\BookNotExistException;
use App\Entity\Book;
use App\Entity\Author;
use App\Entity\Translations;
use App\Repository\BookRepository;
use App\Library\CountriesFetcher;

trait BookForm
{
    use CountriesFetcher;
    use AuthorUtility;

    private $validator;
    private $csrfTokenManager;
    private $bookRepository;
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
    public function setBookRepository(BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
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
        $authors = $this->authorRepository->get();

        return [
            'countries' => $countries,
            'authors' => $authors,
        ];
    }

    public function getBook(int $id): Book
    {
        $book = $this->bookRepository->findOneById($id);

        if($book === null)
            throw new BookNotExistException("Book not exist.");
        
        return $book;
    }

    public function checkCsrfToken($token)
    {
        $token = new CsrfToken('book', $token);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }
    }

    public function saveBook(Book $book)
    {
        return $this->bookRepository->insertBook($book);
    }

    public function updateBook(Book $book)
    {
        return $this->bookRepository->updateBook($book);
    }

    /**
     * @return Collection|Translations[]
     */
    public function getTranslations($translations): Collection
    {
        $translations = array_map(function($name)
        {
            $translation = new Translations();
            $translation->setName($name);
            return $translation;
        }, $translations);

        return new ArrayCollection($translations); 
    }

        /**
     * 
     */
    public function validateBook(Book $book)
    {
        $errors = $this->validator->validate($book);

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

    private function addErrorValidation($key, $errorValidation)
    {
        $this->errorsValidation[$key][] = $errorValidation;
    }

    public function getErrorsValidation():Array
    {
        return $this->errorsValidation;
    }
}
