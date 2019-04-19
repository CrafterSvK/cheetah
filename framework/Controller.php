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
	public function render(string $view) {
		if (!file_exists($view)) {
			echo "View {$view} doesn't exist.";

			exit();
		}

		require $view;
	}

	public function json($json) {
		header("Content-Type: application/json; charset=utf8");

		echo json_encode($json);
	}
}