<?php

use Djalone\KkmServerClasses\Cheque;
use Djalone\KkmServerClasses\Cheque\Enums\ChequeType;
use Djalone\KkmServerClasses\Cheque\Enums\PaymentTypes;
use Djalone\KkmServerClasses\Cheque\Items\Position;
use Djalone\KkmServerClasses\Services\CustomGUID;
use Djalone\KkmServerClasses\Services\Helper;

require_once 'vendor/autoload.php';

$posNal = new Position('Товар 1', 1000, 1000);
$posElectron = (new Position('Товар 2', 2000, 2000))->setPaymentType(PaymentTypes::Electronic);

$arrayOfCheques = [
    [
        'type' => ChequeType::INCOME,
        'positions' => [
            $posNal
        ],
        'id' => 'i_n',
        'name' => 'Приход. Наличные'
    ],
    [
        'type' => ChequeType::INCOME,
        'positions' => [
            $posElectron
        ],
        'id' => 'i_e',
        'name' => 'Приход. Безнал'
    ],
    [
        'type' => ChequeType::INCOME,
        'positions' => [
            $posNal,
            $posElectron
        ],
        'id' => 'i_ne',
        'name' => 'Приход. Нал+Безнал'
    ],
    [
        'type' => ChequeType::INCOME_RETURN,
        'positions' => [
            $posNal
        ],
        'id' => 'r_n',
        'name' => 'Возврат. Наличные'
    ],
    [
        'type' => ChequeType::INCOME_RETURN,
        'positions' => [
            $posElectron
        ],
        'id' => 'r_e',
        'name' => 'Возврат. Безнал'
    ],
    [
        'type' => ChequeType::INCOME_RETURN,
        'positions' => [
            $posNal,
            $posElectron
        ],
        'id' => 'r_ne',
        'name' => 'Возврат. Нал+Безнал'
    ],
];


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Стартовая страница</title>
</head>

<body>
    <a href="/frontend/menu.php?cashierName=Иванов И.И.&cashierVatin=123456789012">Меню работы с ККТ</a><br>
    <a href="/frontend/copyPrinter.php?fiscalNumber=2&cashierName=Иванов И.И.&cashierVatin=123456789012">Печать копии чека №2</a><br>
    <?php
    foreach ($arrayOfCheques as $chequeData) {
        $cheque = new Cheque(
            'Иванов И.И.',
            '860205784807',
            '',
            CustomGUID::getCommandGuid()
        );
        $cheque->setClientAddress('+79998887766')->setClientInfo('Петров П.П.')->setIsFiscal(true);
        foreach ($chequeData['positions'] as $pos) {
            $cheque->addPosition($pos);
        }
        $cheque->setChequeType($chequeData['type']);
        echo Helper::echoForm($cheque, 'testCallback.php', $chequeData['id'], [], false);
        echo "<button type=\"button\" onclick=\"" . Helper::formSubmitScript($chequeData['id']) . "\">Тестовый чек. " . $chequeData['name'] . "</button><br>";
    }
    ?>

</body>

</html>