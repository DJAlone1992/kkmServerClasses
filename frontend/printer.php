<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

use Djalone\KkmServerClasses\Cheque\Enums\PaymentTypes;
use Djalone\KkmServerClasses\Services\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;

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


$twig = new Environment(new FilesystemLoader(__DIR__ . '/templates'));
$request = Request::createFromGlobals();

$callbackUrl = $request->request->get('callbackUrl', null);
$chequeJson = $request->request->get('chequeJson', null);
if (strpos($chequeJson, '&quot;') !== false) {
	$chequeJson = htmlspecialchars_decode($chequeJson);
}

$error = false;
$errors = [];
if (!$chequeJson) {
	$error = true;
	$errors[] = 'Не передан чек в формате JSON';
} elseif (!$callbackUrl) {
	$error = true;
	$errors[] = 'Не передан указана ссылка возврата данных';
} else {
	try {
		$cheque = Serializer::deserializeCheque($chequeJson);
		if (!$cheque->isValid()) {
			$error = true;
			$errors = array_merge($errors, $cheque->getErrors());
		}
	} catch (Exception $e) {
		$error = true;
		$errors[] = $e->getMessage();
	}
}
if ($error) {
	$context = [
		'errors' => $errors,
	];
	$response = new Response(
		$twig->render('error.html.twig', $context),
		Response::HTTP_BAD_REQUEST
	);
	$response->prepare($request);
	$response->send();
	exit();
}

$context = [
	'chequeJson' => $cheque->toJson(),
	'cheque' => $cheque->toRealArray(),
	'callbackUrl' => $callbackUrl,
	'vendorDir' => VENDOR_DIR,
	'frontendDir' => FRONTEND_DIR,
	'backendDir' => BACKEND_DIR,
	'TypeCheckString' => $cheque->getChequeType()->getName()
];
$twig->addFilter(
	new TwigFilter(
		'paymentType',
		fn(?int $value) => PaymentTypes::getShortName(
			is_null($value)
				? PaymentTypes::Cash
				: (PaymentTypes::tryFrom($value) ?:
					PaymentTypes::Cash)
		)
	)
);
$response = new Response(
	$twig->render('printer.html.twig', $context),
	Response::HTTP_OK
);
$response->prepare($request);
$response->send();

function num_to_words(int $value, array $words, bool $show = true)
{
	$num = $value % 100;
	if ($num > 19) {
		$num %= 10;
	}

	$out = $show ? $value . ' ' : '';
	switch ($num) {
		case 1:
			$out .= $words[0];
			break;
		case 2:
		case 3:
		case 4:
			$out .= $words[1];
			break;
		default:
			$out .= $words[2];
			break;
	}

	return $out;
}
