<?php
declare(strict_types=1);

namespace cheetah\database;

/**
 * Delete query (part of database abstraction layer)
 * @param string name of a table
 * @param \mysqli connection
 * @author Jakub Janek
 */
class DeleteQuery extends Query {
	public function __construct(string $table, \mysqli $db) {
		parent::__construct($table, $db);

		$this->from .= $this->add($table);
	}

	/**
	 * Execute delete query
	 * @return int
	 */
	public function execute(): int {
		$this->query = "DELETE {$this->from}";
		$this->query .= $this->conditions !== 'WHERE' ? $this->add($this->conditions) : '';
		$this->query .= $this->add($this->order);

		$this->db->query($this->query);

		return $this->db->insert_id;
	}
}