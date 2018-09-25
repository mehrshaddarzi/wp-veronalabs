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
            'add_new' => _x( 'New Book', $t_d ),
            'add_new_item' => __( 'Add New Book', $t_d ),
            'edit_item' => __( 'Edit Book', $t_d ),
            'new_item' => __( 'New Book', $t_d ),
            'all_items' => __( 'All Books', $t_d ),
            'view_item' => __( 'Show Book', $t_d ),
            'search_items' => __( 'Search in Books', $t_d ),
            'not_found' => __( 'Not found Any Book', $t_d ),
            'not_found_in_trash' => __( 'Not found any Book in Trash', $t_d ),
            'parent_item_colon'  => __( 'Parent Books: ', 'your-plugin-textdomain' ),
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
            'rewrite'  => array( 'slug' => 'book' ),

            /* Rest Api */
            'show_in_rest'       => true,
            'rest_base'          => 'books_api',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        );
        register_post_type( 'book', $args );

        
    }

}