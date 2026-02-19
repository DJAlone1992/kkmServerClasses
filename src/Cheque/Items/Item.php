<?php

namespace Djalone\KkmServerClasses\Cheque\Items;

abstract class Item{
    abstract public function toArray():array;
    public function toJson():string{
        return json_encode($this->toArray());
    }

}