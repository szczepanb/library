<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Repository\BookRepository;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function home(BookRepository $repository): Response
    {
        $rows = $repository->getForGrid(5, 0);

        return $this->render('home\index.html.twig', [
            'rows' => $rows
        ]);
    }
}
