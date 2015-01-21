<?php 
namespace CkTools\Database\Type;

use Cake\Database\Driver;
use Cake\Database\Type;

/**
 * Usage: 
 * In config/bootstrap.php, add
 * 		Type::map('json', '\CkTools\Database\Type\JsonType');
 * 
 * In your Table::initialize(), use 
 * 		$this->schema()->columnType('your_field', 'json');
 * 
 * to map the field to a JsonType
 *
 * @package CkTools
 */
class JsonType extends Type {

/**
 * from database to PHP conversion
 * 
 * @param string $value 	the value
 * @param Driver $driver 	the driver
 * @return array
 */
	public function toPHP($value, Driver $driver) {
		if ($value === null) {
			return null;
		}
		return json_decode($value, true);
	}

/**
 * from PHP to database conversion
 * 
 * @param array|string $value 	the value
 * @param Driver $driver 		the driver
 * @return array
 */
	public function toDatabase($value, Driver $driver) {
		return json_encode($value);
	}
}