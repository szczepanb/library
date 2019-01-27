<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Library\Exception\ExceptionInterface;
use App\Library\Exception\ActivePageNotExistException;
use App\Security\AuthenticationUtility;
use App\Entity\Author;
use App\Library\Utilities\AuthorUtility;
use App\Library\AuthorForm;
use App\Library\AuthorGrid;
use App\Repository\AuthorRepository;
use App\Utilities\PaginatorUtility;

class AuthorController extends AbstractController
{
    use AuthorUtility;
    use AuthorForm;
    use AuthorGrid;

    private $session;
    private $repository;
    private $authenticationUtils;

    public function __construct(SessionInterface $session, AuthorRepository $repository, AuthenticationUtility $authenticationUtils)
    {
        $this->session = $session;
        $this->repository = $repository;
        $this->authenticationUtils = $authenticationUtils;
    }

    /**
     * @Route("/author/{page}", name="author", requirements={"page": "\d+"});
     */
    public function index($page = 1, Request $request, UrlGeneratorInterface $router)
    {
        $filter = $this->setFilter($request);

        $limit = 3;
        $total = $this->repository->getCountForGrid($filter['q'], $filter['country']);

        $parameters = $this->getParameters($filter);
        $paginatorUrl = new PaginatorUrlGenerator($router, 'author', $parameters, null);
        $paginator = new PaginatorUtility($paginatorUrl, $total, $limit, $page);
        $pagination = $paginator->getPaginator();

        $rows = $this->repository->getForGrid($limit, $pagination['offset'], $filter['q'], $filter['country']);

        $countries = $this->repository->getDistinctCountries();
        return $this->render('author/index.html.twig', [
            'pagination' => $pagination,
            'rows' => $rows,
            'countries' => $countries,
            'filter' => $filter,
            'exceptions' => $this->session->getFlashBag()->get('exceptions', []),
            'messages' => $this->session->getFlashBag()->get('messages', []),
        ]);
    }

    /**
     * @Route("/author/add/{id}", name="add_edit_author", requirements={"id": "\d+"});
     */
    public function addEdit($id = null, Request $request)
    {
        if(!$this->authenticationUtils->isUserLogged())
        {
            $message = (empty($id)?"You have to login to add author.":"You have to login to edit author.");
            $this->session->getFlashBag()->add('messages', $message);
            return $this->redirectToRoute('app_login', ['continue' => $this->generateUrl('add_edit_author', ['id' => $id])]);
        }

        $options = $this->buildForm();

        $author = new Author();
        if($id !== null)
        {
            try {
                $author = $this->getAuthor($id);
            } catch (ExceptionInterface $e) {
                $options['exception'] = $e->getMessageKey();
            }
        }

        if(
            'add_edit_author' === $request->attributes->get('_route') && 
            $request->isMethod('POST')
        )
        {
            $this->checkCsrfToken($request->get('_csrf_token'));

            $author->setName((string) $request->request->get('name'));
            $author->setSurname((string) $request->request->get('surname'));
            $author->setCountry((string) $request->request->get('country'));

            if($this->validateAuthor($author))
            {
                if($author->getId() !== null)
                    $this->updateAuthor($author);
                else
                {
                    $author = $this->saveAuthor($author);
                }
                
                switch ($request->request->get('action')) {
                    case 'save_add':
                        return $this->redirectToRoute('add_edit_author');
                    case 'save_return':
                        return $this->redirectToRoute('author');
                    default:
                        return $this->redirectToRoute('add_edit_author', ['id' => $author->getId()]);
                }
            }
            else
            {
                $options['errors'] = $this->getErrorsValidation();
            }
        }

        return $this->render('author/add_edit.html.twig', array_merge($options, [
            'author' => $author
        ]));
    }

    /**
     * @Route("/author/del/{id}", name="del_author", requirements={"id": "\d+"});
     */
    public function delete($id, Request $request)
    {
        if(!$this->authenticationUtils->isUserLogged())
        {
            $this->session->getFlashBag()->add('messages', "You have to login to remove author.");
            return $this->redirectToRoute('app_login', ['continue' => $this->generateUrl('del_author', ['id' => $id])]);
        }

        try {
            $author = $this->getAuthor($id);
            $this->repository->removeAuthor($author);
            $this->session->getFlashBag()->add('messages', "Author was delete.");
        } catch (ExceptionInterface $e) {
            $this->session->getFlashBag()->add('exceptions', $e->getMessageKey());
        }

        if(!empty($request->server->get('HTTP_REFERER')))
            return $this->redirect($request->server->get('HTTP_REFERER'));
        else
            return $this->redirectToRoute('author');
    }
}
