<?php

namespace App\Interfaces\Contact;
use Illuminate\Http\Request;

interface ContactInterface{
    public function index();
    public function store(Request $request);
    public function show($id);
    public function update(Request $request, $id);
    public function delete($id);
}