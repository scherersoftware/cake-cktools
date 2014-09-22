<?php
namespace CkTools\Utility;

trait TableUtilitiesTrait {

/**
 * Updates a single field for the given primaryKey
 *
 * @param mixed $primaryKey The primary key
 * @param string $field field name
 * @param string $value string value
 * @return bool
 */
	public function updateField($primaryKey, $field, $value = null) {
		return $this->query()
			->update()
			->set([
				$field => $value
			])
			->where([
				$this->primaryKey() => $primaryKey
			])
			->execute();
	}
}