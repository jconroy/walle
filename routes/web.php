<?php

use App\Jobs\ExampleJob;

$app->get('/', function () use ($app) {
	//dispatch(new ExampleJob);
    //return $app->version();
    return 'WALL-E';
});
