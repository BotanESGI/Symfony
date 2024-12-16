<?php

declare(strict_types=1);

namespace App\Controller\Movie;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Media;
use App\Repository\MediaRepository;

class MovieController extends AbstractController
{
    #[Route(path: '/movie/{id}', name: 'page_detail_movie')]
    public function detail($id, MediaRepository $mediaRepository): Response
    {
        $movie = $mediaRepository->find($id);
        if (!$movie) {
            $this->addFlash('error', sprintf(
                'Le film avec l\'ID %d que vous avez demandé n\'existe pas.',
                $id
            ));

            return $this->redirectToRoute('page_homepage');
        }

        return $this->render('movie/detail.html.twig', [
            'movie' => $movie,
        ]);
    }


    #[Route(path: '/serie', name: 'page_detail_serie')]
    public function detailSerie(): Response
    {
        return $this->render(view: 'movie/detail_serie.html.twig');
    }
}
