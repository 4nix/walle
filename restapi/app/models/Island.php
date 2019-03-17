<?php

class Island extends \Phalcon\Mvc\Model
{
    public $id;
    public $name;

    public function initialize()
    {
        $this->setSource('island');
    }

    public function getSource()
    {
        return 'island';
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