<?php
namespace CkTools\Test\TestCase\Model\Behavior;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use CkTools\Model\Behavior\SortableBehavior;

/**
 * CkTools\Model\Behavior\SortableBehavior Test Case
 */
class SortableBehaviorTest extends TestCase
{

    public $fixtures = [
        'plugin.CkTools.News'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->News = TableRegistry::get('CkTools.News');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->News);

        parent::tearDown();
    }

    /**
     * Test restore sorting
     *
     * @return void
     */
    public function testRestoreSorting()
    {
        $this->__createRecords(5);
        $this->News->addBehavior('CkTools.Sortable', [
            'sortField' => 'sorting',
            'defaultOrder' => ['sorting ASC']
        ]);

        // create a gap in sorting
        $entity = $this->News->get(5);
        $entity->sort = 8;
        $this->News->save($entity);

        $this->News->restoreSorting();

        $records = $this->News->find()->order(['sorting' => 'ASC'])->toArray();
        $this->assertEquals(1, $records[0]->sorting);
        $this->assertEquals(2, $records[1]->sorting);
        $this->assertEquals(3, $records[2]->sorting);
        $this->assertEquals(4, $records[3]->sorting);
        $this->assertEquals(5, $records[4]->sorting);
    }

    /**
     * testGetNextSortValue
     *
     * @return void
     */
    public function testGetNextSortValue()
    {
        $this->News->addBehavior('CkTools.Sortable', [
            'sortField' => 'sorting',
            'defaultOrder' => ['sorting ASC']
        ]);
        $this->assertEquals($this->News->getNextSortValue(), 1);
        $this->__createRecords(3);
        $this->assertEquals($this->News->getNextSortValue(), 4);
    }

    /**
     * Test adding a new record without sort key
     *
     * @return void
     */
    public function testNewRecord()
    {
        $this->__createRecords(3);
        $this->News->addBehavior('CkTools.Sortable', [
            'sortField' => 'sorting',
            'defaultOrder' => ['sorting ASC']
        ]);
        $entity = $this->News->newEntity([
            'name' => 'New Entry',
            'field1' => 'scope1',
            'field2' => 'scope2'
        ]);
        $this->News->save($entity);

        $savedEntity = $this->News->get($entity->id);
        $this->assertEquals(4, $savedEntity->sorting);
        
        $entity = $this->News->newEntity([
            'name' => 'New Entry',
            'field1' => 'scope1',
            'field2' => 'scope2'
        ]);
        $this->News->save($entity);

        $savedEntity = $this->News->get($entity->id);
        $this->assertEquals(5, $savedEntity->sorting);
    }

    /**
     * Insert a record with an existing sort value, make sure sortings are moved accordingly
     *
     * @return void
     */
    public function testOverrideSorting()
    {
        $this->__createRecords(3);
        $this->News->addBehavior('CkTools.Sortable', [
            'sortField' => 'sorting',
            'defaultOrder' => ['sorting ASC']
        ]);
        $entity = $this->News->newEntity([
            'name' => 'New Entry',
            'field1' => 'scope1',
            'field2' => 'scope2',
            'sorting' => 2
        ]);
        $this->News->save($entity);

        $savedEntity = $this->News->get($entity->id);
        $this->assertEquals(2, $savedEntity->sorting);

        $records = $this->News->find()->order(['sorting' => 'ASC'])->toArray();

        $this->assertEquals(1, $records[0]->id);
        $this->assertEquals(1, $records[0]->sorting);

        $this->assertEquals(2, $records[2]->id);
        $this->assertEquals(3, $records[2]->sorting);

        $this->assertEquals(3, $records[3]->id);
        $this->assertEquals(4, $records[3]->sorting);
    }


    /**
     * Create $count test records
     *
     * @param int $count
     * @return void
     */
    private function __createRecords($count, $field1 = 'scope1', $field2 = 'scope2')
    {
        for ($i = 0; $i < $count; $i++) {
            $sort = $i + 1;
            $query = $this->News->query();
            $query->insert(['name', 'field1', 'field2', 'sorting']);
            $query->values([
                'name' => 'Entry ' . $sort,
                'field1' => $field1,
                'field2' => $field2,
                'sorting' => $sort
            ]);
            $query->execute();
        }
    }
}
