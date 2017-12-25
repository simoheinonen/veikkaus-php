```php
<?php

use Dudgeon\Veikkaus\Client;
use Dudgeon\Veikkaus\DateTimeHandler;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializerBuilder;

require_once 'vendor/autoload.php';

$client = new \GuzzleHttp\Client([
    'base_uri' => 'https://www.veikkaus.fi/',
    'cookies' => true,
    'headers' => [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'X-ESA-API-Key' => 'ROBOT',
    ]
]);

$serializer = SerializerBuilder::create()
    ->addMetadataDir(__DIR__ . '/config/')
    ->setPropertyNamingStrategy(new SerializedNameAnnotationStrategy(new \JMS\Serializer\Naming\IdenticalPropertyNamingStrategy()))
    ->configureHandlers(
        function (HandlerRegistry $registry) {
            $registry->registerSubscribingHandler(new DateTimeHandler());
        }
    )
    ->build();

$veikkaus = new Client($client, 'username', 'password', $serializer);

$draws = $veikkaus->draws(['SPORT']);

/** @var \Dudgeon\Veikkaus\Draw $draw */
foreach ($draws as $draw) {
    $rows = $draw->getRows();

    echo $draw->getName() . PHP_EOL;

    /** @var \Dudgeon\Veikkaus\Row $row */
    foreach ($rows as $row) {
        $outcome = $row->getOutcome();
        echo '   ' . $outcome->getHome()->getName() . ' - ' . $outcome->getAway()->getName() . PHP_EOL;
    }

    echo PHP_EOL;
}
```

Will print something like

```
Vakio Grand Prix
   Manchester U - Burnley
   Chelsea - Brighton
   Watford - Leicester
   Bournemouth - West Ham
   Huddersfield - Stoke
   West Bromwich - Everton
   Liverpool - Swansea
   Barnsley - Preston
   Bristol C - Reading FC
   Nottingham - Sheffield W
   Hull - Derby
   Middlesbrough - Bolton
   Birmingham - Norwich C
   Burton Albion - Leeds U
   Sheffield U - Sunderland
   Cardiff C - Fulham
   Ipswich T - QPR
   Brentford - Aston Villa

Futisvakio
   Newcastle U - Manchester C
   KV Kortrijk - Standard
   STVV - R Antwerp FC
   Rangers - Motherwell
   Hamilton - Kilmarnock
   Hearts - Hibernian
   Aberdeen - Partick Th
   Ross Co - St Johnstone
   Parma - Spezia

Vakio
   Liverpool - Leicester
   Chelsea - Stoke
   Huddersfield - Burnley
   Bournemouth - Everton
   Watford - Swansea
   Newcastle U - Brighton
   Manchester U - Southampton
   Nottingham - Sunderland
   Ipswich T - Derby
   Middlesbrough - Aston Villa
   Birmingham - Leeds U
   Brentford - Sheffield W
   Barnsley - Reading FC
```