<?php
declare(strict_types=1);

namespace cheetah\database;

use Exception;
use mysqli;

/**
 * Update query (part of database abstraction layer)
 * @param string name of a table
 * @param mysqli connection
 * @author Jakub Janek
 */
class UpdateQuery extends Query {
	private $set;

	public function __construct($table, mysqli $db) {
		parent::__construct($table, $db);
	}

	/**
	 * Add value and column to update
	 * @param array|string table.column | column
	 * @param string|int value to add
	 * @return UpdateQuery
	 */
	public function value($column, $value): UpdateQuery {
		if (is_array($column)) {
			$key = array_key_first($column);
			$column = "`{$key}`.{$column[$key]}";
		} else {
			$column = "`{$column}`";
		}

		$value = !is_null($value) ? "'{$this->db->real_escape_string((string)$value)}'" : 'NULL';

		$set = "{$column} = {$value}";

		$this->set .= empty($this->set) ? $set : ", {$set}";

		return $this;
	}

	/**
	 * Add values and columns to update
	 * @param array columns as keys and values as values
	 * @return UpdateQuery
	 */
	public function values(array $values): UpdateQuery {
		foreach ($values as $key => $value) $this->value($key, $value);

		return $this;
	}

	/**
	 * Execute update query
	 * @return bool|void
	 */
	public function execute(): bool {
		$this->query = sprintf(
			"UPDATE %s SET %s WHERE %s", //I don't know how to suppress this warning...
			$this->from,
			$this->set,
			!empty($this->conditions) ? $this->conditions : '1'
		);

		try {
			$result = $this->db->query($this->query);

			if ($result === false) {
				throw new Exception("Invalid query {$this->query}");
			} else {
				return true;
			}
		} catch (Exception $e) {
			echo $e;
		}
	}
}