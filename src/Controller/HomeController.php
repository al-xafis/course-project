<?php

namespace App\Controller;

use App\Entity\Item;
use App\Repository\ItemCollectionRepository;
use App\Repository\ItemRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: [Request::METHOD_GET])]
    public function index(ItemRepository $itemRepository, ItemCollectionRepository $itemCollectionRepository, TagRepository $tagRepository): Response
    {
        $items = $itemRepository->findLatestItems();
        // $collections = $itemCollectionRepository->findAll();
        $collections = $itemCollectionRepository->findLargestCollections();
        $tags = $tagRepository->findUniqueAll();
        // dd($tags);

        return $this->render('home/index.html.twig', [
            'items' => $items,
            'collections' => $collections,
            'tags' => $tags,
        ]);
    }

    #[Route('/search', name: 'app_search', methods: [Request::METHOD_GET])]
    public function search(Request $request, ItemRepository $itemRepository, ItemCollectionRepository $itemCollectionRepository)
    {
        $query = ($request->query->get('query'));
        $items = $itemRepository->search($query);
        $collections = $itemCollectionRepository->search($query);
        $result = [];
        foreach($items as $value) {
            $item = ['id' => $value->getId(), 'entity' => 'item', 'name' => $value->getName()];
            $result[] = $item;
        }
        foreach($collections as $value) {
            $collection = ['id' => $value->getId(), 'entity' => 'collection', 'name' => $value->getName()];
            $result[] = $collection;
        }

        return new JsonResponse($result);
    }
}
