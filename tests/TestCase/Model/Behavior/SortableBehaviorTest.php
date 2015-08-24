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
            'field1' => 'scope1'
        ]);
        $this->News->save($entity);

        $savedEntity = $this->News->get($entity->id);
        $this->assertEquals(4, $savedEntity->sorting);
        
        $entity = $this->News->newEntity([
            'name' => 'New Entry',
            'field1' => 'scope1'
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
    public function testOverrideSortingInBetween()
    {
        $this->__createRecords(3);
        $this->News->addBehavior('CkTools.Sortable', [
            'sortField' => 'sorting',
            'defaultOrder' => ['sorting ASC']
        ]);
        $entity = $this->News->newEntity([
            'name' => 'New Entry',
            'field1' => 'scope1',
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
     * Insert a record with sort value 1
     *
     * @return void
     */
    public function testOverrideSortingBegin()
    {
        $this->__createRecords(3);
        $this->News->addBehavior('CkTools.Sortable', [
            'sortField' => 'sorting',
            'defaultOrder' => ['sorting ASC']
        ]);
        $entity = $this->News->newEntity([
            'name' => 'New Entry',
            'field1' => 'scope1',
            'sorting' => 1
        ]);
        $this->News->save($entity);

        $savedEntity = $this->News->get($entity->id);
        $this->assertEquals(1, $savedEntity->sorting);

        $records = $this->News->find()->order(['sorting' => 'ASC'])->toArray();

        $this->assertEquals(1, $records[1]->id);
        $this->assertEquals(2, $records[1]->sorting);

        $this->assertEquals(2, $records[2]->id);
        $this->assertEquals(3, $records[2]->sorting);

        $this->assertEquals(3, $records[3]->id);
        $this->assertEquals(4, $records[3]->sorting);
    }

    /**
     * Insert a record with sort value 3
     *
     * @return void
     */
    public function testOverrideSortingEnd()
    {
        $this->__createRecords(3);
        $this->News->addBehavior('CkTools.Sortable', [
            'sortField' => 'sorting',
            'defaultOrder' => ['sorting ASC']
        ]);
        $entity = $this->News->newEntity([
            'name' => 'New Entry',
            'field1' => 'scope1',
            'sorting' => 3
        ]);
        $this->News->save($entity);

        $savedEntity = $this->News->get($entity->id);
        $this->assertEquals(3, $savedEntity->sorting);

        $records = $this->News->find()->order(['sorting' => 'ASC'])->toArray();

        $this->assertEquals(1, $records[0]->id);
        $this->assertEquals(1, $records[0]->sorting);

        $this->assertEquals(2, $records[1]->id);
        $this->assertEquals(2, $records[1]->sorting);

        $this->assertEquals(3, $records[3]->id);
        $this->assertEquals(4, $records[3]->sorting);
    }

    /**
     * testIncrementSortingEndOnExistingRecord
     *
     * @return void
     */
    public function testIncrementSortingEndOnExistingRecord()
    {
        $this->__createRecords(3);
        
        $this->News->addBehavior('CkTools.Sortable', [
            'sortField' => 'sorting',
            'defaultOrder' => ['sorting ASC']
        ]);
        
        $secondRecord = $this->News->get(2);
        $secondRecord->sorting = 3;
        $this->News->save($secondRecord);
        
        $savedRecord = $this->News->get(2);
        $this->assertEquals(3, $savedRecord->sorting);

        $this->assertEquals(1, $this->News->get(1)->sorting);
        $this->assertEquals(2, $this->News->get(3)->sorting);
    }


    /**
     * testDecrementSortingEndOnExistingRecord
     *
     * @return void
     */
    public function testDecrementSortingEndOnExistingRecord()
    {
        $this->__createRecords(3);
        
        $this->News->addBehavior('CkTools.Sortable', [
            'sortField' => 'sorting',
            'defaultOrder' => ['sorting ASC']
        ]);
        
        $secondRecord = $this->News->get(2);
        $secondRecord->sorting = 1;
        $this->News->save($secondRecord);

        $this->assertEquals(1, $this->News->get(2)->sorting);
        $this->assertEquals(2, $this->News->get(1)->sorting);
        $this->assertEquals(3, $this->News->get(3)->sorting);
    }

    /**
     * testColumnScoping
     *
     * @return void
     */
    public function testColumnScoping()
    {
        $this->markTestSkipped('The "Sortable" alias has already been loaded with the following config');
        // Both scopes have their own sorting
        $this->__createRecords(2, 'scope1');
        $this->__createRecords(2, 'scope2');
        
        $this->News->addBehavior('CkTools.Sortable', [
            'sortField' => 'sorting',
            'columnScope' => ['field1']
        ]);

        $entity = $this->News->newEntity([
            'name' => 'New Entry',
            'field1' => 'scope2',
            'sorting' => 2
        ]);
        $this->News->save($entity);

        $savedEntity = $this->News->get($entity->id);
        $this->assertEquals(2, $savedEntity->sorting);

        // Make sure scope1 is untouched
        $scope1Records = $this->News->find()->where(['field1' => 'scope1'])->order(['sorting' => 'ASC'])->toArray();
        $this->assertEquals(1, $scope1Records[0]->id);
        $this->assertEquals(1, $scope1Records[0]->sorting);
        $this->assertEquals(2, $scope1Records[1]->id);
        $this->assertEquals(2, $scope1Records[1]->sorting);

        // Make sure scope2 is as expected
        $scope2Records = $this->News->find()->where(['field1' => 'scope2'])->order(['sorting' => 'ASC'])->toArray();
        $this->assertEquals(3, $scope2Records[0]->id);
        $this->assertEquals(1, $scope2Records[0]->sorting);
        $this->assertEquals(4, $scope2Records[2]->id);
        $this->assertEquals(3, $scope2Records[2]->sorting);
    
        // Test the same with a new entity without sorting info
        $entity = $this->News->newEntity([
            'name' => 'New Entry',
            'field1' => 'scope1'
        ]);
        $this->News->save($entity);
        $savedEntity = $this->News->get($entity->id);
        $this->assertEquals(3, $savedEntity->sorting);
        
        // Make sure scope2 is as expected
        $scope2Records = $this->News->find()->where(['field1' => 'scope2'])->order(['sorting' => 'ASC'])->toArray();
        $this->assertEquals(3, $scope2Records[0]->id);
        $this->assertEquals(1, $scope2Records[0]->sorting);
        $this->assertEquals(5, $scope2Records[1]->id);
        $this->assertEquals(2, $scope2Records[1]->sorting);
        $this->assertEquals(4, $scope2Records[2]->id);
        $this->assertEquals(3, $scope2Records[2]->sorting);
    }

    /**
     * Create $count test records
     *
     * @param int $count
     * @return void
     */
    private function __createRecords($count, $field1 = 'scope1')
    {
        for ($i = 0; $i < $count; $i++) {
            $sort = $i + 1;
            $query = $this->News->query();
            $query->insert(['name', 'field1', 'sorting']);
            $query->values([
                'name' => 'Entry ' . $sort,
                'field1' => $field1,
                'sorting' => $sort
            ]);
            $query->execute();
        }
    }
}
