<?php 
/**
 * @PostBlock Shortcodes
 */
class PostBlock {

    /**
     * Initializes the PostBlock object
     */
    public function __construct()
    {
        
    }

    public function post_block(){
        $action = isset( $_GET['action'] ) ? $_GET['action'] : 'list';

        switch ($action) {
            case 'new':
                $template = __DIR__ . '/templates/post-block-new.php';
                break;

            case 'edit':
                $template = __DIR__ . '/templates/post-block-edit.php';
                break;

            case 'view':
                $template = __DIR__ . '/templates/post-block-view.php';
                break;
            
            default:
                $template = __DIR__ . '/templates/post-block-list.php';
                break;
        }

        if( file_exists( $template ) ){
            include $template;
        }

    }

    public static function block_update_data($block_id){
        global $wpdb;
        $table_name = $wpdb->prefix . 'load_more_post_shortcode_list';

        if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") == $table_name) {
            $query = "SELECT * FROM $table_name WHERE id='$block_id'";

            $results = $wpdb->get_results($query);
            $resultRows = $wpdb->num_rows;
            if ($results && $resultRows > 0) {
                return (array) $results[0];
            }
        }
        return false;
    }


}