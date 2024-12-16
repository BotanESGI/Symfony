<?php

declare(strict_types=1);

namespace App\Controller\Movie;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\MediaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    #[Route(path: '/category/{id}', name: 'page_category')]
    public function category(Category $category = null, CategoryRepository $categoryRepository, MediaRepository $mediaRepository): Response
    {
        if (!$category) {
            $this->addFlash('error', 'La catégorie demandée n\'existe pas.');
            return $this->redirectToRoute('page_homepage');
        }

        $movies = $mediaRepository->findAll();
        $categories = $categoryRepository->findAll();
        $recommendedMovies = [];


        if (count($movies) > 3) {
            $randomKeys = array_rand($movies, 3);
            foreach ($randomKeys as $key) {
                $recommendedMovies[] = $movies[$key];
            }
        } else {
            $recommendedMovies = $movies;
        }

        return $this->render('movie/category.html.twig', [
            'category' => $category,
            'categories' => $categories,
            'movies' => $movies,
            'recommendedMovies' => $recommendedMovies,
        ]);
    }


    #[Route(path: '/discover', name: 'page_discover')]
    public function discover(CategoryRepository $categoryRepository, MediaRepository $mediaRepository): Response
    {
        $movies = $mediaRepository->findAll();
        $recommendedMovies = [];
        if (count($movies) > 3)
        {
            $randomKeys = array_rand($movies, 3);
            foreach ($randomKeys as $key)
            {
                $recommendedMovies[] = $movies[$key];
            }
        }
        else
        {
            $recommendedMovies = $movies;
        }
        $categories = $categoryRepository->findAll();
        return $this->render('movie/discover.html.twig', [
            'categories' => $categories,
            'recommendedMovies' => $recommendedMovies,
        ]);
    }
}
