<?php

namespace App\Controller;

use App\Entity\ItemCollection;
use App\Form\CollectionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CollectionController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {
    }

    #[Route('/collection', name: 'app_collection')]
    public function index(): Response
    {
        return $this->render('collection/index.html.twig', [
            'controller_name' => 'CollectionController',
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
        }

        $this->addFlash('success', 'Collection successfully created');

        return $this->render('collection/form.html.twig', [
            'action' => 'create',
            'form' => $form
        ]);
    }
}
