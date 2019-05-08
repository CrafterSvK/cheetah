<?php
declare(strict_types=1);

namespace cheetah\database;

use mysqli;

/**
 * Query (part of database abstraction layer)
 * @author Jakub Janek
 */
abstract class Query {
	/** @var mysqli */
	protected $db;

	protected $query = "";
	protected $order = "";
	protected $conditions = "";
	protected $table = "";

	use ConditionTrait;

	/**
	 * Query constructor.
	 * @param array|string tables | table
	 * @param $db mysqli object with database
	 */
	public function __construct($table, mysqli $db) {
		$this->db = $db;

		if (is_array($table)) {
			foreach ($table as $key => &$t) {
				$t = is_numeric($key) ? "`{$t}`" : "`{$t}` AS `{$key}`";
			} //sanitize names or add alias

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
	 * Add multiple conditions in a group
	 * @param string operator
	 * @return Condition
	 */
	public function conditions(string $operator): Condition {
		$condition = new Condition($operator, $this->db, $this);

		return $condition;
	}

	/**
	 * Empty execute to fill by child class
	 * There is 100% better replacement for this method
	 */
	public function execute() {
		return false;
	}
}