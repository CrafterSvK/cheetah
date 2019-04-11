<?php
declare(strict_types=1);

namespace cheetah\database;

/**
 * Select query (part of database abstraction layer)
 * @param string name of a table
 * @param \mysqli connection
 * @author Jakub Janek
 */
class SelectQuery extends Query {
	private $items = "";

	public function __construct($table, $db) {
		parent::__construct($table, $db);

		$this->from = $this->add($this->table);
	}

	/**
	 * Add column to query
	 * @param array|string table.column | column
	 * @return SelectQuery
	 */
	public function item($column): SelectQuery {
		if (is_array($column)) {
			$key = array_key_first($column);
			$column = "{$key}.{$column[$key]}";
		}

		$this->items .= empty($this->items) ? $column : ',' . $this->add($column);

		return $this;
	}

	/**
	 * Add multiple columns to query
	 * @param array array of columns
	 * @return SelectQuery
	 */
	public function items(array $columns): SelectQuery {
		foreach ($columns as $column) {
			$this->item($column);
		}

		return $this;
	}

	/**
	 * Execute select query
	 * @return \mysqli_result
	 */
	public function execute(): \mysqli_result {
		$this->query = "SELECT {$this->items} FROM {$this->from}";
		$this->query .= $this->conditions !== 'WHERE' ? $this->add($this->conditions) : '';
		$this->query .= $this->add($this->order);

		try {
			$result = $this->db->query($this->query);

			if ($result === false) {
				throw new \Exception("Invalid query {$this->query}");
			} else {
				return $result;
			}
		} catch (\Exception $e) {
			echo $e;
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