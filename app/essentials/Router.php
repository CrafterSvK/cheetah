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

        $this->json = $this->_getRoutes();

        $request_uri = \preg_replace('/\?[0-9A-Za-z]+=.*/', '',
            $_SERVER['REQUEST_URI']);

        $request_uri = \preg_replace('/\/$/', '', $request_uri);

        $this->_matchRoute($request_uri);
    }

    /**
    * Get url from a json file.
    * @param string name of url in json routes file
    * @return string url address from given name
    * @return string route from given name
    */
    public function getUrl(string $name): string {
        return $this->json[$name]['route'];
    }

    /**
    * Returns an json array of routes.
    * @return array json array
    * @return array array with routes
    */
    private function _getRoutes(): array {
        $file = \file_get_contents($this->routes);
        $json = \json_decode($file, true);

        return $json;
    }

    /**
    * Matches a url parameters with route. Calls Controller function
    * @param string url to match with route
    * @return void
    */
    private function _matchRoute(string $url): void {
        $routes = $this->_getRoutes();
        \preg_match_all('/[^\/]+/', urldecode($url), $current_page);
        $matches = 0;

            foreach ($routes as $routeName => $route) {
                if ($matches !== 1) {
                    $params = array(
                        "_GET" => $_GET,
						"HTTP_RAW_POST_DATA" => $HTTP_RAW_POST_DATA,
                        "_POST" => $_POST,
                        "_router" => $this
                    );

                    $continuation = false;

                    if (($url === '/' || $url === '') &&
                        $route['route'] === '/') {
                        $continuation = true;
                    } else if (\preg_match_all('/\//', $route['route']) ===
                        \preg_match_all('/\//', $url)) {

                        \preg_match_all('/{.*?}|[\w\d]+/',
                            $route['route'], $path);

                        $continuation = true;

                        foreach ($path[0] as $key_bit => $bit) {
                            if (\preg_match('/{/', $bit) === 0 &&
                                $continuation === true) {

                                if (isset($current_page[0][0]))
                                    if ($bit !== $current_page[0][$key_bit])
                                        $continuation = false;

                            } else if ($continuation === true) {
                                $bit_name = \preg_replace('/{|}/', '', $bit);
                                $params[$bit_name] = $current_page[0][$key_bit];
                            }
                        }
                    }

                    if ($continuation === true) {
                        if (isset($route['controller'])) {
                            new $route['controller']($route['view'], $params);
                        } else {
                            new Controller($route['view'], $params);
                        }
                        
                        $matches = 1;
                    }
            }
        }

        if ($matches !== 1)
            new Controller($routes['404']['view']);
    }
}
?>
