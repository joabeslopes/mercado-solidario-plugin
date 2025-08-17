<?php

namespace Mercado_Solidario\Model;
use WC_Product;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Checkin {

    public string $created_by;
    public array $cart;
    private array $products;
    private array $notes;

    public function add_product(WC_Product $product, int $quantity){

        $new_quantity = $product->get_stock_quantity() + $quantity;

        $this->notes[] = [
            'id' => $product->get_id(),
            'sku' => $product->get_sku(),
            'old_quantity' => $product->get_stock_quantity(),
            'new_quantity' => $new_quantity
        ];

        $product->set_stock_quantity($new_quantity);

        $this->products[] = $product;
    }

    public function save(){

        $post_id = wp_insert_post([
            'post_type'   => MERCADO_SOLIDARIO_CHECKIN_POST,
            'post_status' => 'publish',
        ]);

        if (is_wp_error($post_id)) {
            return 0;
        };

        update_post_meta($post_id, 'created_by', $this->created_by);
        update_post_meta($post_id, 'cart', json_encode($this->cart) );
        update_post_meta($post_id, 'notes', json_encode($this->notes) );

        foreach ($this->products as $prod) { 
            $prod->save();
        };

        return $post_id;
    }
};