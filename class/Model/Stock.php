<?php

namespace Mercado_Solidario\Model;
use Mercado_Solidario\Base;
use WP_REST_Response;
use WC_Product_Query;
use WP_REST_Request;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Stock extends Base\Model {

    public function get( WP_REST_Request $request ): WP_REST_Response {

        $args = [
            'limit' => -1,
            'type' => [ 'simple', 'variation' ],
            'orderby' => 'name',
            'order' => 'ASC'
        ];

        $query = new WC_Product_Query($args);

        $products = $query->get_products();

        $stock = [];

        if (!empty( $products )) {
            foreach ($products as $product) {

                $sku = $product->get_sku();

                $image_id = $product->get_image_id();
                $image_url = $image_id  ? wp_get_attachment_image_url( $image_id ) 
                                        : 
                                        MERCADO_SOLIDARIO_URL."/frontend/assets/woocommerce-placeholder.png";

                $stock[$sku] = [
                    'id' => $product->get_id(),
                    'name' => $product->get_name(),
                    'price' => (float)$product->get_price(),
                    'image' => $image_url
                ];
            };
        } else {
            return $this->error_response('Não foi possível encontrar o estoque');
        };

        return $this->success_response($stock);
    }

}