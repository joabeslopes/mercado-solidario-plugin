<?php

namespace Mercado_Solidario\Model;
use Mercado_Solidario\REST\Router;
use WC_Product_Query;
use WP_REST_Request;
use WC_Order;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Stock {

    public function get_stock(): array {

        $args = [
            'limit' => -1,
            'type' => [ 'simple', 'variation' ]
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
        };

        return Router::success_response($stock);
    }

    public function post_checkout( WP_REST_Request $request ) {

        $status = 200;
        $messages = [];

        $order = new WC_Order();
        $user = wp_get_current_user();

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

                $result = $query->get_products();

                if ($result){
                    $product = $result[0];

                    $quantity = $product->get_stock_quantity() - $cartProd['quantity'];

                    if ($quantity >= 0) {
                        $order->add_product( $product, $cartProd['quantity'] );
                    } else {
                        $status = 400;
                        $messages[] = $product->get_name() . ' sem estoque suficiente';
                    };

                } else {
                    $status = 400;
                    $messages[] = $cartProd['sku'] . ' não existe';
                };
            };
        };

        if ($status == 200) {
            $order->set_created_via( 'admin' );
            $order->calculate_totals();
            $order->set_status('completed', 'Mercado Solidario');
            $order->add_order_note( 'Criado por '.$user->user_login );

            $order_id = $order->save();

            return Router::success_response( [ 'order' => $order_id ] );
        } else {
            return Router::error_response('mercado_solidario_error_checkout', $messages);
        };
    }

    public function post_checkin( WP_REST_Request $request ) {
        $cart = $request['cart'];

        return Router::error_response('mercado_solidario_error_checkin', 'Não disponível no momento');
    }

}