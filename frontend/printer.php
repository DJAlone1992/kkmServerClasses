<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

use Djalone\KkmServerClasses\Cheque;
use Djalone\KkmServerClasses\Cheque\Enums\PaymentTypes;
use Djalone\KkmServerClasses\Cheque\Items\Position;
use Djalone\KkmServerClasses\Services\CustomGUID;
use Djalone\KkmServerClasses\Services\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;

require_once __DIR__ . '/../vendor/autoload.php';

$twig = new Environment(new FilesystemLoader(__DIR__ . '/templates'));
$request = Request::createFromGlobals();
$callbackUrl = $request->request->get('callbackUrl', null);

$chequeJson = $request->request->get('chequeJson', null);

$callbackUrl = '../backend/testCallback.php';
$cheque = new Cheque(
	'Иванов И.И.',
	'123456789012',
	'',
	CustomGUID::getCommandGuid()
);
$cheque->setClientAddress('+791234567')->setClientInfo('Иванов Иван Иванович');
$cheque->addItem(
	(new Position('Товар 1', 1000, 2000))->setPaymentType(
		PaymentTypes::Electronic
	) /*
	->addItem(new Position('Товар 2', 2000, 1000))
	->addItem(new Position('Товар 2', 2000, 1000))
	->addItem(new Position('Товар 2', 2000, 1000))
	->addItem(new Position('Товар 2', 2000, 1000))
	->addItem(new Position('Товар 2', 2000, 1000))
	->addItem(new Position('Товар 2', 2000, 1000))*/
);
$chequeJson = Serializer::serializeCheque($cheque);

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
];
$twig->addFilter(
	new TwigFilter(
		'paymentType',
		fn(?int $value) => (is_null($value)
			? PaymentTypes::Cash
			: (PaymentTypes::tryFrom($value) ?:
			PaymentTypes::Cash)
		)->getShortName()
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
