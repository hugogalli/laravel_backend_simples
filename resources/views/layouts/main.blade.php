<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@section('title', 'SH3 API')


<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title')</title>


    <!-- Css bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">

    <!-- Scripts do Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Fontes do Google -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto" rel="stylesheet">

    <!-- Css e js do sistema -->
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="{{ asset('fontawesome-free-6.4.2-web/css/all.min.css') }}">
    <script src="/js/scripts.js"></script>

</head>

<body>
    <header>
        <div class="px-3 py-2 text-bg-dark border-bottom">
            <div class="container">
                <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
                    <a href="/"
                        class="d-flex align-items-center my-2 my-lg-0 me-lg-auto text-white text-decoration-none">
                        <img src="/img/logo.png" alt="Logo" class="logo-img me-2">
                    </a>

                    <ul class="nav col-12 col-lg-auto my-2 justify-content-center my-md-0 text-small">
                        {{-- <li>
                            <a href="#" class="nav-link text-white d-flex flex-column align-items-center">
                                <i class="fa-solid fa-house mb-1"></i>
                                Página Inicial
                            </a>
                        </li>
                        <li>
                            <a href="#" class="nav-link text-white d-flex flex-column align-items-center">
                                <i class="fa-solid fa-pen-to-square mb-1"></i>
                                Histórico de Atendimentos
                            </a>
                        </li>
                        <li class="d-flex align-items-center">
                            <a href="#" class="nav-link text-white d-flex flex-column align-items-center">
                                <i class="fa-solid fa-users mb-1"></i>
                                Clientes e Áreas
                            </a>
                        </li> --}}
                        <li>
                            <a href="/logar" class="btn btn-primary">Entrar</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    @yield('content')

    <div class="container">
        <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top">
            <p class="col-md-4 mb-0 text-body-secondary">&copy; SH3 API Backend by Hugo Galli</p>

            <a href="/"
                class="col-md-4 d-flex align-items-center justify-content-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
                <img src="/img/logo.png" alt="Logo" class="logo-img me-2">
            </a>

            <ul class="nav col-md-4 justify-content-end">
                <li class="nav-item"><a href="/" class="nav-link px-2 text-body-secondary">Início</a></li>

                <form method="POST" action="{{ url('api/logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-link px-2 ">Sair</button>
                </form>

            </ul>
        </footer>
    </div>

</body>

</html>
