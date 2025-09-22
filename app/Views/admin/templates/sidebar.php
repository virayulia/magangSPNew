<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

<!-- Sidebar - Brand -->
<a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
    <div class="sidebar-brand-icon rotate-n-15">
        <i class="fas fa-home"></i>           
    </div>
    <div class="sidebar-brand-text mx-3">Admin PTSP</div>
</a>

<?php if(in_groups('admin')): ?>
    <div class="sidebar-heading">
        Magang
    </div>
    <li class="nav-item">
        <a class="nav-link" href="<?= base_url('admin/manage-pendaftaran'); ?>">
            <i class="fas fa-fw fa-table"></i>
            <span>Data Pendaftar</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?= base_url('admin/manage-seleksi'); ?>">
            <i class="fas fa-fw fa-table"></i>
            <span>Seleksi Pendaftar</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?= base_url('admin/manage-validasi-konfirmasi'); ?>">
            <i class="fas fa-fw fa-table"></i>
            <span>Approval Konfirmasi</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?= base_url('admin/manage-kelengkapan-berkas'); ?>">
            <i class="fas fa-fw fa-table"></i>
            <span>Kelengkapan Berkas</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?= base_url('admin/manage-hasil-tes'); ?>">
            <i class="fas fa-fw fa-table"></i>
            <span> Hasil Tes Safety</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?= base_url('admin/manage-magang'); ?>">
            <i class="fas fa-fw fa-table"></i>
            <span> Peserta Magang</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?= base_url('admin/manage-alumni'); ?>">
            <i class="fas fa-fw fa-table"></i>
            <span> Alumni Magang</span></a>
    </li>
    <div class="sidebar-heading">
        Penelitian
    </div>
    <!-- Nav Item - Tables -->
    <li class="nav-item">
        <a class="nav-link" href="<?= base_url('admin/manage-penelitian'); ?>">
            <i class="fas fa-fw fa-table"></i>
            <span>Data Pendaftar</span></a>
    </li>
    <div class="sidebar-heading">
        Kelola
    </div>

    <li class="nav-item">
        <a class="nav-link" href="<?= base_url('admin/kelola-lowongan'); ?>">
            <i class="fas fa-fw fa-table"></i>
            <span>Lowongan</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?= base_url('admin/kuota-unit'); ?>">
            <i class="fas fa-fw fa-table"></i>
            <span>Kuota Magang</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?= base_url('admin/jurusan-unit'); ?>">
            <i class="fas fa-fw fa-table"></i>
            <span>Jurusan Unit</span></a>
    </li>
    <!-- <li class="nav-item">
        <a class="nav-link collapsed" href="/kelola-unit" data-toggle="collapse" data-target="#collapseThree"
            aria-expanded="true" aria-controls="collapseTwo">
            <i class="fas fa-fw fa-table"></i>
            <span>Unit dan Kuota</span>
        </a>
        <div id="collapseThree" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="/kelola-kuota-unit">Kuota Unit</a>
                <a class="collapse-item" href="/kelola-kuota">Lihat Kuota Tersedia</a>
            </div>
        </div>
    </li> -->
    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="<?= base_url('admin/manage-user'); ?>" data-toggle="collapse" data-target="#collapseTwo"
            aria-expanded="true" aria-controls="collapseTwo">
            <i class="fas fa-fw fa-cog"></i>
            <span>Pengguna</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?= base_url('admin/manage-user'); ?>">Data User</a>
                <a class="collapse-item" href="<?= base_url('admin/manage-user-admin'); ?>">Data Admin</a>
                <a class="collapse-item" href="<?= base_url('admin/manage-user-pembimbing'); ?>">Data Pembimbing</a>
            </div>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="<?= base_url('admin/kelola-unit'); ?>" data-toggle="collapse" data-target="#collapseFour"
            aria-expanded="true" aria-controls="collapseFour">
            <i class="fas fa-fw fa-table"></i>
            <span>Data Master</span>
        </a>
        <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?= base_url('admin/kelola-unit'); ?>">Unit Kerja</a>
                <a class="collapse-item" href="<?= base_url('admin/kelola-instansi'); ?>">Perguruan Tinggi/Sekolah</a>
                <a class="collapse-item" href="<?= base_url('admin/kelola-jurusan'); ?>">Jurusan</a>
                <a class="collapse-item" href="<?= base_url('admin/kelola-rfid'); ?>">RFID</a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

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
    <?php endif; ?>

    <div class="sidebar-heading">
        Penilaian
    </div>
    <li class="nav-item">
        <a class="nav-link" href="<?= base_url('pembimbing/penilaian'); ?>">
            <i class="fas fa-fw fa-table"></i>
            <span>Peserta Magang</span></a>
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