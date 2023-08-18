<?php
add_shortcode('wpkueisoner', function ($atts) {
    ob_start();

    $atribut = shortcode_atts(array(
        'id'   => '',
    ), $atts);

    $id = $atribut['id'];
    if ($id) {
        $form = new Wpz_Kueisoner_Form();

        //form
        echo $form->form($id);

        ///if submit
        if (isset($_POST['form_kueisoner']) && wp_verify_nonce($_POST['form_kueisoner'], 'form_kueisoner')) {
            $form->submit($_POST);
        }
    }

    return ob_get_clean();
});
