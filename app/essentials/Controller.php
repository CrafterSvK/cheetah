<?php
declare(strict_types=1);

namespace cheetah\essentials;

/**
* Abstract Controller to be extended
*/
class Controller {
    public function __construct(string $view, array $params = array()) {
        $this->view = $view;
        $this->params = $params;

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
