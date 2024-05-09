<?php

namespace App\Controller;

use App\Repository\ItemCollectionRepository;
use App\Repository\ItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ItemRepository $itemRepository, ItemCollectionRepository $itemCollectionRepository): Response
    {
        $items = $itemRepository->findAll();
        $collections = $itemCollectionRepository->findAll();
        // dd($items);
        return $this->render('home/index.html.twig', [
            'items' => $items,
            'collections' => $collections,
        ]);
    }
}
