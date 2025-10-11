<?php

namespace Mercado_Solidario\Model;
use Mercado_Solidario\Base;
use WP_REST_Response;
use WP_REST_Request;
use WC_Order;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Checkout extends Base\Model {

    public function post( WP_REST_Request $request ): WP_REST_Response {

        $status = 200;
        $messages = [];

        $order = new WC_Order();
        $user = wp_get_current_user();
        $cart = $request['cart'];

        if (!$user || $user->ID === 0) {
            $status = 400;
            $messages[] = 'Usuario não logado';
        };

        if (!$cart || empty($cart)) {
            $status = 400;
            $messages[] = 'Carrinho vazio';
        } else {
            foreach ($cart as $cartProd) {

                $prodSku = sanitize_text_field($cartProd['sku']);
                $prodQuantity = (int) sanitize_text_field($cartProd['quantity']);
                $prodID = wc_get_product_id_by_sku($prodSku);

                if ($prodID){
                    $product = wc_get_product($prodID);

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

            return $this->success_response($order_id);
        } else {
            return $this->error_response($messages);
        };
    }

}