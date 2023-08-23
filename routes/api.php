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
    Route::get('user/{id}', 'getProfileById')->name('user.getProfileById');
    Route::get('userinfo', 'getMyProfile')->name('user.getMyProfile');

});

Route::controller(ClienteController::class)->group(function () {
    Route::get('clientes', 'getTodos')->name('clientes.getTodos');
    Route::post('cliente', 'criarNovo')->name('cliente.criarNovo');
    Route::get('cliente/{id}', 'getClienteById')->name('cliente.getClienteById');
    Route::put('cliente/{id}', 'atualizarTitulo')->name('cliente.atualizarTitulo');
    Route::delete('cliente/{id}', 'destroy')->name('cliente.destroy');
}); 

Route::controller(AreaController::class)->group(function () {
    Route::get('areas', 'getTodos')->name('areas.getTodos');
    Route::post('area', 'criarNovo')->name('area.criarNovo');
    Route::get('area/{id}', 'getAreaById')->name('area.getAreaById');
    Route::put('area/{id}', 'atualizarTitulo')->name('area.atualizarTitulo');
    Route::delete('area/{id}', 'destroy')->name('area.destroy');
}); 

Route::controller(AtendimentoController::class)->group(function () {
    Route::get('atendimentos', 'getTodos')->name('atendimentos.getTodos');
    Route::post('atendimento', 'criarNovo')->name('atendimento.criarNovo');
    Route::get('atendimento/{id}', 'getAtendimentoById')->name('atendimento.getAtendimentoById');
    Route::put('atendimento/posse/{id}', 'tomarPosse')->name('atendimento.tomarPosse');
    Route::put('atendimento/completar/{id}', 'concluir')->name('atendimento.concluir');
    Route::put('atendimento/transferir/{atendimentoId}/analista/{analistaId}', 'transferirPosse')->name('atendimento.transferirPosse');
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



