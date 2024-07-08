<?php

namespace App\Interfaces\Contact;
use Illuminate\Http\Request;
use App\Models\Contact;

interface ContactInterface{
    public function index();
    public function store(Request $request);
    public function show($id);
    public function update(Contact $contacto,Request $request);
    public function delete(Contact $contacto);
    public function restoreContacts();
}
