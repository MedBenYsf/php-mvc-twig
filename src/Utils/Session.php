<?php
namespace App\Utils;

class Session { 

    // Session singleton
    protected static $instance;

    private function __construct()
    {
        //start the session
        session_start();
        Session::$instance = $this; 
    }

    public static function instance()
    {
        if (Session::$instance === NULL)
        {
            // Create a new instance
            new Session;
        }
        return Session::$instance;
    }

    public function getItem ($key) {
        return $_SESSION[$key];
    }

    public function setItem ($key, $value) {
        return $_SESSION[$key] = $value;
    }

    public function removeItem ($key) {
        unset($_SESSION[$key]);
    }
}