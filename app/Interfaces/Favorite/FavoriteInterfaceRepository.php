<?php

namespace App\Interfaces\Favorite;
use Illuminate\Http\Request;
use App\Models\Favorite;

interface FavoriteInterfaceRepository{
    public function index();
    public function store(Request $request);
    public function show($id);
    public function showById($id);
    public function delete(Favorite $favorite);
}
