<?php
$router->addRoute('GET', '/user', 'ApiController@showUsers');
$router->addRoute('GET', '/user/{id}', 'ApiController@showUser');
$router->addRoute('POST', '/user', 'ApiController@createUser');
// Add more routes here...