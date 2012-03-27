<?php
class UserFixture extends CakeTestFixture {

	public $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'deleted' => array('type' => 'boolean', 'null' => false, 'default' => false),
		'name' => array('type'=>'string', 'null' => false, 'default' => NULL),
		'password' => array('type'=>'string', 'null' => false)
	);

	public $records = array(
		array(
			'id'  => 1,
			'deleted' => false,
			'name'  => 'joao',
			'password'  => 'senha'
		),
		array(
			'id'  => 2,
			'deleted' => false,
			'name'  => 'tonho',
			'password'  => 'sem_hash'
		)
	);
}