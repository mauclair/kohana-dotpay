<?php defined('SYSPATH') or die('No direct script access.');

class Model_Payment extends ORM {
    protected $_has_many = array(
        'incoming'  => array('model' => 'payment_incoming'),
    );
    
    protected $_table_columns = array(
        'id'                =>  array('type' => 'int'),
        'control'           =>  array('type' => 'string'),
        'amount'            =>  array('type' => 'float'),
        'description'       =>  array('type' => 'string'),
        'name'              =>  array('type' => 'string', 'null' => TRUE),
        'service'           =>  array('type' => 'string', 'null' => TRUE),
    );
}