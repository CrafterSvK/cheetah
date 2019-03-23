<?php
declare(strict_types=1);

namespace cheetah;

use cheetah\database\{SelectQuery, DeleteQuery, InsertQuery, UpdateQuery};

/**
 * Database abstraction layer. Other stuff in framework may require this piece of code, sorry for that.
 * WIP
 * @author Jakub Janek
 */
class Database {
	public function __construct() {
		$file = \file_get_contents('config.json'); //hardcoded config json sorry
		$json = \json_decode($file);

		$this->db = [];

		$this->db['default'] = 
			new \mysqli(
				$json->database->host, 
				$json->database->user,
				$json->database->password,
				$json->database->name
			);

		$this->active = $this->db['default'];
	}

	/**
	 * Create select query on active database
	 * @param string table name
	 * @return SelectQuery object
	 */
	public function select($table) {
		$query = new SelectQuery($table, $this->active);

		return $query;
	}

	/**
	 * Create delete query on active database
	 * @param string table name
	 * @return DeleteQuery object
	 */
	public function delete($table) {
		$query = new DeleteQuery($table, $this->active);

		return $query;
	}

	/**
	 * Create insert query on active database
	 * @param string table name
	 * @return InsertQuery object
	 */
	public function insert($table) {
		$query = new InsertQuery($table, $this->active);

		return $query;
	}

	/**
	 * Create update query on active database
	 * @param string table name
	 * @return UpdateQuery object
	 */
	public function update($table) {
		$query = new UpdateQuery($table, $this->active);

		return $query;
	}

	/**
	 * Perform a simple query to database
	 * @param string query with placeholders
	 * @param array values to replace placeholders
	 * @return mixed
	 */
	public function query($query, $values) {
		foreach ($values as $key => $value) {
			$value = '\'' . $this->active->real_escape_string($value) . '\'';
			$query = \str_replace($key, $value, $query);
		}

		return $this->active->query($query);
	}

	/**
	 * Add database 
	 * @param string internal name
	 * @param string hostname
	 * @param string name of user
	 * @param string password to given user
	 * @param string name of database
	 */
	public function add($systemName, $host, $name, $password, $database) {
		$this->db[$systemName] = new mysqli($host, $name, $password, $database);
	}

	/**
	 * Set database by given internal name active
	 * @param string internal name 'default' for default
	 */
	public function setActive($systemName) {
		$this->active = $this->db[$systemName];
	}
}
