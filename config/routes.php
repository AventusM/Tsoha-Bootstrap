<?php

$routes->get('/', function() {
    HelloWorldController::index();
});

$routes->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
});

$routes->get('/login', function () {
    HelloWorldController::login();
});

$routes->get('/potilas/1', function() {
    HelloWorldController::patientIndex();
});

$routes->get('/potilas/pyynto/1', function() {
    HelloWorldController::patientHelpRequest();
});

$routes->get('/laakari/1', function() {
//HelloWorldController::
});
