<?php
class Wpz_Kueisoner_Indikator
{
    public function autoload()
    {
        add_action('init', array($this, 'register_post_type'));
        add_filter('rwmb_meta_boxes', array($this, 'register_meta_boxes'));
    }

    public function register_post_type()
    {
        $labels = array(
            'name'               => 'Indikator',
            'singular_name'      => 'Indikator',
            'menu_name'          => 'Indikator',
            'add_new'            => 'Tambah Indikator',
            'add_new_item'       => 'Tambah Indikator Baru',
            'edit_item'          => 'Edit Indikator',
            'new_item'           => 'Buku Indikator',
            'view_item'          => 'Lihat Indikator',
            'search_items'       => 'Cari Indikator',
            'not_found'          => 'Indikator tidak ditemukan',
            'not_found_in_trash' => 'Tidak ada dalam tong sampah',
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => 'edit.php?post_type=kueisoner',
            'query_var'          => true,
            'rewrite'            => array('slug' => 'indikator-kueisoner'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array('title'),
        );

        register_post_type('indikator-kueisoner', $args);
    }

    public function register_meta_boxes($meta_boxes)
    {
        $prefix = '';

        $meta_boxes[] = [
            'title'      => esc_html__('Detail Indikator', 'wpz'),
            'id'         => 'detail_indikator',
            'post_types' => ['indikator-kueisoner'],
            'context'    => 'after_title',
            'fields'     => [
                [
                    'type'       => 'post',
                    'name'       => esc_html__('Dimensi Kuesioner', 'wpz'),
                    'id'         => $prefix . 'kueisoner',
                    'post_type'  => 'kueisoner',
                    'field_type' => 'select_advanced',
                    'std'        => isset($_GET['idkueisoner']) ? $_GET['idkueisoner'] : '',
                ],
                [
                    'type'       => 'post',
                    'name'       => esc_html__('Faktor', 'wpz'),
                    'id'         => $prefix . 'faktor',
                    'post_type'  => 'faktor-kueisoner',
                    'field_type' => 'select_advanced',
                    'std'        => isset($_GET['idfaktor']) ? $_GET['idfaktor'] : '',
                ],
                [
                    'type' => 'text',
                    'name' => esc_html__('Kode', 'wpz'),
                    'id'   => $prefix . 'kode',
                ],
                [
                    'type' => 'textarea',
                    'name' => esc_html__('Indikator', 'wpz'),
                    'id'   => $prefix . 'indikator',
                ],
                [
                    'name' => 'Order',
                    'id'   => '_order',
                    'type' => 'number',
                    'min'  => 1,
                    'step' => 1,
                    'std'  => '1',
                    'desc' => 'Order / urutan indikator',
                ],
            ],
        ];

        return $meta_boxes;
    }

    public function get($idfaktor = null)
    {
        $result = [];
        $args = array(
            'post_type'         => 'indikator-kueisoner',
            'posts_per_page'    => -1,
            'orderby'           => 'title',
            'order'             => 'ASC',
            'meta_key'          => 'faktor',
            'meta_value'        => $idfaktor,
            'meta_compare'      => '=='
        );
        $the_query = new WP_Query($args);
        if ($the_query->have_posts()) {
            while ($the_query->have_posts()) {
                $the_query->the_post();
                global $post;
                $theposts   = [
                    'ID'                => $post->ID,
                    'title'             => $post->post_title,
                    'kode'              => get_post_meta($post->ID, 'kode', true),
                    'indikator'         => get_post_meta($post->ID, 'indikator', true),
                    'kueisoner'         => get_post_meta($post->ID, 'kueisoner', true),
                    'faktor'            => get_post_meta($post->ID, 'faktor', true),
                    'order'             => get_post_meta($post->ID, '_order', true),
                ];
                $result[]               = $theposts;
            }
        }
        // Restore original Post Data.
        wp_reset_postdata();

        return $result;
    }
}

$indikator = new Wpz_Kueisoner_Indikator();
$indikator->autoload();