<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MeetingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});




Route::get('/dashboard', [MeetingController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/meetings',[MeetingController::class,'index'])->name('meeting.index');
    Route::get('/create',[MeetingController::class,'create'])->name('meeting.create');
    Route::get('/send',[MeetingController::class,'sendNotification'])->name('sendNotification');

    Route::get('/meeting/{meeting}/postpone', [MeetingController::class, 'edit'])->name('meeting.postpone');
    Route::put('/meeting/{meeting}/postpone', [MeetingController::class, 'update'])->name('meeting.updatePostpone');

    Route::post('/meetings',[MeetingController::class,'store'])->name('store');
    Route::get('/meeting/{meeting_id}',[MeetingController::class,'show'])->name('meeting.show');

    Route::delete('/meeting/{id}',[MeetingController::class,'deleteMeeting'])->name('meeting.delete');


    Route::get('/create/zoom',[MeetingController::class,'saveMeeting'])->name('saveMeeting');
    Route::post('/create/zoom',[MeetingController::class,'saveMeeting'])->name('saveMeeting');
});

require __DIR__.'/auth.php';
