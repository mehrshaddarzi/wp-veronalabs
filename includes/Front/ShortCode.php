<?php
namespace Front;
use Admin\PostType;

class ShortCode
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


    public function Search_Component()
    {
        global $post, $wpdb;
        $text = '';
        $t_d = \WP_VERONALABS_TEST::text_doamin;
        $search = (isset($_POST['isbn_search_form']) ? trim($_POST['isbn_search_form']) : "");

        /*
         * show Form First
         */
        $nonce = wp_create_nonce( 'isbn_search_key' );
        $text .='
        <div class="book-search-box">
        <form method="post" action="">
            <table border="0">
                <tr>
                    <td>
                        '.__("Book ISBN", $t_d) .'
                    </td>
                    <td>
                        <input type="hidden" name="wp_nonce_isbn" value="'.$nonce.'">
                        <input type="text" name="isbn_search_form" value="'.$search.'" placeholder="'.__("Please Enter ISBN Book Number", $t_d) .'" required>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="submit" value="'.__("Search in Books", $t_d) .'">
                    </td>
                </tr>
            </table>
        </form>
        </div>
        ';


        /*
         * Process form
         */
        if( isset($_POST['isbn_search_form']) and wp_verify_nonce( $_POST['wp_nonce_isbn'], 'isbn_search_key' ) ) {


            /*
             * Query
             */
            $search = sanitize_text_field($_POST['isbn_search_form']);
            $query = $wpdb->get_results("select * FROM `{$wpdb->prefix}books_info` WHERE `isbn` LIKE '%{$search}%'", ARRAY_A);

            if(count($query) >0) {
                $post_type = new PostType();

                $text.= '<table id="isbn_table">';
                $text.= '<tr>';
                $text.= '<td>'.__("Book Name", $t_d).'</td>';
                $text.= '<td>'.__("ISBN", $t_d).'</td>';
                $text.= '<td>'.__("Authors Book", $t_d).'</td>';
                $text.= '<td>'.__("Publisher Book", $t_d).'</td>';
                $text.= '<tr>';
                foreach($query as $book) {
                    $post_id = $book['post_id'];
                    $text.= '<tr>';
                    $text.= '<td>'.get_the_title($post_id).'</td>';
                    $text.= '<td>'.$book['isbn'].'</td>';
                    $text.= '<td>';
                    $text.= $post_type->show_list_term_of_postid($post_id, 'authors');
                    $text.= '</td>';
                    $text.= '<td>';
                    $text.= $post_type->show_list_term_of_postid($post_id, 'Publisher');
                    $text.= '</td>';
                    $text.= '<tr>';
                }
                $text.= '</table>';
                $text.= '<style>#isbn_table{border-collapse:collapse;width:100%}#isbn_table td,#isbn_table th{border:1px solid #ddd;padding:8px}#isbn_table tr:nth-child(even){background-color:#f2f2f2}#isbn_table tr:hover{background-color:#ddd}#isbn_table th{padding-top:12px;padding-bottom:12px;text-align:left;background-color:#4CAF50;color:#fff}</style>';
            } else {
                $text .= '<br><br>'.__("No books found with this ISBN", $t_d);
            }

        }
        

        return $text;
    }
    
    
    
    
    
}