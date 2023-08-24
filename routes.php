<?php
$router->addRoute('GET', '/user', 'ApiController@showUsers');
$router->addRoute('GET', '/user/{id}', 'ApiController@showUser');
$router->addRoute('POST', '/user', 'ApiController@createUser');
$router->addRoute('PUT', '/user', 'ApiController@putUser');
$router->addRoute('DELETE', '/user/{code}', 'ApiController@deleteUser');
// Add more routes here...