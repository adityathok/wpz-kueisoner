<?php
class Wpz_Kueisoner
{
    public function autoload()
    {
        add_action('init', array($this, 'register_post_type'));
        add_filter('rwmb_meta_boxes', array($this, 'register_meta_boxes'));
        add_action('add_meta_boxes', array($this, 'add_detail_metabox'));
        add_action('manage_kueisoner_posts_columns', array($this, 'add_column'));
        add_action('manage_kueisoner_posts_custom_column', array($this, 'add_render_column'), 10, 2);
    }

    public function register_post_type()
    {
        $labels = array(
            'name'               => 'Kueisoner',
            'singular_name'      => 'Kueisoner',
            'menu_name'          => 'Kueisoner',
            'add_new'            => 'Tambah Kueisoner',
            'add_new_item'       => 'Tambah Kueisoner Baru',
            'edit_item'          => 'Edit Kueisoner',
            'new_item'           => 'Buku Kueisoner',
            'view_item'          => 'Lihat Kueisoner',
            'search_items'       => 'Cari Kueisoner',
            'not_found'          => 'Kueisoner tidak ditemukan',
            'not_found_in_trash' => 'Tidak ada Kueisoner dalam tong sampah',
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'kueisoner'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array('title'),
        );

        register_post_type('kueisoner', $args);
    }

    public function register_meta_boxes($meta_boxes)
    {
        $prefix = '';

        $meta_boxes[] = [
            'title'      => esc_html__('Pengaturan', 'wpz'),
            'id'         => 'opsi_kueisoner',
            'post_types' => ['kueisoner'],
            'context'    => 'after_title',
            'fields'     => [
                [
                    'type'      => 'text',
                    'name'      => esc_html__('Opsi Pilihan', 'wpz'),
                    'id'        => $prefix . 'opsi',
                    'clone'     => true,
                    'desc'      => esc_html__('Isi pilihan opsi, Nilai opsi otomatis dari 0', 'wpz'),
                    'std'       => ['Tidak tahu', 'Sangat tidak setuju', 'Tidak setuju', 'Netral', 'Setuju', 'Sangat Setuju'],
                ],
            ],
        ];

        return $meta_boxes;
    }

    public function add_column($columns)
    {
        $columns['shortcode'] = __('Shortcode');
        return $columns;
    }

    public function add_render_column($column, $post_id)
    {
        if ('shortcode' === $column) {
            echo '<span>[wpkueisoner id="' . $post_id . '"]</span>';
        }
    }

    public function add_detail_metabox()
    {
        add_meta_box(
            'detail_kueisoner_metabox',
            'Detail kueisoner',
            array($this, 'render_detail_metabox'),
            'kueisoner',
            'normal',
            'default'
        );
    }

    public function render_detail_metabox($post)
    {
        if (isset($post->ID)) {
            echo '<p>';
                echo '<span class="button">[wpkueisoner id="' . $post->ID . '"]</span> ';
                echo '<a class="button" href="' . get_dashboard_url() . 'post-new.php?post_type=dimensi-kueisoner&idkueisoner=' . $post->ID . '" target="_blank">Tambah Dimensi</a>';
            echo '</p>';

            $newdim  = new Wpz_Kueisoner_Dimensi();
            $newdim  = $newdim->get($post->ID);
            if ($newdim) {
                echo '<table class="wp-list-table widefat fixed striped table-view-list pages">';
                    echo '<thead>';
                    echo '<tr><th>Dimensi</th><th>Faktor</th><th>Indikator</th></tr>';
                    echo '</thead>';
                    echo '<tbody>';
                    foreach ($newdim as $dim) {
                        if ($dim['faktor']) {
                            foreach ($dim['faktor'] as $kfak => $faktor) {
                                if ($faktor['indikator']) {
                                    foreach ($faktor['indikator'] as $kind => $indi) {
                                        echo '<tr>';
                                            echo '<td>';
                                                if($kfak == 0 && $kind == 0) {
                                                    echo '<strong>'.$dim['post_title'].'</strong>';
                                                    echo '<div>';
                                                        echo '<a href="' . get_dashboard_url() . 'post.php?post=' . $dim['ID'] . '&action=edit#wpbody-content" target="_blank">Edit</a> | ';
                                                        echo '<a href="' . get_dashboard_url() . 'post-new.php?post_type=faktor-kueisoner&iddimensi=' . $dim['ID'] . '" target="_blank">Tambah Faktor</a>';
                                                    echo '</div>';
                                                }
                                            echo '</td>';
                                            echo '<td>';
                                                if($kind == 0) {
                                                    echo '<strong>'.$faktor['post_title'].'</strong>';
                                                    echo '<div>';
                                                        echo '<a href="' . get_dashboard_url() . 'post.php?post=' . $faktor['ID'] . '&action=edit#wpbody-content" target="_blank">Edit</a> | ';
                                                        echo '<a href="' . get_dashboard_url() . 'post-new.php?post_type=indikator-kueisoner&iddimensi=' . $dim['ID'] . '&idfaktor=' . $faktor['ID'] . '" target="_blank">Tambah Indikator</a>';
                                                    echo '</div>';
                                                }
                                            echo '</td>';
                                            echo '<td>';
                                                echo '<strong>'.$indi['kode'].'</strong> | ';
                                                echo $indi['indikator'];
                                                echo '<div>';
                                                    echo '<a href="' . get_dashboard_url() . 'post.php?post=' . $faktor['ID'] . '&action=edit#wpbody-content" target="_blank">Edit</a>';
                                                echo '</div>';
                                            echo '</td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr>';
                                        echo '<td></td>';
                                        echo '<td>';
                                            echo '<strong>'.$faktor['post_title'].'</strong>';
                                            echo '<div>';
                                                echo '<a href="' . get_dashboard_url() . 'post.php?post=' . $faktor['ID'] . '&action=edit#wpbody-content" target="_blank">Edit</a> | ';
                                                echo '<a href="' . get_dashboard_url() . 'post-new.php?post_type=indikator-kueisoner&iddimensi=' . $dim['ID'] . '&idfaktor=' . $faktor['ID'] . '" target="_blank">Tambah Indikator</a>';
                                            echo '</div>';
                                        echo '</td>';
                                        echo '<td></td>';
                                    echo '</tr>';
                                }
                            }
                        } else {
                            echo '<tr>';
                                echo '<td>';                                    
                                    echo '<strong>'.$dim['post_title'].'</strong>';
                                    echo '<div>';
                                        echo '<a href="' . get_dashboard_url() . 'post.php?post=' . $dim['ID'] . '&action=edit#wpbody-content" target="_blank">Edit</a> | ';
                                        echo '<a href="' . get_dashboard_url() . 'post-new.php?post_type=faktor-kueisoner&iddimensi=' . $dim['ID'] . '" target="_blank">Tambah Faktor</a>';
                                    echo '</div>';
                                echo '</td>';
                                echo '<td></td>';
                                echo '<td></td>';
                            echo '</tr>';
                        }
                    }
                    echo '</tbody>';
                echo '</table>';
            }
        }
    }

    public function get($idkueisoner = null)
    {
        $thedimensi             = new Wpz_Kueisoner_Dimensi();
        $thedimensi             = $thedimensi->get($idkueisoner);
        $result                 = [
            'ID'                => $idkueisoner,
            'post_title'        => get_the_title($idkueisoner),
            'opsi'              => get_post_meta($idkueisoner, 'opsi', true),
            'dimensi'           => $thedimensi,
            'total_dimensi'     => count($thedimensi),
        ];

        return $result;
    }
}

$kueisoner = new Wpz_Kueisoner();
$kueisoner->autoload();
