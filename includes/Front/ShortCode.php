<?php
namespace Front;

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
        $text = '';








        return $text;
    }
    
    
    
    
    
}