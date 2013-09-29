<?php
namespace tyabrary;

/********************************************************************
* 列挙型を実現する為の抽象クラス
********************************************************************/
abstract class Enum
{
    private $scalar;

    function __construct($value){
        $ref    = new \ReflectionObject($this);
        $consts = $ref->getConstants();
        if (!in_array($value, $consts, true)){
            throw new \InvalidArgumentException;
        }

        $this->scalar = $value;
    }

    final static function __callStatic($label, $args){
        $class = get_called_class();
        $const = constant("$class::$label");
        return new $class($const);
    }

    final function get(){
        return $this->scalar;
    }

    final function __toString(){
        return (string)$this->scalar;
    }
}




/* example


final class Suit extends tyabrary\Enum {
    const Spade   = 'spade';
    const Heart   = 'heart';
    const Club    = 'club';
    const Diamond = 'diamond';
}


echo Suit::Spade() . "\n";

*/



