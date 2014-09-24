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
		'title' => true,
		'content' => true
	];

/**
 * Render a field by replacing the placeholders
 *
 * @param string $field field name
 * @param array $vars array of view vars
 * @return string
 */
	public function render($field, array $vars = []) {
		return String::insert($this->get($field), $vars, [
			'before' => '{{',
			'after' => '}}',
		]);
	}
}
