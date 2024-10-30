<?php

declare(strict_types=1);

namespace App\Controller\Movie;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    #[Route(path: '/category', name: 'page_category')]
    public function category(): Response
    {
        return $this->render(view: 'movie/category.html.twig');
    }

    #[Route(path: '/discover', name: 'page_discover')]
    public function discover(): Response
    {
        return $this->render(view: 'movie/discover.html.twig');
    }
}
