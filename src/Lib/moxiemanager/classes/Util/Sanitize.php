<?php
/**
 * Sanitize.php
 *
 * Copyright 2016, Ephox, All rights reserved.
 */

/**
 * This class is an utility class for handling sanitizing of various inputs.
 *
 * @package MOXMAN_Util
 */
class MOXMAN_Util_Sanitize {
	/**
	 * Sanitize a string id
	 *
	 * @param String $str String to check against.
	 * @return String Sanitized string according to regexp rule.
	 */
	public static function id($str) {
		return preg_replace('/[^a-z0-9]+/i', '', $str);
	}

	/**
	 * Sanitize a filename
	 *
	 * @param String $filename Filename string to check against.
	 * @return String Sanitized filename as string according to rules.
	 */
	public static function fileName($name) {
		$name = preg_replace('/[\x00-\x19?:"|><]/', '', $name);
		$name = MOXMAN_Util_PathUtils::toUnixPath($name);
		$name = basename($name); // Remove and path artifacts
		$name = str_replace("..", "", $name);
		$name = trim($name);

		// As this should alaways return a name, generate one
		if (strlen($name) == 0) {
			$name = "noname.dat";
		}

		return $name;
	}

	/**
	 * Sanitize a path
	 *
	 * @param String $path String to check against.
	 * @return String Sanitized path according to regexp rule.
	 */
	public static function path($path) {

		// Prefix variants
		// c:\inetpub\wwwroot
		// s3:\\kalle
		// //myuncpath/is/here

		$prefix = "";

		$path = preg_replace('/[\x00-\x19?"|>|<]/', '', $path);
		$path = MOXMAN_Util_PathUtils::toUnixPath($path);

		// Detect prefix and remove it
		if (preg_match('/^([a-z0-9]+:\/\/)(.+)$/i', $path, $matches)) {
			$prefix = $matches[1];
			$path = $matches[2];
		}

		if (preg_match('/^([a-z0-9]+:\/\/\/)(.+)$/i', $path, $matches)) {
			$prefix = $matches[1];
			$path = $matches[2];
		}

		if (preg_match('/^([a-z]:\/)(.+)$/i', $path, $matches)) {
			$prefix = $matches[1];
			$path = $matches[2];
		}

		if (preg_match('/^(\/\/)(.+)$/i', $path, $matches)) {
			$prefix = $matches[1];
			$path = $matches[2];
		}

		$path = self::childPath($path);
		$path = preg_replace("/\/\/+/", '//', $path);

		$path = $prefix . $path;

		return $path;
	}

	/**
	 * Sanitize a child path
	 *
	 * @param String $path String to check against.
	 * @return String Sanitized path according to rules.
	 */
	public static function childPath($path) {
		$path = MOXMAN_Util_PathUtils::toUnixPath($path);
		$path = preg_replace('/[\x00-\x19?"|><];|:/', '', $path);

		$pathExp = explode("/", $path);
		$pathOut = array();

		foreach($pathExp as $exp) {
			$exp = trim($exp);
			if ($exp != "." && $exp != "..") {
				$pathOut[] = $exp;
			}
		}

		$path = implode("/", $pathOut);
		$path = preg_replace("/\/+/", '/', $path);

		return $path;
	}
}