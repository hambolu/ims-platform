<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>KaltaniIms</title>

    <!-- General CSS Files -->
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.1.1/css/all.css">
    <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap/css/bootstrap.min.css') }}">
    <!-- <link rel="stylesheet" href="{{ asset('assets/modules/fontawesome/css/all.min.css') }}"> -->

    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('assets/modules/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/weather-icon/css/weather-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/weather-icon/css/weather-icons-wind.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/summernote/summernote-bs4.css') }}">

    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
    <!-- Start GA -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <!-- /END GA -->
</head>

<body>
    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            <div class="navbar-bg"></div>
            <nav class="navbar navbar-expand-lg main-navbar">
                <form class="form-inline mr-auto">
                    <ul class="navbar-nav mr-3">
                        <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i
                                    class="fas fa-bars"></i></a></li>
                        <li><a href="#" data-toggle="search" class="nav-link nav-link-lg d-sm-none"><i
                                    class="fas fa-search"></i></a></li>
                    </ul>
                    <div class="search-element">
                       

                    </div>
                </form>
                <ul class="navbar-nav navbar-right">
                    
                    <li ><a href="#" 
                            class="nav-link nav-link-lg nav-link-user">
                            <img alt="image" src="assets/img/avatar/avatar-1.png" class="rounded-circle mr-1">
                            <div class="d-sm-none d-lg-inline-block">{{Auth::user()->first_name}}</div>
                        </a>
                        
                    </li>
                </ul>
            </nav>
            <div class="main-sidebar sidebar-style-2">
                <aside id="sidebar-wrapper">
                    <div class="sidebar-brand">
                        <a href="/dashboard">
                        <img src="{{ asset('img/logo.png')}}" class="img-fluid" width="90px" alt="KaltaniIms" srcset="">
                        </a>
                    </div>
                    <div class="sidebar-brand sidebar-brand-sm">
                        <a href="/dashboard">
                            <img src="{{ asset('img/logo.png')}}" class="img-fluid" alt="KaltaniIms" srcset="">
                        </a>
                    </div>
                    <ul class="sidebar-menu">
                        <li>
                            <a class="nav-link" href="/dashboard"><i class="fas fa-solid fa-grid-horizontal fa-3x"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i
                                    class="fas fa-columns"></i>
                                <span>Items</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link" href="/item">Create Items</a></li>
                                <li><a class="nav-link" href="/bailing_item"> Create Bailing Items</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a class="nav-link has-dropdown" href="#" data-toggle="dropdown"><i class="fas fa-solid fa-map-location fa-3x"></i>
                                <span>Collections</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link" href="/locations">Create Collection Center</a></li>
                                <li><a class="nav-link" href="/addCollection">Add Collection</a></li>
                            </ul>
                        </li>
                        <li>
                            <a class="nav-link" href="/sorting"><i class="fas fa-solid fa-filter-list fa-3x"></i>
                                <span>Sorting</span>
                            </a>
                        </li>
                        <li>
                            <a class="nav-link" href="/bailing"><i class="fas fa-solid fa-grid-2-plus fa-3x"></i>
                                <span>Bailing</span>
                            </a>
                        </li>
                        <li>
                            <a class="nav-link" href="/transfer"><i
                                    class="fas fa-solid fa-arrow-right-arrow-left fa-3x"></i>
                                <span>Transfer</span>
                            </a>
                        </li>
                        <li>
                            <a class="nav-link" href="/factory"><i
                                    class="fas fa-solid fa-industry-windows fa-3x"></i>
                                <span>Factory</span>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a class="nav-link has-dropdown" href="/users" data-toggle="dropdown"><i class="fas fa-solid fa-users fa-3x"></i>
                                <span>Users</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link" href="/users">Create Users</a></li>
                                <li><a class="nav-link" href="/manage/role">Create Role</a></li>
                            </ul>
                        </li>
                        <li>
                            <a class="nav-link" href="/recycle"><i class="fas fa-solid fa-recycle fa-3x"></i>
                                <span>Recycle</span>
                            </a>
                        </li>
                        <li>
                            <a class="nav-link" href="/sales"><i class="fas fa-solid fa-messages-dollar fa-3x"></i>
                                <span>Sales</span>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a class="nav-link has-dropdown" href="#"><i
                                    class="fas fa-solid fa-file-chart-column fa-3x"></i>
                                <span>Report</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link" href="/collection_report">Collection Report</a></li>
                                <li><a class="nav-link" href="/bailed_report">Bailing Report</a></li>
                                <li><a class="nav-link" href="/sorting_report">Sorting Report</a></li>
                                <li><a class="nav-link" href="/transfered_report">Transfered Report</a></li>
                                <li><a class="nav-link" href="/recycled_report">Recycled Report</a></li>
                                <li><a class="nav-link" href="/sales_report">Sales Report</a></li>
                            </ul>
                        </li>
                        <li>
                            <a class="nav-link" href="/logout"><i
                                    class="fas fa-solid fa-arrow-right-from-bracket fa-3x"></i>
                                <span>Logout</span>
                            </a>
                        </li>


                    </ul>

                </aside>
            </div>

            <!-- Dashboard wrapper start -->
           
                



                @yield('content')

                <footer class="main-footer">
                    <div class="footer-left">
                        Copyright &copy; 2022
                    </div>
                    <div class="footer-right">

                    </div>
                </footer>
            </div>
        </div>
        
        
            <script>
                
                $(document).ready(function(){
                      $(".alert").delay(5000).slideUp(300);
                });
            
                </script>
        <!-- General JS Scripts -->
        <script src="{{ asset('assets/modules/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/modules/popper.js') }}"></script>
        <script src="{{ asset('assets/modules/tooltip.js') }}"></script>
        <script src="{{ asset('assets/modules/bootstrap/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('assets/modules/nicescroll/jquery.nicescroll.min.js') }}"></script>
        <script src="{{ asset('assets/modules/moment.min.js') }}"></script>
        <script src="{{ asset('assets/js/stisla.js') }}"></script>

        <!-- JS Libraies -->
        <script src="{{ asset('assets/modules/simple-weather/jquery.simpleWeather.min.js') }}"></script>
        <script src="{{ asset('assets/modules/chart.min.js') }}"></script>
        <script src="{{ asset('assets/modules/jqvmap/dist/jquery.vmap.min.js') }}"></script>
        <script src="{{ asset('assets/modules/jqvmap/dist/maps/jquery.vmap.world.js') }}"></script>
        <script src="{{ asset('assets/modules/summernote/summernote-bs4.js') }}"></script>
        <script src="{{ asset('assets/modules/chocolat/dist/js/jquery.chocolat.min.js') }}"></script>

        <!-- Page Specific JS File -->
        <script src="{{ asset('assets/js/page/index-0.js') }}"></script>

        <!-- Template JS File -->
        <script src="{{ asset('assets/js/scripts.js') }}"></script>
        <script src="{{ asset('assets/js/custom.js') }}"></script>
</body>

</html>
