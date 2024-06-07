<?php

namespace App\Controller;

use App\Repository\ItemCollectionRepository;
use App\Repository\ItemRepository;
use App\Repository\TicketRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    public function __construct(private Security $security) {}

    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(Request $request, ItemRepository $itemRepository, ItemCollectionRepository $itemCollectionRepository, TicketRepository $ticketRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        if ($this->security->isGranted('ROLE_ADMIN')) {
            $items = $itemRepository->findAll();
            $collections = $itemCollectionRepository->findAll();
            $tickets = $ticketRepository->findAll();
        } else {
            $owner = $this->getUser();
            $items = $itemRepository->findBy(['owner' => $owner]);
            $collections = $itemCollectionRepository->findBy(['owner' => $owner]);

            $ticketPage = $request->get('ticketPage', 1);
            $limit = 3;
            $tickets = $ticketRepository->getTicketsInRange($owner, $ticketPage, $limit);
        }

        $paginatedTicket = new Paginator($tickets, fetchJoinCollection: true);
        $totalTickets= $paginatedTicket->count();
        $totalTicketPage = (int) ceil($totalTickets / $limit);


        return $this->render('dashboard/index.html.twig', [
            'items' => $items,
            'collections' => $collections,
            'tickets' => $paginatedTicket,
            'totalTicketPage' => $totalTicketPage
        ]);
    }
}
