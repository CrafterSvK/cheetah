<?php
declare(strict_types=1);

namespace cheetah\database;

use \mysqli;

/**
 * Insert query (part of database abstraction layer)
 * @param string name of a table
 * @param mysqli connection
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
			$column = "{$key}.{$column[$key]}";
		}
		
		$value = '\'' . $this->db->real_escape_string((string)$value) . '\'';

		$this->columns .= empty($this->columns) ? $column : ',' . $this->add($column);
		$this->values .= empty($this->values) ? $value : ',' . $this->add($value);

		return $this;
	}

	/**
	 * Add values and columns to insert
	 * @param array columns as keys and values as values
	 * @return InsertQuery
	 */
	public function values(array $values): InsertQuery {
		foreach ($values as $key => $value) {
			$this->value($key, $value);
		}
		
		return $this;
	}

	/**
	 * Execute insert query
	 * @return int
	 */
	public function execute(): int {
		$this->query = "INSERT INTO {$this->table} ({$this->columns}) VALUES ({$this->values})";

		$this->result = true;

		if ($this->db->query($this->query) === false) {
			$this->result = false;
		}

		return $this->db->insert_id;
	}
}