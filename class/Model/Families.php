<?php

namespace Mercado_Solidario\Model;

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Families {

    public function get_all_families(): array {

        $families = [
            [ 
                'id' => 1,
                'main_person' => 'Joabe',
                'balance' => 200
            ],
            [ 
                'id' => 2,
                'main_person' => 'Luiz',
                'balance' => 150
            ],
        ];

        return $families;
    }

    public function get_families_permission(): bool {
        return true;
    }

}