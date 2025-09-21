<?php

namespace Mercado_Solidario\Controller;
use Mercado_Solidario\Model;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Checkout extends Base{

    public function __construct(){

        $this->model = new Model\Checkout();
        $this->base_route = 'checkout';

        $this->register('post');
    }

};