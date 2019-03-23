<?php
namespace cheetah;

abstract class Controller {
	public function render(string $view) {
		if (!\file_exists($view)) {
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