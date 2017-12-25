<?php

namespace Dudgeon\Veikkaus;

use JMS\Serializer\Serializer;

class Client
{
    private $httpClient;
    private $serializer;

    public function __construct(\GuzzleHttp\Client $httpClient, string $username, string $password, Serializer $serializer)
    {
        $this->httpClient = $httpClient;

        $this->login($username, $password);
        $this->serializer = $serializer;
    }

    public function draws(array $gameNames = [])
    {
        $options = [];

        if ($gameNames) {
            $options = ['query' => ['game-names' => implode(',', $gameNames)]];
        }

        $res = $this->httpClient->get('api/v1/sport-games/draws', $options);

        return $this->serializer->deserialize($res->getBody(), Draws::class, 'json');
    }

    private function login(string $username, string $password)
    {
        $this->httpClient->post('api/bff/v1/sessions', [
            'json' => [
                'type' => 'STANDARD_LOGIN',
                'login' => $username,
                'password' => $password,
            ]
        ]);
    }
}
