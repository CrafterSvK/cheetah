<?php

namespace cheetah\database;

/**
 * Condition trait
 * @author Jakub Janek
 */
trait ConditionTrait {
	protected $operator = 'AND';

	/**
	 * Writes condition into string
	 * @param string condition
	 * @return void
	 */
	protected function add($conditionString): void {
		$this->conditions .= empty($this->conditions)
			? $conditionString
			: " {$this->operator} $conditionString";
	}

	/**
	 * Set operator of a next conditions to OR
	 * @return Object
	 */
	public function or() {
		$this->operator = "OR";

		return $this;
	}

	/**
	 * Set operator of a next conditions to AND
	 * @return Object
	 */
	public function and() {
		$this->operator = "AND";

		return $this;
	}

	/**
	 * Set operator of a next conditions to XOR
	 * @return Object
	 */
	public function xor() {
		$this->operator = "XOR";

		return $this;
	}
}