<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

// Rute CRUD
Route::get('/', [TaskController::class, 'index']); // Read & Search & Form Edit
Route::post('/add', [TaskController::class, 'store']); // Create
Route::post('/update/{id}', [TaskController::class, 'update']); // Update
Route::get('/delete/{id}', [TaskController::class, 'destroy']); // Delete
