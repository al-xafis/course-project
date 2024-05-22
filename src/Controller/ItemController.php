<?php

namespace App\Controller;

use App\Entity\Item;
use App\Entity\ItemAttribute;
use App\Form\ItemType;
use App\Form\ItemUpdateType;
use App\Form\Type\CustomCollectionType;
use App\Repository\ItemCollectionRepository;
use App\Repository\ItemRepository;
use App\Repository\TagRepository;
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


    #[Route('/items', name: 'app_items', methods: [Request::METHOD_GET])]
    public function index(): Response
    {
        return $this->render('item/index.html.twig', [
            'controller_name' => 'ItemController',
        ]);
    }

    #[Route('/items/search', name: 'app_items_by_tag', methods: [Request::METHOD_GET])]
    public function showByTag(Request $request, TagRepository $tagRepository, ItemRepository $itemRepository,): Response
    {
        $queryTag = ($request->query->get('tag'));
        $tags = $tagRepository->findBy(['name' => $queryTag]);
        $items = [];
        foreach($tags as $tag) {
            $items[] = $tag->getItem();
        }

        return $this->render('item/items.html.twig', [
            'items' => $items
        ]);
    }

    #[Route('/item/{id}', name: 'app_item', methods: [Request::METHOD_GET])]
    public function show(Item $item): Response
    {
        // dd($item);
        return $this->render('item/item.html.twig', [
            'item' => $item
        ]);
    }



    #[Route('/items/create', name: 'app_item_create', methods: [Request::METHOD_POST, Request::METHOD_GET])]
    public function create(Request $request, ItemCollectionRepository $rep): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $itemCollection = $rep->findAll();
        if (!$itemCollection) {
            $this->addFlash('warning', 'Please create at least one collection before creating an Item');
            return $this->redirectToRoute('app_home');
        }

        $item = new Item();
        $owner = $this->getUser();

        $form = $this->createForm(ItemType::class,  $item, ['action' => $this->generateUrl('app_item_create'), 'allow_extra_fields' => true] );

        $metadata = $this->entityManager->getClassMetadata(Item::class);
        $keys = $metadata->getFieldNames();
        $keys[] = "itemCollection";
        $keys[] = "tags";
        $keys[] = "owner";
        $keys[] = '_token';
        $keys = array_flip($keys);

        $requestAttributes = $request->request->all();
        $itemAttributes = $requestAttributes['item'] ?? null;
        if (isset($itemAttributes)) {
            $customAttributes = array_diff_key($itemAttributes, $keys);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (isset($customAttributes)) {
                foreach($customAttributes as $name => $value) {
                    $itemAttribute = new ItemAttribute();
                    $itemAttribute->setName($name);
                    $itemAttribute->setValue($value);
                    $item->addItemAttribute($itemAttribute);
                }
            }
            $item->setOwner($owner);
            $this->entityManager->persist($item);
            $this->entityManager->flush();
            $this->addFlash('success', 'Item successfully created');
        }

        return $this->render('item/form.html.twig', [
            'form' => $form,
            'action' => 'create'
        ]);
    }


    #[Route('/items/{id}/update', name: 'app_item_update', methods: [Request::METHOD_POST, Request::METHOD_GET])]
    public function update(Request $request, Item $item): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $form = $this->createForm(ItemUpdateType::class,  $item, ['allow_extra_fields' => true]);

        $metadata = $this->entityManager->getClassMetadata(Item::class);
        $keys = $metadata->getFieldNames();
        $keys[] = "itemCollection";
        $keys[] = "tags";
        $keys[] = "owner";
        $keys[] = '_token';
        $keysToBeRemoved = array_flip($keys);

        $requestAttributes = $request->request->all();
        $itemAttributes = $requestAttributes['item_update'] ?? null;
        if (isset($itemAttributes)) {
            $customAttributes = array_diff_key($itemAttributes, $keysToBeRemoved);
        }

        $oldCustomAttributes = $item->getItemAttributes();

        $form->handleRequest($request);
        // dd($form);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach($oldCustomAttributes as $oldItemAttribute) {
                $item->removeItemAttribute($oldItemAttribute);
            }
            if (isset($customAttributes)) {
                foreach($customAttributes as $name => $value) {
                    $itemAttribute = new ItemAttribute();
                    $itemAttribute->setName($name);
                    $itemAttribute->setValue($value);
                    $item->addItemAttribute($itemAttribute);
                }
            }

            // $this->entityManager->persist($item);
            $this->entityManager->flush();
            $this->addFlash('success', 'Item successfully updated');
            $this->redirectToRoute('app_items');
        }
        // dd($item->getItemAttributes()->getValues());
        return $this->render('item/update.html.twig', [
            'form' => $form,
            'action' => 'update',
            'item' => $item
        ]);
    }


    #[Route('/items/{id}/delete', name: 'app_item_delete', methods: [Request::METHOD_POST, Request::METHOD_GET])]
    public function delete(Item $item): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $this->entityManager->remove($item);
        $this->entityManager->flush();
        $this->addFlash('success', 'Item successfully deleted');

        return $this->redirectToRoute('app_items');
    }


}
