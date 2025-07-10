<?php

use App\Http\Controllers\Admin\UserCrudController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get("admin/assign/role",[UserCrudController::class,"assignedUser"]);