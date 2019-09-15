<?php
/**
 * Plugin Name: Ps Wishlist
 * Plugin URI: http://demo.plugins.ps
 * Description: Wishlist plugin.
 * Author: Sagar Paul
 * Author URI: http://demo.plugins.ps
 * Version:1.0.0
 */

if (!defined('ABSPATH')) exit;
/**
 * Class psNewsLetter
 */
class psWishlist
{

    /**
     * @var
     */
    public static $_instance;

    /**
     * @var string
     */
    public $plugin_name = 'Ps Wishlist';

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
        require_once(plugin_dir_path($this->file) . 'public/shortcode.php');
        require_once(plugin_dir_path($this->file) . 'inc/function.php');
        add_action( 'admin_notices', array( $this, 'psNotice' ) );
        add_action('woocommerce_after_add_to_cart_button', array($this,'psWishlistButton'));
        //add_action( 'admin_init', array( $this, 'psInit' ) );
        add_action('wp_enqueue_scripts', array($this, 'psEnqueueScript'));
    }

    /**
     * add menu
     */

    public function install()
    {
        $this->insertPage();
        
    }

    public function insertPage(){
        $ps_install = get_option( '_ps_wishlist');
        if($ps_install != 1){
            $page_id = wp_insert_post( array(
                'post_title'     => esc_html__('ps wishlist','ps-wishlist'),
                'post_type'      => 'page',
                'post_status'    => 'publish',
                'comment_status' => 'closed',
                'post_content'   => '[ps_wishlist]'
            ) );
            update_option( '_ps_wishlist', 1);
        }
        
    }

    public function psWishlistButton()
    {
        $product_id = get_the_ID();
        echo '<a href="'.get_permalink($product_id) .'" data-product-id="'. esc_attr($product_id) .'" class="ps_add_to_wishlist button" >'.esc_html__('Add to Wishlist','ps-wishlist').'</a>';
    }

    /**
     *
     */
    public function psEnqueueScript()
    {
        wp_enqueue_script('ps-wishlist-ajax', plugin_dir_url($this->file) . 'assets/js/main.js', array('jquery'), '', TRUE);

        /*Ajax Call*/
        $params = array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'ajax_nonce' => wp_create_nonce('ps_security_check_wishlist'),
        );
        wp_localize_script('ps-wishlist-ajax', 'ps_check_wishlist_obj', $params);
    }


    public function psNotice()
    {
        if(!class_exists( 'WooCommerce' )){
            $message    = esc_html__( 'Ps Wishlist requires WooCommerce to be installed and active.','ps_wisglist' );

            printf( '<div class="error"><p><strong>%1$s</strong></p></div>', $message );
        }
    }

    /**
     * @return psWishlist
     */
    public static function psInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new psWishlist();
        }
        return self::$_instance;
    }

}
$psNewsLetter = psWishlist::psInstance();