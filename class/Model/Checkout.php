<?php

namespace Mercado_Solidario\Model;
use Mercado_Solidario\REST\Router;
use WC_Product_Query;
use WP_REST_Request;
use WC_Order;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Checkout {

    public function post( WP_REST_Request $request ) {

        $status = 200;
        $messages = [];

        $order = new WC_Order();
        $user = wp_get_current_user();

        $cart = $request['cart'];

        if (!$cart) {
            $status = 400;
        } else {
            foreach ($cart as $cartProd) {

                $prodSku = sanitize_text_field($cartProd['sku']);
                $prodQuantity = (int) sanitize_text_field($cartProd['quantity']);

                $args = [
                    'sku' => $prodSku,
                    'limit' => 1
                ];
                $query = new WC_Product_Query($args);

                $result = $query->get_products();

                if ($result){
                    $product = $result[0];

                    $quantity = $product->get_stock_quantity() - $prodQuantity;

                    if ($quantity >= 0) {
                        $order->add_product( $product, $prodQuantity );
                    } else {
                        $status = 400;
                        $messages[] = $product->get_name() . ' sem estoque suficiente';
                    };

                } else {
                    $status = 400;
                    $messages[] = $prodSku . ' não existe';
                };
            };
        };

        if ($status == 200) {
            $order->set_created_via( 'admin' );
            $order->calculate_totals();
            $order->set_status('completed', 'Mercado Solidario');
            $order->add_order_note( 'Criado por '.$user->user_login );

            $order_id = $order->save();

            return Router::success_response( $order_id );
        } else {
            return Router::error_response('mercado_solidario_error_checkout', $messages);
        };
    }

}