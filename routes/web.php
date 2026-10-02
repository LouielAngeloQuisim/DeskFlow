<?php

use App\Http\Controllers\AgentTicketController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [TicketController::class, 'dashboard'])->name('dashboard');
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket:reference}', [TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket:reference}/replies', [TicketController::class, 'reply'])->name('tickets.replies.store');

    Route::get('/agent/queue', [AgentTicketController::class, 'index'])->name('agent.queue');
    Route::patch('/agent/tickets/{ticket:reference}', [AgentTicketController::class, 'update'])->name('agent.tickets.update');
});

require __DIR__.'/settings.php';
