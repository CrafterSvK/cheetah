<?php
namespace cheetah;

class RandomController extends Controller {
	public function random($min, $max) {
		$json = new \stdClass();
		$json->random = \rand($min, $max);

		$this->json($json);

	}

	public function home() {
		echo "Ahoj světe!";
	}

	public function ahoj($name, $surname) {
		echo "Ahoj ty jeden, {$name} {$surname}";
	}
}