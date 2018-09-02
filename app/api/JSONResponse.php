<?php
declare(strict_types=1);

namespace cheetah\api;

/**
* Class that creates and manipulates JSON Responses
* @param array parameters to be displayed
* @author Jakub Janek
*/
class JSONResponse {
    public function __construct(array $params) {
        $this->json = \json_encode($params);
    }

    /**
    * Shows the json or the error
    * @return void
    */
    public function show(): void {
        echo $this->json;
    }
}
