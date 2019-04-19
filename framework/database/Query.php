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

	protected $query = "";
	protected $order = "";
	protected $conditions = "";
	protected $table = "";
	protected $operator = 'AND';

	public function __construct($table, mysqli $db) {
		$this->db = $db;

		if (is_array($table)) {
			foreach ($table as &$t) $t = "`{$t}`"; //sanitize names

			$table = implode(', ', $table);
		} else {
			$table = "`{$table}`";
		}

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
		$condition = new Condition('', $this->db, $this);

		$condition->condition($column, $value, $type)
			->close();

		return $this;
	}

	/**
	 * Adds condition string to conditions.
	 * Do not use if you don't know what you are doing!
	 * @param string
	 * @return void
	 */
	public function add($conditionString): void {
		$this->conditions .= empty($this->conditions)
			? $conditionString
			: " {$this->operator} $conditionString";
	}

	/**
	 * Add multiple conditions in a group
	 * @param string operator
	 * @return Condition
	 */
	public function conditions(string $operator): Condition {
		$condition = new Condition($operator, $this->db, $this);

		return $condition;
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

	/**
	 * Empty execute to fill by child class
	 * There is 100% better replacement for this method
	 */
	public function execute() {
		return false;
	}
}