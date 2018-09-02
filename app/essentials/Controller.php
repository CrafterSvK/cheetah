<?php
declare(strict_types=1);

namespace cheetah\essentials;

/**
* Abstract Controller
* @param string view to be displayed
* @param array parameters for view to display
* @author Jakub Janek
*/
class Controller {
    public function __construct(string $view, array $params = array()) {
        $this->view = $view;
        $this->params = $params;

        $this->router = isset($this->params['_router']) ?
            $this->params['_router'] : null;

        $this->manipulate();
    }

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
        require $this->view;
    }
}
