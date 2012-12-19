<?php
class User extends CakeTestModel
{
	public $name = 'User';
	public $actsAs = array('SoftDelete.SoftDelete');
}

class Post extends CakeTestModel
{
	public $name = 'Post';
	public $actsAs = array('SoftDelete.SoftDelete');
	public $belongsTo = array('User');
}

class SoftDeleteBehaviorTest extends CakeTestCase
{
	public $fixtures = array('plugin.SoftDelete.User', 'plugin.SoftDelete.Post');

	public $User;

	public function setUp()
	{
		parent::setUp();
		$this->User = ClassRegistry::init('User');
	}

	public function tearDown()
	{
		parent::tearDown();
		ClassRegistry::flush();
		unset($this->User);
	}

	public function testSimpleDelete()
	{
		$this->assertTrue($this->User->softDelete(1));

		$result = $this->User->find('first', array('conditions' => array('User.id' => 1)));
		$this->assertEqual($result, array());

		$this->User->Behaviors->disable('SoftDelete');
		$result = $this->User->find('first', array('conditions' => array('User.id' => 1)));
		$expected = array(
			'User' => array(
				'id'  => 1,
				'deleted' => true,
				'name'  => 'joao',
				'password'  => 'senha'
			)
		);

		$this->assertEquals($result, $expected);
	}

	public function testFindDeleted()
	{
		$this->assertTrue($this->User->softDelete(1));

		$result = $this->User->find('first', array('conditions' => array('User.id' => 1, 'User.deleted' => false)));
		$this->assertEqual($result, array());
	}

	public function testSimpleDeleteUnDelete()
	{
		$this->assertTrue($this->User->softDelete(1));
		$this->assertTrue($this->User->unDelete(1));

		$result = $this->User->find('first', array('conditions' => array('User.id' => 1)));
		$expected = array(
			'User' => array(
				'id'  => 1,
				'deleted' => false,
				'name'  => 'joao',
				'password'  => 'senha'
			)
		);

		$this->assertEquals($result, $expected);
	}

	public function testDeleteAndFindAll()
	{
		$this->assertTrue($this->User->softDelete(1));

		$result = $this->User->find('all');
		$expected = array(
			array(
				'User' => array(
					'id'  => 2,
					'deleted' => false,
					'name'  => 'tonho',
					'password'  => 'sem_hash'
				)
			)
		);

		$this->assertEquals($result, $expected);
	}

	public function testFindAssociated()
	{
		$this->Post = ClassRegistry::init('Post');

		$result = $this->Post->find('first', array('conditions' => array('User.id' => 1)));
		$expected = array(
			'Post' => array(
				'id'  => 2,
				'title' => 'Post very interesting',
				'content'  => "D'oh",
				'user_id'  => 1
			),
			'User' => array(
				'id'  => 1,
				'deleted' => false,
				'name'  => 'joao',
				'password'  => 'senha'
			)
		);
		$this->assertEquals($result, $expected);

		$this->User->softDelete(1);
		$result = $this->Post->find('first', array('conditions' => array('Post.user_id' => 1)));
		$expected = array(
			'Post' => array(
				'id'  => 2,
				'title' => 'Post very interesting',
				'content'  => "D'oh",
				'user_id'  => 1
			),
			'User' => array(
				'id'  => null,
				'deleted' => null,
				'name'  => null,
				'password'  => null
			)
		);

		$this->assertEquals($result, $expected);

		$result = $this->Post->find('first', array('conditions' => array('User.id' => 1)));
		$this->assertEqual($result, array());
	}
}