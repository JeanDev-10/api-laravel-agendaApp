<?php

namespace App\Swagger;


/**
 * @OA\Schema(
 *     schema="PaginationLinks",
 *     type="object",
 *     @OA\Property(property="first", type="string", example="http://localhost/api/contacts?page=1"),
 *     @OA\Property(property="last", type="string", example="http://localhost/api/contacts?page=10"),
 *     @OA\Property(property="prev", type="string", nullable=true, example=null),
 *     @OA\Property(property="next", type="string", nullable=true, example="http://localhost/api/contacts?page=2")
 * )
 */

Class PaginationLinks{

}
