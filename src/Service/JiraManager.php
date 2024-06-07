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
                    'name' => 'Story'
                ],
                'project' => [
                    'id' => '10000'
                ],
                'customfield_10033' => $link,
                'customfield_10034' => [
                    'value' => $priority
                ],
                'customfield_10035' => [
                    'value' => $status
                ],
                'reporter' => [
                    'id' => $reporter_id
                ],
                'summary' => $summary
            ]]

        ]);

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
}