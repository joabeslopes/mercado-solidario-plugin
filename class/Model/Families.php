<?php

namespace Mercado_Solidario\Model;
use Mercado_Solidario\REST;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Families {

    public function get_all_families(): array {

        $test1 = new Family();
        $test1->id = 1;
        $test1->name = 'Joabe';
        $test1->balance = 100;
        $test1->valid_until = date('Y-m-d');

        $test2 = new Family();
        $test2->id = 2;
        $test2->name = 'Fulano';
        $test2->balance = 200;
        $test2->valid_until = date('Y-m-d');

        $families = [
            (array)$test1,
            (array)$test2
        ];

        return REST\success_response($families);
    }

    public function get_permission(): bool {
        return current_user_can( 'edit_pages' );
    }

}