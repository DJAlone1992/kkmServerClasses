<?php

use Djalone\KkmServerClasses\Cheque;
use Djalone\KkmServerClasses\Cheque\Items\Position;
use Djalone\KkmServerClasses\Services\CustomGUID;
use Djalone\KkmServerClasses\Services\Helper;

require_once 'vendor/autoload.php';




$cheque = new Cheque(
    'Иванов И.И.',
    '123456789012',
    '',
    CustomGUID::getCommandGuid()
);
$cheque->setClientAddress('+79998887766')->setClientInfo('Петров П.П.');

$cheque->addPosition(
    new Position('Товар 1', 1000, 1000)
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
    <a href="/frontend/menu.php?cashierName=Иванов И.И.&cashierVatin=123456789012">Меню работы с ККТ</a>
    <?= Helper::echoForm($cheque, 'testCallback.php', '', false) ?>
    <button type="button" onclick="<?= Helper::formSubmitScript() ?>">Тестовый чек</button>
</body>

</html>