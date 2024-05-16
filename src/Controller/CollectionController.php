<?php

namespace App\Controller;

use App\Entity\ItemCollection;
use App\Form\CollectionType;
use App\Repository\ItemCollectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CollectionController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {
    }

    #[Route('/collections', name: 'app_collections')]
    public function index(): Response
    {
        return $this->render('collection/index.html.twig', [
            'controller_name' => 'CollectionController',
        ]);
    }


    #[Route('/collection/get', name: 'app_collection_get', methods: ['POST'])]
    public function sendCollection(Request $request, ItemCollectionRepository $rep): Response
    {

        $id = $request->request->get('collectionId');
        if ($id) {
            $collection = $rep->find($id);
            $customItemAttributes = $collection->getCustomItemAttributes()->getValues();
            $response = [];
            foreach ($customItemAttributes as $attribute) {
                $response[] = ['name' => $attribute->getName(), 'type'=>$attribute->getType()];
            }
            return new JsonResponse($response);
        }

        return new Response('No collection ID provided!');


    }

    #[Route('/collection/{id}', name: 'app_collection')]
    public function show(ItemCollection $itemCollection): Response
    {

        return $this->render('collection/collection.html.twig', [
            'collection' => $itemCollection
        ]);
    }




    #[Route('/collections/create', name: 'app_collection_create', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function create(Request $request): Response
    {
        $collection = new ItemCollection();

        $form = $this->createForm(CollectionType::class,  $collection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($collection);
            $this->entityManager->flush();
            $this->addFlash('success', 'Collection successfully created');
        }

        return $this->render('collection/form.html.twig', [
            'action' => 'create',
            'form' => $form
        ]);
    }


    #[Route('/collections/{id}/update', name: 'app_collection_update', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function update(Request $request, ItemCollection $collection): Response
    {
        $form = $this->createForm(CollectionType::class,  $collection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'Collection successfully updated');
        }

        return $this->render('collection/form.html.twig', [
            'action' => 'update',
            'form' => $form,
            '$collection' => $collection
        ]);
    }


    #[Route('/collections/{id}/delete', name: 'app_collection_delete', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function delete(ItemCollection $collection): Response
    {
        $this->entityManager->remove($collection);
        $this->entityManager->flush();
        $this->addFlash('success', 'Collection successfully deleted');

        return $this->redirectToRoute('app_collections');
    }
}
