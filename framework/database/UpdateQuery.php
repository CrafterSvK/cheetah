<?php
namespace cheetah\database;

/**
 * Update query
 * @author Jakub Janek
 */
class UpdateQuery extends Query {
	public function __construct($table, $db) {
		parent::__construct($table, $db);

		$this->from = "{$table}";
		$this->items = '';
		$this->order = '';
	}

	/**
	 * Add value and column to update
	 * @param array|string table.column | column
	 * @param string value to add
	 * @return UpdateQuery
	 */
	public function value($column, $value) {
		if (is_array($column)) {
			$key = array_key_first($column);
			$column = "{$key}.{$column[$key]}";
		}
		
		$value = '\'' . $this->db->real_escape_string((string)$value) . '\'';

		$set = "{$column} = {$value}";

		$this->set .= empty($this->set) ? $set : ',' . $this->add($set);

		return $this;
	}

	/**
	 * Add values and columns to update
	 * @param array columns as keys and values as values
	 * @return UpdateQuery
	 */
	public function values($values) {
		foreach ($values as $key => $value) {
			$this->value($key, $value);
		}
		
		return $this;
	}

	/**
	 * Execute update query
	 * @return UpdateQuery
	 */
	public function execute() {
		$this->query = "UPDATE {$this->from} SET {$this->set} ";
		$this->query .= $this->conditions !== 'WHERE' ? $this->add($this->conditions) : '';

		try {
			$result = $this->db->query($this->query);

			if ($result === false) {
				throw new \Exception("Invalid query {$this->query}");
			} else {
				//$this->result = $result->fetch_all(MYSQLI_ASSOC); todo: fix
			}
		} catch (\Exception $e) {
			$this->result = false;
		}

		return $this;
	}
}