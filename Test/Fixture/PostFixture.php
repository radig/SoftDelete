<?php
class PostFixture extends CakeTestFixture {

	public $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'title' => array('type'=>'string', 'null' => false, 'default' => NULL),
		'content' => array('type'=>'text', 'null' => false, 'default' => NULL),
		'user_id' => array('type'=>'integer', 'null' => false)
	);

	public $records = array(
		array(
			'id'  => 1,
			'title' => 'Post about something',
			'content'  => 'Nothing',
			'user_id'  => 2
		),
		array(
			'id'  => 2,
			'title' => 'Post very interesting',
			'content'  => "D'oh",
			'user_id'  => 1
		)
	);
}