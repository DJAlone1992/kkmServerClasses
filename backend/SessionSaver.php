<?php

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require_once __DIR__ . '/../vendor/autoload.php';

$request = Request::createFromGlobals();

if (!$request->isXmlHttpRequest()) {
	$response = new Response(
		'Only ajax requests allowed',
		Response::HTTP_BAD_REQUEST
	);
	$response->prepare($request);
	$response->send();
	exit();
}

$action = $request->query->get('action');

if (session_status() == PHP_SESSION_NONE) {
	session_start();
}
switch ($action) {
	case 'get':
		if (
			array_key_exists('currentKktNumber', $_SESSION) &&
			isset($_SESSION['currentKktNumber'])
		) {
			$data = [
				'error' => false,
				'kktNumber' => $_SESSION['currentKktNumber'],
			];
		} else {
			$data = [
				'error' => true,
				'errorCode' => 2,
				'errorText' => 'Не указан номер ККТ в сессии',
			];
		}
		break;
	case 'set':
		$kktNumber = $request->query->get('kktNumber');
		if ($kktNumber) {
			$_SESSION['currentKktNumber'] = $kktNumber;
			$data = [
				'error' => false,
				'kktNumber' => $_SESSION['currentKktNumber'],
			];
		}
		break;
	case 'clear':
		$_SESSION['currentKktNumber'] = null;
		$data = [
			'error' => false,
		];
		break;
}

$response = new JsonResponse($data);
$response->prepare($request);
$response->send();
