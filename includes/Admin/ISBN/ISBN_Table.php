<?php
namespace Admin\ISBN;

class ISBN_Table extends \WP_List_Table
{

    // Get instance
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

    /** Class constructor */
    public function __construct() {

        $t_b = \WP_VERONALABS_TEST::text_doamin;
        parent::__construct( [
            'singular' => __( 'ISBN', $t_b ),
            'plural'   => __( 'ISBN Lists', $t_b ),
            'ajax'     => false
        ] );

    }
    
    /**
     * Retrieve customers data from the database
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @return mixed
     */
    public static function get_isbn_list( $per_page = 5, $page_number = 1 ) {
        global $wpdb;

        $where = ISBN_Table::where_search();
        $sql = "SELECT * FROM `{$wpdb->prefix}books_info`$where";
        
        if ( ! empty( $_REQUEST['orderby'] ) ) {
            $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
        } else {
            $sql .= ' ORDER BY `id`';
        }
        $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' DESC';
        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
        
        $result = $wpdb->get_results( $sql, 'ARRAY_A' );
        return $result;
    }


    /*
     * Prefix Sql Search Button
     */
    public static function where_search()
    {
        /*
         * Search
         */
        $where = '';
        if( isset($_POST['s']) || isset($_REQUEST['s']) ) {
            if( trim($_POST['s']) !="" ) {
                $where = ' WHERE `isbn` = "'.trim($_POST['s']).'"';
            } else {
                $where = ' WHERE `isbn` = "'.trim($_REQUEST['s']).'"';
            }
        }

        return $where;
    }

    /**
     * Delete a customer record.
     *
     * @param int $id customer ID
     */
    public static function delete_isbn( $id ) {
        global $wpdb;
        $wpdb->delete($wpdb->prefix.'books_info', [ 'id' => $id ], [ '%d' ]);
    }


    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public static function record_count() {
        global $wpdb;
        $where = ISBN_Table::where_search();
        $sql = "SELECT COUNT(*) FROM `{$wpdb->prefix}books_info`$where";
        return $wpdb->get_var( $sql );
    }


    /** Text displayed when no customer data is available */
    public function no_items() {
        $t_b = \WP_VERONALABS_TEST::text_doamin;
        _e( 'No ISBN avaliable.', $t_b );
    }

    /**
     * Render a column when no column specific method exist.
     *
     * @param array $item
     * @param string $column_name
     *
     * @return mixed
     */
    public function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'bookname':
            case 'ISBN':
                return $item[ $column_name ];
            default:
                return print_r( $item, true ); //Show the whole array for troubleshooting purposes
        }
    }

    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
    function column_cb( $item ) {
        return sprintf('<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']);
    }


    function column_bookname( $item ) {

        $delete_nonce = wp_create_nonce( 'sp_delete_isbn' );
        $actions = [
            'delete' => sprintf( '<a href="?post_type=book&page=%s&action=%s&remove_isbn=%s&_wpnonce=%s">'.__('Delete This ISBN', \WP_VERONALABS_TEST::text_doamin).'</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
        ];
        return get_the_title($item['post_id']) . $this->row_actions( $actions );

    }

    function column_ISBN( $item ) {
        return $item['isbn'];
    }


    function column_view( $item ) {
        return '<a href="'.get_the_permalink($item['post_id']).'" target="_blank">'.__("View Post", \WP_VERONALABS_TEST::text_doamin).'</a>';
    }


    /**
     *  Associative array of columns
     *
     * @return array
     */
    function get_columns() {

        $t_b = \WP_VERONALABS_TEST::text_doamin;
        $columns = [
            'cb'      => '<input type="checkbox" />',
            'bookname'    => __( "Book Name" , $t_b ),
            'isbn'    => __( 'ISBN', $t_b ),
            'view'    => '',
        ];

        return $columns;
    }

    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public function get_sortable_columns() {
        $sortable_columns = array(
            /*
             * Nothing For This Table
             */
        );

        return $sortable_columns;
    }


    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public function get_bulk_actions() {
        $actions = [
            /*
             * Nothing For this Project
             */
        ];
        return $actions;
    }


    /* Search Box */
    public function search_box( $text, $input_id ) {
        if ( empty( $_REQUEST['s'] ) && !$this->has_items() )
            return;
        $input_id = $input_id . '-search-input';
        ?>
        <p class="search-box">
            <label class="screen-reader-text" for="<?php echo $input_id ?>"><?php echo $text; ?>:</label>
            <input type="search" id="<?php echo $input_id ?>" name="s" value="<?php _admin_search_query(); ?>" />
            <?php submit_button( $text, 'button', false, false, array('id' => 'search-submit') ); ?>
        </p>
        <?php
    }


    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items() {
        
        $this->_column_headers = $this->get_column_info();

        /** Process bulk action */
        $this->process_bulk_action();

        $per_page     = $this->get_items_per_page( 'isbn_per_page', 5 );
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();

        $this->set_pagination_args( [
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ] );

        $this->items = self::get_isbn_list( $per_page, $current_page );
    }

    
    public function process_bulk_action() {

        if ( 'delete' === $this->current_action() ) {

            $nonce = esc_attr( $_REQUEST['_wpnonce'] );
            if ( ! wp_verify_nonce( $nonce, 'sp_delete_isbn' ) ) {
                die( __("You are Not Allow", \WP_VERONALABS_TEST::text_doamin) );
            }
            else {
                self::delete_isbn( absint( $_GET['remove_isbn'] ) );
            }
        }

    }

}