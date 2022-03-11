<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $settings = Setting::all();
        $count = $settings->count();
        if($count < 1)
            return view('setup');
        else
            return view('dashboard');
    }
    public function store(Request $request)
    {
        $request->validate([
            'it' => 'required',
            'email' => 'required|email',
            'default_assignee' => 'numeric',
        ]);

        if($request->it == "yes")
        {
            $settings = new Setting;
            $settings->default_email = $request->email;
            if($request->default_assignee > 0)
                $settings->default_assignee = $request->default_assignee;
            $settings->save();

            User::where('id', Auth::user()->id)->update(['role' => 'IT']);
            return redirect ('/dashboard');
        }
         else
            return redirect('/dashboard')->with('error', 'Must be a member of IT to complete this setup');

    }
}
