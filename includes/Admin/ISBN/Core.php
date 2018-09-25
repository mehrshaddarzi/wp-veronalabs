<?php
namespace Admin\ISBN;


class Core
{
    // Get instance
    protected static $instance = NULL;

    // Isbn WP_List_Table object
    public $isbn_obj;


    public function __construct()
    {

    }


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



    function Set_Screen_option($status, $option, $value) {
        if ( 'isbn_per_page' == $option ) return $value;
        return $status;
    }


    public function Screen_option()
    {

        $t_d = \WP_VERONALABS_TEST::text_doamin;
        $option = 'per_page';
        $args   = [
            'label'   => __("ISBN Per Page", $t_d),
            'default' => 10,
            'option'  => 'isbn_per_page'
        ];
        add_screen_option( $option, $args );
        $this->isb_obj = new ISBN_Table();

    }


    public function ShowPage_ISBN()
    {



        /*
         * Show Ui List Table Page
         */

?>
<div class="wrap">
<h2><?php echo __("ISBN List", \WP_VERONALABS_TEST::text_doamin); ?> </h2>
<div id="poststuff">
<div id="post-body" class="metabox-holder columns">
<div>
<div class="meta-box-sortables ui-sortable">
    <form id="nds-user-list-form" method="get">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
        <input type="hidden" name="post_type" value="book" />
<?php
$wp_list_table = new ISBN_Table();
$wp_list_table->prepare_items();
$wp_list_table->search_box( __("Search ISBN", \WP_VERONALABS_TEST::text_doamin), 'search_isbn_input');
$wp_list_table->display();
?>
</form>
</div>
</div>
</div>
<br class="clear">
</div>
</div>
<?php

    }

}