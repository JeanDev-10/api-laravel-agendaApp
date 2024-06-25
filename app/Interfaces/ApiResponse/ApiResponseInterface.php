<?php

namespace App\Interfaces\ApiResponse;

interface ApiResponseInterface{
    public static function successs($message,$statusCode,$data);
    public static function error($message,$statusCode,$data);
}
