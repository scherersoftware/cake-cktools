<?php
namespace CkTools\Model\Behavior;

use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\Utility\Hash;

/**
 * Sortable behavior
 */
class SortableBehavior extends Behavior
{

    private $__originalSortValue;

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
    public function restoreSorting(array $scope = [])
    {
        $records = $this->_table->find()
            ->select($this->_table->primaryKey(), $this->config('sortField'))
            ->order($this->config('defaultOrder'));
        if (!empty($scope)) {
            $records->where($scope);
        }

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
     * beforeSave Event
     *
     * @param Event $event Event
     * @param Entity $entity Record
     * @return void
     */
    public function beforeSave(Event $event, Entity $entity)
    {
        $this->__originalSortValue = $entity->getOriginal('sorting');
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
        $fields = array_merge([
            $this->_table->primaryKey(),
            $this->config('sortField')
        ], $this->config('columnScope'));
        $savedEntity = $this->_table->get($entity->get($this->_table->primaryKey()), [
            'fields' => $fields
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
            if ($this->__originalSortValue < $entity->sorting) {
                $decrementScope = $scope;
                $query = $this->_table->query()->update();
                $query->set([
                    $this->config('sortField') => $query->newExpr($this->config('sortField') . ' - 1')
                ]);
                $decrementScope[$this->config('sortField')] = $entitySort;
                $decrementScope[$this->_table->primaryKey() . ' !='] = $entityId;
                $query->where($decrementScope);
                $query->execute();
            } else if ($this->__originalSortValue > $entity->sorting){
                $incrementScope = $scope;
                $query = $this->_table->query()->update();
                $query->set([
                    $this->config('sortField') => $query->newExpr($this->config('sortField') . ' + 1')
                ]);
                $incrementScope[$this->config('sortField')] = $entitySort;
                $incrementScope[$this->_table->primaryKey() . ' !='] = $entityId;
                $query->where($incrementScope);
                $query->execute();
            } else if ($this->__originalSortValue == $entity->sorting) {
                $incrementScope = $scope;
                $query = $this->_table->query()->update();
                $query->set([
                    $this->config('sortField') => $query->newExpr($this->config('sortField') . ' + 1')
                ]);
                $incrementScope[$this->config('sortField') . ' >='] = $entitySort;
                $incrementScope[$this->_table->primaryKey() . ' !='] = $entityId;
                $query->where($incrementScope);
                $query->execute();
            }
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
        $scope = Hash::merge($this->config('scope'), $scope);
        if (!empty($scope)) {
            $query->where($scope);
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
