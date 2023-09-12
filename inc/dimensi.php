<?php
class Wpz_Kueisoner_Dimensi
{
    public function autoload()
    {
        add_action('init', array($this, 'register_post_type'));
        add_filter('rwmb_meta_boxes', array($this, 'register_meta_boxes'));
        add_action('add_meta_boxes', array($this, 'add_faktor_metabox'));
    }

    public function register_post_type()
    {
        $labels = array(
            'name'               => 'Dimensi',
            'singular_name'      => 'Dimensi',
            'menu_name'          => 'Dimensi',
            'add_new'            => 'Tambah Dimensi',
            'add_new_item'       => 'Tambah Dimensi Baru',
            'edit_item'          => 'Edit Dimensi',
            'new_item'           => 'Buku Dimensi',
            'view_item'          => 'Lihat Dimensi',
            'search_items'       => 'Cari Dimensi',
            'not_found'          => 'Dimensi tidak ditemukan',
            'not_found_in_trash' => 'Tidak ada Dimensi dalam tong sampah',
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => 'edit.php?post_type=kueisoner',
            'query_var'          => true,
            'rewrite'            => array('slug' => 'dimensi-kueisoner'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array('title'),
        );

        register_post_type('dimensi-kueisoner', $args);
    }

    public function register_meta_boxes($meta_boxes)
    {
        $prefix = '';

        $meta_boxes[] = [
            'title'      => esc_html__('Detail Dimensi', 'wpz'),
            'id'         => 'detail_dimensi',
            'post_types' => ['dimensi-kueisoner'],
            'context'    => 'after_title',
            'fields'     => [
                [
                    'type'       => 'post',
                    'name'       => esc_html__('Kuesioner', 'wpz'),
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
                    'desc' => 'Order / urutan dimensi',
                ],
            ],
        ];

        return $meta_boxes;
    }

    public function get($idkueisoner = null)
    {
        $result = [];
        $args = array(
            'post_type'         => 'dimensi-kueisoner',
            'posts_per_page'    => -1,
            'orderby'           => 'meta_value_num',
            'meta_key'          => '_order',
        );
        if ($idkueisoner) {
            $args['meta_key']       = 'kueisoner';
            $args['meta_value']     = $idkueisoner;
            $args['meta_compare']   = '==';
        }
        $the_query = new WP_Query($args);
        if ($the_query->have_posts()) {
            while ($the_query->have_posts()) {
                $the_query->the_post();
                global $post;
                $thepost                = json_decode(json_encode($post), true);
                $faktor                 = new Wpz_Kueisoner_Faktor();
                $thefaktor              = $faktor->get($post->ID);
                $theposts   = [
                    'ID'                => $thepost['ID'],
                    'post_title'        => $thepost['post_title'],
                    'faktor'            => $thefaktor,
                    'total_faktor'      => count($thefaktor),
                    'order'             => get_post_meta($post->ID, '_order', true),
                ];
                $result[]       = $theposts;
            }
        }
        // Restore original Post Data.
        wp_reset_postdata();

        return $result;
    }

    public function add_faktor_metabox()
    {
        add_meta_box(
            'custom_dimensi_metabox',
            'Faktor Dimensi',
            array($this, 'render_faktor_metabox'),
            'dimensi-kueisoner',
            'normal',
            'default'
        );
    }

    public function render_faktor_metabox($post)
    {
        echo '<p style="text-align:right;">';
        echo '<a class="button" href="' . get_dashboard_url() . 'post-new.php?post_type=faktor-kueisoner&iddimensi=' . $post->ID . '" target="_blank">Tambah Faktor</a>';
        echo '</p>';

        $newfaktor  = new Wpz_Kueisoner_Faktor();
        $getfaktor  = $newfaktor->get($post->ID);
        if ($getfaktor) {
            echo '<table class="wp-list-table widefat fixed striped table-view-list pages">';
            echo '<thead>';
            echo '<tr><th>Faktor</th><th>Indikator</th></tr>';
            echo '</thead>';
            echo '<tbody>';
            foreach ($getfaktor as $faktor) {
                if ($faktor['indikator']) {
                    foreach ($faktor['indikator'] as $kind => $indi) {
                        echo '<tr>';
                            echo '<td>';                            
                            if($kind == 0) {
                                echo '<strong>' . $faktor['post_title'] . '</strong>';
                                echo '<div>';
                                    echo '<a href="' . get_dashboard_url() . 'post.php?post=' . $faktor['ID'] . '&action=edit#wpbody-content" target="_blank">Edit</a> | ';
                                    echo '<a href="' . get_dashboard_url() . 'post-new.php?post_type=indikator-kueisoner&iddimensi=' . $faktor['dimensi'] . '&idfaktor=' . $faktor['ID'] . '" target="_blank">Tambah Indikator</a>';
                                echo '</div>';
                            }
                            echo '</td>';
                            echo '<td>';
                                echo '<strong>' . $indi['kode'] . '</strong>';
                                echo '<div>' . $indi['indikator'] . '</div>';
                                echo '<div>';
                                    echo '<a href="' . get_dashboard_url() . 'post.php?post=' . $faktor['ID'] . '&action=edit#wpbody-content" target="_blank">Edit</a>';
                                echo '</div>';
                            echo '</td>';
                        echo '</tr>';
                    }
                } else {                    
                    echo '<tr>';
                        echo '<td>';                            
                        if($kind == 0) {
                            echo '<strong>' . $faktor['post_title'] . '</strong>';
                            echo '<div>';
                                echo '<a href="' . get_dashboard_url() . 'post.php?post=' . $faktor['ID'] . '&action=edit#wpbody-content" target="_blank">Edit</a> | ';
                                echo '<a href="' . get_dashboard_url() . 'post-new.php?post_type=indikator-kueisoner&iddimensi=' . $faktor['dimensi'] . '&idfaktor=' . $faktor['ID'] . '" target="_blank">Tambah Indikator</a>';
                            echo '</div>';
                        }
                        echo '</td>';
                        echo '<td></td>';
                    echo '</tr>';
                }
            }
            echo '</tbody>';
            echo '</table>';
        }
    }
}

$dimensi = new Wpz_Kueisoner_Dimensi();
$dimensi->autoload();
