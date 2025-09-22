<nav class="navbar navbar-expand-lg navbar-light fixed-top py-3 <?= (isset($force_scrolled) && $force_scrolled) ? 'navbar-scrolled' : '' ?>" id="mainNav">
  <div class="container px-4 px-lg-5">
    <a class="navbar-brand" href="/">
      <img src="<?= base_url('assets/img/sp-white.png') ?>" alt="Logo Putih" height="40" class="logo-default">
      <img src="<?= base_url('assets/img/sp-black.png') ?>" alt="Logo Hitam" height="40" class="logo-scrolled d-none">
    </a>
    <button
      class="navbar-toggler navbar-toggler-right"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#navbarResponsive"
      aria-controls="navbarResponsive"
      aria-expanded="false"
      aria-label="Toggle navigation"
    >
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarResponsive">
      <ul class="navbar-nav ms-auto my-2 my-lg-0 d-flex align-items-center">
        <li class="nav-item"><a class="nav-link" href="/">Beranda</a></li>
        <li class="nav-item"><a class="nav-link" href="/magang">Ketersediaan</a></li>
        <li class="nav-item"><a class="nav-link" href="/tentang-kami">Tentang Kami</a></li>
        <?php if (logged_in()) : ?>
            <!-- Tampil hanya saat layar besar (lg ke atas) -->
            <li class="nav-item dropdown d-none d-lg-block">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="<?= base_url('uploads/user-image/' . (user()->user_image ?? 'default.svg')); ?>" class="rounded-circle" width="30" height="30" alt="Profile">
                    <span class="ms-2"><?= esc(user()->fullname); ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?= site_url('profile'); ?>">Lihat Profil</a></li>
                    <li><a class="dropdown-item" href="<?= url_to('logout'); ?>">Logout</a></li>
                </ul>
            </li>

            <!-- Tampil saat layar kecil atau jendela dikecilkan (bukan lg ke atas) -->
            <li class="nav-item d-block d-lg-none">
                <a class="nav-link" href="<?= site_url('profile'); ?>">Lihat Profil</a>
            </li>
            <li class="nav-item d-block d-lg-none">
                <a class="nav-link" href="<?= url_to('logout'); ?>">Logout</a>
            </li>
        <?php else : ?>
            <li class="nav-item">
                <a class="nav-link btn btn-danger rounded-pill ms-2 py-1 px-4" href="<?= site_url('login'); ?>">Login</a>
            </li>
        <?php endif; ?>


      </ul>
    </div>
  </div>
</nav>
