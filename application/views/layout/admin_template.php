<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Magang BPS | <?= $title ?? 'Dashboard' ?></title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/fontawesome-free/css/all.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/adminlte/dist/css/adminlte.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') ?>">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

  <style>
    .brand-link { background-color: #1e3a8a !important; }
    .sidebar-dark-primary { background-color: #0f172a !important; }
    .nav-pills .nav-link.active, .nav-pills .show>.nav-link { background-color: #0099CC !important; }
  </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link text-danger font-weight-bold" href="<?= base_url('auth/logout') ?>">
          <i class="fas fa-sign-out-alt mr-2"></i> Logout
        </a>
      </li>
    </ul>
  </nav>

  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link">
      <img src="<?= base_url('assets/img/logo.png') ?>" alt="BPS" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light text-white">Magang BPS</span>
    </a>

    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
           <img src="https://ui-avatars.com/api/?name=<?= urlencode($this->session->userdata('username')) ?>&background=random" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block text-white"><?= ucfirst($this->session->userdata('role')) ?></a>
        </div>
      </div>

      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
          
          <?php if($this->session->userdata('role') == 'admin'): ?>
            <li class="nav-item">
              <a href="<?= base_url('admin') ?>" class="nav-link <?= ($this->uri->segment(1)=='admin' && $this->uri->segment(2)=='')?'active':'' ?>">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?= base_url('admin/broadcast') ?>" class="nav-link <?= ($this->uri->segment(2)=='broadcast')?'active':'' ?>">
                <i class="nav-icon fab fa-whatsapp"></i>
                <p>Broadcast WA</p>
              </a>
            </li>
            <li class="nav-header">MASTER DATA</li>
            <li class="nav-item">
                <a href="<?= base_url('admin/master_kampus') ?>" class="nav-link <?= ($this->uri->segment(2)=='master_kampus')?'active':'' ?>">
                    <i class="nav-icon fas fa-university"></i>
                    <p>Data Kampus</p>
                </a>
            </li>
             <li class="nav-item">
                <a href="<?= base_url('admin/master_jurusan') ?>" class="nav-link <?= ($this->uri->segment(2)=='master_jurusan')?'active':'' ?>">
                    <i class="nav-icon fas fa-book"></i>
                    <p>Data Jurusan</p>
                </a>
            </li>
          <?php endif; ?>

          <?php if($this->session->userdata('role') == 'peserta'): ?>
             <li class="nav-item">
              <a href="<?= base_url('peserta') ?>" class="nav-link <?= ($this->uri->segment(1)=='peserta' && $this->uri->segment(2)=='')?'active':'' ?>">
                <i class="nav-icon fas fa-home"></i>
                <p>Dashboard</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?= base_url('peserta/absen_area') ?>" class="nav-link <?= ($this->uri->segment(2)=='absen_area')?'active':'' ?>">
                <i class="nav-icon fas fa-camera"></i>
                <p>Absensi</p>
              </a>
            </li>
          <?php endif; ?>

        </ul>
      </nav>
    </div>
  </aside>

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"><?= $title ?? '' ?></h1>
          </div>
        </div>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
        <?= $content ?>
      </div>
    </section>
  </div>

  <footer class="main-footer">
    <strong>&copy; 2025 <a href="#">BPS Provinsi Banten</a>.</strong>
  </footer>
</div>

<script src="<?= base_url('assets/adminlte/plugins/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/dist/js/adminlte.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  $(function () {
    // Datatable Init
    $("#example1, .datatable-init").DataTable({
      "responsive": true, "autoWidth": false,
    });

    // SweetAlert Flashdata
    <?php if ($this->session->flashdata('success')): ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: '<?= $this->session->flashdata('success') ?>',
            timer: 3000,
            showConfirmButton: false
        });
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: '<?= $this->session->flashdata('error') ?>'
        });
    <?php endif; ?>
  });
</script>

</body>
</html>
