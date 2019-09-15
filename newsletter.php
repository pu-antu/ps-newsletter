<?php
/**
 * Plugin Name: Ps Newsletter
 * Plugin URI: http://demo.plugins.ps
 * Description: Newsletter plugin.
 * Author: Sagar Paul
 * Author URI: http://demo.plugins.ps
 * Version:1.0.0
 */

if (!defined('ABSPATH')) exit;

/**
 * Class psNewsLetter
 */
class psNewsLetter
{

    /**
     * @var
     */
    public static $_instance;

    /**
     * @var string
     */
    public $plugin_name = 'Ps Newsletter';

    /**
     * @var string
     */
    public $plugin_version = '1.0.0';

    /**
     * @var string
     */
    public $file = __FILE__;

    /**
     * psNewsLetter constructor.
     */
    public function __construct()
    {
        $this->psPluginInit();
    }

    /**
     *
     */
    public function psPluginInit()
    {
        register_activation_hook($this->file, array($this, 'install'));
        require_once(plugin_dir_path($this->file) . 'admin/subscribe-list.php');
        require_once(plugin_dir_path($this->file) . 'public/shortcode.php');
        require_once(plugin_dir_path($this->file) . 'inc/function.php');
        add_action('admin_menu', array($this, 'adminMenu'));
        add_action('wp_enqueue_scripts', array($this, 'psEnqueueScript'));

    }

    /**
     * add menu
     */

    public function adminMenu()
    {
        add_menu_page('Newsletter ', 'Newsletter', 'manage_options','ps-newsletter', array($this, 'psPluginPage'),'dashicons-admin-users');

    }

    /**
     * install db table and insert shortCode page
     */

    public function install()
    {
        $this->createTables();
        $this->insertPage(); 
    }

    /**
     * install db table
     */

    public function createTables()
    {
        global $wpdb;
        $sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}newsletter_subscribers` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(100) NOT NULL,
                `email` varchar(100) NOT NULL,
                PRIMARY KEY (`id`)
            ) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    /**
     * insert shortCode page
     */
    public function insertPage(){
        $ps_install = get_option( '_ps_newsletter');
        if($ps_install != 1){
            $page_id = wp_insert_post( array(
                'post_title'     => esc_html__('ps newsletter','ps-newsletter'),
                'post_type'      => 'page',
                'post_status'    => 'publish',
                'comment_status' => 'closed',
                'post_content'   => '[ps_newsletter]'
            ) );
            update_option( '_ps_newsletter', 1);
        }
    }

    /**
     *
     */
    public function psPluginPage()
    {
        ?>
        <div class="wrap">
            <form class="ps_newsletter_form" method="get">
                <input type="hidden" name="page" value="ps-subscribe-list">
                <?php
                $subscribe_list_obj = new SubscriberListTable();
                $subscribe_list_obj->prepare_items();
                $subscribe_list_obj->display();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * enqueue js file
     */
    public function psEnqueueScript()
    {
        wp_enqueue_script('ps-newsletter-ajax', plugin_dir_url($this->file) . 'assets/js/main.js', array('jquery'), '', TRUE);

        /*Ajax Call*/
        $params = array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'ajax_nonce' => wp_create_nonce('ps_security_check'),
        );
        wp_localize_script('ps-newsletter-ajax', 'ps_check_obj', $params);
    }


    /**
     * @return psNewsLetter
     */
    public static function psInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new psNewsLetter();
        }
        return self::$_instance;
    }

}

$psNewsLetter = psNewsLetter::psInstance();