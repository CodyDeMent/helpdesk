<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HardwareRequest;
use Illuminate\Support\Facades\Auth;

class HardwareRequestController extends Controller
{
    public function index()
    {
        return view('ticket/new/hardware-request');
    }
    public function store(Request $request)
    {
        $hardwareRequest = new HardwareRequest;
        $hardwareRequest->user_id = Auth::user()->id;
        $hardwareRequest->hardware_type = $request->hardware_type;
        $hardwareRequest->reason = $request->reason;
        $hardwareRequest->urgency = $request->urgency;
        $hardwareRequest->status = "Active";
        $hardwareRequest->save();
        return redirect('dashboard')->with('status', 'Your request has been submitted and is being reviewed by the IT Team.');
    }
}
