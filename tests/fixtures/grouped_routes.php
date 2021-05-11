<?php

use LukasJankowski\Routing\RouteBuilder;

RouteBuilder::enableStaticCollection();

RouteBuilder::get('/')->build();
RouteBuilder::post('/another')->build();

RouteBuilder::group(['path' => '/prefix'], function () {
    RouteBuilder::get('/nested')->build();
});
