@php
    use Illuminate\Support\Facades\Auth;
    $authUser = Auth::user();
@endphp
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/logo-icon.png') }}">
    <title>POS-Kasir</title>
    
    <!-- MDI Icons -->
    <link rel="stylesheet" href="{{ asset('assets/mdi/css/materialdesignicons.min.css') }}">
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="{{ asset('assets/flatpickr/flatpickr.min.css') }}">

    <!-- Bootstrap CSS (GUNAKAN VERSI STABIL) -->
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
    
    <!-- Custom CSS -->
    <link href="{{ asset('assets/libs/chartist/dist/chartist.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('dist/css/style.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout-custom.css') }}">

    @yield('css')


    @yield('plugin')
</head>
<body>
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full" data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <header class="topbar" data-navbarbg="skin5">
            <nav class="navbar top-navbar navbar-expand-md navbar-dark">
                <div class="navbar-header" data-logobg="skin5">
                    <a class="navbar-brand" href="{{ url('/') }}">                                            
                        <span class="logo-text">                          
                            <img src="{{ asset('assets/images/icon.ico') }}" alt="Logo" class="logo-icon"> Aplikasi POS                             
                        </span>
                    </a>
                    <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"><i class="ti-menu ti-close"></i></a>
                </div>
                <div class="navbar-collapse collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav float-left mr-auto">
                        <a class="navbar-brand" href="{{ url('/dashboard') }}"></a>                   
                    </ul>

                    <!-- Tambahan: Info kasir dan waktu sekarang -->
                    <ul class="navbar-nav float-right d-flex align-items-center me-3" style="font-size: 1.1rem; gap: 1rem;">
                        @if(Auth::check())
                        <li class="nav-item text-white me-3" style="font-weight: 500;">
                            <i class="mdi mdi-account fs-5"></i> 
                            <span>User: <strong>{{ Auth::user()->nama }}</strong></span>
                        </li>
                        @endif
                        <li class="nav-item text-white me-3" style="font-weight: 500;">
                            <i class="mdi mdi-calendar fs-5"></i> 
                            <span id="tanggal-sekarang"></span>
                        </li>
                        <li class="nav-item text-white" style="font-weight: 500;">
                            <i class="mdi mdi-clock-outline fs-5"></i> 
                            <span id="jam-sekarang"></span>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <aside class="left-sidebar" data-sidebarbg="skin6">
            <div class="scroll-sidebar">
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <!-- User Profile-->
                        <li>
                            <div class="user-profile d-flex no-block dropdown m-t-20"> 
                                <div class="user-content hide-menu m-l-10">
                                    <a href="javascript:void(0)" class="" id="Userdd" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        @if(Auth::check())
                                        <h5 class="m-b-0 user-name font-medium">{{ Auth::user()->nama }}<i class="ml-2 fa fa-angle-down"></i></h5>
                                        @endif
                                        <span class="op-5 user-email"></span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="Userdd">						                                       										
                                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" ><i class="fa fa-power-off m-r-5 m-l-5"></i> Logout</a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            @csrf
                                        </form>
                                    </ul>
                                </div>
                            </div>
                        </li>

                        <!-- Home -->
                        <li class="sidebar-item mt-3">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ url('/') }}">
                                <i class="mdi mdi-home-outline"></i>
                                <span class="hide-menu">Dashboard</span>
                            </a>
                        </li>

                        <!-- Kasir -->
                        <li class="sidebar-item mt-3">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('kasir.index') }}">
                                <i class="mdi mdi-cart-outline"></i>
                                <span class="hide-menu">Kasir</span>
                            </a>
                        </li>

                        <!-- Product -->
                        <li class="sidebar-item mt-3">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" data-bs-toggle="collapse" href="#productMenu">
                                <i class="mdi mdi-package-variant-closed"></i>
                                <span class="hide-menu">Product</span>
                            </a>
                            <ul id="productMenu" class="collapse first-level">
                                <li class="sidebar-item mt-3">
                                    <a href="{{ route('kategori.index') }}" class="sidebar-link">
                                        <i class="mdi mdi-shape-outline"></i>
                                        <span class="hide-menu">Kategori</span>
                                    </a>
                                </li>
                                <li class="sidebar-item mt-3">
                                    <a href="{{ route('produk.index') }}" class="sidebar-link">
                                        <i class="mdi mdi-package-variant-closed"></i>
                                        <span class="hide-menu">Items</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Stock -->
                        <li class="sidebar-item mt-3">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" data-bs-toggle="collapse" href="#transaksiMenu">
                                <i class="mdi mdi-database-outline"></i>
                                <span class="hide-menu">Stock</span>
                            </a>
                            <ul id="transaksiMenu" class="collapse first-level">
                                <li class="sidebar-item">
                                    <a href="{{ route('stock_logs.index') }}" class="sidebar-link">
                                        <i class="mdi mdi-database-import"></i>
                                        <span class="hide-menu">Stock Logs</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Settlement -->
                        <li class="sidebar-item mt-3">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" data-bs-toggle="collapse" href="#settlementMenu">
                                <i class="mdi mdi-book-open-page-variant"></i>
                                <span class="hide-menu">Settlement</span>
                            </a>
                            <ul id="settlementMenu" class="collapse first-level">
                                <li class="sidebar-item">
                                    <a href="{{ route('settlement.menu') }}" class="sidebar-link">
                                        <i class="mdi mdi-book-open-page-variant"></i>
                                        <span class="hide-menu">Settlement Shift</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="{{ route('settlement.history') }}" class="sidebar-link">
                                        <i class="mdi mdi-history"></i>
                                        <span class="hide-menu">History</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Laporan -->
                        <li class="sidebar-item mt-3">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" data-bs-toggle="collapse" href="#laporanMenu">
                                <i class="mdi mdi-chart-bar"></i>
                                <span class="hide-menu">Laporan</span>
                            </a>
                            <ul id="laporanMenu" class="collapse first-level">
                                <li class="sidebar-item">
                                    <a href="{{ route('laporan.index') }}" class="sidebar-link">
                                        <i class="mdi mdi-file-document-outline"></i>
                                        <span class="hide-menu">Transaksi Penjualan</span>
                                    </a>
                                </li>
                                <li class="sidebar-item mt-3">
                                    <a href="{{ route('laporan.summary') }}" class="sidebar-link">
                                        <i class="mdi mdi-chart-bar"></i>
                                        <span class="hide-menu">Summary Penjualan</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Pengaturan -->
                        <li class="sidebar-item mt-3">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" data-bs-toggle="collapse" href="#pengaturanMenu">
                                <i class="mdi mdi-cog-outline"></i>
                                <span class="hide-menu">Pengaturan</span>
                            </a>
                            <ul id="pengaturanMenu" class="collapse first-level">
                                <li class="sidebar-item mt-3">
                                    <a href="{{ route('user.index') }}" class="sidebar-link">
                                        <i class="mdi mdi-account-cog-outline"></i>
                                        <span class="hide-menu">Pengaturan User</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Logout -->
                        <li class="sidebar-item mt-3">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="mdi mdi-logout"></i>
                                <span class="hide-menu">Keluar</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>
        <!-- ============================================================== -->
        <!-- End Left Sidebar - style you can find in sidebar.scss -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <div class="page-breadcrumb">
                <div class="row align-items-center">
                    <div class="col-5">
                        <h4 class="page-title">Dashboard</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    @yield('breadcrumb')                                  
                                </ol>
                            </nav>
                        </div>
                    </div>             
                </div>
            </div>
            <div class="container-fluid">
                @yield('content')
            </div>
            <footer class="footer text-center">
            </footer>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- All Jquery & Bootstrap JS -->
    <!-- ============================================================== -->
    <!-- PENTING: Pemuatan JavaScript yang Benar -->
    <!-- jQuery (diperlukan oleh template lama Anda) -->
    <script src="{{ asset('assets/libs/jquery/dist/jquery.min.js') }}"></script>
    
    <!-- Bootstrap Bundle JS (Popper.js sudah termasuk) -->
    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- UI & Custom JS -->
    <script src="{{ asset('assets/libs/popper.js/dist/umd/popper.min.js') }}"></script>
    <script src="{{ asset('dist/js/app-style-switcher.js') }}"></script>
    <script src="{{ asset('dist/js/waves.js') }}"></script>
    <script src="{{ asset('dist/js/sidebarmenu.js') }}"></script>
    <script src="{{ asset('dist/js/custom.js') }}"></script>

    <!-- Optional Libraries -->
    <script src="{{ asset('assets/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/libs/chartist/dist/chartist.min.js') }}"></script>
    <script src="{{ asset('assets/libs/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js') }}"></script>

    <!-- SweetAlert -->
    <script src="{{ asset('vendor/sweetalert/sweetalert.all.js') }}"></script>
    @include('sweetalert::alert')

    @yield('sweet')
    @yield('js')
    @yield('script')

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        function updateWaktu() {
            const sekarang = new Date();
            const optionsTanggal = { year: 'numeric', month: 'long', day: 'numeric' };
            const tanggal = sekarang.toLocaleDateString('id-ID', optionsTanggal);

            let jam = sekarang.getHours();
            let menit = sekarang.getMinutes();
            let detik = sekarang.getSeconds();
            const ampm = jam >= 12 ? 'PM' : 'AM';
            jam = jam % 12;
            jam = jam ? jam : 12;
            if (jam < 10) jam = '0' + jam;
            if (menit < 10) menit = '0' + menit;
            if (detik < 10) detik = '0' + detik;
            const waktu = `${jam}:${menit}:${detik} ${ampm}`;

            document.getElementById('tanggal-sekarang').textContent = tanggal;
            document.getElementById('jam-sekarang').textContent = waktu;
        }

        updateWaktu();
        setInterval(updateWaktu, 1000);
    });

        document.addEventListener('DOMContentLoaded', () => {
        const sidebarLinks = document.querySelectorAll('[data-sidebarbg="skin6"] .sidebar-link');

        sidebarLinks.forEach(link => {
            link.addEventListener('click', () => {
                sidebarLinks.forEach(l => l.classList.remove('active'));
                link.classList.add('active');
            });
        });

        // Highlight berdasarkan URL saat load halaman
        const currentPath = window.location.pathname;
        sidebarLinks.forEach(link => {
            if (link.getAttribute('href') === currentPath) {
                link.classList.add('active');
            }
        });
    });

    </script>
</body>
</html>