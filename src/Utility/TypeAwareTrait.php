<?php
namespace CkTools\Utility;

/**
 * Add-in trait for handling type constants and their descriptions
 *
 */
trait TypeAwareTrait {

/**
 * Should be implemented by the class
 *
 * @return void
 */
	public static function typeDescriptions() {
		return [];
	}

/**
 * Get the description for the given type
 *
 * @param string $type type to get description of
 * @return string
 */
	public static function getTypeDescription($type) {
		$descriptions = self::typeDescriptions();
		if (isset($descriptions[$type])) {
			return $descriptions[$type];
		}
		return $type;
	}

/**
 * Get a map of [type => typeDescription] from the passed arguments
 *
 * @return array
 */
	public static function getTypeMap() {
		$types = func_get_args();

		$map = [];
		foreach ($types as $type) {
			$map[$type] = self::getTypeDescription($type);
		}
		return $map;
	}
}