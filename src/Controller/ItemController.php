<?php

namespace App\Controller;

use App\Entity\Item;
use App\Form\ItemType;
use App\Form\Type\CustomCollectionType;
use App\Repository\ItemCollectionRepository;
use Doctrine\DBAL\Types\TextType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ItemController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {
    }


    #[Route('/items', name: 'app_items')]
    public function index(): Response
    {
        return $this->render('item/index.html.twig', [
            'controller_name' => 'ItemController',
        ]);
    }

    #[Route('/item/{id}', name: 'app_item')]
    public function show(Item $item): Response
    {

        return $this->render('item/item.html.twig', [
            'item' => $item
        ]);
    }



    #[Route('/items/create', name: 'app_item_create', methods: ['POST', 'GET'])]
    public function create(Request $request, ItemCollectionRepository $rep): Response
    {

        $item = new Item();

        $form = $this->createForm(ItemType::class,  $item, ['action' => $this->generateUrl('app_item_create'), 'allow_extra_fields' => true] );


        $form->handleRequest($request);

        // $customAttributes = $request->request->all();
        // foreach($customAttributes as $attr) {
        //     dump($attr);
        // }

        if ($form->isSubmitted() && $form->isValid()) {
            // dd($form->get('Author'));
            $this->entityManager->persist($item);
            $this->entityManager->flush();
            $this->addFlash('success', 'Item successfully created');
        }

        return $this->render('item/form.html.twig', [
            'form' => $form,
            'action' => 'create'
        ]);
    }


    #[Route('/items/{id}/update', name: 'app_item_update')]
    public function update(Request $request, Item $item): Response
    {

        $form = $this->createForm(ItemType::class,  $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'Item successfully updated');
        }

        return $this->render('item/form.html.twig', [
            'form' => $form,
            'action' => 'update',
            'item' => $item
        ]);
    }


    #[Route('/items/{id}/delete', name: 'app_item_delete')]
    public function delete(Item $item): Response
    {
        $this->entityManager->remove($item);
        $this->entityManager->flush();
        $this->addFlash('success', 'Item successfully deleted');

        return $this->redirectToRoute('app_items');
    }


}
