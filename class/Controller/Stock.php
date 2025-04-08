<?php

namespace Mercado_Solidario\Controller;
use Mercado_Solidario\Model;

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Stock {

    private Model\Stock $model;

    public function __construct(){
        $this->model = new Model\Stock();
    }

    public function get_stock(){

        $stock = $this->model->get_stock();

        return $stock;

    }

    public function get_stock_permission(){
        return true;
    }

}