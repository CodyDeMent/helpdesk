<?php
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;
use PHPUnit\TextUI\XmlConfiguration\Group;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('login');
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::middleware('auth:sanctum', 'verified')->group(function(){

    Route::controller(TicketController::class)->group(function(){
        //New Tickets
        Route::get('/ticket/new', 'index')->name('ticket.new');
        Route::post('/ticket/new/store', 'store');
        //View Tickets
        Route::get('/ticket/view/{id}', 'view');
        Route::post('/ticket/comment/store', 'store_comment');
        Route::post('/ticket/close', 'close_ticket');
        Route::get('/ticket/file/{id}', 'download');
    });

    //Hardware Requests
    /*Route::controller(HardwareRequestController::class)->group(function(){
        Route::get('/ticket/new/hardware-request', 'index');
        Route::post('/ticket/new/hardware-request/store', 'store');
    });

    //Remote Access
    Route::controller(RemoteAccessController::class)->group(function(){
        Route::get('/ticket/new/remote-access', 'index');
        Route::post('/ticket/new/remote-access/store', 'store');
    });

    //Technical Support
    Route::controller(TechnicalSupportController::class)->group(function(){
        Route::get('/ticket/new/technical-support', 'index');
        Route::post('/ticket/new/technical-support/store', 'store');
    });*/
});

//IT Support Team Area
Route::middleware('isIT')->group(function () {

    Route::controller(TicketController::class)->group(function(){
        Route::get('/tickets', 'list')->name('tickets.view');
        Route::post('/ticket/assign', 'assign');
        Route::post('/ticket/assign/update', 'update_assign');
    });
});
