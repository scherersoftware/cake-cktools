<?php
declare(strict_types = 1);
namespace CkTools\Utility;

use CakeApiBaselayer\Lib\ApiReturnCode;
use Cake\Network\Exception\NotFoundException;
use Cake\Orm\TableRegistry;
use FrontendBridge\Lib\ServiceResponse;

/**
 * This trait provides the endpoint for sortable lists.
 * The controller using this trait MUST set a property $sortModelName with the name of the table to
 * get and save the entity in question
 *
 * @property \Cake\Network\Request $request
 */
trait SortableControllerTrait
{

    /**
     * endpoint for sorting ajax calls
     *
     * @return \FrontendBridge\Lib\ServiceResponse
     * @throws \Cake\Network\Exception\NotFoundException if either not a post request
     *         or missing/wrong params
     */
    public function sort(): ServiceResponse
    {
        $this->getRequest()->allowMethod('post');
        if (!empty($this->sortModelName) && !empty($this->getRequest()->getData('foreignKey'))) {
            $table = TableRegistry::getTableLocator()->get($this->sortModelName);
            $entity = $table->get($this->getRequest()->getData('foreignKey'));
            $entity->sort = $this->getRequest()->getData('sort');
            if ($table->save($entity)) {
                return new ServiceResponse(ApiReturnCode::SUCCESS, [
                    $entity->id,
                    $this->getRequest()->getData('sort'),
                ]);
            }

            return new ServiceResponse(ApiReturnCode::INTERNAL_ERROR, []);
        }
        throw new NotFoundException();
    }
}
