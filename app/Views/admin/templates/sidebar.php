<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

<!-- Sidebar - Brand -->
<a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
    <div class="sidebar-brand-icon rotate-n-15">
        <i class="fas fa-home"></i>           
    </div>
    <div class="sidebar-brand-text mx-3">Admin PTSP</div>
</a>

<?php if(in_groups('admin')): ?>

    <hr class="sidebar-divider my-0">

    <li class="nav-item active">
        <a class="nav-link" href="<?= base_url('admin/dashboard'); ?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">Magang & Penelitian</div>

    <!-- Collapse Magang -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuMagang" aria-expanded="false">
            <i class="fas fa-fw fa-user-graduate"></i>
            <span>Magang</span>
        </a>
        <div id="menuMagang" class="collapse">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?= base_url('admin/manage-pendaftaran'); ?>">Data Pendaftar</a>
                <a class="collapse-item" href="<?= base_url('admin/manage-seleksi'); ?>">Seleksi Pendaftar</a>
                <a class="collapse-item" href="<?= base_url('admin/manage-pendaftaran-gagal'); ?>">Pendaftaran Ditolak</a>
                <a class="collapse-item" href="<?= base_url('admin/manage-validasi-konfirmasi'); ?>">Approval Konfirmasi</a>
                <a class="collapse-item" href="<?= base_url('admin/manage-kelengkapan-berkas'); ?>">Kelengkapan Berkas</a>
                <a class="collapse-item" href="<?= base_url('admin/manage-hasil-tes'); ?>">Hasil Tes Safety</a>
                <a class="collapse-item" href="<?= base_url('admin/manage-magang'); ?>">Peserta Magang</a>
                <a class="collapse-item" href="<?= base_url('admin/manage-feedback'); ?>">Feedback</a>
                <a class="collapse-item" href="<?= base_url('admin/manage-alumni'); ?>">Alumni Magang</a>
            </div>
        </div>
    </li>

    <!-- Collapse Penelitian -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuPenelitian" aria-expanded="false">
            <i class="fas fa-fw fa-flask"></i>
            <span>Penelitian</span>
        </a>
        <div id="menuPenelitian" class="collapse">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?= base_url('admin/manage-penelitian'); ?>">Data Pendaftar</a>
                <a class="collapse-item" href="<?= base_url('admin/penelitian/validasi-konfirmasi'); ?>">Approval Konfirmasi</a>
                <a class="collapse-item" href="<?= base_url('admin/penelitian/kelengkapan-berkas'); ?>">Kelengkapan Berkas</a>
                <a class="collapse-item" href="<?= base_url('admin/manage-peserta-penelitian'); ?>">Peserta Penelitian</a>
            </div>
        </div>
    </li>


    <!-- Divider -->
    <hr class="sidebar-divider">


    <!-- Heading -->
    <div class="sidebar-heading">Setting</div>

    <!-- Konfigurasi Program -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuProgram">
            <i class="fas fa-fw fa-sliders-h"></i>
            <span>Konfigurasi Program</span>
        </a>
        <div id="menuProgram" class="collapse">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?= base_url('admin/kelola-lowongan'); ?>">Lowongan</a>
                <a class="collapse-item" href="<?= base_url('admin/kuota-unit'); ?>">Kuota Magang</a>
                <a class="collapse-item" href="<?= base_url('admin/jurusan-unit'); ?>">Jurusan Unit</a>
            </div>
        </div>
    </li>

    <!-- Pengguna -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuPengguna">
            <i class="fas fa-fw fa-users-cog"></i>
            <span>Pengguna</span>
        </a>
        <div id="menuPengguna" class="collapse">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?= base_url('admin/manage-user'); ?>">Data User</a>
                <a class="collapse-item" href="<?= base_url('admin/manage-user-admin'); ?>">Data Admin</a>
                <a class="collapse-item" href="<?= base_url('admin/manage-user-pembimbing'); ?>">Data Pembimbing</a>
                <!-- <a class="collapse-item" href="<?= base_url('admin/manage-struktur'); ?>">Data Struktur</a> -->
                <a class="collapse-item" href="<?= base_url('admin/manage-struktur'); ?>">Data Struktur Organisasi</a>
            </div>
        </div>
    </li>

    <!-- Data Master -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuMaster">
            <i class="fas fa-fw fa-database"></i>
            <span>Data Master</span>
        </a>
        <div id="menuMaster" class="collapse">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?= base_url('admin/kelola-unit'); ?>">Unit Kerja</a>
                <a class="collapse-item" href="<?= base_url('admin/kelola-instansi'); ?>">Perguruan Tinggi / Sekolah</a>
                <a class="collapse-item" href="<?= base_url('admin/kelola-jurusan'); ?>">Jurusan</a>
                <a class="collapse-item" href="<?= base_url('admin/kelola-rfid'); ?>">RFID</a>
                <a class="collapse-item" href="<?= base_url('admin/kelola-keyword'); ?>">Keyword</a>
            </div>
        </div>
    </li>

    <!-- Logs & Bantuan -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuLogs">
            <i class="fas fa-fw fa-clipboard-list"></i>
            <span>Logs</span>
        </a>
        <div id="menuLogs" class="collapse">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?= base_url('admin/access-log'); ?>">Access Log</a>
                <a class="collapse-item" href="<?= base_url('admin/change-log'); ?>">Change Log</a>
                <!-- <a class="collapse-item" href="<?= base_url('admin/user-manual'); ?>">User Manual</a> -->
            </div>
        </div>
    </li>

    <!-- Logs & Bantuan -->
    <li class="nav-item">
        <a class="nav-link" href="<?= base_url('admin/user-manual'); ?>" >
            <i class="fas fa-fw fa-clipboard-list"></i>
            <span>User Manual</span>
        </a>
        <!-- <div id="menuLogs" class="collapse">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?= base_url('admin/access-log'); ?>">Access Log</a>
                <a class="collapse-item" href="<?= base_url('admin/change-log'); ?>">Change Log</a>
                <a class="collapse-item" href="<?= base_url('admin/user-manual'); ?>">User Manual</a>
            </div>
        </div> -->
    </li>


    <!-- Collapse Kelola -->
    <!-- <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuKelola" aria-expanded="false">
            <i class="fas fa-fw fa-tasks"></i>
            <span>Kelola</span>
        </a>
        <div id="menuKelola" class="collapse">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?= base_url('admin/kelola-lowongan'); ?>">Lowongan</a>
                <a class="collapse-item" href="<?= base_url('admin/kuota-unit'); ?>">Kuota Magang</a>
                <a class="collapse-item" href="<?= base_url('admin/jurusan-unit'); ?>">Jurusan Unit</a>
            </div>
        </div>
    </li> -->

    <!-- Collapse Pengguna -->
    <!-- <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuPengguna" aria-expanded="false">
            <i class="fas fa-fw fa-users-cog"></i>
            <span>Pengguna</span>
        </a>
        <div id="menuPengguna" class="collapse">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?= base_url('admin/manage-user'); ?>">Data User</a>
                <a class="collapse-item" href="<?= base_url('admin/manage-user-admin'); ?>">Data Admin</a>
                <a class="collapse-item" href="<?= base_url('admin/manage-user-pembimbing'); ?>">Data Pembimbing</a>
            </div>
        </div>
    </li> -->

    <!-- Collapse Data Master -->
    <!-- <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuMaster" aria-expanded="false">
            <i class="fas fa-fw fa-database"></i>
            <span>Data Master</span>
        </a>
        <div id="menuMaster" class="collapse">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?= base_url('admin/kelola-unit'); ?>">Unit Kerja</a>
                <a class="collapse-item" href="<?= base_url('admin/kelola-instansi'); ?>">Perguruan Tinggi / Sekolah</a>
                <a class="collapse-item" href="<?= base_url('admin/kelola-jurusan'); ?>">Jurusan</a>
                <a class="collapse-item" href="<?= base_url('admin/kelola-rfid'); ?>">RFID</a>
                <a class="collapse-item" href="<?= base_url('admin/kelola-keyword'); ?>">Keyword</a>
            </div>
        </div>
    </li> -->

    <!-- Collapse Logs -->
    <!-- <li class="nav-item mb-2">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuLogs" aria-expanded="false">
            <i class="fas fa-fw fa-database"></i>
            <span>Logs</span>
        </a>
        <div id="menuLogs" class="collapse">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?= base_url('admin/access-log'); ?>">Access Log</a>
                <a class="collapse-item" href="<?= base_url('admin/change-log'); ?>">Change Log</a>
                <a class="collapse-item" href="<?= base_url('admin/user-manual'); ?>">User Manual</a>
            </div>
        </div>
    </li> -->

<?php elseif(in_groups('pembimbing')): ?>
    <?php 
    $unitIds = array_column($unitPembimbing, 'unit_id'); 
    if (in_array(44, $unitIds) && user() && user()->eselon === '2'): ?>
        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('pembimbing/approve-magang'); ?>">
                <i class="fas fa-fw fa-table"></i>
                <span>Approve Magang</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('pembimbing/manage-alumni'); ?>">
                <i class="fas fa-fw fa-table"></i>
                <span> Alumni Magang</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('pembimbing/approve-penelitian'); ?>">
                <i class="fas fa-fw fa-table"></i>
                <span>Approve Penelitian</span>
            </a>
        </li>
    <?php elseif(in_array(44, $unitIds) && user() && user()->eselon === '3'): ?>
        <div class="sidebar-heading">
            Penelitian
        </div>
        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('pembimbing/approve-unit-penelitian'); ?>">
                <i class="fas fa-fw fa-table"></i>
                <span>Approve Unit Penelitian</span>
            </a>
        </li>
    <?php endif; ?>

    <div class="sidebar-heading">
        Penilaian
    </div>
    <li class="nav-item">
        <a class="nav-link" href="<?= base_url('pembimbing/penilaian'); ?>">
            <i class="fas fa-fw fa-table"></i>
            <span>Peserta Magang</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?= base_url('pembimbing/peserta-penelitian'); ?>">
            <i class="fas fa-fw fa-table"></i>
            <span>Peserta Penelitian</span></a>
    </li>
    <?php if (user() && user()->eselon === '2'): ?>
        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('pembimbing/approve'); ?>">
                <i class="fas fa-fw fa-table"></i>
                <span>Approve Nilai</span>
            </a>
        </li>
    <?php endif; ?>

<?php endif;?>

<!-- Sidebar Toggler (Sidebar) -->
<div class="text-center d-none d-md-inline">
    <button class="rounded-circle border-0" id="sidebarToggle"></button>
</div>

</ul>