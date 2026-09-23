<?php

namespace App\Http\Controllers;

use App\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        return response(['data' => Role::query()->orderBy('id')->get(['id', 'name'])]);
    }
}
