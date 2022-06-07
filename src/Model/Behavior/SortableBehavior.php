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
     * @var string|int|null
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
        'defaultOrder' => [],
    ];

    /**
     * Restores the sorting based on defaultOrder
     *
     * @param array $scope Scope condition
     * @return void
     */
    public function restoreSorting(array $scope = []): void
    {
        $records = $this->_table->find()
            ->select([$this->_table->getPrimaryKey(), $this->getConfig('sortField')])
            ->select($this->getConfig('columnScope'))
            ->order($this->getConfig('defaultOrder'));

        if (!empty($scope)) {
            $records->where($scope);
        }

        $sorts = [];
        foreach ($records as $entity) {
            $individualScope = '';
            foreach ($this->getConfig('columnScope') as $column) {
                $individualScope .= $entity->get($column);
            }

            if (!isset($sorts[$individualScope])) {
                $sorts[$individualScope] = 0;
            }

            $sort = ++$sorts[$individualScope];
            $this->_table->updateAll([
                $this->getConfig('sortField') => $sort,
            ], [
                $this->_table->getPrimaryKey() => $entity->get($this->_table->getPrimaryKey()),
            ]);
        }
    }

    /**
     * beforeSave Event
     *
     * @param \Cake\Event\Event $event Event
     * @param \Cake\ORM\Entity $entity Record
     * @return void
     */
    public function beforeSave(Event $event, Entity $entity): void
    {
        $this->__originalSortValue = $entity->getOriginal($this->getConfig('sortField'));
    }

    /**
     * afterSave Event
     *
     * @param \Cake\Event\Event $event Event
     * @param \Cake\ORM\Entity $entity Record
     * @return void
     */
    public function afterSave(Event $event, Entity $entity): void
    {
        $fields = array_merge([
            $this->_table->getPrimaryKey(),
            $this->getConfig('sortField'),
        ], $this->getConfig('columnScope'));
        $savedEntity = $this->_table->get($entity->get($this->_table->getPrimaryKey()), [
            'fields' => $fields,
        ]);
        $scope = $this->getConfig('scope');

        foreach ($this->getConfig('columnScope') as $column) {
            $scope[$column] = $savedEntity->get($column);
        }
        $entitySort = $savedEntity->{$this->getConfig('sortField')};
        $entityId = $entity->get($this->_table->getPrimaryKey());

        if (empty($entitySort)) {
            $nextSortValue = $this->getNextSortValue($scope);
            $this->_table->updateAll([
                $this->getConfig('sortField') => $nextSortValue,
            ], [
                $this->_table->getPrimaryKey() => $entityId,
            ]);
        } else {
            if ($entitySort < $this->__originalSortValue) {
                $currentScope = $scope;
                $currentScope[] = "{$this->getConfig('sortField')} <" . $this->__originalSortValue;
                $currentScope[] = "{$this->getConfig('sortField')} >=" . $entitySort;
                $currentScope[] = [
                    $this->_table->getPrimaryKey() . ' !=' => $entity->id,
                ];

                $query = $this->_table->query()->update();
                $query->where($currentScope);
                $query->set([
                    $this->getConfig('sortField') => $query->newExpr($this->getConfig('sortField') . ' + 1'),
                ]);
                $query->execute();
            } elseif ($entitySort > $this->__originalSortValue) {
                $currentScope = $scope;
                $currentScope[] = "{$this->getConfig('sortField')} >" . $this->__originalSortValue;
                $currentScope[] = "{$this->getConfig('sortField')} <=" . $entitySort;
                $currentScope[] = [
                    $this->_table->getPrimaryKey() . ' !=' => $entity->id,
                ];

                $query = $this->_table->query()->update();
                $query->where($currentScope);
                $query->set([
                    $this->getConfig('sortField') => $query->newExpr($this->getConfig('sortField') . ' - 1'),
                ]);
                $query->execute();
            } elseif ($entitySort == $this->__originalSortValue) {
                $currentScope = $scope;
                $currentScope[] = "{$this->getConfig('sortField')} >=" . $entitySort;
                $currentScope[] = [
                    $this->_table->getPrimaryKey() . ' !=' => $entity->id,
                ];

                $query = $this->_table->query()->update();
                $query->where($currentScope);
                $query->set([
                    $this->getConfig('sortField') => $query->newExpr($this->getConfig('sortField') . ' + 1'),
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
        $scope = Hash::merge($this->getConfig('scope'), $scope);
        if (!empty($scope)) {
            $query->where($scope);
        }
        $query->select([
            'maxSort' => $query->func()->max($this->getConfig('sortField'), ['integer']),
        ]);
        $res = $query->enableHydration(false)->first();
        if (empty($res)) {
            return 1;
        }

        return $res['maxSort'] + 1;
    }
}
