<?php
declare(strict_types=1);

namespace cheetah;

use function file_get_contents;
use function json_decode;

/** Router with arbitrary parameter position
 * @param string name of json file with routes
 * @param string name of json file with internal config vars
 * @author Jakub Janek
 */
class Router {
	public $routes = [];
	public $config = [];

	public function __construct(string $routes, string $config) {
		$this->routes = $this->_jsonFile($routes);
		$this->config = $this->_jsonFile($config);

		$this->_matchRoute();
	}

	/** Decode json file (just a macro)
	 * @param string file to decode
	 * @return array
	 */
	private function _jsonFile(string $file): array {
		$file = file_get_contents($file);
		$json = json_decode($file, true);

		return $json;
	}

	/** Get URL by name from json routes
	 * @param string name of json object defining route
	 * @param mixed params
	 * @return string
	 */
	public function url(string $name, ...$params): string {
		if (!isset($this->routes[$name])) return "#";

		$route = $this->routes[$name]['route'];

		foreach ($params as $param) {
			$route = preg_replace("/{\p{L}\p{N}}/u", $param, $route, 1);
		}

		return $this->routes[$name]['route'];
	}

	/**
	 * Match route defined by routes file. This is the critical part of router as it requires to be super fast.
	 */
	private function _matchRoute(): void {
		$err = true;

		$request_uri = preg_replace('/\?[\p{L}\p{N}]+=.*/u', '',
			$_SERVER['REQUEST_URI']); //remove get parameters

		if ($this->routes['home']['route'] === $request_uri) {
			$this->_spawn($this->routes['home']);

			return;
		}

		$request_uri = preg_replace('/\/$/u', '', urldecode($request_uri)); //remove leading /

		preg_match_all("/[\p{L}\p{N}]+/u", $request_uri, $uri);
		$uri = $uri[0];
		$numberRequest = count($uri);

		foreach ($this->routes as $routeName => $route) {
			preg_match_all("/(?:{[\p{L}\p{N}]+})|([\p{L}\p{N}]+)/u", $route['route'], $current_route);
			$numberRoute = count($current_route[0]);

			if ($numberRoute !== $numberRequest) continue; //Next route iteration

			$params = [];

			foreach ($current_route[1] as $index => $name) {
				if (empty($name)) {
					$paramName = str_replace(['{', '}'], '', $current_route[0][$index]);
					$params[$paramName] = $uri[$index];

					continue;
				} //Add to params & next route value

				if ($name !== $uri[$index]) continue 2; //Next route iteration
			}

			$err = false; break;
		}

		if ($err) {
			$this->_spawn($this->routes['404']);
			return;
		}

		$this->_spawn($route, $params);
	}

	/** Spawn session and controller
	 * @param array route with parameters
	 * @param array array of params
	 * @return void
	 */
	private function _spawn(array $route, array $params = []): void {
		session_start();

		if (isset($route['view'])) {
			require $this->config['view-prefix'] . $route['view'];

			return;
		}

		$prefix = $this->config['namespace-prefix'] ?? "";

		$call = explode("::", $route['controller']);
		$controller = "{$prefix}{$call[0]}";

		$controller = new $controller;

		$controller->router = $this; //Chain router into controller to access

		call_user_func_array([$controller, $call[1]], $params);
	}
}
