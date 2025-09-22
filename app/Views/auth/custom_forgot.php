<?= $this->extend('auth/template_plain'); ?>
<?= $this->section('content'); ?>

<!-- Tambah style khusus -->
<style>
  .bg-login-image {
    background: url('<?= base_url('assets/img/bg-masthead2.jpeg'); ?>');
    background-position: center;
    background-size: cover;
  }
</style>

<section class="vh-100 d-flex align-items-center bg-light">
  <div class="container px-4 px-lg-5">
    <div class="row justify-content-center">
      <div class="col-lg-7 col-md-9">
        <div class="card shadow-lg border-0 rounded-4">
          <div class="row g-0">
            <!-- Background image -->
            <div class="col-md-5 d-none d-md-block bg-login-image rounded-start-4"></div>
            <!-- Form -->
            <div class="col-md-7 p-4">
              <div class="text-center mb-4">
                <h3 class="text-primary fw-bold"><?=lang('Auth.forgotPassword')?></h3>
                <p class="text-muted small"><?=lang('Auth.enterEmailForInstructions')?></p>
              </div>

              <?= view('auth/_message_block') ?>

              <form action="<?= url_to('forgot') ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-3">
                  <label for="email" class="form-label"><?=lang('Auth.emailAddress')?></label>
                  <input type="email" class="form-control <?php if (session('errors.email')) : ?>is-invalid<?php endif ?>"
                    name="email" placeholder="<?=lang('Auth.email')?>" required>
                  <div class="invalid-feedback">
                    <?= session('errors.email') ?>
                  </div>
                </div>
                <button type="submit" class="btn btn-primary w-100"><?=lang('Auth.sendInstructions')?></button>
              </form>

              <hr>
              <div class="text-center">
                <a class="small text-muted" href="<?= url_to('login') ?>"><?=lang('Auth.backToLogin') ?: 'Kembali ke Login'?></a>
              </div>

            </div>
          </div>
        </div>
        <!-- End Card -->
      </div>
    </div>
  </div>
</section>

<?= $this->endSection(); ?>
