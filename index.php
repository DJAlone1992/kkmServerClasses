<?php

use Djalone\KkmServerClasses\Cheque;
use Djalone\KkmServerClasses\Cheque\Enums\PaymentTypes;
use Djalone\KkmServerClasses\Cheque\Items\Position;
use Djalone\KkmServerClasses\Services\CustomGUID;
use Djalone\KkmServerClasses\Services\Helper;

require_once 'vendor/autoload.php';




$cheque = new Cheque(
    'Иванов И.И.',
    '860205784807',
    '',
    CustomGUID::getCommandGuid()
);
$cheque->setClientAddress('+79998887766')->setClientInfo('Петров П.П.')->setIsFiscal(true);

$cheque->addPosition(
    new Position('Товар 1', 1000, 1000)
)->addPosition(
    (new Position('Товар 2', 2000, 2000))->setPaymentType(PaymentTypes::Electronic)
);

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
    <?= Helper::echoForm($cheque, 'testCallback.php', '', [], false) ?>
    <button type="button" onclick="<?= Helper::formSubmitScript() ?>">Тестовый чек</button>
</body>

</html>