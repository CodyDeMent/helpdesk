<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    public function index(){
        return view('update-role');
    }
    public function store(Request $request){
        $request->validate([
            'user' => 'required|numeric',
            'role' => 'required|string',
        ]);

        if($request->role == "IT")
            User::where('id', $request->user)->update(['role' => 'IT']);
        else
            User::where('id', $request->user)->update(['role' => 'User']);
        return redirect('dashboard');
    }
}
