<?php

namespace Mercado_Solidario\Controller;
use Mercado_Solidario\Model;
use Mercado_Solidario\Base;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Stock extends Base\Controller{

    public function __construct(){

        $this->model = new Model\Stock();
        $this->base_route = 'stock';

        $this->register('get');
    }

};