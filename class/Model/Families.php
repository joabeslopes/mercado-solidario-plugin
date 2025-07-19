<?php

namespace Mercado_Solidario\Model;
use Mercado_Solidario\REST;

// don't call the file directly
defined( 'ABSPATH' ) || die;

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
        return REST\success_response($families);
    }

    public function get_permission(): bool {
        return current_user_can( 'edit_pages' );
    }

}