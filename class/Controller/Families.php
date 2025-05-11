<?php

namespace Mercado_Solidario\Controller;
use Mercado_Solidario\Model;

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Families {

    public Model\Families $model;

    public function __construct(){
        $this->model = new Model\Families();
    }

    public function get_all(){

        $families = $this->model->get_all();

        return $families;

    }

    public function get_people_permission(){
        return true;
    }

}