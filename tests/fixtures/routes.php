<?php

use LukasJankowski\Routing\RouteBuilder;

return [
    RouteBuilder::get('/')->build(),
    RouteBuilder::post('/another')->build(),
];
