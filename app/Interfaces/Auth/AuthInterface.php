<?php
namespace App\Interfaces\Auth;
use Illuminate\Http\Request;

interface AuthInterface{
    public function login(Request $request);
    public function register(Request $request);
    public function userProfile();
    public function logout();
    public function refresh();
}
