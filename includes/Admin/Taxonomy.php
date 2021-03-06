<?php
namespace Admin;

class Taxonomy
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


    public function Create_taxonomy_book()
    {

        $t_d = \WP_VERONALABS_TEST::text_doamin;

        /*
         * Authors Taxonomy
         */
        $labels = array(
            'name' =>  __( 'Author Book', $t_d),
            'singular_name' => __( 'Author List', $t_d),
            'search_items' => __( 'Search in Authors', $t_d),
            'all_items' => __( 'All Authors', $t_d),
            'parent_item' => null,
            'parent_item_colon' => null,
            'separate_items_with_commas' => __( 'Separate Author with commas', $t_d),
            'choose_from_most_used' => __('choose more author', $t_d),
            'add_or_remove_items' => __('Add or remove author', $t_d),
            'edit_item' => __( 'Edit Author', $t_d),
            'update_item' => __( 'Update Author', $t_d),
            'add_new_item' => __( 'Add New Author', $t_d),
            'new_item_name' => __( 'New Author', $t_d),
            'menu_name' => __( 'Authors List', $t_d),
        );
        $args = array(
            'labels' => $labels,
            'hierarchical' => false, //Authors is not Parent Item
            'rewrite'   => array( 'slug' => 'authors' ),
            'query_var'    => true,
            'show_ui'               => true,
            'show_admin_column'     => true,
           // 'show_in_quick_edit'         => false, Not Show in Qick Edit
            //'meta_box_cb'                => false, Not Show in Post Add MetaBox
            'update_count_callback' => '_update_post_term_count',
        );
        register_taxonomy( 'authors', \WP_VERONALABS_TEST::post_type, $args );


        /*
         * Publisher Taxonomy
         */
        $labels = array(
            'name' =>  __( 'Publisher', $t_d),
            'singular_name' => __( 'Publisher List', $t_d),
            'search_items' => __( 'Search in Publisher', $t_d),
            'all_items' => __( 'All Publisher', $t_d),
            'parent_item' => __( 'The Publisher Parent', $t_d),
            'parent_item_colon' => __( 'Current Publisher', $t_d),
            'edit_item' => __( 'Edit Publisher', $t_d),
            'update_item' => __( 'Update Publisher', $t_d),
            'add_new_item' => __( 'Add New Publisher', $t_d),
            'new_item_name' => __( 'New Publisher', $t_d),
            'menu_name' => __( 'Publisher List', $t_d),
        );
        $args = array(
            'labels' => $labels,
            'hierarchical' => true,
            'rewrite'   => array( 'slug' => 'publishers' ),
            'query_var'    => true,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'update_count_callback' => '_update_post_term_count',
        );
        register_taxonomy( 'Publisher', \WP_VERONALABS_TEST::post_type, $args );

    }


}
