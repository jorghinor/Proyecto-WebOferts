<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- <link rel="icon" href="/docs/4.1/assets/img/favicons/favicon.ico"> -->

    <title>Admin Webofertas</title>    

    <!-- Bootstrap core CSS -->
    <link href="{{ url('assets/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="{{ url('asset/dashboard.css')}}" rel="stylesheet">
</head>

<body>
    <div id="app">
        <nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow">
            <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="{{ url('/') }}">Web Ofertas</a>
            <input class="form-control form-control-dark w-100" type="text" placeholder="Search" aria-label="Search" />
            <ul class="navbar-nav px-3">
                <li class="nav-item text-nowrap">
                    <a class="nav-link" 
                        href="{{ route('logout') }}" 
                        onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">
                        {{ __("Logout") }}
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </nav>
        <div class="container-fluid">
            <div class="row">
                <nav class="col-md-2 d-none d-md-block bg-light sidebar">
                    <div class="sidebar-sticky">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link active" href="{{ url('/admin') }}">
                                    <span data-feather="home"></span>
                                    Dashboard <span class="sr-only">(current)</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{url('/admin/negocios')}}">
                                    <span data-feather="briefcase"></span>
                                    Negocios
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{url('/admin/categorias')}}">
                                    <span data-feather="layers"></span>
                                    Categorias
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('/admin/usuarios') }}">
                                    <span data-feather="users"></span>
                                    Usuarios
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('/admin/paquetes') }}">
                                    <span data-feather="package"></span>
                                    Paquetes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('/admin/anuncios') }}">
                                    <span data-feather="bar-chart-2"></span>
                                    Anuncios
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('/admin/productos') }}">
                                    <span data-feather="shopping-cart"></span>
                                    Productos
                                </a>
                            </li>
                        </ul>

                        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                            <span>Saved reports</span>
                            <a class="d-flex align-items-center text-muted" href="#">
                                <span data-feather="plus-circle"></span>
                            </a>
                        </h6>
                        <ul class="nav flex-column mb-2">
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <span data-feather="file-text"></span>
                                    Current month
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <span data-feather="file-text"></span>
                                    Last quarter
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <span data-feather="file-text"></span>
                                    Social engagement
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <span data-feather="file-text"></span>
                                    Year-end sale
                                </a>
                            </li>
                        </ul>
                        <hr>
                        <div class="card">
                            <div class="card-header">{{ __('Dashboard') }}</div>

                            <div class="card-body">
                                @if (session('status'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                                </div>
                                @endif

                                {{ __('You are logged in!') }}
                            </div>
                        </div>
                    </div>
                </nav>

                <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                    @yield('main_section')
                </main>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript    
    ================================================== -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <!--     <script>
        window.jQuery || document.write('<script src="../../assets/js/vendor/jquery-slim.min.js"><\/script>')
    </script> -->
    <script src="{{ url('assets/js/popper.min.js') }}"></script>
    <script src="{{ url('assets/js/bootstrap.min.js') }}"></script>

    <!-- Icons -->
    <script src="https://unpkg.com/feather-icons/dist/feather.min.js"></script>
    <script>
        feather.replace()
    </script>

    <!-- Graphs -->


</body>

</html>
