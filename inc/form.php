<?php
class Wpz_Kueisoner_Form
{
    public function form($id = null)
    {
        $theposts   = new Wpz_Kueisoner();
        $theposts   = $theposts->get($id);
        $opsi       = $theposts['opsi'];
        $countopsi  = $theposts['opsi'] ? count($theposts['opsi']) : 0;
        
        ?>
        <?php if ($theposts) : ?>
            <form action="" method="post" class="table-responsive">

                <table class="table table-striped table-bordered">
                    <thead>
                        <tr class="table-primary align-top">
                            <th colspan="2"><?php echo $theposts['title']; ?></th>
                            <?php if ($opsi) : ?>
                                <?php foreach ($opsi as $kop => $op) : ?>
                                    <th class="fw-normal text-center lh-sm" rowspan="2">
                                        <small>
                                            <?php echo $op; ?>
                                        </small>
                                    </th>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($theposts['faktor']) : ?>
                            <?php foreach ($theposts['faktor'] as $kfak => $faktor) : ?>
                                <tr>
                                    <td colspan="<?php echo $countopsi + 3; ?>">
                                        <?php echo $faktor['title']; ?>
                                    </td>
                                </tr>
                                <?php if ($faktor['indikator']) : ?>
                                    <?php foreach ($faktor['indikator'] as $kind => $indikator) : ?>
                                        <tr>
                                            <td>
                                                <?php echo $indikator['kode']; ?>
                                            </td>
                                            <td>
                                                <?php echo $indikator['indikator']; ?>
                                            </td>
                                                <?php if ($opsi) : ?>
                                                    <?php foreach ($opsi as $kop => $op) : ?>
                                                        <?php $idind = $faktor['ID'] . $indikator['ID'] . $kop; ?>
                                                        <td class="text-center position-relative">
                                                            <input class="form-check-input" id="<?php echo $idind; ?>" type="radio" name="value[<?php echo $faktor['ID']; ?>][<?php echo $indikator['ID']; ?>]" value="<?php echo $kop; ?>" required>
                                                            <label class="form-check-label position-absolute top-0 bottom-0 end-0 start-0" title="<?php echo $op; ?>" for="<?php echo $idind; ?>"></label>
                                                        </td>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <input type="hidden" name="id" value="<?php echo $theposts['ID']; ?>">
                <input type="hidden" name="title" value="<?php echo $theposts['title']; ?>">
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

        $result     = [];
        $sumvalue   = 0;
        foreach ($data['value'] as $kfak => $faktor) {
            $count_ind  = count($faktor);
            $sum_ind    = array_sum($faktor);
            $value_fak  = ($sum_ind / $count_ind);
            $result['faktor'][] = [
                'id'        => $kfak,
                'sum'       => $sum_ind,
                'total'     => $count_ind,
                'value'     => $value_fak,
                'title'     => get_the_title($kfak),
            ];
            $sumvalue += $value_fak;
            $result['chart']['datas'][]     = $value_fak;
            $result['chart']['labels'][]    = get_the_title($kfak);
        }
        $result['faktor_total'] = count($data['value']);
        $result['faktor_sum']   = $sumvalue;
        $result['faktor_value'] = ($sumvalue / count($data['value']));

        $data['result'] = $result;

        //insert to HASIL
        $thekuei    = new Wpz_Kueisoner();
        $thekuei    = $thekuei->get($data['id']);
        $my_post = array(
            'post_title'    => wp_strip_all_tags($data['title']),
            'post_content'  => '',
            'post_status'   => 'draft',
            'post_author'   => get_current_user_id(),
            'post_type'     => 'hasil-kueisoner',
            'meta_input'    => array(
                'hasil'             => $data,
                'kuisoner'          => $data['id'],
                'kuisoner_content'  => $thekuei,
                'faktor_total'      => $data['result']['faktor_total'],
                'faktor_sum'        => $data['result']['faktor_sum'],
                'faktor_value'      => $data['result']['faktor_value'],
            ),
        );
        wp_insert_post($my_post);       
        

        $render = new Wpz_Kueisoner_Hasil();
        echo $render->card($data);
    }
}
