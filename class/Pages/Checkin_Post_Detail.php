<?php

namespace Mercado_Solidario\Pages;
use WP_Post;
use Mercado_Solidario\Model;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Checkin_Post_Detail {
    public string $post_type;

    public function __construct($post_type){
        $this->post_type = $post_type;
        add_action('add_meta_boxes', [$this, 'add_metaboxes']);
    }

    public function add_metaboxes() {
        add_meta_box(
            'ms_checkin_meta',
            'Detalhes da entrada',
            [$this, 'render_metabox'],
            $this->post_type,
            'normal'
        );
    }

    public function render_metabox(WP_Post $post) {
        $checkin = Model\Checkin::build_from_post($post);
        $supplier = Model\Supplier::build_from_id($checkin->supplier_id);

        ?>
        <p>
            <label><strong>Criado por:</strong></label><br>
            <label><?php echo $checkin->created_by ?></label><br>
            <label><strong>Fornecedor:</strong></label><br>
            <label><?php echo $supplier->name ?></label><br>
            <label><strong>Observações:</strong></label><br>
            <label><?php echo $checkin->obs ?></label><br>
        </p>
        <ul>
        <?php
            foreach($checkin->notes as $note){
                $name         = $note->name;
                $quantity = $note->quantity;
                ?>
                <li class="card">
                    <p>
                        <label><strong>Produto:</strong></label><br>
                        <label><?php echo $name ?></label><br>
                        <label><strong>Quantidade:</strong></label><br>
                        <label><?php echo $quantity ?></label><br>
                    </p>
                </li>
                <?php
            }
        ?>
        </ul>
        <?php
    }

}