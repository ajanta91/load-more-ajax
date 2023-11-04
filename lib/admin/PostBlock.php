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

    

}