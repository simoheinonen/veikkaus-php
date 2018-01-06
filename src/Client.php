<?php

namespace Dudgeon\Veikkaus;

use GuzzleHttp\Exception\ClientException;
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
            $options = [
                'query' => [
                    'game-names' => implode(',', $gameNames),
                    'previous-days' => 1,
                    'next-days' => 0,
                ]
            ];
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

    public function getall(int $drawId)
    {
        $i = 0;

        $stuffjee = [];

        $draws = $this->draws(['SPORT']);

        /** @var Draw $draw */
        foreach ($draws as $draw) {
            if ($draw->getId() == $drawId) {
                $count = count($draw->getRows());
            }
        }

        do {
            $stuff = $this->getall2($drawId, $i, $count);



            $stuffjee = array_merge($stuffjee, $stuff);

            $i++;
        } while (!empty($stuff));

        return $stuffjee;
    }

    private function getall2(int $drawId, int $page, int $count)
    {
        $outcomes = [];

        for ($i = 0; $i < $count; $i++) {
            $outcomes[] = [
                'home' => ['selected' => true],
                'tie' => ['selected' => true],
                'away' => ['selected' => true],
            ];
        }

        $req = [
            'page' => $page,
            'selections' => [
                [
                    'systemBetType' => 'SYSTEM',
                    'outcomes' => $outcomes,                    ]
                ],
        ];

        $res = $this->httpClient->post('api/v1/sport-games/draws/SPORT/' . $drawId . '/winshares', [
            'json' => $req
        ]);

        $json = json_decode($res->getBody(), true);

        if (empty($json['winShares'])) {
            return [];
        }

        $resee = [];

        foreach ($json['winShares'] as $winShare) {
            $jee = implode('', array_map(function ($jee) {
                $jee = array_keys($jee);
                $aa = reset($jee);

                switch ($aa) {
                    case 'home':
                        return 1;
                        break;
                    case 'tie':
                        return 'X';
                        break;
                    case 'away':
                        return 2;
                        break;
                }

                return '?';
            }, $winShare['selections'][0]['outcomes']));
            $resee[] = [
                '_id' => $jee,
                'numberOfBets' => $winShare['numberOfBets'],
                'value' => $winShare['value'],
            ];
        }

        return $resee;
    }

    public function play(int $drawId, array $rivis, $stake)
    {
        $req = [];

        foreach ($rivis as $i => $rivi) {

            $selections  = [];
            $outcomes = [];

            foreach (str_split($rivi) as $index => $kirjain) {

                $choi =1;

                if ($kirjain == '1') {
                    $choi = 'home';
                }
                if ($kirjain == 'X') {
                    $choi = 'tie';
                }
                if ($kirjain == '2') {
                    $choi = 'away';
                }

                $outcomes[] = [$choi => ['selected' => true]];
            }

            $selections[] = [

                'systemBetType' => 'SYSTEM',
                'outcomes' => $outcomes,
            ];
            $reqPart = [
                'type' => 'NORMAL',
                'drawId' => (string) $drawId,
                'gameName' => 'SPORT',
                'stake' => $stake,
                'price' => $stake *count($selections),
                'selections' => $selections
            ];

            $req[] = $reqPart;
        }

        try {
            $this->httpClient->post('api/v1/sport-games/wagers', ['json' => $req]);

            echo implode("\n", $rivis) . PHP_EOL;
        } catch (ClientException $e) {
            echo 'ERROR' . PHP_EOL;
            echo $e->getResponse()->getBody();
        }
    }
}
