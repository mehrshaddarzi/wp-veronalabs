<?php
/**
 * Plugin Name: Wp VeronaLabs Test
 * Description: A Sample WordPress Plugin For Test My Skill in wordpress developer 
 * Plugin URI:  https://veronalabs.com
 * Version:     1.0
 * Author:      Mehrshad Darzi
 * Author URI:  https://realwp.net
 * License:     MIT
 * Text Domain: wp-veronalabs
 * Domain Path: /languages
 */

/*
 * Plugin Loaded Action
 */
add_action('plugins_loaded', array(WP_VERONALABS_TEST::get_instance(), 'plugin_setup'));

/*
 * Register Activation Hook
 */
register_activation_hook(__FILE__, ['WP_VERONALABS_TEST' , 'activate'] );


class WP_VERONALABS_TEST
{
    /**
     * Plugin instance.
     *
     * @see get_instance()
     * @type object
     */
    protected static $instance = NULL;


    /**
     * URL to this plugin's directory.
     *
     * @type string
     */
    public $plugin_url = '';

    /**
     * Path to this plugin's directory.
     *
     * @type string
     */
    public $plugin_path = '';


    /**
     * TextDomain Name Plugin
     *
     * @type string
     */
    const text_doamin = 'wp-veronalabs';

    /*
     * Post Type Book Slug
     * @type string
     */
    const post_type = 'book';

    /**
     * Access this plugin’s working instance
     *
     * @wp-hook plugins_loaded
     * @since   2012.09.13
     * @return  object of this class
     */
    public static function get_instance()
    {
        if ( NULL === self::$instance )
            self::$instance = new self;
        return self::$instance;
    }

    /**
     * Used for regular plugin work.
     *
     * @wp-hook plugins_loaded
     * @return  void
     */
    public function plugin_setup()
    {

        $this->plugin_url = plugins_url('/', __FILE__);
        $this->plugin_path = plugin_dir_path(__FILE__);

        /*
         * Set Text Domain
         */
         $this->load_language(self::text_doamin);

        /*
         * PSR Autoload
         */
        spl_autoload_register(array($this, 'autoload'));

        /*
         * Admin Action Load
         */
        $this->admin_action();

        /*
        * Public Action Load
        */
        $this->public_action();

    }

    /*
     * Activation Hook
     */
    public static function activate() {
        global $wpdb;

        /*
         * Create Base Table in mysql
         */
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix.'books_info';
        $sql = "CREATE TABLE $table_name (`id` bigint(45) NOT NULL AUTO_INCREMENT,`post_id` bigint(45) NOT NULL,`isbn` varchar(100) NOT NULL ,PRIMARY KEY  (id)) {$charset_collate};";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

        /*
         * Register Flush Rewrite Accept
         */
        if ( ! get_option( 'wp_veronalabs_flush' ) ) {
            add_option( 'wp_veronalabs_flush', true );
        }

    }


    /*
     * List Admin Action Wordpress
     */
    public function admin_action()
    {
        global $pagenow;

        /*
         * Create Book Post Type
         */
        add_action( 'init', [\Admin\PostType::get(), 'create_book_post_type'] );
        add_filter( 'enter_title_here', [\Admin\PostType::get(), 'custom_enter_title'] );


        /*
         * Add Taxonomies For Book
         */
        add_action( 'init', [\Admin\Taxonomy::get(), 'Create_taxonomy_book'] );


        /*
         * New MetaBox For Book PostType
         */
        add_action( 'add_meta_boxes', [\Admin\MetaBox::get(), 'Create_Meta_box'] );
        add_action( 'save_post_'.\WP_VERONALABS_TEST::post_type, [\Admin\MetaBox::get(), 'Save_MetaBox'] , 10, 2 );


        /*
         * Remove Isbn From Table in Complete Deleting Post
         */
        add_action( 'before_delete_post', [\Admin\MetaBox::get(), 'Remove_ISBN_Row'] );

        /*
         * Add Column Book PosType Table
         */
        add_action( 'manage_posts_custom_column' , [\Admin\PostType::get(), 'column_post_table'] , 10, 2 );
        add_filter('manage_'.\WP_VERONALABS_TEST::post_type.'_posts_columns' , [\Admin\PostType::get(), 'column_book']);


        /*
         * AddMenu ISBN Page
         */
        add_action('admin_menu', [$this, 'add_submenu_isbn']);

        /*
         * Flush Rewrite in Not finding Post Type
         */
        add_action( 'init', [$this, 'flush_rewrite'] , 999 );

        /*
         * Set Screen Option
         */
        if( $pagenow =="edit.php" and $_GET['post_type'] ==\WP_VERONALABS_TEST::post_type and $_GET['page'] =="isbn_book") {
            add_filter('set-screen-option', [Admin\ISBN\Core::get(), 'Set_Screen_option'], 10, 3);
        }

    }


    /*
     * Add Isbn Menu
     */
    public function add_submenu_isbn()
    {
        $hook = add_submenu_page( 'edit.php?post_type='.\WP_VERONALABS_TEST::post_type,__("ISBN List", self::text_doamin),__("ISBN List", self::text_doamin),'manage_options', 'isbn_book' ,[Admin\ISBN\Core::get(), 'ShowPage_ISBN']);
        add_action( "load-$hook", [Admin\ISBN\Core::get(), 'Screen_option'] );
    }


    /*
     * Public Action Wordpress
     */
    public function public_action()
    {

        /*
         * Add Shortcode Book Component
         */
        add_shortcode( 'booksearch', [ \Front\ShortCode::get() , 'Search_Component' ] );

    }


    /*
     * Flush Rewrite
     */
    public function flush_rewrite()
    {

            if ( get_option( 'wp_veronalabs_flush' ) ) {
                /*
                 * Flush Rewrite
                 */
                flush_rewrite_rules();

                /*
                 * Remove Option
                 */
                delete_option( 'wp_veronalabs_flush' );
            }
    }


    /**
     * Constructor. Intentionally left empty and public.
     *
     * @see plugin_setup()
     */
    public function __construct(){}


    /**
     * Loads translation file.
     *
     * Accessible to other classes to load different language files (admin and
     * front-end for example).
     *
     * @wp-hook init
     * @param   string $domain
     * @return  void
     */
    public function load_language($domain)
    {
        load_plugin_textdomain( $domain, false, basename( dirname( __FILE__ ) ) . '/languages' );
    }

    /**
     * @param $class
     *
     */
    public function autoload($class)
    {
        $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);

        if (!class_exists($class)) {
            $class_full_path = $this->plugin_path . 'includes/' . $class . '.php';

            if (file_exists($class_full_path)) {
                require $class_full_path;
            }
        }
    }
}
