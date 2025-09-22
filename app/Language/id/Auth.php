<?php

namespace Myth\Auth\Language\id;

return [
    // Exceptions
    'invalidModel'        => 'Model {0} harus dimuat sebelum digunakan.',
    'userNotFound'        => 'Tidak dapat menemukan pengguna dengan ID = {0, number}.',
    'noUserEntity'        => 'Entity User harus disediakan untuk validasi kata sandi.',
    'tooManyCredentials'  => 'Anda hanya dapat memvalidasi 1 kredensial selain kata sandi.',
    'invalidFields'       => 'Kolom "{0}" tidak dapat digunakan untuk memvalidasi kredensial.',
    'unsetPasswordLength' => 'Anda harus menetapkan `minimumPasswordLength` di file konfigurasi Auth.',
    'unknownError'        => 'Maaf, terjadi masalah saat mengirim email. Silakan coba lagi nanti.',
    'notLoggedIn'         => 'Anda harus login untuk mengakses halaman tersebut.',
    'notEnoughPrivilege'  => 'Anda tidak memiliki izin yang cukup untuk mengakses halaman tersebut.',

    // Registration
    'registerDisabled' => 'Maaf, pendaftaran pengguna baru saat ini tidak diizinkan.',
    'registerSuccess' => 'Selamat datang! Pendaftaran berhasil. Silakan login dengan email dan kata sandi yang telah Anda daftarkan.',
    'registerCLI'      => 'Pengguna baru dibuat: {0}, #{1}',

    // Activation
    'activationNoUser'       => 'Tidak dapat menemukan pengguna dengan kode aktivasi tersebut.',
    'activationSubject'      => 'Aktivasi akun Anda',
    'activationSuccess'      => 'Silakan konfirmasi akun Anda dengan mengklik tautan aktivasi yang telah kami kirim melalui email.',
    'activationResend'       => 'Kirim ulang pesan aktivasi sekali lagi.',
    'notActivated'           => 'Akun ini belum diaktivasi.',
    'errorSendingActivation' => 'Gagal mengirim pesan aktivasi ke: {0}',

    // Login
    'badAttempt'      => 'Login gagal! Silakan periksa kembali email dan kata sandi Anda.',
    'loginSuccess'    => 'Selamat datang kembali!',
    'invalidPassword' => 'Login gagal! Kata sandi Anda salah. Gunakan kata Sandi yang sudah didaftarkan',

    // Forgotten Passwords
    'forgotDisabled'  => 'Fitur reset kata sandi saat ini dinonaktifkan.',
    'forgotNoUser'    => 'Tidak dapat menemukan pengguna dengan email tersebut.',
    'forgotSubject'   => 'Instruksi Reset Kata Sandi',
    'resetSuccess'    => 'Kata sandi Anda berhasil diubah. Silakan login dengan kata sandi baru.',
    'forgotEmailSent' => 'Token keamanan telah dikirim ke email Anda. Masukkan token tersebut di bawah untuk melanjutkan.',
    'errorEmailSent'  => 'Tidak dapat mengirim email berisi instruksi reset kata sandi ke: {0}',
    'errorResetting'  => 'Tidak dapat mengirim instruksi reset ke: {0}',

    // Passwords
    'errorPasswordLength'         => 'Kata sandi minimal harus {0, number} karakter.',
    'suggestPasswordLength'       => 'Frasa sandi - hingga 255 karakter - membuat kata sandi lebih aman dan mudah diingat.',
    'errorPasswordCommon'         => 'Kata sandi tidak boleh kata sandi yang umum.',
    'suggestPasswordCommon'       => 'Kata sandi diperiksa terhadap lebih dari 65 ribu kata sandi umum atau kata sandi yang bocor akibat peretasan.',
    'errorPasswordPersonal'       => 'Kata sandi tidak boleh mengandung informasi pribadi yang di-hash ulang.',
    'suggestPasswordPersonal'     => 'Jangan gunakan variasi dari email atau username Anda sebagai kata sandi.',
    'errorPasswordTooSimilar'     => 'Kata sandi terlalu mirip dengan username.',
    'suggestPasswordTooSimilar'   => 'Jangan gunakan bagian dari username dalam kata sandi Anda.',
    'errorPasswordPwned'          => 'Kata sandi {0} telah terbongkar akibat pelanggaran data dan telah muncul {1, number} kali dalam {2} database kata sandi bocor.',
    'suggestPasswordPwned'        => '{0} sebaiknya tidak digunakan sebagai kata sandi. Jika Anda menggunakannya di tempat lain, segera ganti.',
    'errorPasswordPwnedDatabase'  => 'sebuah database',
    'errorPasswordPwnedDatabases' => 'beberapa database',
    'errorPasswordEmpty'          => 'Kata sandi wajib diisi.',
    'passwordChangeSuccess'       => 'Kata sandi berhasil diubah.',
    'userDoesNotExist'            => 'Kata sandi tidak diubah. Pengguna tidak ditemukan.',
    'resetTokenExpired'           => 'Maaf. Token reset Anda sudah kedaluwarsa.',

    // Groups
    'groupNotFound' => 'Tidak dapat menemukan grup: {0}.',

    // Permissions
    'permissionNotFound' => 'Tidak dapat menemukan izin: {0}',

    // Banned
    'userIsBanned' => 'Pengguna telah diblokir. Hubungi administrator.',

    // Too many requests
    'tooManyRequests' => 'Terlalu banyak permintaan. Silakan tunggu {0, number} detik.',

    // Login views
    'home'                      => 'Beranda',
    'current'                   => 'Saat ini',
    'forgotPassword'            => 'Lupa Kata Sandi?',
    'enterEmailForInstructions' => 'Tidak masalah! Masukkan email Anda di bawah dan kami akan mengirimkan instruksi untuk reset kata sandi.',
    'email'                     => 'Email',
    'emailAddress'              => 'Alamat Email',
    'sendInstructions'          => 'Kirim Instruksi',
    'loginTitle'                => 'Login',
    'loginAction'               => 'Login',
    'rememberMe'                => 'Ingat saya',
    'needAnAccount'             => 'Belum punya akun?',
    'forgotYourPassword'        => 'Lupa kata sandi?',
    'password'                  => 'Kata Sandi',
    'repeatPassword'            => 'Ulangi Kata Sandi',
    'emailOrUsername'           => 'Email atau username',
    'username'                  => 'Username',
    'register'                  => 'Daftar',
    'signIn'                    => 'Masuk',
    'alreadyRegistered'         => 'Sudah punya akun?',
    'weNeverShare'              => 'Kami tidak akan membagikan email Anda ke siapa pun.',
    'resetYourPassword'         => 'Reset Kata Sandi',
    'enterCodeEmailPassword'    => 'Masukkan kode yang Anda terima melalui email, alamat email Anda, dan kata sandi baru.',
    'token'                     => 'Token',
    'newPassword'               => 'Kata Sandi Baru',
    'newPasswordRepeat'         => 'Ulangi Kata Sandi Baru',
    'resetPassword'             => 'Reset Kata Sandi',
    'backToLogin'               => 'Kembali ke Halaman Login'

];
