<?php

namespace App\Library\Utilities;

use App\Library\Exception\AuthorNotExistException;
use App\Entity\Author;
use App\Repository\AuthorRepository;

trait AuthorUtility
{
    private $authorRepository;

    /**
     * @required
     */
    public function setAuthorRepository(AuthorRepository $authorRepository)
    {
        $this->authorRepository = $authorRepository;
    }

    public function getAuthor(int $id): ?Author
    {
        $author = $this->authorRepository->findOneById($id);

        if($author === null)
            throw new AuthorNotExistException("Author not exist.");

        return $author;
    }
}
