<?php

namespace Mercado_Solidario\Model;
use Mercado_Solidario\Base;
use WP_REST_Response;
use WP_REST_Request;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Reports extends Base\Model {

    public function get( WP_REST_Request $request ): WP_REST_Response {
        $action = $request->get_param('action');
        
        if ($action === 'categories') {
            return $this->get_categories();
        }

        $type = $request->get_param('type');
        $start_date = $request->get_param('start_date');
        $end_date = $request->get_param('end_date');
        $product_id = $request->get_param('product_id');
        $category_id = $request->get_param('category_id');

        if ($type === 'checkin') {
            $data = $this->get_checkin_report($start_date, $end_date, $product_id, $category_id);
            return $this->success_response($data);
        } elseif ($type === 'checkout') {
            $data = $this->get_checkout_report($start_date, $end_date, $product_id, $category_id);
            return $this->success_response($data);
        }

        return $this->error_response('Tipo de relatório inválido');
    }

    private function get_categories(): WP_REST_Response {
        $terms = get_terms([
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
        ]);

        $categories = [];
        if (!is_wp_error($terms)) {
            foreach ($terms as $term) {
                $categories[] = [
                    'id' => $term->term_id,
                    'name' => $term->name,
                ];
            }
        }
        return $this->success_response($categories);
    }

    private function get_checkin_report($start_date, $end_date, $product_id, $category_id): array {
        if (empty($start_date)) {
            return [];
        }

        $args = [
            'post_type' => \Mercado_Solidario\Controller\Checkin::$post_type,
            'posts_per_page' => -1,
            'post_status' => 'publish',
        ];

        $end = !empty($end_date) ? $end_date : $start_date;

        $args['date_query'] = [[
            'after' => $start_date . ' 00:00:00',
            'before' => $end . ' 23:59:59',
            'inclusive' => true
        ]];

        $posts = get_posts($args);
        $aggregated = [];

        foreach ($posts as $post) {
            $notes_json = get_post_meta($post->ID, 'notes', true);
            if (empty($notes_json)) {
                continue;
            }
            $notes = json_decode($notes_json, true);
            if (!is_array($notes)) {
                continue;
            }

            foreach ($notes as $item) {
                $sku = isset($item['sku']) ? $item['sku'] : '';
                $name = isset($item['name']) ? $item['name'] : '';
                $quantity = isset($item['quantity']) ? (int)$item['quantity'] : 0;

                $prod_id = wc_get_product_id_by_sku($sku);
                if ($prod_id) {
                    if (!empty($product_id) && $prod_id != $product_id) {
                        continue;
                    }
                    if (!empty($category_id)) {
                        $terms = get_the_terms($prod_id, 'product_cat');
                        $has_category = false;
                        if (!empty($terms) && !is_wp_error($terms)) {
                            foreach ($terms as $term) {
                                if ($term->term_id == $category_id) {
                                    $has_category = true;
                                    break;
                                }
                            }
                        }
                        if (!$has_category) {
                            continue;
                        }
                    }
                } else {
                    if (!empty($product_id) || !empty($category_id)) {
                        continue;
                    }
                }

                if (!isset($aggregated[$sku])) {
                    $aggregated[$sku] = [
                        'name' => $name,
                        'sku' => $sku,
                        'quantity' => 0
                    ];
                }
                $aggregated[$sku]['quantity'] += $quantity;
            }
        }

        usort($aggregated, function($a, $b) {
            return $b['quantity'] <=> $a['quantity'];
        });

        return $aggregated;
    }

    private function get_checkout_report($start_date, $end_date, $product_id, $category_id): array {
        if (empty($start_date)) {
            return [];
        }

        $args = [
            'status' => 'completed',
            'limit' => -1,
        ];

        $end = !empty($end_date) ? $end_date : $start_date;

        $args['date_after'] = $start_date . ' 00:00:00';
        $args['date_before'] = $end . ' 23:59:59';

        $orders = wc_get_orders($args);
        $aggregated = [];

        foreach ($orders as $order) {
            foreach ($order->get_items() as $item) {
                $product = $item->get_product();
                if (!$product) {
                    continue;
                }

                $prod_id = $product->get_id();
                $sku = $product->get_sku() ?: 'ID: ' . $prod_id;
                $name = $product->get_name();
                $quantity = $item->get_quantity();

                if (!empty($product_id) && $prod_id != $product_id) {
                    continue;
                }

                if (!empty($category_id)) {
                    $terms = get_the_terms($prod_id, 'product_cat');
                    $has_category = false;
                    if (!empty($terms) && !is_wp_error($terms)) {
                        foreach ($terms as $term) {
                            if ($term->term_id == $category_id) {
                                $has_category = true;
                                break;
                            }
                        }
                    }
                    if (!$has_category) {
                        continue;
                    }
                }

                if (!isset($aggregated[$sku])) {
                    $aggregated[$sku] = [
                        'name' => $name,
                        'sku' => $sku,
                        'quantity' => 0
                    ];
                }
                $aggregated[$sku]['quantity'] += $quantity;
            }
        }

        usort($aggregated, function($a, $b) {
            return $b['quantity'] <=> $a['quantity'];
        });

        return $aggregated;
    }

}
