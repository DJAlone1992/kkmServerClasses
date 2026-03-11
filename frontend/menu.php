<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once __DIR__ . '/../vendor/autoload.php';

$request = Request::createFromGlobals();
$cashierName = $request->query->get('cashierName');
$cashierVatin = $request->query->get('cashierVatin');

$context = [
	'cashierName' => $cashierName,
	'cashierVatin' => $cashierVatin,
];

$twig = new Environment(new FilesystemLoader(__DIR__ . '/templates'));
$response = new Response(
	$twig->render('menu.html.twig', $context),
	Response::HTTP_OK
);
$response->prepare($request);
$response->send();
