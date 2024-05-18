<?php

namespace App\Controller;

use App\Entity\Item;
use App\Repository\ItemCollectionRepository;
use App\Repository\ItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/search', name: 'app_search')]
    public function search(Request $request, ItemRepository $itemRepository, EntityManagerInterface $em)
    {
        // $query = $request->query->get('query', '');
        // $results = $query ? $itemRepository->search($query) : [];
        // return $results;

        // return $this->render('search/search.html.twig', [
        //     'results' => $results,
        // ]);
        $query = ($request->query->get('query'));
        $queryBuilder = $em->getRepository(Item::class)->search($query);
        dd($queryBuilder);
        // $query = $em->createQuery('SELECT i.name FROM App\Entity\Item i where MATCH_AGAINST (i.name, viper) > 0');
        // $result = $query->getResult();
        return new JsonResponse($result);




    }
}
