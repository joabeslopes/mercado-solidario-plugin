<?php

namespace Mercado_Solidario\Model;
use WC_Product_Query;
use WP_REST_Request;
use WP_Error;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Checkout {

    public function checkout_permission(): bool{
        return current_user_can( 'manage_woocommerce' );
    }

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

    public function post_cart( WP_REST_Request $request ) {

        $status = 200;
        $messages = [];

        $cart = $request['cart'];

        if (!$cart) {
            $status = 400;
        } else {
            foreach ($cart as $cartProd) {

                $args = [
                    'sku' => $cartProd['sku'],
                    'limit' => 1
                ];

                $query = new WC_Product_Query($args);
                $product = $query->get_products()[0];

                if ($product){
                    $quantity = $product->get_stock_quantity() - $cartProd['quantity'];
                    if ($quantity < 0) {
                        $status = 400;
                        $messages[] = $product->get_name() . ' sem estoque suficiente';
                    };

                } else {
                    $status = 400;
                    $messages[] = $cartProd['sku'] . ' não existe';
                }
            }
        };

        if ($status != 200) {
            return new WP_Error(code: 'mercado_solidario_checkout_cart_error', data: $messages);
        } else {
            return true;
        };

    }

}