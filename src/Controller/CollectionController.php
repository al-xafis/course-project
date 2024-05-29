<?php

namespace App\Controller;

use App\Entity\ItemCollection;
use App\Form\CollectionType;
use App\Repository\ItemCollectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CollectionController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager, private Security $security, private ItemCollectionRepository $itemCollectionRepository) {
    }

    #[Route('/collections', name: 'app_collections', methods: [Request::METHOD_GET])]
    public function index(): Response
    {
        $collections = $this->itemCollectionRepository->findAll();

        return $this->render('collection/index.html.twig', [
            'collections' => $collections,
        ]);
    }


    #[Route('/collection/get', name: 'app_collection_get', methods: [Request::METHOD_POST])]
    public function sendCollection(Request $request, ): Response
    {

        $id = $request->request->get('collectionId');
        if ($id) {
            $collection = $this->itemCollectionRepository->find($id);
            $customItemAttributes = $collection->getCustomItemAttributes()->getValues();
            $response = [];
            foreach ($customItemAttributes as $attribute) {
                $response[] = ['name' => $attribute->getName(), 'type'=>$attribute->getType()];
            }
            return new JsonResponse($response);
        }

        return new Response('No collection ID provided!');


    }

    #[Route('/collection/{id}', name: 'app_collection', methods: [Request::METHOD_GET])]
    public function show(int $id): Response
    {
        $itemCollection = $this->itemCollectionRepository->FindOneByIdJoined($id);

        return $this->render('collection/collection.html.twig', [
            'collection' => $itemCollection
        ]);
    }




    #[Route('/collections/create', name: 'app_collection_create', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function create(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $collection = new ItemCollection();
        $owner = $this->getUser();

        $form = $this->createForm(CollectionType::class,  $collection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $collection->setOwner($owner);
            $this->entityManager->persist($collection);
            $this->entityManager->flush();
            $this->addFlash('success', 'Collection successfully created');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('collection/form.html.twig', [
            'action' => 'create',
            'form' => $form
        ]);
    }


    #[Route('/collections/{id}/update', name: 'app_collection_update', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function update(Request $request, ItemCollection $collection): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        if ($collection->getOwner() !== $this->getUser() && !$this->security->isGranted('ROLE_ADMIN')) {
            $this->addFlash('warning', 'Unable to update foreign collection');
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(CollectionType::class,  $collection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'Collection successfully updated');
            return $this->redirectToRoute('app_home');
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
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        if ($collection->getOwner() !== $this->getUser() && !$this->security->isGranted('ROLE_ADMIN')) {
            $this->addFlash('warning', 'Unable to delete foreign collection');
            return $this->redirectToRoute('app_home');
        }

        $this->entityManager->remove($collection);
        $this->entityManager->flush();
        $this->addFlash('success', 'Collection successfully deleted');

        return $this->redirectToRoute('app_collections');
    }

    #[Route('/collections/delete/all', name: 'app_collection_delete_all', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function deleteAll(): Response
    {

        $collections = $this->itemCollectionRepository->findAll();
        foreach($collections as $collection) {
            $this->entityManager->remove($collection);
        }
        $this->entityManager->flush();
        $this->addFlash('success', 'Collection successfully deleted');

        return $this->redirectToRoute('app_collections');
    }
}
