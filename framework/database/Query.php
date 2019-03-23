<?php
namespace cheetah\database;

/**
 * Query
 * @param array|string tables | table
 * @param mysqli object with database
 * @author Jakub Janek
 */
class Query {
	public function __construct($table, $db) {
		$this->db = $db;
		
		if (is_array($table)) {
			$table = implode(', ', $table);
		}
		
		$this->table = $table;
		$this->conditions = 'WHERE';
		$this->operator = 'AND';
	}

	/**
	 * Add condition to query
	 * @param array|string table.column | column
	 * @param array|string table.value | value
	 * @param string type of an operation
	 * @return Query
	 */
	public function condition($column, $value, $type = '=') {
		if (is_array($column)) {
			$key = array_key_first($column);
			$column = "{$key}.{$column[$key]}";
		}

		if (is_array($value)) {
			$key = array_key_first($value);
			$value = "{$key}.{$value[$key]}";
		} else {
			$value = "'" . $this->db->real_escape_string((string)$value) . "'";
		}
		
		$this->conditions .= $this->conditions === 'WHERE' 
			? $this->add("{$column} {$type} {$value}")
			: $this->add("{$this->operator} {$column} {$type} {$value}");

		//$this->conditions .= $this->add($string);

		return $this;
	}

	/**
	 * Set operator of a next condition to OR
	 * @return Query
	 */
	public function or() {
		$this->operator = "OR";

		return $this;
	}

	/**
	 * Set operator of a next condition to AND
	 * @return Query
	 */
	public function and() {
		$this->operator = "AND";

		return $this;
	}

	public function add($string) {
		return ' ' . $string;
	}
	
}