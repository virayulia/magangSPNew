<?= $this->extend('auth/template_plain'); ?>
<?= $this->section('content'); ?>

<!-- Tambah style khusus -->
<style>
  .bg-login-image {
    background: url('<?= base_url('assets/img/bg-masthead2.jpeg'); ?>');
    background-position: center;
    background-size: cover;
  }
  .password-wrapper {
    position: relative;
  }

  .toggle-password {
    position: absolute;
    top: 70%;
    transform: translateY(-50%);
    right: 1rem;
    border: none;
    background: none;
    color: #6c757d;
    font-size: 1.25rem;
    cursor: pointer;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
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
                <h3 class="text-primary fw-bold"><?=lang('Auth.loginTitle')?></h3>
                <p class="text-muted small">Silakan masuk untuk melanjutkan</p>
              </div>

              <?= view('auth/_message_block') ?>

              <!-- Form -->
              <form action="<?= url_to('login'); ?>" method="post">
                <?= csrf_field(); ?>
					      <?php if ($config->validFields === ['email']): ?>
                  <div class="mb-3">
                    <label for="login" class="form-label"><?=lang('Auth.email')?></label>
                    <input type="email" class="form-control <?php if (session('errors.login')) : ?>is-invalid<?php endif ?>" 
                      name="login" placeholder="<?=lang('Auth.email')?>" required>
                    <div class="invalid-feedback">
                      <?= session('errors.login') ?>
                    </div>
                  </div>
                <?php else: ?>
                  <div class="mb-3">
                    <label for="login" class="form-label"><?=lang('Auth.emailOrUsername')?></label>
                    <input type="text" class="form-control <?php if (session('errors.login')) : ?>is-invalid<?php endif ?>" 
                      name="login" placeholder="<?=lang('Auth.emailOrUsername')?>" required>
                    <div class="invalid-feedback">
                      <?= session('errors.login') ?>
                    </div>
                  </div>
                <?php endif; ?>
                <div class="mb-3 password-wrapper">
                  <label for="password" class="form-label"><?= lang('Auth.password') ?></label>
                  <input type="password" id="password" name="password"
                    class="form-control <?php if (session('errors.password')) : ?>is-invalid<?php endif ?>"
                    placeholder="<?= lang('Auth.password') ?>" required>
                  <button type="button" class="toggle-password" tabindex="-1">
                    <i class="bi bi-eye" id="toggleIcon"></i>
                  </button>
                  <div class="invalid-feedback">
                    <?= session('errors.password') ?>
                  </div>
                </div>

                <?php if ($config->allowRemembering): ?>
                <div class="mb-3 form-check">
                  <input type="checkbox" class="form-check-input" id="remember" name="remember" <?php if (old('remember')) : ?> checked <?php endif ?>>
                  <label class="form-check-label" for="remember"><?=lang('Auth.rememberMe')?></label>
                </div>
                <?php endif; ?>
                <button type="submit" class="btn btn-danger w-100"><?=lang('Auth.loginAction')?></button>
              </form>
              <hr>
              <?php if ($config->allowRegistration) : ?>
              <div class="text-center">
                <a class="small" href="<?= url_to('register') ?>"><?=lang('Auth.needAnAccount')?></a>
              </div>
              <?php endif; ?>
              <!-- Bikin halaman forgotnya -->
              <?php if ($config->activeResetter): ?>
              <div class="text-center mt-2">
                <a class="small text-muted" href="<?= url_to('forgot') ?>"><?=lang('Auth.forgotYourPassword')?></a>
              </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <!-- End Card -->
      </div>
    </div>
  </div>
</section>
<script>
  const passwordInput = document.getElementById('password');
const toggleBtn = document.querySelector('.toggle-password');
const toggleIcon = document.getElementById('toggleIcon');

toggleBtn.addEventListener('click', function () {
  const isPassword = passwordInput.type === 'password';
  passwordInput.type = isPassword ? 'text' : 'password';
  toggleIcon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
});

</script>


<?= $this->endSection(); ?>
