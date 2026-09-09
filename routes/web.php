<?php

use App\Http\Controllers\CatatanMingguanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\KopSuratController;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\MagangController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\PreviewController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TimeScheduleController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/progress', [ProgressController::class, 'index'])->name('progress');

Route::resource('mahasiswa', MahasiswaController::class);

Route::resource('magang', MagangController::class);

Route::prefix('magang/{magang}')->name('magang.')->group(function () {
    Route::get('presensi', [PresensiController::class, 'index'])->name('presensi.index');
    Route::get('presensi/create', [PresensiController::class, 'create'])->name('presensi.create');
    Route::post('presensi', [PresensiController::class, 'store'])->name('presensi.store');

    Route::get('logbook', [LogbookController::class, 'index'])->name('logbook.index');
    Route::get('logbook/create', [LogbookController::class, 'create'])->name('logbook.create');
    Route::post('logbook', [LogbookController::class, 'store'])->name('logbook.store');

    Route::get('tasks', [TaskController::class, 'index'])->name('task.index');
    Route::get('tasks/create', [TaskController::class, 'create'])->name('task.create');
    Route::post('tasks', [TaskController::class, 'store'])->name('task.store');
    Route::get('tasks/{task}', [TaskController::class, 'show'])->name('task.show');
    Route::get('tasks/{task}/edit', [TaskController::class, 'edit'])->name('task.edit');
    Route::put('tasks/{task}', [TaskController::class, 'update'])->name('task.update');
    Route::delete('tasks/{task}', [TaskController::class, 'destroy'])->name('task.destroy');

    Route::get('time-schedule', [TimeScheduleController::class, 'index'])->name('time-schedule.index');
    Route::get('time-schedule/create', [TimeScheduleController::class, 'create'])->name('time-schedule.create');
    Route::post('time-schedule', [TimeScheduleController::class, 'store'])->name('time-schedule.store');
    Route::get('time-schedule/available', [TimeScheduleController::class, 'available'])->name('time-schedule.available');
    Route::get('time-schedule/{timeSchedule}', [TimeScheduleController::class, 'show'])->name('time-schedule.show');
    Route::get('time-schedule/{timeSchedule}/edit', [TimeScheduleController::class, 'edit'])->name('time-schedule.edit');
    Route::put('time-schedule/{timeSchedule}', [TimeScheduleController::class, 'update'])->name('time-schedule.update');
    Route::delete('time-schedule/{timeSchedule}', [TimeScheduleController::class, 'destroy'])->name('time-schedule.destroy');

    Route::get('catatan-mingguan', [CatatanMingguanController::class, 'index'])->name('catatan.index');
    Route::post('catatan-mingguan', [CatatanMingguanController::class, 'store'])->name('catatan.store');

    Route::get('export', [ExportController::class, 'index'])->name('export.index');
});

Route::get('presensi/{presensi}/edit', [PresensiController::class, 'edit'])->name('presensi.edit');
Route::put('presensi/{presensi}', [PresensiController::class, 'update'])->name('presensi.update');
Route::delete('presensi/{presensi}', [PresensiController::class, 'destroy'])->name('presensi.destroy');

Route::get('logbook/{logbook}/edit', [LogbookController::class, 'edit'])->name('logbook.edit');
Route::put('logbook/{logbook}', [LogbookController::class, 'update'])->name('logbook.update');
Route::delete('logbook/{logbook}', [LogbookController::class, 'destroy'])->name('logbook.destroy');

Route::put('catatan-mingguan/{catatanMingguan}', [CatatanMingguanController::class, 'update'])->name('catatan.update');
Route::delete('catatan-mingguan/{catatanMingguan}', [CatatanMingguanController::class, 'destroy'])->name('catatan.destroy');

Route::resource('kop-surat', KopSuratController::class);

Route::get('/preview/logbook/{magang}', [PreviewController::class, 'logbook'])->name('preview.logbook');
Route::get('/preview/presensi/{magang}', [PreviewController::class, 'presensi'])->name('preview.presensi');

Route::get('/export/{magang}/logbook/word', [ExportController::class, 'logbookWord'])->name('export.logbook.word');
Route::get('/export/{magang}/logbook/pdf', [ExportController::class, 'logbookPdf'])->name('export.logbook.pdf');
Route::get('/export/{magang}/presensi/word', [ExportController::class, 'presensiWord'])->name('export.presensi.word');
Route::get('/export/{magang}/presensi/pdf', [ExportController::class, 'presensiPdf'])->name('export.presensi.pdf');