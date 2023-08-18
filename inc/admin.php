<?php
class Wpz_Kueisoner_Admin
{
    public function autoload()
    {
        add_action('admin_notices', array($this, 'admin_notice'));
    }

    public function admin_notice()
    {

        if (!defined('RWMB_VER')) {
            echo '<div class="notice notice-warning">';
            echo '<p>The WPZ Kueisoner plugin requires the following plugins: <strong><a href="' . get_admin_url() . 'plugin-install.php?tab=plugin-information&plugin=meta-box&TB_iframe=true&width=640&height=500" class="thickbox">Meta Box</a></strong></p>';
            echo '</div>';
        }
    }
}

$admin = new Wpz_Kueisoner_Admin();
$admin->autoload();
