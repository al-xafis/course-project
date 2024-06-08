<?php

namespace App\Controller;

use App\Repository\ItemCollectionRepository;
use App\Repository\ItemRepository;
use App\Repository\TicketRepository;
use App\Service\JiraManager;
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
    public function index(Request $request, ItemRepository $itemRepository, ItemCollectionRepository $itemCollectionRepository, JiraManager $jira): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $ticketPage = $request->get('ticketPage', 1);
        $limit = 3;

        if ($this->security->isGranted('ROLE_ADMIN')) {
            $items = $itemRepository->findAll();
            $collections = $itemCollectionRepository->findAll();
            $tickets = $jira->getTicketsAll($limit * ($ticketPage-1), $limit);
        } else {
            $owner = $this->getUser();
            $items = $itemRepository->findBy(['owner' => $owner]);
            $collections = $itemCollectionRepository->findBy(['owner' => $owner]);
            $tickets = $jira->getTickets($owner->getJiraId(), $limit * ($ticketPage-1), $limit);
        }

        $totalTickets = $tickets['total'];
        $totalTicketPage = (int) ceil($totalTickets / $limit);
        $tickets = $tickets['issues'];


        return $this->render('dashboard/index.html.twig', [
            'items' => $items,
            'collections' => $collections,
            'tickets' => $tickets,
            'totalTicketPage' => $totalTicketPage
        ]);
    }
}
