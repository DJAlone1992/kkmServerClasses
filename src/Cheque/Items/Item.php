<?php

namespace Djalone\KkmServerClasses\Cheque\Items;
/**
 * Абстрактный класс для всех элементов чека
 */
abstract class Item{
    /**
     * Функция преобразования в массив
     *
     * @return array
     */
    abstract public function toArray():array;
    /**
     * Функция преобразования в JSON
     *
     * @return string
     */
    public function toJson():string{
        return json_encode($this->toArray());
    }
    /**
     * Конструктор класса
     */
    public function __construct()
    {

    }
}