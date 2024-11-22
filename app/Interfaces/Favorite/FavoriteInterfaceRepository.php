<?php

namespace App\Interfaces\Favorite;
use Illuminate\Http\Request;
use App\Models\Favorite;

interface FavoriteInterfaceRepository{
    public function index(array $filters);
    public function store($contact_id);
    public function show($id);
    public function showById($id);
    public function delete(Favorite $favorite);
}
