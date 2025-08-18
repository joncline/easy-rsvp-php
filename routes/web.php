<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventAdminController;
use App\Http\Controllers\RsvpController;

// Root route - new event form
Route::get('/', [EventController::class, 'new'])->name('events.new');

// Event routes
Route::post('/', [EventController::class, 'create'])->name('events.create');
Route::get('/{event}', [EventController::class, 'show'])->name('events.show');

// RSVP routes
Route::post('/{event}/rsvps', [RsvpController::class, 'create'])->name('rsvps.create');
Route::delete('/{event}/rsvps/{rsvp}', [RsvpController::class, 'destroy'])->name('rsvps.destroy');

// Admin routes
Route::get('/{event}/admin/{admin_token}', [EventAdminController::class, 'show'])->name('events.admin.show');
Route::get('/{event}/admin/{admin_token}/edit', [EventAdminController::class, 'edit'])->name('events.admin.edit');
Route::put('/{event}/admin/{admin_token}', [EventAdminController::class, 'update'])->name('events.admin.update');
Route::delete('/{event}/admin/{admin_token}', [EventAdminController::class, 'destroy'])->name('events.admin.destroy');
Route::post('/{event}/admin/{admin_token}/toggle-publish', [EventAdminController::class, 'togglePublish'])->name('events.admin.toggle_publish');
