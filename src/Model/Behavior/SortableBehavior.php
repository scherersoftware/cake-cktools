<?php
namespace CkTools\Model\Behavior;

use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\Table;

/**
 * Sortable behavior
 */
class SortableBehavior extends Behavior
{

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        // Table field which stores the sort order
        'sortField' => 'sort',
        // Conditions which must match to include sortable records
        'scope' => [],
        // Array of columns which must be equal between records to sort inside
        'columnScope' => [],
        // If restoreSorting is called, this will be used to order the records
        'defaultOrder' => []
    ];

    /**
     * Restores the sorting based on defaultOrder
     *
     * @return void
     */
    public function restoreSorting()
    {
        $records = $this->_table->find()
            ->select($this->_table->primaryKey(), $this->config('sortField'))
            ->order($this->config('defaultOrder'))
            ->all();

        foreach ($records as $n => $entity) {
            $sort = ($n + 1);
            $this->_table->updateAll([
                $this->config('sortField') => $sort
            ], [
                $this->_table->primaryKey() => $entity->get($this->_table->primaryKey())
            ]);
        }
    }

    /**
     * afterSave Event
     *
     * @param Event $event Event
     * @param Entity $entity Record
     * @return void
     */
    public function afterSave(Event $event, Entity $entity)
    {
        $savedEntity = $this->_table->get($entity->get($this->_table->primaryKey()), [
            'fields' => [$this->_table->primaryKey(), $this->config('sortField')]
        ]);
        $scope = $this->config('scope');
        foreach ($this->config('columnScope') as $column) {
            $scope[$column] = $savedEntity->get($column);
        }
        $entitySort = $savedEntity->{$this->config('sortField')};
        $entityId = $entity->get($this->_table->primaryKey());
        
        if (empty($entitySort)) {
            $nextSortValue = $this->getNextSortValue($scope);
            $this->_table->updateAll([
                $this->config('sortField') => $nextSortValue
            ], [
                $this->_table->primaryKey() => $entityId
            ]);
        } else {
            $query = $this->_table->query()->update();
            $query->set([
                $this->config('sortField') => $query->newExpr($this->config('sortField') . ' + 1')
            ]);
            $query->where([
                $this->config('sortField') . ' >=' => $entitySort,
                $this->_table->primaryKey() . ' !=' => $entityId
            ]);
            $query->execute();
        }
    }

    /**
     * Returns the next highest sort value
     *
     * @param array $scope Optional conditions
     * @return int
     */
    public function getNextSortValue(array $scope = [])
    {
        $query = $this->_table->query();
        if (!empty($this->config('scope'))) {
            $query->where($this->config('scope'));
        }
        $query->select([
            'maxSort' => $query->func()->max($this->config('sortField'))
        ]);
        $res = $query->hydrate(false)->first();
        if (empty($res)) {
            return 1;
        }
        return ($res['maxSort'] + 1);
    }
}
