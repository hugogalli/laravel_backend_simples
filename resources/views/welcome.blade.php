@extends('layouts.main')

@section('content')
    <!-- Header -->
    <header class="py-5">
        <div class="container px-lg-5">
            <div class="p-4 p-lg-5 bg-light rounded-3 text-center">
                <div class="m-4 m-lg-5">
                    <h1 class="display-5 fw-bold">Sistema API Backend SH3</h1>
                    <p class="fs-4">Selecione a funcionalidade para realizar um teste usando PHPUnit</p>
                </div>
            </div>
        </div>
    </header>
    <section class="pt-4">
        <div class="container px-lg-5">
            <div class="row justify-content-center align-items-center">
                <div class="col-lg-6 col-xxl-4 mb-5">
                    <div class="card bg-light border-0 h-100 d-flex flex-column justify-content-center align-items-center">
                        <div class="card-body p-0"> <!-- Removendo o espaçamento interno -->
                            <div class="dropdown">
                                <button class="btn btn-primary btn-lg dropdown-toggle m-0" type="button" id="dropdownTests"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    Testar Funcionalidades
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownTests">
                                    <li><a class="dropdown-item" href="api/todos">Quais os clientes ou pessoas entraram em
                                            contato</a></li>
                                    <li><a class="dropdown-item" href="#">Quantos atendimentos cada analista de
                                            suporte realizou</a></li>
                                    <li><a class="dropdown-item" href="#">Quais áreas de atendimento foram
                                            procuradas</a></li>
                                    <li><a class="dropdown-item" href="#">Quais os tipos de atendimento receberam
                                            ligação naquele dia</a></li>
                                    <li><a class="dropdown-item" href="#">Quais ligações não receberam atendimento do
                                            suporte</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
