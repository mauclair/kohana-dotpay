<?php defined('SYSPATH') or die('No direct script access.');

class Model_Payment_Incoming extends ORM {
    protected $_belongs_to = array(
        'payment' => array(
            'model'       => 'payment',
            'foreign_key' => 'payment_id',
        ),
    );
    
    protected $_table_columns = array(
        'id'                =>  array('type' => 'int'),
        'payment_id'        =>  array('type' => 'int'),
        't_id'              =>  array('type' => 'string'),
        't_status'          =>  array('type' => 'int'),
        'amount'            =>  array('type' => 'float'),
        'email'             =>  array('type' => 'string'),
        'md5'               =>  array('type' => 'string'),
        'description'       =>  array('type' => 'string', 'null' => TRUE),
        'service'           =>  array('type' => 'string', 'null' => TRUE),
        'name'              =>  array('type' => 'string', 'null' => TRUE),
        'code'              =>  array('type' => 'string', 'null' => TRUE),
        'username'          =>  array('type' => 'string', 'null' => TRUE),
        'password'          =>  array('type' => 'string', 'null' => TRUE),
        'created'           =>  array('type' => 'int'),
        'updated'           =>  array('type' => 'int'),
        'status'            =>  array('type' => 'int'),
    );    
}