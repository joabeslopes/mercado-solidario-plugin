<?php

namespace Mercado_Solidario\Model;
use Mercado_Solidario\REST\Router;
use WP_REST_Request;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Families {

    public Family $family;

    public function __construct(){
        add_action('init', [$this, 'register_family_post_type']);
    }

    public function register_family_post_type(){
        register_post_type(MERCADO_SOLIDARIO_FAMILY_POST, [
            'public' => false
        ]);
    }

    public function get_all_families() {

        $families = Family::search_all_valid_families();

        if ($families){
            return Router::success_response($families);
        } else {
            return Router::error_response('error', 'Nenhuma familia encontrada');
        };

    }

    public function post_family( WP_REST_Request $request ){
        $new_family = $request['newFamily'];

        if(!$new_family){
            return Router::error_response('erro', 'Faltou enviar a nova familia');
        };

        $family = new Family(
            name: $new_family['name'],
            cpf: $new_family['cpf'],
            phone: $new_family['phone'],
            balance: $new_family['balance'],
            valid_until: $new_family['valid_until'],
            notes: $new_family['notes']
        );

        if ( $family->save() ){
            return Router::success_response();
        } else {
            return Router::error_response('erro', 'erro na hora de salvar');
        };

    }

    public function get_permission(): bool {
        return current_user_can( 'edit_pages' );
    }

}