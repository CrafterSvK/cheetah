<?php
declare(strict_types=1);

namespace cheetah\essentials;

class Database extends \mysqli {
    public function __construct() {
        $database = array(
            "host" => "localhost",
            "name" => "root",
            "password" => "",
            "db_name" => "cheetah"
        );

        parent::__construct(
            $database["host"],
            $database["name"],
            $database["password"],
            $database["db_name"]
        );

        parent::set_charset('utf8mb4');
    }
}