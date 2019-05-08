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

	/**
	 * SelectQuery constructor.
	 * @param string|array table | tables
	 * @param mysqli connection
	 */
	public function __construct($table, $db) {
		parent::__construct($table, $db);
	}

	/**
	 * Add column to query
	 * @param array|string table.column | column
	 * @param string alias of given item
	 * @param string DISTINCT or other options
	 * @return SelectQuery
	 */
	public function item($column, string $alias = null, string $prefix = null): SelectQuery {
		if (is_array($column)) {
			$key = array_key_first($column);

			if (is_array($column[$key])) {
				foreach ($column[$key] as $col) $this->item([$key => $col]);

				return $this;
			}

			$column = "`{$key}`.{$column[$key]}";
		}

		if (!is_null($alias)) $column .= " AS `{$alias}`";
		if (!is_null($prefix)) $column = "{$prefix} {$column}";

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
	 * Execute select query
	 * @return mysqli_result
	 * @throws Exception if the query is invalid
	 */
	public function execute(): mysqli_result {
		$this->query = sprintf(
			"SELECT %s FROM %s WHERE %s %s",
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