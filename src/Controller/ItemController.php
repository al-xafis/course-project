<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Item;
use App\Entity\ItemAttribute;
use App\Form\CommentType;
use App\Form\ItemType;
use App\Form\ItemUpdateType;
use App\Form\Type\CustomCollectionType;
use App\Repository\ItemCollectionRepository;
use App\Repository\ItemRepository;
use App\Repository\TagRepository;
use Doctrine\DBAL\Types\TextType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ItemController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager, private Security $security, private ItemRepository $itemRepository) {
    }


    #[Route('/items', name: 'app_items', methods: [Request::METHOD_GET])]
    public function index(): Response
    {
        $items = $this->itemRepository->findAll();

        return $this->render('item/index.html.twig', [
            'items' => $items,
        ]);
    }

    #[Route('/items/search', name: 'app_items_by_tag', methods: [Request::METHOD_GET])]
    public function showByTag(Request $request, TagRepository $tagRepository): Response
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

    #[Route('/item/{id}', name: 'app_item', methods: [Request::METHOD_POST, Request::METHOD_GET])]
    public function show(Request $request, int $id, HubInterface $hub): Response
    {
        $item = $this->itemRepository->FindOneByIdJoined($id);
        $user = $this->getUser();
        $comment = new Comment();
        $form = $this->createForm(CommentType::class,  $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUser($user);
            $comment->setItem($item);
            $this->entityManager->persist($comment);
            $this->entityManager->flush();



            $update = new Update(
                '/comments',
                json_encode(['comment' => $comment->getComment(),
                            'author_first_name' => $user->getFirstName(),
                            'author_last_name' => $user->getLastName()])
            );

            $hub->publish($update);

            unset($form);
            unset($comment);
            $comment = new Comment();
            $form = $this->createForm(CommentType::class,  $comment);

        }

        return $this->render('item/item.html.twig', [
            'form' => $form,
            'item' => $item,
        ]);
    }



    #[Route('/items/create', name: 'app_item_create', methods: [Request::METHOD_POST, Request::METHOD_GET])]
    public function create(Request $request, ItemCollectionRepository $itemCollectionRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $owner = $this->getUser();

        if ($this->security->isGranted('ROLE_ADMIN')) {
            $itemCollection = $itemCollectionRepository->findAll();
        } else {
            $itemCollection = $itemCollectionRepository->findBy(['owner' => $owner]);
        }

        if (!$itemCollection) {
            $this->addFlash('warning', 'Please create at least one your own collection before creating an Item');
            return $this->redirectToRoute('app_home');
        }

        $item = new Item();


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
            // dd($customAttributes);
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
            return $this->redirectToRoute('app_items');
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

        if ($item->getOwner() !== $this->getUser() && !$this->security->isGranted('ROLE_ADMIN')) {
            $this->addFlash('warning', 'Unable to update foreign item');
            return $this->redirectToRoute('app_home');
        }

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
                    $item->setUpdatedAt(new \DateTime());
                }
            }

            $this->entityManager->flush();
            $this->addFlash('success', 'Item successfully updated');
            $this->redirectToRoute('app_items');
        }

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

        if ($item->getOwner() !== $this->getUser() && !$this->security->isGranted('ROLE_ADMIN')) {
            $this->addFlash('warning', 'Unable to delete foreign item');
            return $this->redirectToRoute('app_home');
        }

        $this->entityManager->remove($item);
        $this->entityManager->flush();
        $this->addFlash('success', 'Item successfully deleted');

        return $this->redirectToRoute('app_items');
    }




}
