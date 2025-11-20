<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MetaTagsController;
use App\Http\Controllers\PagesController;




Route::post('/pages', [PagesController::class, 'store']);
Route::get('/pages', [PagesController::class, 'index']);
Route::put('/pages/{id}', [PagesController::class, 'update']);
Route::delete('/pages/{id}', [PagesController::class, 'destroy']);


Route::post('/metatags', [MetaTagsController::class, 'store']);
Route::put('/metatags/{id}', [MetaTagsController::class, 'update']);
Route::get('/pages/{id}/tags', [MetaTagsController::class, 'getTagsByPage']);
Route::get('/metatags/{id}', [MetaTagsController::class, 'show']);
Route::delete('/metatags/{id}', [MetaTagsController::class, 'destroy']);

