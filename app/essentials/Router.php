<?php
declare(strict_types=1);

namespace microframework\essentials;

/**
* Basic router with parameter techniques.
* @author Jakub Janek
*/
class Router {
    public function __construct(string $routes) {
        $this->routes_file = $routes;

        $request_uri = \preg_replace('/\?[0-9A-Za-z]+=.*/', '',
            $_SERVER['REQUEST_URI']);

        $current_page = \preg_match('/\/$/', $request_uri) ?
            \preg_replace('/\/$/', '', $request_uri) :
                $request_uri;

        foreach ($this->_getRoutes() as $route) {
            $path = $this->_splitRoutes($route['route']);

            $params['_POST'] = $_POST;
            $params['_GET'] = $_GET;

            $success = false;
            if ($current_page === $path['name']) {
                $success = true;
            } else if (\strpos($current_page, $path['name']) !== false) {

                $current_params =
                    \str_replace($path['name'], '', $current_page);

                $current_params = \explode('/',
                    \preg_replace('/^\//', '', $current_params, 1));

                $params_input = \explode('/',
                    \preg_replace('/^\//', '', $path['params'], 1));

                foreach ($current_params as $key => $parameter) {
                    $params[$params_input[$key]] = $parameter;
                }

                $success = true;
            }

            if ($success === true) new Controller($route['view'], $params);
        }

    }

    /**
    * Open routes file
    * @return array json array
    */
    private function _getRoutes(): array {
        $file = \file_get_contents($this->routes_file);
        $json = \json_decode($file, true);

        return $json;
    }

    /**
    * Function splits route and parameters into array
    * @param string $route route path with parameters
    * @return array splitted array into routes and params if params doesn't
    * exist params => false
    */
    private function _splitRoutes(string $route): array {
        $path_array['name'] = \preg_match('/\{/', $route) ?
            \preg_replace('/\/\{.*/', '', $route) : $route;

        $path_array['params'] = $path_array['name'] != $route ?
            \str_replace(['{', '}'], '',
                \str_replace($path_array['name'], '', $route)) : false;

        return $path_array;
    }
}

?>
