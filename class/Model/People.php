<?php

namespace Mercado_Solidario\Model;

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class People {

    public static function get_all(){

        $people = [
            [ 
                'id' => 1,
                'name' => 'Joabe',
                'age' => 23
            ],
            [ 
                'id' => 2,
                'name' => 'Luiz',
                'age' => 15
            ],
        ];

        return $people;
    }
}