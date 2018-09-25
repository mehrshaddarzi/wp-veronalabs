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
        $this->load_language($this->text_domain);

        /*
         * PSR Autoload
         */
        spl_autoload_register(array($this, 'autoload'));

        /*
         * Admin Action Load
         */
        $this->admin_action();

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

    }


    /*
     * List Admin Action Wordpress
     */
    public function admin_action()
    {

        /*
         * Create Book Post Type
         */
        add_action( 'init', [\Admin\PostType::get(), 'create_book_post_type'] );


        /*
         * Add Taxonomies For Book
         */
        add_action( 'init', [\Admin\Taxonomy::get(), 'Create_taxonomy_book'] );


        /*
         * New MetaBox For Book PostType
         */
        add_action( 'add_meta_boxes', [\Admin\MetaBox::get(), 'Create_Meta_box'] );
        add_action( 'save_post_book', [\Admin\MetaBox::get(), 'Save_MetaBox'] , 10, 2 );

        /*
         * AddMenu ISBN Page
         */
        add_action('admin_menu', [$this, 'add_submenu_isbn']);

    }


    /*
     * Add Isbn Menu
     */
    public function add_submenu_isbn()
    {
        add_submenu_page( 'edit.php?post_type=book',__("ISBN List", self::text_doamin),__("ISBN List", self::text_doamin),'manage_options', 'isbn_book' ,[Admin\ISBN\Core::get(), 'ShowPage_ISBN']);
    }


    /**
     * Constructor. Intentionally left empty and public.
     *
     * @see plugin_setup()
     */
    public function __construct()
    {
    }


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
        load_plugin_textdomain($domain, FALSE, $this->plugin_path . '/languages');
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
