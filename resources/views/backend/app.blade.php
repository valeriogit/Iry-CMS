<!DOCTYPE html>

<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    {!! CookieConsent::printCookie() !!}

    {!! GoogleAnalytics::printCode() !!}

    @if(session()->has('downloadPlugin'))
        <meta http-equiv="refresh" content="5;url={{ asset('/tmp/'.session('downloadPlugin')) }}">
        <meta http-equiv="refresh" content="5;url={{ asset('/tmp/'.session('downloadPlugin')) }}">
    @endif

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">

  @yield('title')

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="{{ asset('css/fontawesome-free/css/all.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('css/adminlte/adminlte.min.css') }}">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <!-- Sweet Alert -->
  <link rel="stylesheet" href="{{ asset('css/sweetalert2/bootstrap-4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/backend/backend.css') }}">

  <!-- Important for backend & frontend -->
  <link rel="stylesheet" href="{{ asset('global/css/global.css') }}">
  @yield('css')
</head>
<body class="hold-transition sidebar-mini layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="{{route('push')}}" class="btn btn-outline-primary btn-block">Make a Push Notification!</a>
      </li><li class="nav-item d-none d-sm-inline-block">
        <a href="{{ url('/') }}" class="nav-link">Show site</a>
      </li>
      @if(Roles::checkRole(['administrator']))
        <li class="nav-item d-none d-sm-inline-block">
            <a style="cursor:pointer" class="nav-link" onclick="checkUpdate();">Check Update</a>
        </li>
      @endif
    </ul>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
          <span class="badge badge-warning navbar-badge">15</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-header">15 Notifications</span>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-envelope mr-2"></i> 4 new messages
            <span class="float-right text-muted text-sm">3 mins</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-users mr-2"></i> 8 friend requests
            <span class="float-right text-muted text-sm">12 hours</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-file mr-2"></i> 3 new reports
            <span class="float-right text-muted text-sm">2 days</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
        </div>
      </li>
      <li class="navbar-nav">
        <a class="nav-link" href="{{ action('AccessoController@Logout') }}">
            <i class="fas fa-power-off"></i>
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ action('BackendController@index') }}" class="brand-link">
      <img src="{{ asset('img').$config->icoSite }}" alt="{{ $config->nameSite }}" class="brand-image elevation-3"
           style="opacity: .8;max-height: 42.5px !important;margin: .25rem .5rem 0 .5rem !important">
      <span class="brand-text font-weight-light">
        <b>{{ $config->nameSite }}</b>
        <br>
        <p class="brand-text" style="font-size: .85rem!important;margin-bottom:0!important">Iry CMS - v.{{ $config->iryVersion }}</p>
      </span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar" style="padding-top: 10px;">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-4 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{ asset('img').Auth::user()->avatar }}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="{{ action('ProfileController@getProfile') }}" class="d-block">{{ Auth::user()->username }}</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item">
            <a href="{{ action('BackendController@index') }}" class="nav-link parent-hover-animation @if($activePage=='admin') active @endif">
                <i class="nav-icon shake-icon fas fa-home "></i>
              <p>
                Dashboard
              </p>
            </a>
          </li>
        @if(Roles::checkRoleId([1,2,3,4]))
        <li class="nav-item @if($activePage=='post' || $activePage=='managePost') menu-open @endif ">
            <a href="#" class="nav-link parent-hover-animation @if($activePage=='post' || $activePage=='managePost') active @endif">
            <i class="nav-icon shake-icon fas fa-book-reader"></i>
            <p>
                Post
                <i class="right fas fa-angle-left"></i>
            </p>
            </a>
            <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{{ action('ProfileController@showUser') }}" class="nav-link parent-hover-animation @if($activePage=='managePost') active @endif">
                    <i class="nav-icon shake-icon fas fa-book subList"></i>
                    <p>
                        Manage Post
                    </p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ action('PostController@createPost') }}" class="nav-link parent-hover-animation @if($activePage=='post') active @endif">
                    <i class="nav-icon shake-icon fas fa-file-medical subList"></i>
                    <p>
                        Create Post
                    </p>
                </a>
            </li>
            </ul>
        </li>
        @endif
        @if(Roles::checkRoleId([1]))
            <li class="nav-item">
                <a href="{{ action('SettingsController@index') }}" class="nav-link parent-hover-animation @if($activePage=='settings') active @endif">
                    <i class="nav-icon shake-icon fas fa-cogs "></i>
                <p>
                    Settings
                </p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ action('PluginController@show') }}" class="nav-link parent-hover-animation @if($activePage=='plugin') active @endif">
                    <i class="nav-icon shake-icon fas fa-puzzle-piece "></i>
                    <p>
                        Plugins
                    </p>
                </a>
            </li>
            <li class="nav-item @if($activePage=='profile' || $activePage=='manageUser') menu-open @endif ">
                <a href="#" class="nav-link parent-hover-animation @if($activePage=='profile' || $activePage=='manageUser') active @endif">
                <i class="nav-icon shake-icon fas fa-users"></i>
                <p>
                    Users
                    <i class="right fas fa-angle-left"></i>
                </p>
                </a>
                <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ action('ProfileController@showUser') }}" class="nav-link parent-hover-animation @if($activePage=='manageUser') active @endif">
                        <i class="nav-icon shake-icon fas fa-user-cog subList"></i>
                        <p>
                            Manage Users
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ action('ProfileController@getProfile') }}" class="nav-link parent-hover-animation @if($activePage=='profile') active @endif">
                        <i class="nav-icon shake-icon fas fa-user subList"></i>
                        <p>
                            Profile
                        </p>
                    </a>
                </li>
                </ul>
            </li>
            <li class="nav-item @if($activePage=='menu' || $activePage=='manageMenu') menu-open @endif ">
                <a href="#" class="nav-link parent-hover-animation @if($activePage=='menu' || $activePage=='manageMenu') active @endif">
                <i class="nav-icon shake-icon fab fa-elementor"></i>
                <p>
                    Menu
                    <i class="right fas fa-angle-left"></i>
                </p>
                </a>
                <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ action('MenuController@showMenu') }}" class="nav-link parent-hover-animation @if($activePage=='manageMenu') active @endif">
                        <i class="nav-icon shake-icon fas fa-th-list subList"></i>
                        <p>
                            Manage Menu
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ action('MenuController@createMenu') }}" class="nav-link parent-hover-animation @if($activePage=='menu') active @endif">
                        <i class="nav-icon shake-icon fas fa-plus-circle subList"></i>
                        <p>
                            Create Menu
                        </p>
                    </a>
                </li>
                </ul>
            </li>
        @else
            <li class="nav-item">
                <a href="{{ action('ProfileController@getProfile') }}" class="nav-link parent-hover-animation @if($activePage=='profile') active @endif">
                    <i class="nav-icon shake-icon fas fa-user "></i>
                    <p>
                        Profile
                    </p>
                </a>
            </li>
        @endif
        @foreach(Menu::getMenuBackend() as $item)
            @if(Roles::checkRoleMenu($item->roles))
                <li class="nav-item">
                    <a href="{{ $item->url }}" class="nav-link parent-hover-animation @if($activePage==$item->slug) active @endif">
                        <i class="nav-icon shake-icon {{ $item->icon }} "></i>
                    <p>
                        {{$item->name}}
                    </p>
                    </a>
                </li>
            @endif
        @endforeach
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>


  @yield('content')


    <!-- Main Footer -->
    <footer class="main-footer">
      <!-- To the right -->
      <div class="float-right d-none d-sm-inline">
        <b>Version</b> {{ $config->iryVersion }}
      </div>
      <!-- Default to the left -->
      <strong>Copyright &copy; 2014-2019 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
    </footer>
  </div>
  <!-- ./wrapper -->

  <!-- REQUIRED SCRIPTS -->

  <!-- jQuery -->
  <script src="{{ asset('js/jquery/jquery.min.js') }}"></script>
  <!-- Bootstrap 4 -->
  <script src="{{ asset('js/bootstrap/bootstrap.bundle.min.js') }}"></script>
  <!-- AdminLTE App -->
  <script src="{{ asset('js/adminlte/adminlte.min.js') }}"></script>
  <!-- Sweet Alert -->
  <script src="{{ asset('js/sweetalert2/sweetalert2.min.js') }}"></script>
  <!-- bs-custom-file-input -->
  <script src="{{ asset('js/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>

  @if($config->webPush == 1)
    <!-- webPush -->
    <script @if($config->cookieBanner == 1) type="text/plain" cookie-consent="tracking" @endif src="{{ asset('js/enable-push.js') }}"></script>
  @endif

  @yield('js')

  <script type="text/javascript">
    function checkUpdate()
    {
      const Toast = Swal.mixin({
        toast: true,
        position: 'top',
        showConfirmButton: false,
        timer: 3000,
        width: '50rem',
      });

      $.get("/admin/update", function(status){
        if(status==true){
          Swal.queue([{
            title: 'Check Update',
            confirmButtonText: 'Go Update',
            showCancelButton: true,
            text:
              'Update available - Download & Install it?',
            showLoaderOnConfirm: true,
            preConfirm: () => {
              $.post("/admin/update", {_token:"{{csrf_token()}}"}, function(status){
                Swal.queue([{
                  title: 'Updated',
                  confirmButtonText: 'Iry Updated!',
                  text:
                    'Thanks for updated Iry <3',
                  }]);
              });
            }
          }])
        }
        else {
          Swal.queue([{
            title: 'Check Update',
            confirmButtonText: 'OK',
            text:
              'No update available',
          }])
        }
      });
    }
  </script>

  {!! ReCaptcha::printJS() !!}

@yield('script')

<!-- Important for backend & frontend -->
<link rel="stylesheet" href="{{ asset('global/js/global.js') }}">

</body>
</html>
