<?php

namespace Mercado_Solidario\Pages;
use WP_Post;
use Mercado_Solidario\Model;
use Mercado_Solidario\Controller;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Checkin_Post {

    public function __construct(){
        add_action('add_meta_boxes', [$this, 'add_metaboxes']);
    }

    public function add_metaboxes() {
        add_meta_box(
            'ms_checkin_meta',
            'Detalhes da entrada',
            [$this, 'render_metabox'],
            Controller\Checkin::$post_type,
            'side'
        );
    }

    public function render_metabox(WP_Post $post) {
        $checkin = Model\Checkin::build_from_post($post)

        ?>
        <p>
            <label><strong>Criado por:</strong></label><br>
            <label><?php echo esc_attr($checkin->created_by);?></label><br>
            <label><strong>Fornecedor:</strong></label><br>
            <label><?php echo esc_attr($checkin->supplier);?></label><br>
        </p>
        <ul>
        <?php
            foreach($checkin->notes as $note){
                $name         = $note->name;
                $old_quantity = $note->old_quantity;
                $new_quantity = $note->new_quantity;
                ?>
                <li class="card">
                    <p>
                        <label><strong>Produto:</strong></label><br>
                        <label><?php echo esc_attr($name);?></label><br>
                        <label><strong>Quantidade antiga:</strong></label><br>
                        <label><?php echo esc_attr($old_quantity); ?></label><br>
                        <label><strong>Nova quantidade:</strong></label><br>
                        <label><?php echo esc_attr($new_quantity);?></label><br>
                    </p>
                </li>
                <?php
            }
        ?>
        </ul>
        <?php
    }

}