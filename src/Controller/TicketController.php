<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Form\TicketType;
use App\Repository\TicketRepository;
use App\Service\JiraManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TicketController extends AbstractController
{
    #[Route('/ticket', name: 'app_tickets')]
    public function index(): Response
    {
        return $this->render('ticket/index.html.twig', [
            'controller_name' => 'TicketController',
        ]);
    }



    #[Route('/ticket/create', name: 'app_ticket_create', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function create(Request $request, EntityManagerInterface $entityManager, JiraManager $jira, ParameterBagInterface $params): Response
    {
        $owner = $this->getUser();
        $ticket = new Ticket();
        $form = $this->createForm(TicketType::class,  $ticket, ['action' => $this->generateUrl('app_ticket_create')]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

            $ticket->setReporter($owner);
            $ticket->setLink($request->headers->get('referer'));
            $result = $jira->createTicket(
                $ticket->getSummary(),
                $ticket->getLink(),
                $ticket->getPriority(),
                $ticket->getStatus(),
                $owner->getJiraId(),
            );

            if (array_key_exists('self', $result)) {
                $ticket->setJiraLink($params->get('JIRA_URL_TICKET') . $result['key']);
                $entityManager->persist($ticket);
                $entityManager->flush();
                $this->addFlash('success', 'New ticket: ' . $ticket->getJiraLink());
                return $this->redirectToRoute('app_home');
            }

        }

        return $this->render('ticket/_form.html.twig', [
            'action' => 'create',
            'form' => $form
        ]);
    }

    #[Route('/ticket/{id}', name: 'app_ticket', methods: [Request::METHOD_GET])]
    public function show(int $id, JiraManager $jira): Response
    {
        $ticket = $jira->getTicket($id);
        return $this->render('ticket/ticket.html.twig', [
            'ticket' => $ticket,
        ]);
    }
}
