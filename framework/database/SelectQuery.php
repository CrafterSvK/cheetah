<?php
declare(strict_types=1);

namespace cheetah\database;

use Exception;
use mysqli;
use mysqli_result;

/**
 * Select query (part of database abstraction layer)
 * @author Jakub Janek
 */
class SelectQuery extends Query {
	private $items = "";
	public $join;
	protected $option;

	/**
	 * SelectQuery constructor.
	 * @param string|array table | tables
	 * @param mysqli connection
	 * @param string option like DISTINCT
	 */
	public function __construct($table, $db, ?string $option = null) {
		parent::__construct($table, $db);

		$this->option = $option;
	}

	/**
	 * Add column to query
	 * @param array|string table.column | column
	 * @param string alias of given item
	 * @param string DISTINCT or other options
	 * @return SelectQuery
	 */
	public function item($column, string $alias = null): SelectQuery {
		if (is_array($column)) {
			$key = array_key_first($column);

			if (is_array($column[$key])) {
				foreach ($column[$key] as $col) $this->item([$key => $col]);

				return $this;
			}

			$column = "`{$key}`.{$column[$key]}";
		}

		if (!is_null($alias)) $column .= " AS `{$alias}`";

		$this->items .= empty($this->items) ? $column : ", {$column}";

		return $this;
	}

	/**
	 * Add multiple columns to query
	 * @param array array of columns
	 * @return SelectQuery
	 */
	public function items(array $columns): SelectQuery {
		foreach ($columns as $column) $this->item($column);

		return $this;
	}

	/**
	 * @param string table to join
	 * @param string type of join
	 * @param string alias of joined table
	 * @param string operator of join conditions
	 * @return Condition
	 */
	public function join(string $table, ?string $type = null, ?string $alias = null, ?string $operator = null): Condition {
		$join = "";

		if (!is_null($type)) $join .= "{$type} ";
		$join .= "JOIN `{$table}`";
		if (!is_null($alias)) $join .= " AS `{$alias}`";

		$this->join = $join;

		return new Condition($operator, $this->db, $this);
	}

	/**
	 * Execute select query
	 * @return mysqli_result
	 * @throws Exception if the query is invalid
	 */
	public function execute(): mysqli_result {
		$this->query = sprintf(
			"SELECT %s %s FROM %s WHERE %s %s",
			$this->option ?? "",
			$this->items,
			$this->table,
			!empty($this->conditions) ? $this->conditions : '1',
			$this->order
		);

		$result = $this->db->query($this->query);

		if ($result === false) {
			throw new Exception("Invalid query {$this->query}");
		} else {
			return $result;
		}
	}

	/**
	 * Add order to query
	 * @param string order
	 * @return SelectQuery
	 */
	public function order(string $order): SelectQuery {
		$this->order = "ORDER BY {$order}";

		return $this;
	}
}