<?php
namespace cheetah\database;

/**
 * Select query
 * @author Jakub Janek
 */
class SelectQuery extends Query {
	public function __construct($table, $db) {
		parent::__construct($table, $db);

		$this->from = "FROM {$this->table}";
		$this->items = '';
		$this->order = '';
	}

	/**
	 * Add column to query
	 * @param array|string table.column | column
	 * @return SelectQuery
	 */
	public function item($column) {
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
	public function items($columns) {
		foreach ($columns as $column) {
			$this->item($column);
		}

		return $this;
	}

	/**
	 * Execute select query
	 * @return SelectQuery
	 */
	public function execute() {
		$this->query = "SELECT {$this->items} {$this->from}";
		$this->query .= $this->conditions !== 'WHERE' ? $this->add($this->conditions) : '';
		$this->query .= $this->add($this->order);

		try {
			$result = $this->db->query($this->query);

			if ($result === false) {
				throw new \Exception("Invalid query {$this->query}");
			} else {
				$this->result = $result->fetch_all(MYSQLI_ASSOC);
			}
		} catch (\Exception $e) {
			$this->result = false;
		}

		return $this;
	}

	/**
	 * Add order to query
	 * @param string order
	 * @return SelectQuery
	 */
	public function order($order) {
		$this->order = "ORDER BY {$order}";

		return $this;
	}
}