<?php
namespace cheetah\database;

/**
 * Delete query
 * @author Jakub Janek
 */
class DeleteQuery extends Query {
	public function __construct($table, $db) {
		parent::__construct($table, $db);

		$this->from = "FROM {$table}";
	}

	/**
	 * Execute delete query
	 * @return DeleteQuery
	 */
	public function execute() {
		$this->query = "DELETE {$this->from}";
		$this->query .= $this->conditions !== 'WHERE' ? $this->add($this->conditions) : '';
		$this->query .= $this->add($this->order);

		$this->db->query($this->query);
		$this->result = true;

		return $this;
	}
}