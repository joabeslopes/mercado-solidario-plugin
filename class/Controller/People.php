<?php

namespace Mercado_Solidario\Controller;
use Mercado_Solidario\Model;

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class People {

    public Model\People $model;

    public function __construct(){
        $this->model = new Model\People();
    }

    public function get_all(){

        $people = $this->model->get_all();

        return $people;

    }

    public function get_people_permission(){
        return true;
    }

}