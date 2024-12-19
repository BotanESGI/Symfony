<?php

declare(strict_types=1);

namespace App\Controller\Movie;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Media;
use App\Repository\MediaRepository;
use App\Repository\WatchHistoryRepository;

class MovieController extends AbstractController
{
    #[Route(path: '/movie/{id}', name: 'page_detail_movie')]
    public function detail($id, MediaRepository $mediaRepository, WatchHistoryRepository $watchHistoryRepository): Response
    {
        $movie = $mediaRepository->find($id);
        $numberOfViews = $watchHistoryRepository->countViewsByMediaId($id);
        if (!$movie) {
            $this->addFlash('error', sprintf(
                'Le film avec l\'ID %d que vous avez demandé n\'existe pas.',
                $id
            ));

            return $this->redirectToRoute('page_homepage');
        }

        $languages = $movie->getLanguages();

        return $this->render('movie/detail.html.twig', [
            'movie' => $movie,
            'languages' => $languages,
            'numberOfViews' => $numberOfViews,
        ]);
    }


    #[Route(path: '/serie', name: 'page_detail_serie')]
    public function detailSerie(): Response
    {
        return $this->render(view: 'movie/detail_serie.html.twig');
    }
}
