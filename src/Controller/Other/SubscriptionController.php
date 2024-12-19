<?php

declare(strict_types=1);

namespace App\Controller\Other;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\SubscriptionRepository;

class SubscriptionController extends AbstractController
{
    #[Route('/subscriptions', name: 'page_subscription')]
    public function index(SubscriptionRepository $subscriptionRepository): Response
    {
        $user = $this->getUser();
        if ($user) {
            $currentSubscription = $user->getCurrentSubscription();
        }
        else
        {
            $currentSubscription = "Aucun";
        }
        $subscriptions = $subscriptionRepository->findAll();
        return $this->render('other/abonnements.html.twig', [
            'subscriptions' => $subscriptions,
            'currentSubscription' => $currentSubscription,
        ]);
    }
}



