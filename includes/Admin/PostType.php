<?php
namespace Admin;

class PostType
{

    protected static $instance = NULL;


    /**
     * Singleton class instance.
     *
     * @return Class
     */
    public static function get() {
        if ( NULL === self::$instance )
            self::$instance = new self;
        return self::$instance;
    }

    /*
     * Create Book Admin
     */
    public function create_book_post_type()
    {
        $t_d = \WP_VERONALABS_TEST::text_doamin;
        $labels = array(
            'name' => __( 'Book', $t_d),
            'singular_name' => __( 'Book',  $t_d ),
            'add_new' => __( 'New Book', $t_d ),
            'add_new_item' => __( 'Add New Book', $t_d ),
            'edit_item' => __( 'Edit Book', $t_d ),
            'new_item' => __( 'New Book', $t_d ),
            'all_items' => __( 'All Books', $t_d ),
            'view_item' => __( 'Show Book', $t_d ),
            'search_items' => __( 'Search in Books', $t_d ),
            'not_found' => __( 'Not found Any Book', $t_d ),
            'not_found_in_trash' => __( 'Not found any Book in Trash', $t_d ),
            'parent_item_colon'  => __( 'Parent Books', $t_d ),
            'menu_name' => __( 'Books List', $t_d ),
        );
        $args = array(
            'labels' => $labels,
            'description' => __( 'Books List', $t_d ),
            'public' => true,
            'menu_position' => 5,
            'has_archive' => true,
            'show_in_admin_bar'   => false,
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'menu_icon'           => 'dashicons-book',
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'page',

            /* support */
            'supports' => array( 'title', 'editor', 'thumbnail', 'author'),

            /* Rewrite */
            'rewrite'  => array( 'slug' => \WP_VERONALABS_TEST::post_type ),

            /* Rest Api */
            'show_in_rest'       => true,
            'rest_base'          => 'books_api',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        );
        register_post_type( \WP_VERONALABS_TEST::post_type, $args );

        
    }
    
    
    /*
     * Column Table Post List
     */
    public function column_post_table($column, $post_id)
    {
        global $wpdb;
        /*
         * Isbb
         */
        if ($column == 'isbn'){
            $isbn = $wpdb->get_var("SELECT `isbn` FROM `{$wpdb->prefix}books_info` WHERE `post_id` = {$post_id}");
            if(trim($isbn) !="")  { echo $isbn; } else { echo '-'; }
        }

    }


    /*
     * Show List Of Term From Post
     */
    public function show_list_term_of_postid($post_id, $term)
    {
        $text = '';
        $list = wp_get_post_terms( $post_id, $term  );
        if(count($list) ==0) {
            $text = "-";
        } else {
            $i = 1;
            foreach($list as $term) {
                $text .= '<a href="'.get_term_link( $term ).'" target="_blank">'.$term->name.'</a>';
                if($i !=count($list)) { $text .= ' , '; }
                $i++;
            }
        }

        return $text;
    }
    
    
    /*
     * Column Book Table Add
     */
    public function column_book($columns)
    {
        /*
         * Add isbn column
         */
        $columns['isbn'] = __("ISBN", \WP_VERONALABS_TEST::text_doamin);

        /*
         * change Title and Author
         */
        foreach($columns as $key => $value)
        {
            if($key =="title") {
                $columns[$key] = __("Book Name", \WP_VERONALABS_TEST::text_doamin);
            }
            if($key =="author") {
                $columns[$key] = __("author post", \WP_VERONALABS_TEST::text_doamin);
            }
        }

        return $columns;
    }
    
    
    /*
     * Change Title Enter Here
     */
    public function custom_enter_title( $input )
    {
        if ( \WP_VERONALABS_TEST::post_type === get_post_type() ) {
            return __( 'Please enter the name of the book', \WP_VERONALABS_TEST::text_doamin );
        }

        return $input;
    }
    
    
    /*
    Remove All Extra Metabox
    */
    /*
     * Remove Extra MetaBox
     */
    public static function remove_extra_metabox()
    {
        global $wp_meta_boxes, $post_type;

        /** Check the post type (remove if you don't want/need) */
        if(!in_array($post_type, array(
            self::post_type
        ))) :
            return false;
        endif;

        /** Create an array of meta boxes exceptions, ones that should not be removed (remove if you don't want/need) https://codex.wordpress.org/Function_Reference/remove_meta_box */
        $exceptions = array(
            'submitdiv', //Date and Publish meta box
            //BR_Taxonomy::taxonomy.'div', //Custom Taxonomy For Review Post Type
        );

        if(!empty($wp_meta_boxes)) : foreach($wp_meta_boxes as $page => $page_boxes) :

            if(!empty($page_boxes)) : foreach($page_boxes as $context => $box_context) :

                if(!empty($box_context)) : foreach($box_context as $box_type) :

                    if(!empty($box_type)) : foreach($box_type as $id => $box) :

                        if(!in_array($id, $exceptions)) :
                            /** Remove the meta box */
                            remove_meta_box($id, $page, $context);
                        endif;

                    endforeach;
                    endif;

                endforeach;
                endif;

            endforeach;
            endif;

        endforeach;
        endif;
    }
    

}
