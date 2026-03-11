<?php

declare(strict_types=1);

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Djalone\KkmServerClasses\Services\Logger;

require_once __DIR__ . '/../vendor/autoload.php';

$logger = Logger::getInstance();

$request = Request::createFromGlobals();

$logger->info('SessionSaver request received', [
    'action' => $request->query->get('action'),
    'method' => $request->getMethod(),
    'isAjax' => $request->isXmlHttpRequest(),
    'clientIp' => $request->getClientIp(),
    'userAgent' => $request->headers->get('User-Agent'),
]);

if (!$request->isXmlHttpRequest()) {
    $logger->warning('Non-AJAX request rejected', [
        'clientIp' => $request->getClientIp(),
    ]);
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

$data = [];

// Валидация action
if (!$action || trim($action) === '') {
    $data = [
        'error' => true,
        'errorCode' => 1,
        'errorText' => 'Параметр action не указан',
    ];
    $logger->warning('Missing or empty action parameter', [
        'clientIp' => $request->getClientIp(),
    ]);
} else {
    try {
        $action = trim($action);
        switch ($action) {
            case 'get':
                if (isset($_SESSION['currentKktNumber'])) {
                    $data = [
                        'error' => false,
                        'kktNumber' => $_SESSION['currentKktNumber'],
                    ];
                    $logger->info('KKT number retrieved from session', ['kktNumber' => $_SESSION['currentKktNumber']]);
                } else {
                    $data = [
                        'error' => true,
                        'errorCode' => 2,
                        'errorText' => 'Не указан номер ККТ в сессии',
                    ];
                    $logger->warning('Attempt to get KKT number but not set in session');
                }
                break;
            case 'set':
                $kktNumber = $request->query->get('kktNumber');
                if ($kktNumber && trim($kktNumber) !== '') {
                    $_SESSION['currentKktNumber'] = trim($kktNumber);
                    $data = [
                        'error' => false,
                        'kktNumber' => $_SESSION['currentKktNumber'],
                    ];
                    $logger->info('KKT number set in session', ['kktNumber' => $_SESSION['currentKktNumber']]);
                } else {
                    $data = [
                        'error' => true,
                        'errorCode' => 3,
                        'errorText' => 'Неверный номер ККТ',
                    ];
                    $logger->warning('Attempt to set invalid KKT number', ['kktNumber' => $kktNumber]);
                }
                break;
            case 'clear':
                $_SESSION['currentKktNumber'] = null;
                $data = [
                    'error' => false,
                ];
                $logger->info('KKT number cleared from session');
                break;
            default:
                $data = [
                    'error' => true,
                    'errorCode' => 1,
                    'errorText' => 'Неверное действие',
                ];
                $logger->warning('Invalid action requested', ['action' => $action]);
                break;
        }
    } catch (\Throwable $e) {
        $logger->error('Unexpected error in SessionSaver', [
            'exception' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'action' => $action,
        ]);
        $data = [
            'error' => true,
            'errorCode' => 4,
            'errorText' => 'Внутренняя ошибка сервера',
        ];
    }
}

$response = new JsonResponse($data);
$response->headers->set('Content-Type', 'application/json; charset=utf-8');
$response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
$response->headers->set('Pragma', 'no-cache');
$response->headers->set('Expires', '0');
$response->prepare($request);
$response->send();
