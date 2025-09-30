<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>@yield('title', 'Partner Panel')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Vite resources -->
  {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
  @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/theme/partner.css'])


  <!-- AdminLTE CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">

  <!-- Select2 CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">

  <!-- Toastr CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

  <!-- Summernote CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs4.min.css">

  <!-- Bootstrap 4 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>




  <style>
  :root{
    --choco:#8c1000;
    --soft-choco:#c12814;
    --ink:#22272b;
    --paper:#f7f7f8;

    /* aksen UI */
    --radius: 12px;
    --shadow: 0 6px 20px rgba(0,0,0,.08);

    /* bootstrap override ringan */
    --primary: var(--choco);
    --secondary: #6b7280;
  }

  /* ===== Layout polish ===== */
  body { background: var(--paper); }

  .card{
    border: 0;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow: hidden;
  }
  .card-header{
    background: #fff;
    border-bottom: 1px solid #eef1f4;
    padding: .85rem 1rem;
  }

  .content-header{
    padding: 18px 0.5rem;
  }
  .content-wrapper{
    background: transparent;
  }

  /* ===== Navbar ===== */
  .main-header.navbar{
    background: #fff !important;
    border-bottom: 1px solid #eef1f4 !important;
    box-shadow: 0 2px 10px rgba(0,0,0,.03);
  }
  .navbar .nav-link{ color: var(--ink); }
  .navbar .nav-link:hover{ color: var(--choco); }

  /* ===== Brand / Sidebar ===== */
  .brand-link{
    background: linear-gradient(135deg,var(--choco),var(--soft-choco)) !important;
    border-bottom: 0;
  }
  .brand-link .brand-image{ background:#fff; }

  .main-sidebar{
    background: #f3f3f3; /* gelap netral agar choco menonjol */
  }
  .sidebar{
    padding-top: .5rem;
  }

  /* item level 1 */
  .nav-sidebar .nav-item > .nav-link{
    border-radius: 10px;
    margin: 4px 8px;
    color: #ac0000;
    transition: .2s ease;
  }
  .nav-sidebar .nav-item > .nav-link:hover{
    background: rgba(208, 178, 178, 0.06);
    color: #710000;
  }
  .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link.active{
    background: linear-gradient(135deg,var(--choco),var(--soft-choco));
    color: #fff;
    border-left: 0;
    box-shadow: 0 6px 16px rgba(207,26,2,.25);
  }

  /* tree level 2+ */
  .nav-sidebar .nav-treeview > .nav-item > .nav-link{
    margin: 2px 16px;
    border-radius: 20px;
    color:#c7cdd6;
  }
  .nav-sidebar .nav-treeview > .nav-item > .nav-link.active{
    background: rgba(219,70,48,.18);
    color:#fff;
  }

  /* user panel garis halus */
  .user-panel{ border-bottom: 1px dashed rgba(255,255,255,.08); }

  /* ===== Buttons / Badges ===== */
  .btn-primary{
    background: var(--choco);
    border-color: var(--choco);
  }
  .btn-primary:hover{
    background: var(--soft-choco);
    border-color: var(--soft-choco);
  }
  .badge-warning.navbar-badge{
    background: var(--soft-choco);
    color:#fff;
  }

  /* ===== Tables / DataTables ===== */
  table.dataTable thead th{
    border-bottom: 2px solid #eef1f4 !important;
  }
  .table thead th{
    background: #fff;
  }

  /* ===== Select2 ===== */
  .select2-container--bootstrap-5 .select2-selection{
    border-radius: 10px;
    border-color: #e5e7eb;
  }
  .select2-container--bootstrap-5 .select2-results__option--highlighted{
    background: var(--soft-choco);
  }

  /* ===== Summernote toolbar ===== */
  .note-toolbar{
    border-radius: 10px;
    border:1px solid #eef1f4;
  }

  /* ===== Footer ===== */
  .main-footer{
    border-top: 1px solid #eef1f4;
    background:#fff;
    border-radius: var(--radius) var(--radius) 0 0;
  }

  /* ===== Utility ===== */
  .bg-choco{ background: var(--choco) !important; }
  .text-choco{ color: var(--choco) !important; }
  .soft-shadow{ box-shadow: var(--shadow); }
  .rounded-2xl{ border-radius: 1rem; }
</style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom-0">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button">
            <i class="fas fa-bars"></i>
          </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
          <a href="{{ route('partner.dashboard') }}" class="nav-link">Dashboard</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
          <a href="#" class="nav-link">Support</a>
        </li>
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
            <span class="dropdown-item dropdown-header">15 Notifications</span>
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

        <!-- Fullscreen Toggle -->
        <li class="nav-item">
          <a class="nav-link" data-widget="fullscreen" href="#" role="button">
            <i class="fas fa-expand-arrows-alt"></i>
          </a>
        </li>

        <!-- User Dropdown Menu -->
        <li class="nav-item dropdown user-menu">
          <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
            <img src="https://adminlte.io/themes/v3/dist/img/user2-160x160.jpg"
              class="user-image img-circle elevation-2" alt="User Image">
            <span class="d-none d-md-inline">
              @auth {{ auth()->user()->name }} @else User Partner @endauth
            </span>
          </a>
          <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
            <!-- User image -->
            <li class="user-header bg-gradient-primary">
              <img src="https://adminlte.io/themes/v3/dist/img/user2-160x160.jpg" class="img-circle elevation-2"
                alt="User Image">
              <p>
                @auth {{ auth()->user()->name }} @else User Partner @endauth
                <small>Member since @auth {{ auth()->user()->created_at->format('M. Y') }} @else User Partner @endauth</small>
              </p>
            </li>
            <!-- Menu Footer-->
            <li class="user-footer">
              <a href="#" class="btn btn-default btn-flat">Profile</a>
              <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-default btn-flat float-right">
                  Sign out
                </button>
              </form>

            </li>
          </ul>
        </li>
      </ul>
    </nav>

    <!-- Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a href="{{ route('partner.dashboard') }}" class="brand-link bg-choco">
        <img src="{{ asset('images/cava-logo2-gradient.png') }}" alt="Cavaa Logo"
                    class="brand-image rounded-full" style="opacity: .8">
        <span class="brand-text font-weight-light">Partner Panel</span>
      </a>

      <!-- Sidebar -->
      <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
          <div class="image">
            <img src="https://adminlte.io/themes/v3/dist/img/user2-160x160.jpg" class="img-circle elevation-2"
              alt="User Image">
          </div>
          <div class="info">
            <a href="#" class="d-block text-choco">
              @auth {{ auth()->user()->name }} @else User Partner @endauth
            </a>
          </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item">
              <a href="{{ route('partner.dashboard') }}"
                class="nav-link @if(Route::is('partner.dashboard')) active @endif">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
              </a>
            </li>

            @php
              $employeeRoutes = ['partner.user-management.*'];
            @endphp

            <li class="nav-item {{ Route::is($employeeRoutes) ? 'menu-open' : '' }}">
              <a href="#" class="nav-link {{ Route::is($employeeRoutes) ? 'active' : '' }}">
                <i class="nav-icon fas fa-users"></i>
                <p>
                  User Management
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{ route('partner.user-management.employees.index') }}"
                    class="nav-link {{ Route::is('partner.user-management.employees.*') ? 'active' : '' }}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Employees</p>
                  </a>
                </li>
                {{-- <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Roles</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Permissions</p>
                  </a>
                </li> --}}
              </ul>
            </li>

            {{-- store --}}
            @php
              $storeRoutes = ['partner.store.*'];
            @endphp

            <li class="nav-item {{ Route::is($storeRoutes) ? 'menu-open' : '' }}">
              <a href="#" class="nav-link {{ Route::is($storeRoutes) ? 'active' : '' }}">
                <i class="nav-icon fas fa-shopping-cart"></i>
                <p>
                  Store
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>

              <ul class="nav nav-treeview">

                {{-- Table Management --}}
                <li
                  class="nav-item {{ Route::is('partner.store.tables.*') || Route::is('partner.store.seat-layouts.*') ? 'menu-open' : '' }}">
                  <a href="#"
                    class="nav-link {{ Route::is('partner.store.tables.*') || Route::is('partner.store.seat-layouts.*') ? 'active' : '' }}">
                    <i class="fas fa-table nav-icon"></i>
                    <p>
                      Table Management
                      <i class="fas fa-angle-left right"></i>
                    </p>
                  </a>

                  <ul class="nav nav-treeview">
                    {{-- Tables --}}
                    <li class="nav-item">
                      <a href="{{ route('partner.store.tables.index') }}"
                        class="nav-link {{ Route::is('partner.store.tables.*') ? 'active' : '' }}">
                        <i class="far fa-dot-circle nav-icon"></i>
                        <p>Tables</p>
                      </a>
                    </li>

                    {{-- Seat Layout --}}
                    {{-- <li class="nav-item">
                      <a href="{{ route('partner.products.index') }}"
                        class="nav-link {{ Route::is('partner.products.*') ? 'active' : '' }}">
                        <i class="far fa-dot-circle nav-icon"></i>
                        <p>Seat Layout</p>
                      </a>
                    </li> --}}
                  </ul>
                </li>

              </ul>
            </li>


            @php
              $productRoutes = ['partner.products.*'];
              $categoryRoutes = ['partner.categories.*'];
            @endphp

            <li class="nav-item {{ Route::is($productRoutes, $categoryRoutes) ? 'menu-open' : '' }}">
              <a href="#" class="nav-link {{ Route::is($productRoutes) ? 'active' : '' }}">
                <i class="nav-icon fas fa-shopping-cart"></i>
                <p>
                  Products
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{ route('partner.products.index') }}"
                    class="nav-link {{ Route::is('partner.products.*') ? 'active' : '' }}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>All Products</p>
                  </a>
                </li>
                {{-- <li class="nav-item">
                  <a href="{{ route('partner.categories.index') }}"
                    class="nav-link {{ Route::is('partner.categories.*') ? 'active' : '' }}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Categories</p>
                  </a>
                </li> --}}

              </ul>
            </li>

            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="nav-icon fas fa-cog"></i>
                <p>Settings</p>
              </a>
            </li>

            <li class="nav-header">REPORTS</li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="nav-icon fas fa-chart-line"></i>
                <p>Sales Analytics</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="nav-icon fas fa-chart-pie"></i>
                <p>Traffic Reports</p>
              </a>
            </li>
          </ul>
        </nav>
      </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
      <!-- Content Header -->
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>@yield('page_title', 'Dashboard')</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('partner.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">@yield('page_title', 'Dashboard')</li>
              </ol>
            </div>
          </div>
        </div>
      </section>

      <!-- Main Content -->
      @yield('content')
    </div>

    <!-- Footer -->
    <footer class="main-footer bg-white">
      <div class="float-right d-none d-sm-block">
        <b>Version</b> 3.2.0
      </div>
      <strong>Copyright &copy; 2024-{{ date('Y') }} <a href="https://vastech.co.id">Vastech.co.id</a>.</strong>
      All rights reserved.
    </footer>
  </div>

  <!-- REQUIRED SCRIPTS -->
  <!-- jQuery -->
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
  <!-- AdminLTE App -->
  <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
  <!-- DataTables -->
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
  <!-- Select2 -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <!-- Toastr -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <!-- Summernote -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs4.min.js"></script>
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
  <!-- Popper.js -->
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>

  <!-- Bootstrap 4 JS Bundle (includes Popper) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>

  <!-- CDN SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


  <script>
    $(function () {
      // Initialize DataTable
      $('.datatable').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
      });

      // Initialize Select2
      $('.select2').select2({
        theme: 'bootstrap-5'
      });

      // Initialize Summernote
      $('.summernote').summernote({
        height: 200,
        toolbar: [
          ['style', ['style']],
          ['font', ['bold', 'underline', 'clear']],
          ['color', ['color']],
          ['para', ['ul', 'ol', 'paragraph']],
          ['table', ['table']],
          ['insert', ['link', 'picture', 'video']],
          ['view', ['fullscreen', 'codeview', 'help']]
        ]
      });

      // Toastr notification example
      @if(session('success'))
        toastr.success('{{ session('success') }}');
      @endif
      @if(session('error'))
        toastr.error('{{ session('error') }}');
      @endif
        // Example chart
        var ctx = document.getElementById('myChart').getContext('2d');
      var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: ['January', 'February', 'March', 'April', 'May', 'June'],
          datasets: [{
            label: 'Sales 2023',
            data: [12, 19, 3, 5, 2, 3],
            backgroundColor: [
              'rgba(255, 99, 132, 0.2)',
              'rgba(54, 162, 235, 0.2)',
              'rgba(255, 206, 86, 0.2)',
              'rgba(75, 192, 192, 0.2)',
              'rgba(153, 102, 255, 0.2)',
              'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
              'rgba(255, 99, 132, 1)',
              'rgba(54, 162, 235, 1)',
              'rgba(255, 206, 86, 1)',
              'rgba(75, 192, 192, 1)',
              'rgba(153, 102, 255, 1)',
              'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
          }]
        },
        options: {
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });
    });
  </script>
  <style>
        .sidebar-dark-primary .nav-sidebar > .nav-header {
            color: #8c1000;
        }

        /* level 1 (Store) */
        .nav-sidebar .nav-item {
            background-color: #eae4e4;
            color: #ffffff;
        }

        /* level 2 (Table Management) */
        .nav-sidebar .nav-treeview>.nav-item {
            background-color: #8c1000;
            /* lebih terang */
            color: #fff;
            margin-left: 20px;
            border-radius: 20px;
        }

        /* level 3 (Tables, Seat Layout) */
        .nav-sidebar .nav-treeview .nav-treeview>.nav-item {
            margin-left: 10px;
        }

        /* link active */

        /* level 1 (Store) */
        .nav-sidebar .nav-item>.nav-link.active {
            background-color: #343a40;
            /* abu tua */
            color: #ffffff;
        }

        /* level 2 (Table Management) */
        .nav-sidebar .nav-treeview>.nav-item>.nav-link.active {
            background: linear-gradient(90deg, #bf0303, #620000);
            /* lebih terang */
            color: #ffffff;
        }

        .nav-sidebar .nav-treeview>.nav-item>.nav-link.active:hover {
            color: #d79805;
        }

        /* level 3 (Tables, Seat Layout) */
        .nav-sidebar .nav-treeview .nav-treeview>.nav-item>.nav-link.active {
            background-color: #6e7d6c;
            /* lebih cerah lagi */
            color: #fff;
        }

        #toast-container > .toast {
            border-radius: 10px;
            box-shadow: var(--shadow);
        }
        #toast-container > .toast-success { background-color: var(--choco); }
        .swal2-popup{
            border-radius: 14px !important;
        }
        .swal2-confirm{
            background: var(--choco) !important;
            border: none !important;
        }

    </style>

  @yield('scripts')
  @stack('scripts')
</body>

</html>