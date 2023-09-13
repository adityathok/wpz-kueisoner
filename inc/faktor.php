<?php
class Wpz_Kueisoner_Faktor
{
    public function autoload()
    {
        add_action('init', array($this, 'register_post_type'));
        add_filter('rwmb_meta_boxes', array($this, 'register_meta_boxes'));
    }

    public function register_post_type()
    {
        $labels = array(
            'name'               => 'Faktor',
            'singular_name'      => 'Faktor',
            'menu_name'          => 'Faktor',
            'add_new'            => 'Tambah Faktor',
            'add_new_item'       => 'Tambah Faktor Baru',
            'edit_item'          => 'Edit Faktor',
            'new_item'           => 'Buku Faktor',
            'view_item'          => 'Lihat Faktor',
            'search_items'       => 'Cari Faktor',
            'not_found'          => 'Faktor tidak ditemukan',
            'not_found_in_trash' => 'Tidak ada dalam tong sampah',
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => 'edit.php?post_type=kueisoner',
            'query_var'          => true,
            'rewrite'            => array('slug' => 'faktor-kueisoner'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array('title'),
        );

        register_post_type('faktor-kueisoner', $args);
    }

    public function register_meta_boxes($meta_boxes)
    {
        $prefix = '';

        $meta_boxes[] = [
            'title'      => esc_html__('Detail Faktor', 'wpz'),
            'id'         => 'detail_faktor',
            'post_types' => ['faktor-kueisoner'],
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
                    'name' => 'Order',
                    'id'   => '_order',
                    'type' => 'number',
                    'min'  => 1,
                    'step' => 1,
                    'std'  => '1',
                    'desc' => 'Order / urutan faktor',
                ],
            ],
        ];

        return $meta_boxes;
    }

    public function get($idkueisoner = null)
    {
        $result = [];
        $fargs = array(
            'post_type'         => 'faktor-kueisoner',
            'posts_per_page'    => -1,
            'orderby'           => 'date',
            'order'             => 'ASC',
            'meta_key'          => 'kueisoner',
            'meta_value'        => $idkueisoner,
            'meta_compare'      => '=='
        );
        $faktor_query = new WP_Query($fargs);
        if ($faktor_query->have_posts()) {
            while ($faktor_query->have_posts()) {
                $faktor_query->the_post();
                global $post;
                $thepost                = json_decode(json_encode($post), true);
                $newindi                = new Wpz_Kueisoner_Indikator();
                $theindikator           = $newindi->get($thepost['ID']);
                $theposts   = [
                    'ID'                => $thepost['ID'],
                    'title'             => $thepost['post_title'],
                    'indikator'         => $theindikator,
                    'total_indikator'   => count($theindikator),
                    'dimensi'           => get_post_meta($thepost['ID'], 'dimensi', true),
                    'order'             => get_post_meta($thepost['ID'], '_order', true),
                ];
                $result[] = $theposts;
            }
        }
        // Restore original Post Data.
        wp_reset_postdata();

        return $result;
    }
}

$faktor = new Wpz_Kueisoner_Faktor();
$faktor->autoload();