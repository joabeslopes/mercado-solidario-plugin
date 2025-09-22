<?php

namespace Mercado_Solidario\Model;
use Mercado_Solidario\REST\Router;
use Mercado_Solidario\Controller;
use WC_Product;
use WP_Post;
use WC_Product_Query;
use WP_REST_Request;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Checkin {

    public string $created_by;
    public string $supplier;
    public array $cart;
    private array $products;
    public array $notes;

    public function add_product(WC_Product $product, int $quantity){

        $new_quantity = $product->get_stock_quantity() + $quantity;

        $this->notes[] = [
            'name' => $product->get_name(),
            'old_quantity' => $product->get_stock_quantity(),
            'new_quantity' => $new_quantity
        ];

        $product->set_stock_quantity($new_quantity);

        $this->products[] = $product;
    }

    public function save(){

        $post_id = wp_insert_post([
            'post_type'   => Controller\Checkin::$post_type,
            'post_status' => 'publish'
        ]);

        if (is_wp_error($post_id)) {
            return 0;
        };

        wp_update_post([
            'ID' => $post_id,
            'post_title' => $post_id,
        ]);

        update_post_meta($post_id, 'created_by', $this->created_by);
        update_post_meta($post_id, 'cart', json_encode($this->cart) );
        update_post_meta($post_id, 'notes', json_encode($this->notes) );

        foreach ($this->products as $prod) { 
            $prod->save();
        };

        return $post_id;
    }

    public static function build_from_post(WP_Post $post): Checkin {

        $selected_checkin = new Checkin();

        $selected_checkin->created_by = get_post_meta($post->ID, 'created_by', true);
        $notes_json = get_post_meta($post->ID, 'notes', true);
        $selected_checkin->notes = json_decode($notes_json);

        $cart_json = get_post_meta($post->ID, 'cart', true);
        $selected_checkin->cart = json_decode($cart_json);

        $selected_checkin->supplier = get_post_meta($post->ID, 'supplier', true);

        return $selected_checkin;
    }

    public function post( WP_REST_Request $request ) {

        $status = 200;
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
                    $this->add_product($product, $prodQuantity);
                } else {
                    $status = 400;
                };
            };
        };

        if ($status != 200) {
            return Router::error_response('mercado_solidario_checkin', 'Não foi possível atualizar o estoque');
        } else {
            $this->created_by = $user->user_login;
            $this->cart = $cart;
            $new_post = $this->save();
            if ($new_post > 0){
                return Router::success_response( $new_post );
            } else {
                return Router::error_response('mercado_solidario_checkin', 'Não foi possível atualizar o estoque');
            };
        };

    }

};