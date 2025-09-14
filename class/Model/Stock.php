<?php

namespace Mercado_Solidario\Model;
use Mercado_Solidario\REST\Router;
use WC_Product_Query;
use WP_REST_Request;
use WC_Order;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Stock {

    public function __construct(){
        add_action('init', [$this, 'register_checkin_post_type']);
    }

    public function register_checkin_post_type(){
        register_post_type(MERCADO_SOLIDARIO_CHECKIN_POST, [
            'public' => false
        ]);
    }

    public function get_stock() {

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
            return Router::error_response('mercado_solidario_error_stock', 'Não foi possível encontrar o estoque');
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

    public function post_checkin( WP_REST_Request $request ) {

        $status = 200;
        $user = wp_get_current_user();
        $cart = $request['cart'];
        $checkin = new Checkin();

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
                    $checkin->add_product($product, $prodQuantity);
                } else {
                    $status = 400;
                };
            };
        };

        if ($status != 200) {
            return Router::error_response('mercado_solidario_checkin', 'Não foi possível atualizar o estoque');
        } else {
            $checkin->created_by = $user->user_login;
            $checkin->cart = $cart;
            $new_post = $checkin->save();
            if ($new_post > 0){
                return Router::success_response( $new_post );
            } else {
                return Router::error_response('mercado_solidario_checkin', 'Não foi possível atualizar o estoque');
            };
        };

    }

}