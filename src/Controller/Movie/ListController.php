<?php

declare(strict_types=1);

namespace App\Controller\Movie;

use App\Entity\Playlist;
use App\Repository\PlaylistRepository;
use App\Repository\PlaylistSubscriptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ListController extends AbstractController
{
    private PlaylistRepository $playlistRepository;
    private PlaylistSubscriptionRepository $playlistSubscriptionRepository;

    public function __construct(PlaylistRepository $playlistRepository, PlaylistSubscriptionRepository $playlistSubscriptionRepository)
    {
        $this->playlistRepository = $playlistRepository;
        $this->playlistSubscriptionRepository = $playlistSubscriptionRepository;
    }


    #[Route('/lists', name: 'page_lists')]
    #[IsGranted('ROLE_USER')]
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        if (!$user)
        {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page.');
            return $this->redirectToRoute('page_homepage');
        }

        $playlists = $this->playlistRepository->findBy(['creator' => $user]);
        $selectedPlaylistId = $request->query->get('selectedPlaylist');
        $selectedPlaylist = null;

        if ($selectedPlaylistId)
        {
            $selectedPlaylist = $this->playlistRepository->find($selectedPlaylistId);

            if ($selectedPlaylist && $selectedPlaylist->getCreator() !== $user)
            {
                throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à accéder à cette playlist.');
            }
        }

        return $this->render('movie/lists.html.twig', [
            'playlists' => $playlists,
            'selectedPlaylist' => $selectedPlaylist,
        ]);
    }
}
