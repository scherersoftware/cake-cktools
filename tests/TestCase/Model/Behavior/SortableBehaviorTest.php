<?php
declare(strict_types = 1);
namespace CkTools\Test\TestCase\Model\Behavior;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * CkTools\Model\Behavior\SortableBehavior Test Case
 */
class SortableBehaviorTest extends TestCase
{

    /**
     * Fxitures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.CkTools.News'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->News = TableRegistry::get('CkTools.News');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->News);
        TableRegistry::remove('CkTools.News');
        parent::tearDown();
    }

    /**
     * Test restore sorting
     *
     * @return void
     */
    public function testRestoreSorting(): void
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
    public function testGetNextSortValue(): void
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
    public function testNewRecord(): void
    {
        $this->__createRecords(5);
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
        $this->assertEquals(6, $savedEntity->sorting);

        $entity = $this->News->newEntity([
            'name' => 'New Entry',
            'field1' => 'scope1'
        ]);
        $this->News->save($entity);

        $savedEntity = $this->News->get($entity->id);
        $this->assertEquals(7, $savedEntity->sorting);
    }

    /**
     * Insert a record with an existing sort value, make sure sortings are moved accordingly
     *
     * @return void
     */
    public function testOverrideSortingInBetween(): void
    {
        $this->__createRecords(5);
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

        $this->assertEquals(6, $records[1]->id);
        $this->assertEquals(2, $records[1]->sorting);

        $this->assertEquals(2, $records[2]->id);
        $this->assertEquals(3, $records[2]->sorting);

        $this->assertEquals(3, $records[3]->id);
        $this->assertEquals(4, $records[3]->sorting);

        $this->assertEquals(4, $records[4]->id);
        $this->assertEquals(5, $records[4]->sorting);

        $this->assertEquals(5, $records[5]->id);
        $this->assertEquals(6, $records[5]->sorting);
    }

    /**
     * Insert a record with sort value 1
     *
     * @return void
     */
    public function testOverrideSortingBegin(): void
    {
        $this->__createRecords(5);
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

        $this->assertEquals(4, $records[4]->id);
        $this->assertEquals(5, $records[4]->sorting);

        $this->assertEquals(5, $records[5]->id);
        $this->assertEquals(6, $records[5]->sorting);

        $this->assertEquals(6, $records[0]->id);
        $this->assertEquals(1, $records[0]->sorting);
    }

    /**
     * Insert a record with sort value 3
     *
     * @return void
     */
    public function testOverrideSortingEnd(): void
    {
        $this->__createRecords(5);
        $this->News->addBehavior('CkTools.Sortable', [
            'sortField' => 'sorting',
            'defaultOrder' => ['sorting ASC']
        ]);
        $entity = $this->News->newEntity([
            'name' => 'New Entry',
            'field1' => 'scope1',
            'sorting' => 5
        ]);
        $this->News->save($entity);

        $savedEntity = $this->News->get($entity->id);
        $this->assertEquals(5, $savedEntity->sorting);

        $records = $this->News->find()->order(['sorting' => 'ASC'])->toArray();

        $this->assertEquals(1, $records[0]->id);
        $this->assertEquals(1, $records[0]->sorting);

        $this->assertEquals(2, $records[1]->id);
        $this->assertEquals(2, $records[1]->sorting);

        $this->assertEquals(3, $records[2]->id);
        $this->assertEquals(3, $records[2]->sorting);

        $this->assertEquals(4, $records[3]->id);
        $this->assertEquals(4, $records[3]->sorting);

        $this->assertEquals(6, $records[4]->id);
        $this->assertEquals(5, $records[4]->sorting);

        $this->assertEquals(5, $records[5]->id);
        $this->assertEquals(6, $records[5]->sorting);
    }

    /**
     * testIncrementSortingEndOnExistingRecord
     *
     * @return void
     */
    public function testIncrementSortingEndOnExistingRecord(): void
    {
        $this->__createRecords(5);

        $this->News->addBehavior('CkTools.Sortable', [
            'sortField' => 'sorting',
            'defaultOrder' => ['sorting ASC']
        ]);

        $secondRecord = $this->News->get(2);
        $secondRecord->sorting = 5;
        $this->News->save($secondRecord);

        $savedRecord = $this->News->get(2);
        $this->assertEquals(5, $savedRecord->sorting);

        $this->assertEquals(1, $this->News->get(1)->sorting);
        $this->assertEquals(2, $this->News->get(3)->sorting);
        $this->assertEquals(3, $this->News->get(4)->sorting);
        $this->assertEquals(4, $this->News->get(5)->sorting);
        $this->assertEquals(5, $this->News->get(2)->sorting);

        $secondRecord = $this->News->get(3);
        $secondRecord->sorting = 4;
        $this->News->save($secondRecord);

        $savedRecord = $this->News->get(3);
        $this->assertEquals(4, $savedRecord->sorting);

        $this->assertEquals(1, $this->News->get(1)->sorting);
        $this->assertEquals(5, $this->News->get(2)->sorting);
        $this->assertEquals(4, $this->News->get(3)->sorting);
        $this->assertEquals(2, $this->News->get(4)->sorting);
        $this->assertEquals(3, $this->News->get(5)->sorting);
    }

    /**
     * testDecrementSortingEndOnExistingRecord
     *
     * @return void
     */
    public function testDecrementSortingEndOnExistingRecord(): void
    {
        $this->__createRecords(5);

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
        $this->assertEquals(4, $this->News->get(4)->sorting);
        $this->assertEquals(5, $this->News->get(5)->sorting);
    }

    /**
     * testDecrementSortingEndOnLastRecord
     *
     * @return void
     */
    public function testDecrementSortingEndOnLastRecord(): void
    {
        $this->__createRecords(5);

        $this->News->addBehavior('CkTools.Sortable', [
            'sortField' => 'sorting',
            'defaultOrder' => ['sorting ASC']
        ]);

        $secondRecord = $this->News->get(3);
        $secondRecord->sorting = 1;
        $this->News->save($secondRecord);

        $this->assertEquals(1, $this->News->get(3)->sorting);
        $this->assertEquals(2, $this->News->get(1)->sorting);
        $this->assertEquals(3, $this->News->get(2)->sorting);
        $this->assertEquals(4, $this->News->get(4)->sorting);
        $this->assertEquals(5, $this->News->get(5)->sorting);

        $secondRecord = $this->News->get(4);
        $secondRecord->sorting = 2;
        $this->News->save($secondRecord);

        $this->assertEquals(1, $this->News->get(3)->sorting);
        $this->assertEquals(3, $this->News->get(1)->sorting);
        $this->assertEquals(4, $this->News->get(2)->sorting);
        $this->assertEquals(2, $this->News->get(4)->sorting);
        $this->assertEquals(5, $this->News->get(5)->sorting);
    }

    /**
     * testColumnScoping
     *
     * @return void
     */
    public function testColumnScoping(): void
    {
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
     * @param int $count Count
     * @param string $field1 Field
     * @return void
     */
    private function __createRecords(int $count, string $field1 = 'scope1'): void
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
