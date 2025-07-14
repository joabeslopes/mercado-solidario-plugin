<?php

namespace Mercado_Solidario\Model;
use WC_Product_Query;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Checkout {

    public function get_stock(): array {

        $args = [
            'limit' => -1,
            'stock_status' => 'instock',
        ];

        // Perform Query
        $query = new WC_Product_Query($args);

        // Collect Product Object
        $products = $query->get_products();

        $stock = [];

        // Loop through products
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
        };

        return $stock;

    }

    public function get_stock_permission(): bool{
        return true;
    }

}