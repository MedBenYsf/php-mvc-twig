<?php
namespace App\Utils;

class FilterInput { 

    public static function getInput($inputFilter, $key) {
        return filter_input($inputFilter, $key);
    }
}