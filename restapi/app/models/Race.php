<?php

class Race extends \Phalcon\Mvc\Model
{
    public $id;
    public $name;

    public function initialize()
    {
        $this->setSchema('wx_zoo');
        $this->setSource('race');
    }

    public function getSource()
    {
        return 'race';
    }

    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
}