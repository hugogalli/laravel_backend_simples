<?php

use App\Http\Controllers\AnalistaAreaController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\AtendimentoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\TodoController;

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login')->name('login');
    Route::post('register', 'register')->name('register');
    Route::post('logout', 'logout')->name('logout');
    Route::post('refresh', 'refresh')->name('refresh');
    Route::get('user/{id}', 'show')->name('user.show');
    Route::get('userinfo', 'getId')->name('user.getid');

});

Route::controller(ClienteController::class)->group(function () {
    Route::get('clientes', 'index')->name('clientes.index');
    Route::post('cliente', 'store')->name('cliente.store');
    Route::get('cliente/{id}', 'show')->name('cliente.show');
    Route::put('cliente/{id}', 'update')->name('cliente.update');
    Route::delete('cliente/{id}', 'destroy')->name('cliente.destroy');
}); 

Route::controller(AreaController::class)->group(function () {
    Route::get('areas', 'index')->name('areas.index');
    Route::post('area', 'store')->name('area.store');
    Route::get('area/{id}', 'show')->name('area.show');
    Route::put('area/{id}', 'update')->name('area.update');
    Route::delete('area/{id}', 'destroy')->name('area.destroy');
}); 

Route::controller(AtendimentoController::class)->group(function () {
    Route::get('atendimentos', 'index')->name('atendimentos.index');
    Route::post('atendimento', 'store')->name('atendimento.store');
    Route::get('atendimento/{id}', 'show')->name('atendimento.show');
    Route::put('atendimento/posse/{id}', 'posse')->name('atendimento.posse');
    Route::put('atendimento/completar/{id}', 'completar')->name('atendimento.completar');
    Route::put('atendimento/transferir/{atendimentoId}/analista/{analistaId}', 'transferir')->name('atendimento.transferir');
    Route::delete('atendimento/{id}', 'destroy')->name('atendimento.destroy');
    Route::get('atendimentos/relatorios/clienteshoje', 'clientes')->name('atendimentos.relatorio.clienteshoje');
    Route::get('atendimentos/relatorios/analistashoje', 'analistas')->name('atendimentos.relatorio.analistashoje');
    Route::get('atendimentos/relatorios/areashoje', 'areas')->name('atendimentos.relatorio.areashoje');
    Route::get('atendimentos/relatorios/tiposhoje', 'tipos')->name('atendimentos.relatorio.tiposhoje');
    Route::get('atendimentos/relatorios/pendenteshoje', 'pendentes')->name('atendimentos.relatorio.pendenteshoje');
    Route::get('atendimentos/relatorios/pendentesporanalista', 'pendentesporanalista')->name('atendimentos.relatorio.pendentesporanalista');
}); 

Route::controller(AnalistaAreaController::class)->group(function () {
    Route::post('analistas/{analistaId}/areas/{areaId}', 'associate')->name('analista.associate');
    Route::delete('analistas/{analistaId}/areas/{areaId}', 'dissociate')->name('analista.dissociate');
}); 



