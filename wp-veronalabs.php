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
register_activation_hook(__FILE__, array( WP_VERONALABS_TEST::get_instance(), 'activate') );


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
     * Access this plugin’s working instance
     *
     * @wp-hook plugins_loaded
     * @since   2012.09.13
     * @return  object of this class
     */
    public static function get_instance()
    {
        NULL === self::$instance and self::$instance = new self;
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
        $this->load_language('wp-veronalabs');

        /*
         * PSR Autoload
         */
        spl_autoload_register(array($this, 'autoload'));

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
