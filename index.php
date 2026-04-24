<?php

use Djalone\KkmServerClasses\Cheque;
use Djalone\KkmServerClasses\Cheque\Enums\BarCodeType;
use Djalone\KkmServerClasses\Cheque\Enums\ChequeType;
use Djalone\KkmServerClasses\Cheque\Enums\PaymentTypes;
    use Djalone\KkmServerClasses\Cheque\Items\BarCode;
    use Djalone\KkmServerClasses\Cheque\Items\Image;
use Djalone\KkmServerClasses\Cheque\Items\Position;
use Djalone\KkmServerClasses\Cheque\Items\Text;
use Djalone\KkmServerClasses\Services\CustomGUID;
use Djalone\KkmServerClasses\Services\Helper;

require_once 'vendor/autoload.php';

$posNal = new Position('Товар 1', 1000, 1000);
$posElectron = (new Position('Товар 2', 2000, 2000))->setPaymentType(PaymentTypes::Electronic);
    $textBefore = new Text('Текстовый блок до');
    $textAfter = new Text('Текстовый блок после');
    $ean = new BarCode('9780201379624', BarCodeType::EAN13);
    $code39 = new BarCode('9780201379624', BarCodeType::CODE39);
    $code128 = new BarCode('9780201379624', BarCodeType::CODE128);
    $qr = new BarCode('9780201379624', BarCodeType::QR);
    $pdf = new BarCode('9780201379624', BarCodeType::PDF417);
    $ClientAddress = '+79998887766';
    $image = new Image(__DIR__ . '/frontend/bmp.jpg');
    $ClientInfo = 'Петров П.П.';
    $oneLine = 'Одиночная строка';
    $multiline = implode("\n", [
        "Мой дядя самых честных правил,",
        "Когда не в шутку занемог,",
        "Он уважать себя заставил",
        "И лучше выдумать не мог.",
        "Его пример другим наука;",
        "Но, боже мой, какая скука",
        "С больным сидеть и день и ночь,",
        "Не отходя ни шагу прочь!",
        "Какое низкое коварство",
        "Полуживого забавлять,",
        "Ему подушки поправлять,",
        "Печально подносить лекарство,",
        "Вздыхать и думать про себя:",
        "Когда же черт возьмет тебя!"
    ]);
    $superLongText = $multiline . "\n" . implode("\n", [
        "Так думал молодой повеса,",
        "Летя в пыли на почтовых,",
        "Всевышней волею Зевеса",
        "Наследник всех своих родных. —",
        "Друзья Людмилы и Руслана!",
        "С героем моего романа",
        "Без предисловий, сей же час",
        "Позвольте познакомить вас:",
        "Онегин, добрый мой приятель,",
        "Родился на брегах Невы,",
        "Где, может быть, родились вы",
        "Или блистали, мой читатель;",
        "Там некогда гулял и я:",
        "Но вреден север для меня.",
        "Служив отлично-благородно,",
        "Долгами жил его отец,",
        "Давал три бала ежегодно",
        "И промотался наконец.",
        "Судьба Евгения хранила:",
        "Сперва Madame за ним ходила,",
        "Потом Monsieur ее сменил;",
        "Ребенок был резов, но мил.",
        "Monsieur l’Abbé, француз убогой,",
        "Чтоб не измучилось дитя,",
        "Учил его всему шутя,",
        "Не докучал моралью строгой,",
        "Слегка за шалости бранил",
        "И в Летний сад гулять водил."
    ]);
$arrayOfCheques = [
    [
        'isFiscal' => true,
        'clientAddress' => $ClientAddress,
        'clientInfo' => $ClientInfo,
        'type' => ChequeType::INCOME,
        'items' => [
            $textBefore,
            $posNal,
            $textAfter
        ],
        'id' => 'i_n',
        'name' => 'Приход. Наличные'
    ],
    [
        'isFiscal' => true,
        'clientAddress' => $ClientAddress,
        'clientInfo' => $ClientInfo,
        'type' => ChequeType::INCOME,
        'items' => [
            $textBefore,
            $posElectron,
            $textAfter
        ],
        'id' => 'i_e',
        'name' => 'Приход. Безнал'
    ],
    [
        'isFiscal' => true,
        'clientAddress' => $ClientAddress,
        'clientInfo' => $ClientInfo,
        'type' => ChequeType::INCOME,
        'items' => [
            $textBefore,
            $posNal,
            $textAfter,
            $posElectron
        ],
        'id' => 'i_ne',
        'name' => 'Приход. Нал+Безнал'
    ],
    [
        'isFiscal' => true,
        'clientAddress' => $ClientAddress,
        'clientInfo' => $ClientInfo,
        'type' => ChequeType::INCOME_RETURN,
        'items' => [
            $posNal
        ],
        'id' => 'r_n',
        'name' => 'Возврат. Наличные'
    ],
    [
        'isFiscal' => true,
        'clientAddress' => $ClientAddress,
        'clientInfo' => $ClientInfo,
        'type' => ChequeType::INCOME_RETURN,
        'items' => [
            $posElectron
        ],
        'id' => 'r_e',
        'name' => 'Возврат. Безнал'
    ],
    [
        'isFiscal' => true,
        'clientAddress' => $ClientAddress,
        'clientInfo' => $ClientInfo,
        'type' => ChequeType::INCOME_RETURN,
        'items' => [
            $posNal,
            $posElectron
        ],
        'id' => 'r_ne',
        'name' => 'Возврат. Нал+Безнал'
    ],
    [
        'isFiscal' => false,
        'clientAddress' => '',
        'clientInfo' => '',
        'items' => [
            $oneLine
        ],
        'type' => ChequeType::INCOME,
        'id' => 'oneLineText',
        'name' => 'Печать текста. Одна строка'
    ],
    [
        'isFiscal' => false,
        'clientAddress' => '',
        'clientInfo' => '',
        'multiline' => [
            $multiline
        ],
        'type' => ChequeType::INCOME,
        'id' => 'multiLineText',
        'name' => 'Печать текста. Несколько строк'
    ],
    [
        'isFiscal' => false,
        'clientAddress' => '',
        'clientInfo' => '',
        'multiline' => [
            $superLongText
        ],
        'type' => ChequeType::INCOME,
        'id' => 'superLongText',
        'name' => 'Печать текста. Много строк'
    ],
    [
        'isFiscal' => false,
        'clientAddress' => '',
        'clientInfo' => '',
        'items' => [
            $image
        ],
        'type' => ChequeType::INCOME,
        'id' => 'image',
        'name' => 'Печать изображения.'
    ],
    [
        'isFiscal' => false,
        'clientAddress' => '',
        'clientInfo' => '',
        'items' => [
            $ean
        ],
        'type' => ChequeType::INCOME,
        'id' => 'EAN13',
        'name' => 'Печать штрих-кода.EAN13'
    ],
    [
        'isFiscal' => false,
        'clientAddress' => '',
        'clientInfo' => '',
        'items' => [
            $code39
        ],
        'type' => ChequeType::INCOME,
        'id' => 'CODE39',
        'name' => 'Печать штрих-кода.CODE39'
    ],
    [
        'isFiscal' => false,
        'clientAddress' => '',
        'clientInfo' => '',
        'items' => [
            $code128
        ],
        'type' => ChequeType::INCOME,
        'id' => 'CODE128',
        'name' => 'Печать штрих-кода.CODE128'
    ],
    [
        'isFiscal' => false,
        'clientAddress' => '',
        'clientInfo' => '',
        'items' => [
            $qr
        ],
        'type' => ChequeType::INCOME,
        'id' => 'QR',
        'name' => 'Печать штрих-кода.QR'
    ],
    [
        'isFiscal' => false,
        'clientAddress' => '',
        'clientInfo' => '',
        'items' => [
            $pdf
        ],
        'type' => ChequeType::INCOME,
        'id' => 'PDF417',
        'name' => 'Печать штрих-кода.PDF417'
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
        $cheque->setClientAddress($chequeData['clientAddress'])->setClientInfo($chequeData['clientInfo'])->setIsFiscal($chequeData['isFiscal']);
        if (array_key_exists('items', $chequeData)) {
            foreach ($chequeData['items'] as $pos) {
                $cheque->addItem($pos);
            }
        }
        if (array_key_exists('multiline', $chequeData)) {
            foreach ($chequeData['multiline'] as $multiline) {
                $cheque->addMultipleLineText($multiline);
            }
        }
        $cheque->setChequeType($chequeData['type']);
        echo Helper::echoForm($cheque, 'testCallback.php', $chequeData['id'], [], false);
        echo "<button type=\"button\" onclick=\"" . Helper::formSubmitScript($chequeData['id']) . "\">Тестовый чек. " . $chequeData['name'] . "</button><br>";
    }
    ?>

</body>

</html>