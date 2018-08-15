<?php
declare(strict_types=1);

namespace cheetah\essentials;

/**
* Basic router with parameter recognition techniques.
* @param $routes routes file name
* @author Jakub Janek
*/
class Router {
    public function __construct(string $routes) {
        $this->routes = $routes;

        $json = $this->_getRoutes();

        $request_uri = \preg_replace('/\?[0-9A-Za-z]+=.*/', '',
            $_SERVER['REQUEST_URI']);

        $request_uri = \preg_replace('/\/$/', '', $request_uri);

        \preg_match_all('/[\w\d]+/', $request_uri, $current_page);

        foreach ($json as $key => $route) {
            $params = array();

            $request_uri = $request_uri === '' ? '/' : $request_uri;

            $continuation = false;

            if (\preg_match_all('/\//', $route['route']) ===
                \preg_match_all('/\//', $request_uri)) {

                \preg_match_all('/{.*?}|[\w\d]+/', $route['route'], $path);

                $continuation = true;
                foreach ($path[0] as $key_bit => $bit) {
                    if (\preg_match('/{/', $bit) === 0 &&
                        $continuation === true) {

                        if ($bit !== $current_page[0][$key_bit])
                            $continuation = false;

                    } else if ($continuation === true) {
                        $bit_name = \preg_replace('/{|}/', '', $bit);
                        $params[$bit_name] = $current_page[0][$key_bit];
                    }
                }
            }

            if ($continuation === true) {
                $params['_GET'] = $_GET;
                $params['_POST'] = $_POST;

                new Controller($route['view'], $params);
            }
        }
    }

    /**
    * Open routes file
    * @return array json array
    */
    private function _getRoutes(): array {
        $file = \file_get_contents($this->routes);
        $json = \json_decode($file, true);

        return $json;
    }
}

?>
