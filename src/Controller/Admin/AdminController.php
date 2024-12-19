<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdminController extends AbstractController
{

    #[Route(path: '/admin', name: 'page_admin')]
    #[IsGranted('ROLE_ADMIN')]
    public function admin(): Response
    {
        return $this->render(view: 'admin/admin.html.twig');
    }

    #[Route(path: '/admin/movies', name: 'page_admin_movies')]
    #[IsGranted('ROLE_ADMIN')]
    public function films(): Response
    {
        return $this->render(view: 'admin/admin_films.html.twig');
    }

    #[Route(path: '/admin/movies/add', name: 'page_admin_movies_add')]
    #[IsGranted('ROLE_ADMIN')]
    public function addMovies(): Response
    {
        return $this->render(view: 'admin/admin_add_films.html.twig');
    }

    #[Route(path: '/admin/users', name: 'page_admin_users')]
    #[IsGranted('ROLE_ADMIN')]
    public function users(): Response
    {
        return $this->render(view: 'admin/admin_users.html.twig');
    }
}
