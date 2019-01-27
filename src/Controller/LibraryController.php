<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Security\AuthenticationUtility;
use App\Library\Exception\ExceptionInterface;
use App\Library\Exception\AuthorNotExistException;
use App\Library\BookForm;
use App\Library\LibraryGrid;
use App\Repository\BookRepository;
use App\Entity\Book;
use App\Entity\Author;
use App\Entity\Translations;
use App\Utilities\Paginator\PaginatorUtility;
use App\Utilities\Paginator\PaginatorUrlGenerator;
use App\Utilities\Paginator\Exceptions\ExceptionInterface as PaginatorExceptionInterface;

class LibraryController extends AbstractController
{
    use Bookform;
    use LibraryGrid;

    private $session;
    private $repository;
    private $authenticationUtils;

    public function __construct(SessionInterface $session, BookRepository $repository, AuthenticationUtility $authenticationUtils)
    {
        $this->session = $session;
        $this->repository = $repository;
        $this->authenticationUtils = $authenticationUtils;
    }

    /**
     * @Route("/library/{page}", name="library", requirements={"page": "\d+"});
     */
    public function index($page = 1, Request $request, UrlGeneratorInterface $router)
    {
        $filter = $this->setFilter($request);

        $limit = 5;
        $total = $this->repository->getCountForGrid($filter['q'], $filter['author'], $filter['translation']);

        $parameters = $this->getParameters($filter);

        try {
            $paginatorUrl = new PaginatorUrlGenerator($router, 'library', $parameters, null);
            $paginator = new PaginatorUtility($paginatorUrl, $total, $limit, $page);
            $pagination = $paginator->getPaginator();
        } catch (PaginatorExceptionInterface $e) {
            throw $this->createNotFoundException('Page not exist');
        }

        $rows = $this->repository->getForGrid($limit, $pagination->getOffset(), $filter['q'], $filter['author'], $filter['translation']);

        $translations = $this->repository->getDistinctTranslations();
        $authors = $this->repository->getDistinctAuthors();
        return $this->render('library/index.html.twig', [
            'pagination' => $pagination,
            'rows' => $rows,
            'translations' => $translations,
            'authors' => $authors,
            'filter' => $filter,
            'exceptions' => $this->session->getFlashBag()->get('exceptions', []),
            'messages' => $this->session->getFlashBag()->get('messages', []),
        ]);
    }

    /**
     * @Route("/library/add/{id}", name="add_edit_book", requirements={"id": "\d+"});
     */
    public function addEdit($id = null, Request $request)
    {
        if(!$this->authenticationUtils->isUserLogged())
        {
            $message = (empty($id)?"You have to login to add book.":"You have to login to edit book.");
            $this->session->getFlashBag()->add('messages', $message);
            return $this->redirectToRoute('app_login', ['continue' => $this->generateUrl('add_edit_book', ['id' => $id])]);
        }

        $options = $this->buildForm();

        $book = new Book();
        if($id !== null)
        {
            try {
                $book = $this->getBook($id);
            } catch (ExceptionInterface $e) {
                $options['exception'] = $e->getMessageKey();
            }
        }
        
        if(
            'add_edit_book' === $request->attributes->get('_route') && 
            $request->isMethod('POST')
        )
        {
            $this->checkCsrfToken($request->get('_csrf_token'));

            $book->setTitle((string) $request->request->get('title'));

            try {
                $author = $this->getAuthor((int) $request->request->get('author'));
                $book->setAuthor($author);
            } catch (ExceptionInterface $e) {
                //let the validator check foreign key exist prevent throw
            }
            
            try {
                $book->setPublicationDate(new \DateTime($request->request->get('publication_date')));
            } catch (\Throwable $th) {
                //let the validator check date even if user type uncorrect;
            }

            $book->removeTranslations();
            if(is_array($request->request->get('translations')))
            {
                foreach($request->request->get('translations') as $translation)
                {
                    $trans = new Translations();
                    $trans->setName($translation);
                    $book->addTranslation($trans);
                }
            }

            if($this->validateBook($book))
            {
                if($book->getId() !== null)
                    $this->updateBook($book);
                else
                {
                    $book = $this->saveBook($book);
                }
                
                switch ($request->request->get('action')) {
                    case 'save_add':
                        return $this->redirectToRoute('add_edit_book');
                    case 'save_return':
                        return $this->redirectToRoute('library');
                    default:
                        return $this->redirectToRoute('add_edit_book', ['id' => $book->getId()]);
                }
            }
            else
            {
                $options['errors'] = $this->getErrorsValidation();
            }
        }
        
        return $this->render('library/add_edit_book.html.twig', array_merge($options, [
            'book' => $book
        ]));
    }

    /**
     * @Route("/library/del/{id}", name="del_book", requirements={"id": "\d+"});
     */
    public function delete($id, Request $request)
    {
        if(!$this->authenticationUtils->isUserLogged())
        {
            $this->session->getFlashBag()->add('messages', "You have to login to remove book.");
            return $this->redirectToRoute('app_login', ['continue' => $this->generateUrl('del_book', ['id' => $id])]);
        }

        try {
            $book = $this->getBook($id);
            $this->repository->removeBook($book);
            $this->session->getFlashBag()->add('messages', "Book was delete.");
        } catch (ExceptionInterface $e) {
            $this->session->getFlashBag()->add('exceptions', $e->getMessageKey());
        }

        if(!empty($request->server->get('HTTP_REFERER')))
            return $this->redirect($request->server->get('HTTP_REFERER'));
        else
            return $this->redirectToRoute('library');
    }
}
