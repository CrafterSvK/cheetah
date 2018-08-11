<?php
declare(strict_types=1);

namespace microframework\essentials;

/**
* Abstract Controller to be extended
*/
class Controller {
    public function __construct(string $view, array $params) {
        $this->view = $view;

        $this->manipulate();
    }

    //Standard functions

    /**
    * Manipulates the model
    * @return void
    */
    public function manipulate(): void {
        $this->show();
    }

    /**
    * Displays the view based on a json route
    * @return void
    */
    public function show(): void {
        require 'view/' . $this->view;
    }

    //Tools
}
