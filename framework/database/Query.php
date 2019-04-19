<?php
declare(strict_types=1);

namespace cheetah\database;

use mysqli;

/**
 * Query (part of database abstraction layer)
 * @param array|string tables | table
 * @param mysqli object with database
 * @author Jakub Janek
 */
abstract class Query {
	/** @var mysqli */
	protected $db;

	protected $from = "FROM";
	protected $query = "";
	protected $order = "";
	protected $conditions = '';
	protected $table = "";
	protected $operator = 'AND';

	public function __construct($table, mysqli $db) {
		$this->db = $db;
		
		if (is_array($table)) $table = implode(', ', $table);
		
		$this->table = $table;
	}

	/**
	 * Add condition to query
	 * @param array|string table.column | column
	 * @param array|string table.value | value
	 * @param string type of an operation
	 * @return Query
	 */
	public function condition($column, $value, string $type = '='): Query {
		if (is_array($column)) {
			$key = array_key_first($column);
			$column = "{$key}.{$column[$key]}";
		}

		if (is_array($value)) {
			$key = array_key_first($value);
			$value = "{$key}.{$value[$key]}";
		} else {
			if (!is_null($value))
				$value = "'{$this->db->real_escape_string((string)$value)}'";
		}
		
		$this->conditions .= empty($this->conditions)
			? $this->add("{$column} {$type} {$value}")
			: $this->add("{$this->operator} {$column} {$type} {$value}");

		//$this->conditions .= $this->add($string);

		return $this;
	}

	/**
	 * Set operator of a next conditions to OR
	 * @return Query
	 */
	public function or(): Query {
		$this->operator = "OR";

		return $this;
	}

	/**
	 * Set operator of a next conditions to AND
	 * @return Query
	 */
	public function and(): Query {
		$this->operator = "AND";

		return $this;
	}

	/**
	 * Set operator of a next conditions to XOR
	 * @return Query
	 */
	public function xor(): Query {
		$this->operator = "XOR";

		return $this;
	}

	protected function add($string): string {
		return ' ' . $string;
	}

	/**
	 * Empty execute to fill by child class
	 */
	public function execute() {
		return false;
	}
}