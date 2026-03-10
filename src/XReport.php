<?php

namespace Djalone\KkmServerClasses;
/**
 * Команда печати X-отчета.
 */
class XReport extends Command
{
    /**
     * Конструктор команды X-отчет.
     *
     * @param string $cashierName Имя кассира.
     * @param string $cashierVatin ИНН кассира.
     * @param string $kktNumber Номер ККТ.
     * @param string $idCommand Идентификатор команды.
     */
    public function __construct(string $cashierName = '', string $cashierVatin = '', string $kktNumber = '', string $idCommand = '')
    {
        parent::__construct($cashierName, $cashierVatin, $kktNumber, $idCommand);
        $this->command = "XReport";
    }
    /**
     * Возвращает пустой массив параметров для X-отчета.
     *
     * @return array{}
     */
    public function toArray(): array
    {
        return [];
    }
}
