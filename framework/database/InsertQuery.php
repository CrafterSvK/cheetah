<?php
declare(strict_types=1);

namespace cheetah\database;

use mysqli;

/**
 * Insert query (part of database abstraction layer)
 * @author Jakub Janek
 */
class InsertQuery extends Query {
	private $columns = '';
	private $values = '';
	private $result = '';

	public function __construct(string $table, mysqli $db) {
		parent::__construct($table, $db);
	}

	/**
	 * Add value and column to insert
	 * @param array|string table.column | column
	 * @param string|int value to add
	 * @return InsertQuery
	 */
	public function value($column, $value): InsertQuery {
		if (is_array($column)) {
			$key = array_key_first($column);
			$column = "`{$key}`.{$column[$key]}";
		}

		if (!is_null($value))
			$value = "'{$this->db->real_escape_string((string)$value)}'";
		else
			$value = "NULL";

		$this->columns .= empty($this->columns) ? $column : ", {$column}";
		$this->values .= empty($this->values) ? $value : ", {$value}";

		return $this;
	}

	/**
	 * Add values and columns to insert
	 * @param array columns as keys and values as values
	 * @return InsertQuery
	 */
	public function values(array $values): InsertQuery {
		foreach ($values as $key => $value) $this->value($key, $value);

		return $this;
	}

	/**
	 * Execute insert query
	 * @return int|bool
	 */
	public function execute() {
		$this->query = sprintf(
			"INSERT INTO %s (%s) VALUES (%s)",
			$this->table,
			$this->columns,
			$this->values
		);

		$this->result = true;

		if ($this->db->query($this->query) === false) {
			$this->result = false;
		}

		return $this->db->insert_id == 0 ? false : $this->db->insert_id;
	}
}