<?php
namespace Admin;


class MetaBox
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


    public function Create_Meta_box()
    {
        $t_d = \WP_VERONALABS_TEST::text_doamin;
        add_meta_box('veronalabs_test_metabox_isb', __('ISBN Book', $t_d), [$this , 'isbn_metabox'], \WP_VERONALABS_TEST::post_type, 'normal', 'high');
    }


    public function isbn_metabox()
    {
        global $post, $wpdb;

        //get this Book Isbn
        $isbn = $wpdb->get_var("SELECT `isbn` FROM `{$wpdb->prefix}books_info` WHERE `post_id` = {$post->ID}");

        //Nounce Security
        wp_nonce_field( basename( __FILE__ ), 'isbn_fields_security' );

        $t_d = \WP_VERONALABS_TEST::text_doamin;
        echo '
        <table class="form-table">
	    <tbody>
	    <tr>
		<th scope="row">'.__('ISBN Number', $t_d).'</th>
		<td>
		<input type="text" name="isbn_book" value="'.$isbn.'" class="widefat">
        </td>
        </tr>
        </tbody>
        </table>
        ';
    }


    /*
     * Save MetaBox Isbn
     */
    public function Save_MetaBox( $post_id, $post )
    {
        global $wpdb;


      /*
       * Check User Not Permission
       */
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return $post_id;
        }


        /*
         * check Isset Post Requet
         */
        if ( ! isset( $_POST['isbn_book'] ) || ! wp_verify_nonce( $_POST['isbn_fields_security'], basename(__FILE__) ) ) {
            return $post_id;
        }

        /*
         * Update Or Add To Database
         */
        $exist_post_id = $wpdb->get_var("select count(*) FROM `{$wpdb->prefix}books_info` WHERE `post_id` = {$post_id}");
        if($exist_post_id >0) {

            $wpdb->update(
                $wpdb->prefix.'books_info',
                array('isbn' => sanitize_text_field($_POST['isbn_book'])),
                array( 'post_id' => $post_id ),
                array('%s'),
                array( '%d' )
            );

        } else {

            $wpdb->insert(
                $wpdb->prefix.'books_info',
                array('post_id' => $post_id, 'isbn' => sanitize_text_field($_POST['isbn_book'])),
                array('%d', '%s')
            );

        }

    }
    
    
    /*
     * Remove ISBN in Deleting Complete post
     */
    public function Remove_ISBN_Row( $postid  )
    {
        global $wpdb, $post_type;
        if ( $post_type == \WP_VERONALABS_TEST::post_type ) {
            $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}books_info WHERE post_id = %d", $postid));
        }
    }

}