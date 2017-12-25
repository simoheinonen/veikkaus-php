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


        $wtfgsr = <<< JSON
{
      "rows": [
        {
          "id": "0",
          "tvChannel": "Viasat",
          "outcome": {
            "home": {
              "id": "Man Utd",
              "name": "Manchester U"
            },
            "away": {
              "id": "Burnley",
              "name": "Burnley"
            }
          },
          "eventId": "91107453",
          "additionalPrizeTier": true
        },
        {
          "id": "14",
          "outcome": {
            "home": {
              "id": "Chelsea",
              "name": "Chelsea"
            },
            "away": {
              "id": "Brighton",
              "name": "Brighton"
            }
          },
          "eventId": "91107565",
          "additionalPrizeTier": true
        }
    ]
}
JSON;


        $wtf = $this->serializer->deserialize($wtfgsr, Rows::class, 'json');

#        dump($wtf); exit;

        $res = $this->httpClient->get('api/v1/sport-games/draws', $options);

#        echo $res->getBody(); exit;

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
