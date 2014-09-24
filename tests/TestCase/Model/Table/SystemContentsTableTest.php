<?php
namespace CkTools\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use CkTools\Model\Table\SystemContentsTable;
use Cake\TestSuite\TestCase;

/**
 * CkTools\Model\Table\SystemContentsTable Test Case
 */
class SystemContentsTableTest extends TestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'plugin.ck_tools.system_contents'
	];

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$config = TableRegistry::exists('SystemContents') ? [] : ['className' => 'CkTools\Model\Table\SystemContentsTable'];
		$this->SystemContents = TableRegistry::get('SystemContents', $config);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->SystemContents);

		parent::tearDown();
	}

	public function testPlaceholder() {
		
	}

}
