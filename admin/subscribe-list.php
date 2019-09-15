<?php
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/**
 * Class newsListTable
 */
class SubscriberListTable extends WP_List_Table
{


    /**
     * newsListTable constructor.
     */
    public function __construct()
    {
        parent::__construct(array(
            'singular' => 'ps-subscribe-list',
            'plural' => 'ps-subscribe-lists',
            'ajax' => false
        ));

    }


    /**
     *
     */

    public function prepare_items()
    {

        $columns = $this->get_columns();
        $hidden = array();
        $this->_column_headers = array($columns, $hidden);
        $per_page = 20;
        $current_page = $this->get_pagenum();
        $this->get_data(array('delete' => true));
        $total_items = $this->get_data(array('count' => true));

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page' => $per_page
        ]);

        $offset = ($current_page - 1) * $per_page;
        $args = [
            'offset' => $offset,
            'number' => $per_page,
        ];

        $this->items = $this->get_data($args);
    }

    /**
     * @param object $item
     * @param string $column_name
     * @return mixed|void
     */

    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'name':
            case 'email':
                return $item[$column_name];
            default:
                return print_r($item, true);
        }
    }


    /**
     * @param object $item
     * @return string|void
     */

    public function column_cb($item)
    {
        return sprintf('<input type="checkbox" name="maillist[]" value="%d" />', $item['id']);
    }

    /**
     * @return array
     */

    public function get_columns()
    {
        $columns = [
            'cb' => '<input type="checkbox" />',
            'name' => __('Name', 'jadroo'),
            'email' => __('Email', 'jadroo')
        ];

        return $columns;
    }

    /**
     * @param $item
     * @return string
     */

    public function column_name($item)
    {

        $actions = array();
        $wpnonce = wp_create_nonce('ps-newsletter-delete-nonce');
        $admin_url = admin_url('admin.php?page=ps-newsletter&id=' . $item['id'] . '&_psnonce=' . $wpnonce);
        $delete_url = $admin_url . '&action=delete';
        $actions['delete'] = sprintf('<a href="%s">%s</a>', $delete_url, esc_html__('Delete', 'ps-news'));
        $title = sprintf(
            '<a class="row-title" href="%1s" >%3s</a>',
            $delete_url,
            $item['name']
        );

        return sprintf('%1$s %2$s', $title, $this->row_actions($actions));
    }

    /**
     *
     */
    public function no_items()
    {
        echo esc_html__('No order data found.', 'jadroo');
    }

    /**
     * @param $args
     * @return array|object|string|null
     */

    public function get_data($args)
    {
        global $wpdb;
        if (isset($args['count'])) {
            return $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}newsletter_subscribers");
        }
        if (isset($args['delete'])) {
            $nonce = isset( $_REQUEST['_psnonce'] ) ?  $_REQUEST['_psnonce'] : '';

            if ( ! wp_verify_nonce(  $nonce, 'ps-newsletter-delete-nonce' ) ) {
                return false;
            }
            if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'delete' ){
                return $wpdb->get_var("DELETE FROM {$wpdb->prefix}newsletter_subscribers WHERE id =".absint( $_REQUEST['id'] )." ");
            }
        }
        $sql = "SELECT * FROM {$wpdb->prefix}newsletter_subscribers ORDER BY id DESC  LIMIT {$args['offset']} , {$args['number']}";
        $result = $wpdb->get_results($sql, 'ARRAY_A');
        return $result;
    }

}