<?php
class Wpz_Kueisoner_Hasil
{
    public function autoload()
    {
        add_action('init', array($this, 'register_post_type'));
        add_action('add_meta_boxes', array($this, 'add_hasil_metabox'));
        add_action('manage_hasil-kueisoner_posts_columns', array($this, 'add_column'));
        add_action('manage_hasil-kueisoner_posts_custom_column', array($this, 'add_render_column'), 10, 2);
    }

    public function register_post_type()
    {
        $labels = array(
            'name'               => 'Hasil Kueisoner',
            'singular_name'      => 'hasil_kueisoner',
            'menu_name'          => 'Hasil',
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => false,
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


    public function add_column($columns)
    {
        $columns['kueisoner']       = __('Kueisoner');
        $columns['faktor_value']    = __('Value');
        return $columns;
    }

    public function add_render_column($column, $post_id)
    {
        if ('kueisoner' === $column) {
            $data = get_post_meta($post_id,'kueisoner',true);
            echo $data?'<strong>'.get_the_title($data).'</strong>':'';
        }
        if ('faktor_value' === $column) {
            $data = get_post_meta($post_id,'faktor_value',true);
            echo $data?'<strong>'.round($data,2).'</strong>':'';
        }
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
        $datas              = get_post_meta($post->ID,'hasil',true);
        $kuisoner_content   = get_post_meta($post->ID,'kuisoner_content',true);
        if ($datas) {
            echo $this->radar($datas);
        }
        if ($kuisoner_content) {        
            ?>
            <br>
            <table class="wp-list-table widefat fixed striped table-view-list pages">
                <thead>
                    <th>Faktor</th>
                    <th>Indikator</th>
                    <th>Jawaban</th>
                </thead>
                <tbody>
                    <?php foreach( $kuisoner_content['faktor'] as $faktor): ?>
                        <?php foreach( $faktor['indikator'] as $kind => $indikator): ?>
                            <tr>
                                <td>
                                    <?php echo $kind==0?$faktor['title']:''; ?>
                                </td>
                                <td>
                                    <?php echo $indikator['indikator']; ?>
                                </td>
                                <td>
                                    <?php
                                    $val = $datas['value'][$faktor['ID']][$indikator['ID']];
                                    echo $kuisoner_content['opsi'][$val];
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php 
        }
    }
    
    public function radar($data=null)
    {    
        $dataresult = $data['result'];
        $datafaktor = $dataresult['faktor'];
        $datachart  = $dataresult['chart'];
        ?>
            <div class="card shadow m-2">
                <div class="card-header bg-dark text-light py-3">
                    <?php echo get_the_title($data['id']); ?>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="row">
                            <?php foreach( $datafaktor as $faktor): ?>
                                <div class="col-md-4 col-6 pb-3">
                                    <div class="card shadow-sm border-0">
                                        <div class="card-header">
                                            <?php echo get_the_title($faktor['id']); ?>
                                        </div>
                                        <div class="card-body fst-italic">
                                            <?php echo $faktor['sum']; ?> / <?php echo $faktor['total']; ?> = 
                                            <span class="fs-4">
                                                <?php echo $faktor['value']; ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <div class="col-md-4 col-6 pb-3">
                                <div class="card shadow-sm border-0">
                                    <div class="card-header">
                                        Total
                                    </div>
                                    <div class="card-body fst-italic">
                                        <?php echo $dataresult['faktor_sum']; ?> / <?php echo $dataresult['faktor_total']; ?> = 
                                        <span class="fs-4">
                                            <?php echo round($dataresult['faktor_value'],2); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
                                            labels: ['<?php echo implode("','", $datachart['labels']); ?>'],
                                            datasets: [{
                                                label: '<?php echo get_the_title($data['id']); ?>',
                                                data: [<?php echo implode(",", $datachart['datas']); ?>],
                                                borderWidth: 1
                                            }]
                                        },
                                        fill: false,
                                    });
                                });
                            });
                        </script>
                    </div>

                </div>
            </div>
        <?php
    }

}

$dimensi = new Wpz_Kueisoner_Hasil();
$dimensi->autoload();
