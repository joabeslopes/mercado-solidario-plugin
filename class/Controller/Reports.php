<?php

namespace Mercado_Solidario\Controller;
use Mercado_Solidario\Model;
use Mercado_Solidario\Base;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Reports extends Base\Controller {

    public function __construct() {
        $this->model = new Model\Reports();
        $this->register('get');
    }

}
