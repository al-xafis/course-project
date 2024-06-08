<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

// use Symfony\Component\HttpFoundation\Request;

class JiraManager
{

    public function __construct(private ParameterBagInterface $params, private HttpClientInterface $client) {
    }

    public function createTicket(string $summary, string $link, string $priority, string $status, string $reporter_id)
    {
        $response = $this->client->request(
            Request::METHOD_POST,
            $this->params->get('JIRA_URL_CREATE_TICKET'),
            [
            'auth_basic' => [$this->params->get('JIRA_USERNAME'), $this->params->get('JIRA_TOKEN')],
            'headers' => [
                'Content-Type' => 'Application/json',
            ],
            'json' => ['fields' => [
                'issuetype' => [
                    'name' => 'Ticket'
                ],
                'project' => [
                    'id' => '10000'
                ],
                'customfield_10037' => $link,
                'customfield_10038' => [
                    'value' => $priority
                ],
                'customfield_10036' => [
                    'value' => $status
                ],
                'reporter' => [
                    'id' => $reporter_id
                ],
                'summary' => $summary
            ]]

        ]);
        // dd($response);
        $content = $response->toArray();

        return $content;
    }

    public function createUser(string $email)
    {
        $response = $this->client->request(
            Request::METHOD_POST,
            $this->params->get('JIRA_URL_CREATE_USER'),
            [
            'auth_basic' => [$this->params->get('JIRA_USERNAME'), $this->params->get('JIRA_TOKEN')],
            'headers' => [
                'Content-Type' => 'Application/json',

            ],
            'json' => [
                'emailAddress' => $email,
                'products' => ['jira-software']
                ]

        ]);

        $content = $response->toArray();
        return $content;
    }

    public function getTickets(string $jira_id, string $start_at, string $max_results) {
        $response = $this->client->request(
            Request::METHOD_GET,
            $this->params->get('JIRA_URL_SEARCH') . '?jql=reporter=' . $jira_id .
            '&startAt=' . $start_at .
            '&maxResults=' . $max_results,
            [
            'auth_basic' => [$this->params->get('JIRA_USERNAME'), $this->params->get('JIRA_TOKEN')],
            'headers' => [
                'Content-Type' => 'Application/json',
            ],
        ]);
        $content = $response->toArray();
        return $content;
    }

    public function getTicketsAll(string $start_at, string $max_results) {
        $response = $this->client->request(
            Request::METHOD_GET,
            $this->params->get('JIRA_URL_SEARCH') . '?startAt=' . $start_at .
            '&maxResults=' . $max_results,
            [
            'auth_basic' => [$this->params->get('JIRA_USERNAME'), $this->params->get('JIRA_TOKEN')],
            'headers' => [
                'Content-Type' => 'Application/json',
            ],
        ]);
        $content = $response->toArray();
        return $content;
    }

    public function getTicket(string $ticket_id) {
        $response = $this->client->request(
            Request::METHOD_GET,
            $this->params->get('JIRA_URL_GET_TICKET') . $ticket_id,
            [
            'auth_basic' => [$this->params->get('JIRA_USERNAME'), $this->params->get('JIRA_TOKEN')],
            'headers' => [
                'Content-Type' => 'Application/json',
            ],
        ]);
        $content = $response->toArray();
        return $content;
    }
}