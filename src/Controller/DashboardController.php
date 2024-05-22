<?php

namespace App\Controller;

use App\Repository\ItemCollectionRepository;
use App\Repository\ItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    public function __construct(private ItemRepository $itemRepository, private ItemCollectionRepository $itemCollectionRepository) {}

    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $owner = $this->getUser();

        $items = $this->itemRepository->findBy(['owner' => $owner]);
        $collections = $this->itemCollectionRepository->findBy(['owner' => $owner]);

        return $this->render('dashboard/index.html.twig', [
            'items' => $items,
            'collections' => $collections,
        ]);
    }
}
