<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

if (!class_exists('Symfony\Component\HttpFoundation\Request')) {
	require_once __DIR__ . '/../vendor/autoload.php';
}

if (!defined('FRONTEND_DIR')) {
	define('FRONTEND_DIR', str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__));
}
if (!defined('BACKEND_DIR')) {
	define('BACKEND_DIR', str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__ . '/../backend'));
}
if (!defined('VENDOR_DIR')) {
	define('VENDOR_DIR', str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__ . '/../vendor'));
}

$request = Request::createFromGlobals();
$cashierName = $request->query->get('cashierName');
$cashierVatin = $request->query->get('cashierVatin');

$context = [
	'cashierName' => $cashierName,
	'cashierVatin' => $cashierVatin,
	'vendorDir' => VENDOR_DIR,
	'frontendDir' => FRONTEND_DIR,
	'backendDir' => BACKEND_DIR,
];

$twig = new Environment(new FilesystemLoader(__DIR__ . '/templates'));
$response = new Response(
	$twig->render('menu.html.twig', $context),
	Response::HTTP_OK
);
$response->prepare($request);
$response->send();
