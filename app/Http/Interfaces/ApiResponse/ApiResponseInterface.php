<?php

namespace App\Http\Interfaces\ApiResponse;

interface ApiResponseInterface{
    public static function succes($message,$statusCode,$data);
    public static function error($message,$statusCode,$data);
}
