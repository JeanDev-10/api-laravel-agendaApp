<?php
namespace App\Interfaces\User;
use Illuminate\Http\Request;


interface UserInterface{
    public function changePassword(Request $request);
}
