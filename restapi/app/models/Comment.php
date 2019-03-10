<?php

class Comment extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $wx_uid;

    /**
     *
     * @var string
     */
    public $author;

    /**
     *
     * @var string
     */
    public $content;

    /**
     *
     * @var integer
     */
    public $up;

    /**
     *
     * @var integer
     */
    public $down;

    /**
     *
     * @var integer
     */
    public $ctime;

    /**
     *
     * @var integer
     */
    public $utime;

    /**
     *
     * @var integer
     */
    public $is_delete;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("wx_zoo");
        $this->setSource("comment");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'comment';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ApiComment[]|ApiComment|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ApiComment|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public static function saveContent($id, $content)
    {
        $model = new self;
        $time = time();
        $data = [
            'oid'   => $id,
            'content'   => htmlentities(trim($content)),
            'ctime'     => $time,
            'utime'     => $time,
            'type'      => 1
        ];

        return $model->save($data);
    }

}
