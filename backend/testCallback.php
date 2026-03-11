<?php

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/../vendor/autoload.php';

$request = Request::createFromGlobals();

$response = new JsonResponse([
	'error' => false,
	'successText' => $request->__toString(),
]);
$response->prepare($request);
$response->send();
