<?php

namespace Mercado_Solidario\Model;
use WP_REST_Response;
use Mercado_Solidario\Controller;
use Mercado_Solidario\Base;
use WC_Product;
use WP_Post;
use WC_Product_Query;
use WP_REST_Request;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Checkin extends Base\Model {

    public string $created_by;
    public int $supplier_id;
    public string $created_at;
    public string $obs;
    private array $products;
    public array $notes;

    public function set_created_by($created_by): void {
        $this->created_by = sanitize_text_field($created_by);
    }

    public function set_created_at($created_at): void {
        $safe_date = sanitize_text_field($created_at);
        $this->created_at = str_replace('T', ' ', $safe_date);
    }

    public function set_obs($obs): void {
        $this->obs = sanitize_text_field($obs);
    }

    public function set_supplier_id($supplier_id): void {
        $this->supplier_id = (int) sanitize_text_field($supplier_id);
    }

    public function add_product(WC_Product $product, int $quantity){

        $new_quantity = $product->get_stock_quantity() + $quantity;
        $sku = $product->get_sku();
        $name = $product->get_name();

        $this->notes[] = [
            'sku' => $sku,
            'name' => $name,
            'quantity' => $quantity
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
            'post_date' => $this->created_at
        ]);

        update_post_meta($post_id, 'created_by', $this->created_by);
        update_post_meta($post_id, 'obs', $this->obs);
        update_post_meta($post_id, 'supplier_id', $this->supplier_id);
        update_post_meta($post_id, 'notes', json_encode($this->notes, JSON_UNESCAPED_UNICODE) );

        foreach ($this->products as $prod) { 
            $prod->save();
        };

        return $post_id;
    }

    public static function build_from_post(WP_Post $post): Checkin {

        $selected_checkin = new Checkin();

        $notes_json = get_post_meta($post->ID, 'notes', true);
        $selected_checkin->notes = json_decode($notes_json);

        $selected_checkin->created_by = get_post_meta($post->ID, 'created_by', true);
        $selected_checkin->obs = get_post_meta($post->ID, 'obs', true);
        $selected_checkin->supplier_id = (int) get_post_meta($post->ID, 'supplier_id', true);

        return $selected_checkin;
    }

    public function post( WP_REST_Request $request ): WP_REST_Response {

        $status = 200;
        $user = wp_get_current_user();
        $cart = $request['cart'];
        $supplier_id = $request['supplier_id'];
        $created_at = $request['created_at'];
        $obs = $request['obs'];

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

        if(!$supplier_id || $supplier_id == 0) {
            $status = 400;
        };

        if ($status != 200) {
            return $this->error_response('Não foi possível atualizar o estoque');
        } else {
            $this->set_created_by($user->user_login);
            $this->set_created_at($created_at);
            $this->set_obs($obs);
            $this->set_supplier_id($supplier_id);
            $new_post = $this->save();
            if ($new_post > 0){
                return $this->success_response($new_post);
            } else {
                return $this->error_response('Não foi possível atualizar o estoque');
            };
        };

    }

};