<?php

namespace Djalone\KkmServerClasses;

/**
 * Команда открытия смены.
 */
class OpenShift extends Command{

    /**
     * Конструктор команды открытия смены.
     *
     * @param string $cashierName Имя кассира.
     * @param string $cashierVatin ИНН кассира.
     * @param string $kktNumber Номер ККТ.
     * @param string $idCommand Идентификатор команды.
     */
    public function __construct(string $cashierName = '', string $cashierVatin = '', string $kktNumber = '', string $idCommand = '')
    {
        parent::__construct($cashierName, $cashierVatin,$kktNumber, $idCommand);
        $this->command = "OpenShift";
    }
    /**
     * Возвращает пустой массив параметров для открытия смены.
     *
     * @return array{}
     */
    public function toArray(): array
    {
        return [];
    }
}