<?php
declare(strict_types = 1);
namespace CkTools\Model\Behavior;

use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\Utility\Hash;

/**
 * Sortable behavior
 */
class SortableBehavior extends Behavior
{

    /**
     * Stores the original value of sortField in beforeSave for later comparisons.
     *
     * @var string|int
     */
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
    public function restoreSorting(array $scope = []): void
    {
        $records = $this->_table->find()
            ->select([$this->_table->primaryKey(), $this->config('sortField')])
            ->select($this->config('columnScope'))
            ->order($this->config('defaultOrder'));

        if (!empty($scope)) {
            $records->where($scope);
        }

        $sorts = [];
        foreach ($records as $n => $entity) {
            $individualScope = '';
            foreach ($this->config('columnScope') as $column) {
                $individualScope .= $entity->get($column);
            }

            if (!isset($sorts[$individualScope])) {
                $sorts[$individualScope] = 0;
            }

            $sort = ++$sorts[$individualScope];
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
    public function beforeSave(Event $event, Entity $entity): void
    {
        $this->__originalSortValue = $entity->getOriginal($this->config('sortField'));
    }

    /**
     * afterSave Event
     *
     * @param Event $event Event
     * @param Entity $entity Record
     * @return void
     */
    public function afterSave(Event $event, Entity $entity): void
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
            if ($entitySort < $this->__originalSortValue) {
                $currentScope = $scope;
                $currentScope[] = "{$this->config('sortField')} <" . $this->__originalSortValue;
                $currentScope[] = "{$this->config('sortField')} >=" . $entitySort;
                $currentScope[] = [
                    $this->_table->primaryKey() . ' !=' => $entity->id
                ];

                $query = $this->_table->query()->update();
                $query->where($currentScope);
                $query->set([
                    $this->config('sortField') => $query->newExpr($this->config('sortField') . ' + 1')
                ]);
                $query->execute();
            } elseif ($entitySort > $this->__originalSortValue) {
                $currentScope = $scope;
                $currentScope[] = "{$this->config('sortField')} >" . $this->__originalSortValue;
                $currentScope[] = "{$this->config('sortField')} <=" . $entitySort;
                $currentScope[] = [
                    $this->_table->primaryKey() . ' !=' => $entity->id
                ];

                $query = $this->_table->query()->update();
                $query->where($currentScope);
                $query->set([
                    $this->config('sortField') => $query->newExpr($this->config('sortField') . ' - 1')
                ]);
                $query->execute();
            } elseif ($entitySort == $this->__originalSortValue) {
                $currentScope = $scope;
                $currentScope[] = "{$this->config('sortField')} >=" . $entitySort;
                $currentScope[] = [
                    $this->_table->primaryKey() . ' !=' => $entity->id
                ];

                $query = $this->_table->query()->update();
                $query->where($currentScope);
                $query->set([
                    $this->config('sortField') => $query->newExpr($this->config('sortField') . ' + 1')
                ]);
                $query->execute();
            }
        }
        $this->__originalSortValue = null;
    }

    /**
     * Returns the next highest sort value
     *
     * @param array $scope Optional conditions
     * @return int
     */
    public function getNextSortValue(array $scope = []): int
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
