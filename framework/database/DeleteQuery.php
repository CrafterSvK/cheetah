<?php
declare(strict_types=1);

namespace cheetah\database;

use mysqli;

/**
 * Delete query (part of database abstraction layer)
 * @author Jakub Janek
 */
class DeleteQuery extends Query {

	/**
	 * DeleteQuery constructor.
	 * @param string|array name of a table
	 * @param mysqli $db mysqli connection
	 */
	public function __construct($table, mysqli $db) {
		parent::__construct($table, $db);
	}

	/**
	 * Execute delete query
	 * @return void
	 */
	public function execute(): void {
		$this->query = sprintf(
			"DELETE FROM %s WHERE %s",
			$this->table,
			!empty($this->conditions) ? $this->conditions : '1'
		);

		$this->db->query($this->query);
	}
}