<?php
declare(strict_types=1);

namespace cheetah\database;

/**
 * Insert query
 * @author Jakub Janek
 */
class InsertQuery extends Query {
	public function __construct($table, string $db) {
		parent::__construct($table, $db);

		$this->columns = '';
		$this->values = '';
	}

	/**
	 * Add value and column to insert
	 * @param array|string table.column | column
	 * @param string value to add
	 * @return InsertQuery
	 */
	public function value($column, string $value): InsertQuery {
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