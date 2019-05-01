<?php

namespace cheetah;

use function file_exists;

/**
 * Fairly useless right now, no template engine is used so wrapping only removes local functions
 * Twig is an option.
 * Developing own template engine is useless as most of stuff is currently happening with javascript.
 * Is there a point of living? Maybe preloading as a concept is bad and we should all fell for the might JS.
 * React is pointless and big.
 */
abstract class Controller {
	/**
	 * Render view file.
	 * @param string view file location
	 */
	public function render(string $view): void {
		if (!file_exists($view)) {
			echo "View {$view} doesn't exist.";

			exit();
		}

		require $view;
		exit();
	}

	/**
	 * Prints JSON content with JSON header formatted as utf8
	 * @param mixed json content
	 */
	public function json($json): void {
		header("Content-Type: application/json; charset=utf8");

		echo json_encode($json);
		exit();
	}
}