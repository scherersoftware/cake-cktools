<?php
namespace CkTools\Model\Entity;

use Cake\ORM\Entity;

/**
 * SystemContent Entity.
 */
class SystemContent extends Entity {

/**
 * Fields that can be mass assigned using newEntity() or patchEntity().
 *
 * @var array
 */
	protected $_accessible = [
		'identifier' => true,
		'notes' => true,
	];

}
