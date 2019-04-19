<?php
declare(strict_types=1);

namespace cheetah;

use cheetah\database\{SelectQuery, DeleteQuery, InsertQuery, UpdateQuery};

use mysqli;
use mysqli_result;
use function file_get_contents;
use function json_decode;
use function str_replace;

/**
 * Database abstraction layer.
 * @author Jakub Janek
 */
class Database {
	private $active;
	private $db = [];

	public function __construct() {
		$file = file_get_contents('config.json'); //hardcoded config json sorry
		$json = json_decode($file);

		$this->db = [];

		$this->db['default'] =
			new mysqli(
				$json->database->host,
				$json->database->user,
				$json->database->password,
				$json->database->name
			);

		$this->active = $this->db['default'];
	}

	/**
	 * Create select query on active database
	 * @param mixed table name
	 * @return SelectQuery
	 */
	public function select($table): SelectQuery {
		$query = new SelectQuery($table, $this->active);

		return $query;
	}

	/**
	 * Create delete query on active database
	 * @param mixed table name
	 * @return DeleteQuery
	 */
	public function delete($table): DeleteQuery {
		$query = new DeleteQuery($table, $this->active);

		return $query;
	}

	/**
	 * Create insert query on active database
	 * @param mixed table name
	 * @return InsertQuery
	 */
	public function insert($table): InsertQuery {
		$query = new InsertQuery($table, $this->active);

		return $query;
	}

	/**
	 * Create update query on active database
	 * @param mixed table name
	 * @return UpdateQuery
	 */
	public function update($table): UpdateQuery {
		$query = new UpdateQuery($table, $this->active);

		return $query;
	}

	/**
	 * Perform a simple query to database
	 * @param string query with placeholders
	 * @param array values to replace placeholders
	 * @return mysqli_result
	 */
	public function query($query, $values): mysqli_result {
		foreach ($values as $key => $value) {
			$value = "'{$this->active->real_escape_string($value)}'";
			$query = str_replace($key, $value, $query);
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
	 * @return void
	 */
	public function add(string $systemName, string $host, string $name, string $password, string $database): void {
		$this->db[$systemName] = new mysqli($host, $name, $password, $database);
	}

	/**
	 * Set database by given internal name active
	 * @param string internal name 'default' for default
	 * @return void
	 */
	public function setActive(string $systemName): void {
		$this->active = $this->db[$systemName];
	}

	public function __destruct() {
		foreach ($this->db as $db) $db->close();
	}
}
