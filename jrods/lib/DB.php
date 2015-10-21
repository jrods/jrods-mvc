<?php 

namespace jrods\lib;

use PDO;

class DB {

	static function createDB($config) {
		$dsn = 'mysql:host=' . $config['db_host'] . ';dbname='    . $config['db_name'] . ';port=' . $config['db_port'];

		// note the PDO::FETCH_OBJ, returning object ($result->id) instead of array ($result["id"])
		// @see http://php.net/manual/de/pdo.construct.php
		$options = [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING];

		return new PDO($dsn, $config['db_user'], $config['db_pass'], $options);
	}
}
