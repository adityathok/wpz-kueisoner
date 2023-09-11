<?php
class Wpz_Kueisoner_Form
{
    public function form($id = null)
    {
        $theposts   = new Wpz_Kueisoner();
        $theposts   = $theposts->get($id);
        $opsi       = $theposts['opsi'];
        $countopsi  = count($theposts['opsi']);
        // echo '<pre>';
        // print_r($theposts);
        // echo '</pre>';
?>
        <?php if ($theposts) : ?>
            <form action="" method="post" class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr class="table-primary">
                            <th colspan="4"><?php echo $theposts['post_title']; ?></th>
                            <?php if ($opsi) : ?>
                                <?php foreach ($opsi as $kop => $op) : ?>
                                    <th class="fw-normal text-center" rowspan="2">
                                        <small>
                                            <?php echo $op; ?>
                                        </small>
                                    </th>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tr>
                        <tr class="table-primary">
                            <th>Dimensi</th>
                            <th>Faktor</th>
                            <th>Kode</th>
                            <th>Indikator</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($theposts['dimensi']) : ?>
                            <?php foreach ($theposts['dimensi'] as $dimensi) : ?>
                                <?php if ($dimensi['faktor']) : ?>
                                    <?php foreach ($dimensi['faktor'] as $kfak => $faktor) : ?>

                                        <?php if ($faktor['indikator']) : ?>
                                            <?php foreach ($faktor['indikator'] as $kind => $indikator) : ?>
                                                <tr>
                                                    <td><?php echo ($kfak == 0 && $kind == 0) ? $dimensi['post_title'] : ''; ?></td>
                                                    <td><?php echo ($kind == 0) ? $faktor['post_title'] : ''; ?></td>
                                                    <td>
                                                        <?php echo $indikator['post_title']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $indikator['indikator']; ?>
                                                    </td>
                                                    <?php if ($opsi) : ?>
                                                        <?php foreach ($opsi as $kop => $op) : ?>
                                                            <td class="text-center">
                                                                <input class="form-check-input" type="radio" name="value[<?php echo $dimensi['ID']; ?>][<?php echo $faktor['ID']; ?>][<?php echo $indikator['ID']; ?>]" value="<?php echo $kop; ?>" required>
                                                            </td>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <tr>
                                                <td><?php echo ($kfak == 0) ? $dimensi['post_title'] : ''; ?></td>
                                                <td>
                                                    <?php echo $faktor['post_title']; ?>
                                                </td>
                                                <td colspan="<?php echo $countopsi + 3; ?>"></td>
                                            </tr>
                                        <?php endif; ?>

                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <input type="hidden" name="title" value="<?php echo $theposts['post_title']; ?>" >
                <input type="hidden" name="id" value="<?php echo $theposts['ID']; ?>" >
                <?php wp_nonce_field('form_kueisoner', 'form_kueisoner'); ?>
                <div class="text-end mt-2 mb-4">
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>
            </form>
        <?php endif; ?>
    <?php
    }

    public function submit($data = null)
    {
        if (empty($data))
            return false;

        $result = [];
        foreach ($data['value'] as $kdim => $dim) {
            $result_dim             = [];
            $result_dim['id']       = $kdim;
            $result_dim['title']    = get_the_title($kdim);
            $sumvalue_fak           = 0;
            foreach ($dim as $kfak => $fak) {
                $sumind     = 0;
                foreach ($fak as $kind => $indi) {
                    $sumind += $indi;
                }
                $count_ind  = count($fak);
                $value_fak  = ($sumind / $count_ind);
                $result_dim['faktor'][] = [
                    'id'    => $kfak,
                    'sum'   => $sumind,
                    'total' => $count_ind,
                    'value' => $value_fak,
                    'title' => get_the_title($kfak),
                ];
                $sumvalue_fak += $value_fak;
            }
            $total_fak = count($dim);
            $total_val = ($sumvalue_fak / $total_fak);
            $result_dim['value_faktor']  = $sumvalue_fak;
            $result_dim['total_faktor']  = $total_fak;
            $result_dim['value']         = $total_val;

            $result['labels'][]         = get_the_title($kdim);
            $result['datas'][]          = $total_val;
            $result['dimensi'][]        = $result_dim;
        }
        $data['result'] = $result;
    ?>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <div style="max-width: 100%;">
            <div>
                <?php echo $data['title']; ?>
            </div>
            <canvas id="Chartkuiz" width="200"></canvas>
        </div>
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

<?php
    }
}
