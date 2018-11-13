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
        $this->request->allowMethod('post');
        if (!empty($this->request->data['foreignKey']) && !empty($this->sortModelName)) {
            $table = TableRegistry::get($this->sortModelName);
            $entity = $table->get($this->request->data['foreignKey']);
            $entity->sort = $this->request->data['sort'];
            if ($table->save($entity)) {
                return new ServiceResponse(ApiReturnCode::SUCCESS, [
                    $entity->id,
                    $this->request->data['sort'],
                ]);
            }

            return new ServiceResponse(ApiReturnCode::INTERNAL_ERROR, []);
        }
        throw new NotFoundException();
    }
}
