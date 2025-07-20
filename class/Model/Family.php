<?php

namespace Mercado_Solidario\Model;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Family {

    public int $id;
    public string $name;
    public string $cpf;
    public string $phone;
    public int $balance;
    public string $valid_until;
    public string $notes;

    public function __construct( int $id=0 ){
        if ($id > 0) {

        };
    }

    public function save(){

    }

};