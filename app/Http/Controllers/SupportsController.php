<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supports;
use Illuminate\Support\Facades\DB;

class SupportsController extends Controller
{
    public function index(){
        return view('supports.view');
    }
    public function add_support(Request $request){
        return view('supports.update', ['request' => $request]);
    }
    public function store(Request $request){
        $request->validate(
            ['add_user' => 'numeric',
            'remove_user' => 'numeric',
            'supporter' => 'numeric|required']
        );

        if($request->add_user > 0){
            $newsupport = new Supports;
            $newsupport->supporter = $request->supporter;
            $newsupport->supported = $request->add_user;
            $newsupport->save();
        }
        if($request->remove_user > 0){
            DB::table('supports')->where('supporter', $request->supporter)->where('supported', $request->remove_user)->delete();
        }

        return redirect('/supports/view/');
    }
}
