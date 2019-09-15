<?php
/**
 *
 * public Global Form subscriptions
 *
 */

add_action('wp_ajax_nopriv_psSubscribeForm', 'psSubscribeForm');
add_action('wp_ajax_psSubscribeForm', 'psSubscribeForm');
if (!function_exists('psSubscribeForm')) {
    function psSubscribeForm()
    {
        global $wpdb;
        check_ajax_referer('ps_security_check', 'ps_security');
        if (defined('DOING_AJAX') && DOING_AJAX) {

            $name = trim(strtolower($_POST['name']));
            $email = trim(strtolower($_POST['email']));
            $data = array(
                'name' => $name,
                'email' => $email
            );
            $exists_email = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}newsletter_subscribers WHERE email = '$email'"  );
            if($exists_email >= 1 ){
                return wp_send_json (array('status' => 'exists'));
            }

            $wpdb->insert( $wpdb->prefix . 'newsletter_subscribers', $data );
            $last_id = $wpdb->insert_id;

            if (!empty($last_id)) {
                return wp_send_json (array('status' => 'success'));
            } else {
                return wp_send_json (array('status' => 'error'));
            }
            wp_die();
        }
    }
}