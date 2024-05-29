<?php

namespace App\Controller;

use App\Repository\ItemCollectionRepository;
use App\Repository\ItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    public function __construct(private ItemRepository $itemRepository, private ItemCollectionRepository $itemCollectionRepository, private Security $security) {}

    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        if ($this->security->isGranted('ROLE_ADMIN')) {
            $items = $this->itemRepository->findAll();
            $collections = $this->itemCollectionRepository->findAll();
        } else {
            $owner = $this->getUser();
            $items = $this->itemRepository->findBy(['owner' => $owner]);
            $collections = $this->itemCollectionRepository->findBy(['owner' => $owner]);
        }


        return $this->render('dashboard/index.html.twig', [
            'items' => $items,
            'collections' => $collections,
        ]);
    }
}
