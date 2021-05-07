<?php

require_once __DIR__ . '/vendor/autoload.php';

use Rx\Observable;
use Rx\Observer\CallbackObserver;
use React\EventLoop\Factory;
use Rx\Scheduler;
use Faker\Factory as Faker;

$faker = Faker::create();

$loop = Factory::create();

$list = [];

do {
    $list[] = $faker->name();
} while (count($list) <= 2000);

Scheduler::setDefaultFactory(FUNCTION() use ($loop) {
    return new Scheduler\EventLoopScheduler($loop);
});

$users = Observable::fromArray($list);

$logger = new CallbackObserver(function ($user) {
    echo 'Logging: ' , $user, PHP_EOL;
},
function (Throwable $t){
    echo $t->getMessage(), PHP_EOL;
},
function() {
    echo 'Stream Completed', PHP_EOL;
}
);

$users->subscribe($logger);

$loop->run();