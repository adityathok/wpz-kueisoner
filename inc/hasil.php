<?php
class Wpz_Kueisoner_Hasil
{
    public function autoload()
    {
        add_action('init', array($this, 'register_post_type'));
        add_action('add_meta_boxes', array($this, 'add_hasil_metabox'));
    }

    public function register_post_type()
    {
        $labels = array(
            'name'               => 'Hasil Kuisoner',
            'singular_name'      => 'hasil_kuisoner',
            'menu_name'          => 'Hasil',
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => 'edit.php?post_type=kueisoner',
            'query_var'          => true,
            'rewrite'            => array('slug' => 'hasil-kueisoner'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array('title'),
        );

        register_post_type('hasil-kueisoner', $args);
    }

    public function add_hasil_metabox()
    {
        add_meta_box(
            'custom_hasil_metabox',
            'Hasil Kuisoner',
            array($this, 'render_hasil_metabox'),
            'hasil-kueisoner',
            'normal',
            'default'
        );
    }

    public function render_hasil_metabox($post)
    {
        $datas  = get_post_meta($post->ID,'hasil',true);
        if ($datas) {
            print_r($datas);
            echo $this->radar($datas);
        }
    }
    
    public function radar($data=null)
    {
        ?>
        <div style="max-width: 100%;">
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <canvas id="Chartkuiz" width="200"></canvas>
            <script>
                jQuery(function($) {
                    $(document).ready(function() {
                        const ctx = document.getElementById('Chartkuiz');
                        new Chart(ctx, {
                            type: 'radar',
                            data: {
                                labels: ['<?php echo implode("','",$data['result']['labels']);?>'],
                                datasets: [
                                    {
                                        label: '<?php echo $data['title']; ?>',
                                        data: [<?php echo implode(",",$data['result']['datas']);?>],
                                        borderWidth: 1
                                    }
                                ]
                            },
                            fill: false,
                        });
                    });
                });
            </script>
        </div>
        <?php
    }

}

$dimensi = new Wpz_Kueisoner_Hasil();
$dimensi->autoload();
