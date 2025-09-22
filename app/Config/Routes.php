<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */


$routes->get('/', 'Home::index', ['filter' => 'roleRedirect']);
$routes->get('/magang', 'HomeController::lowongan');
$routes->get('/tentang-kami', 'HomeController::tentang_kami');
$routes->get('get-images', 'HomeController::getImages');


$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::attemptLogin');
$routes->post('logout', 'AuthController::logout');
$routes->get('register', 'AuthController::register');
$routes->post('register/process', 'AuthController::attemptRegister');
$routes->get('/api/kota', 'AuthController::getCities');
$routes->get('/get-instansi', 'AuthController::getInstansi');

$routes->get('active-account', 'AuthController::activateAccount');
$routes->get('resend-activate-account', 'AuthController::resendActivateAccount');

$routes->get('forgot', 'AuthController::forgotPassword');
$routes->post('forgot', 'AuthController::attemptForgot');
$routes->get('reset-password', 'AuthController::resetPassword');
$routes->post('reset-password', 'AuthController::attemptReset');

//Cronjob
$routes->get('cron/remind-unit/(:segment)', 'CronController::remindUnit/$1');
$routes->get('cron/autoTolakTidakKonfirmasi/(:segment)', 'CronController::autoTolakTidakKonfirmasi/$1');
// $routes->get('cron/autoTolakTidakValidasiBerkas/(:segment)', 'CronController::autoTolakTidakValidasiBerkas/$1');
$routes->get('cron/reminderLengkapiBerkas/(:segment)', 'CronController::reminderLengkapiBerkas/$1');




// $routes->get('/', 'Home::index');
// $routes->get('/tentang_kami', 'Home::tentang_kami');
// $routes->get('/lowongan', 'Home::lowongan');

// $routes->get('login', 'Auth::login');
// $routes->post('login', 'Auth::attemptLogin');
// $routes->post('logout', 'Auth::logout');

// $routes->get('register', 'Auth::register');
// $routes->post('register/process', 'Auth::attemptRegister');
// $routes->get('/api/kota', 'Auth::getCities');
// $routes->get('/get-instansi', 'Auth::getInstansi');

// $routes->get('active-account', 'Auth::activateAccount');
// $routes->get('resend-activate-account', 'Auth::resendActivateAccount');

// $routes->get('forgot', 'Auth::forgotPassword');
// $routes->post('forgot', 'Auth::attemptForgot');
// $routes->get('reset-password', 'Auth::resetPassword');
// $routes->post('reset-password', 'Auth::attemptReset');

// ==========================
// Group untuk Admin
// ==========================
$routes->group('admin', ['namespace' => 'App\Controllers\Admin','filter' => 'admin'], function($routes) {
    // Kelola Users
    $routes->get('manage-user', 'UserController::index');
    $routes->post('manage-user/update/(:num)', 'UserController::update/$1');
    $routes->get('manage-user/activate/(:num)', 'UserController::activate/$1');
    $routes->get('manage-user/delete/(:num)', 'UserController::delete/$1');

    //Kelola Admin
    $routes->get('manage-user-admin', 'UserController::indexAdmin');
    $routes->post('manage-user-admin/saveAdmin', 'UserController::saveAdmin');
    $routes->post('manage-user-admin/updateAdmin/(:num)', 'UserController::updateAdmin/$1');
    $routes->get('manage-user-admin/delete/(:num)', 'UserController::deleteAdmin/$1');

    //Kelola Pembimbing
    $routes->get('manage-user-pembimbing', 'UserController::indexPembimbing');
    $routes->post('manage-user-pembimbing/save', 'UserController::savePembimbing');
    $routes->post('manage-user-pembimbing/update/(:num)', 'UserController::updatePembimbing/$1');
    $routes->get('manage-user-pembimbing/delete/(:num)', 'UserController::deletePembimbing/$1');
    $routes->post('manage-user-pembimbing/import', 'UserController::importExcel');


    //Kelola Unit Kerja
    $routes->get('kelola-unit', 'UnitKerjaController::index');
    $routes->post('unit/save', 'UnitKerjaController::save');
    $routes->post('kelola-unit/update/(:num)', 'UnitKerjaController::update/$1');

    // Kelola Instansi
    $routes->get('kelola-instansi', 'InstansiController::index');
    $routes->post('instansi/save', 'InstansiController::save');
    $routes->post('instansi/update/(:num)', 'InstansiController::update/$1');
    $routes->post('instansi/delete/(:num)', 'InstansiController::delete/$1');

    // Kelola Jurusan
    $routes->get('kelola-jurusan', 'JurusanController::index');
    $routes->post('jurusan/save', 'JurusanController::save');
    $routes->post('jurusan/update/(:num)', 'JurusanController::update/$1');
    $routes->post('jurusan/delete/(:num)', 'JurusanController::delete/$1');

    // Kelola RFID
    $routes->get('kelola-rfid', 'RfidController::index');
    $routes->post('rfid/save', 'RfidController::save');
    $routes->post('rfid/update/(:num)', 'RfidController::update/$1');
    $routes->post('rfid/delete/(:num)', 'RfidController::delete/$1');

    //Kelola Jurusan Unit
    $routes->get('jurusan-unit', 'JurusanUnitController::index');
    $routes->post('jurusan-unit/save', 'JurusanUnitController::save');
    $routes->post('jurusan-unit/addJurusan', 'JurusanUnitController::addJurusan');
    $routes->post('jurusan-unit/deleteJurusan/(:num)', 'JurusanUnitController::deleteJurusan/$1');
    // $routes->post('jurusan-unit/delete/(:num)', 'JurusanUnitController::delete/$1');

    // Kelola Kuota Unit
    $routes->get('kuota-unit', 'KuotaUnitController::index');
    $routes->post('kuota-unit/save', 'KuotaUnitController::save');
    $routes->post('kelola-kuota-unit/update/(:num)', 'KuotaUnitController::update/$1');
    // $routes->get('kelola-kuota-unit', 'Admin::indexKuotaUnit');

    // Kelola Lowongan
    $routes->get('kelola-lowongan', 'LowonganController::index');
    $routes->post('periode/save', 'LowonganController::periodesave');
    $routes->post('periode/update/(:num)', 'LowonganController::update/$1');
    $routes->get('periode/delete/(:num)', 'LowonganController::delete/$1');
    
    //Kelola Penelitian
    $routes->get('manage-penelitian', 'PenelitianController::index');

    // Kelola Pendaftaran Magang
    $routes->get('manage-pendaftaran', 'MagangController::index');
    $routes->get('manage-pendaftaran/detail/(:num)', 'MagangController::detail/$1');
    $routes->get('manage-seleksi', 'MagangController::seleksi');
    $routes->get('manage-seleksi/pendaftar', 'MagangController::pendaftar');
    $routes->post('manage-seleksi/terima-banyak', 'MagangController::terimaBanyak');
    $routes->post('manage-seleksi/tolak-banyak', 'MagangController::tolakBanyak');
    $routes->get('manage-validasi-konfirmasi', 'MagangController::validasi');
    $routes->post('manage-validasi-konfirmasi/bulk', 'MagangController::bulkValidasi');
    
    $routes->post('manage-validasi-konfirmasi/valid/(:num)', 'MagangController::validKonfirmasi/$1');
    $routes->get('manage-kelengkapan-berkas', 'MagangController::berkas');
    $routes->get('manage-kelengkapan-berkas/(:num)', 'MagangController::berkas/$1');
    $routes->post('manage-kelengkapan-berkas/valid/(:num)', 'MagangController::valid/$1');
    $routes->post('manage-kelengkapan-berkas/tidakValid/(:num)', 'MagangController::tidakValid/$1');

    // Kelola Peserta Magang
    $routes->get('manage-magang', 'MagangController::pesertaMagang');
    $routes->post('updateMagang/(:num)', 'MagangController::updateMagang/$1');
    $routes->post('batalkanMagang', 'MagangController::batalkanMagang');
    $routes->post('setRFID', 'MagangController::setRFID');
    $routes->post('returnRFID', 'MagangController::returnRFID');
    $routes->post('tolakLaporan', 'MagangController::tolakLaporan');
    $routes->post('tolakAbsensi', 'MagangController::tolakAbsensi');

    $routes->post('finalisasi/(:num)', 'MagangController::finalisasi/$1');
    
    //Alumni
    $routes->get('manage-alumni', 'MagangController::alumniMagang');
    $routes->get('cetak-sertifikat/(:num)', 'MagangController::cetakSertifikat/$1');



    //Kelola Hasil Tes Safety
    $routes->get('manage-hasil-tes', 'MagangController::safety');
    

    
    
    // Old
    $routes->post('manage-pendaftaran/approve/(:num)', 'Admin::approve/$1');
    $routes->post('manage-pendaftaran/reject/(:num)', 'Admin::reject/$1');
    $routes->post('manage-pendaftaran/konfirmasi/(:num)', 'User::konfirmasi/$1');
    $routes->get('generateSuratPenerimaan/(:num)', 'GeneratePDF::suratPenerimaan/$1');
    $routes->get('generateSuratPenerimaan2/(:num)', 'GeneratePDF::generateAndSavePDF/$1');
});

// ==========================
// Group untuk Pembimbing
// ==========================
$routes->group('pembimbing', ['namespace' => 'App\Controllers\Pembimbing'], function($routes) {
    $routes->get('penilaian', 'MagangController::penilaian');
    $routes->post('assignPembimbing/(:num)', 'MagangController::assignPembimbing/$1');
    $routes->post('updatePembimbing/(:num)', 'MagangController::updatePembimbing/$1');

    $routes->post('penilaian/save', 'MagangController::save');
    $routes->post('penilaian/update/(:num)', 'MagangController::update/$1');
    $routes->get('approve', 'MagangController::approve');
    $routes->post('approve/save', 'MagangController::saveApprove');
    $routes->post('approve/bulk', 'MagangController::bulkApprove');
    $routes->get('approve-magang', 'MagangController::approveMagang');
    $routes->post('setApproveMagang', 'MagangController::setApproveMagang');

    $routes->get('manage-alumni', 'MagangController::alumniMagang');





});


// ==========================
// Group untuk User
// ==========================
$routes->group('', ['namespace' => 'App\Controllers\User','filter' => 'user'], function($routes) {
    //Profile
    $routes->get('profile', 'UserController::profil');
    $routes->get('profile/data-pribadi', 'UserController::dataPribadi');
    $routes->post('profile/data-pribadi', 'UserController::saveDataPribadi');
    $routes->get('profile/data-akademik', 'UserController::dataAkademik');
    $routes->post('profile/data-akademik', 'UserController::saveDataAkademik');

    // Upload routes
    $routes->post('cv/uploads/(:num)', 'UploadController::cv/$1');
    $routes->get('cv/delete/(:num)', 'UploadController::deletecv/$1');
    $routes->post('proposal/uploads/(:num)', 'UploadController::proposal/$1');
    $routes->get('proposal/delete/(:num)', 'UploadController::deleteproposal/$1');
    $routes->post('surat-permohonan/uploads/(:num)', 'UploadController::suratPermohonan/$1');
    $routes->get('surat-permohonan/delete/(:num)', 'UploadController::deleteSuratPermohonan/$1');
    $routes->post('ktp-kk/uploads/(:num)', 'UploadController::ktp_kk/$1');
    $routes->get('ktp/delete/(:num)', 'UploadController::deletektp/$1');
    $routes->post('bpjs-kes/uploads/(:num)', 'UploadController::bpjsKes/$1');
    $routes->get('bpjs-kes/delete/(:num)', 'UploadController::deleteBPJSKes/$1');
    $routes->post('bpjs-tk/uploads/(:num)', 'UploadController::bpjsTK/$1');
    $routes->get('bpjs-tk/delete/(:num)', 'UploadController::deleteBPJSTK/$1');
    $routes->post('buktibpjs-tk/uploads/(:num)', 'UploadController::buktibpjsTK/$1');
    $routes->get('buktibpjs-tk/delete/(:num)', 'UploadController::deletebuktiBPJSTK/$1');

    //Magang
    $routes->get('status-lamaran', 'MagangController::statusLamaran');
    $routes->get('pelaksanaan', 'MagangController::pelaksanaan');
    $routes->post('magang/daftar', 'MagangController::daftar');
    $routes->post('magang/konfirmasi', 'MagangController::konfirmasi');
    $routes->post('magang/validasi-berkas', 'MagangController::validasiBerkas');
    $routes->get('cetak-tanda-pengenal/(:num)', 'MagangController::cetakTandaPengenal/$1');
    $routes->get('magang/surat-pernyataan', 'MagangController::suratPernyataan');
    $routes->post('magang/setujui-surat-pernyataan', 'MagangController::setujuiPernyataan');
    $routes->get('safety-tes', 'MagangController::safetyTes');
    $routes->post('safety/submit', 'MagangController::submitTes');
    $routes->get('sertifikat-magang', 'MagangController::sertifikatIndex');
    $routes->get('unggah-laporan', 'MagangController::unggahIndex');
    $routes->post('unggah-laporan/(:num)', 'MagangController::uploadLaporanAbsensi/$1');
    $routes->post('sertifikat/saveFeedback', 'MagangController::saveFeedback');

    $routes->get('cetak-sertifikat', 'MagangController::cetakSertifikat');

    //Penelitian
    $routes->get('penelitian', 'PenelitianController::index');
    $routes->post('penelitian/daftar', 'Penelitian::daftar');



    //Old
    $routes->post('/pendaftaran/save', 'User::savedaftar');

    
    $routes->post('penelitian/daftar', 'Penelitian::daftar');
});

// $routes->group('', ['filter' => 'user'], function($routes) {
//     $routes->get('/profile', 'User::profil');
//     $routes->get('/data-pribadi', 'User::dataPribadi');
//     $routes->post('/data-pribadi/save', 'User::saveDataPribadi');
//     $routes->post('/data-akademik/save', 'User::saveDataAkademik');

//     $routes->get('/status-lamaran', 'User::statusLamaran');
//     $routes->get('/pelaksanaan', 'User::pelaksanaan');
//     $routes->post('/pendaftaran/save', 'User::savedaftar');

//     $routes->post('magang/daftar', 'Magang::daftar');
//     $routes->post('magang/konfirmasi', 'Magang::konfirmasi');
//     $routes->post('magang/validasi-berkas', 'Magang::validasiBerkas');
//     $routes->get('cetak-tanda-pengenal/(:num)', 'Magang::cetakTandaPengenal/$1');
//     $routes->get('magang/surat-pernyataan', 'Magang::suratPernyataan');
//     $routes->post('magang/setujui-surat-pernyataan', 'Magang::setujuiPernyataan');

//     $routes->post('penelitian/daftar', 'Penelitian::daftar');


//     // Upload routes
//     $routes->post('cv/uploads/(:num)', 'Upload::cv/$1');
//     $routes->get('cv/delete/(:num)', 'Upload::deletecv/$1');
//     $routes->post('proposal/uploads/(:num)', 'Upload::proposal/$1');
//     $routes->get('proposal/delete/(:num)', 'Upload::deleteproposal/$1');
//     $routes->post('surat-permohonan/uploads/(:num)', 'Upload::suratPermohonan/$1');
//     $routes->get('surat-permohonan/delete/(:num)', 'Upload::deleteSuratPermohonan/$1');
//     $routes->post('ktp-kk/uploads/(:num)', 'Upload::ktp_kk/$1');
//     $routes->get('ktp/delete/(:num)', 'Upload::deletektp/$1');
//     $routes->post('bpjs-kes/uploads/(:num)', 'Upload::bpjsKes/$1');
//     $routes->get('bpjs-kes/delete/(:num)', 'Upload::deleteBPJSKes/$1');
//     $routes->post('bpjs-tk/uploads/(:num)', 'Upload::bpjsTK/$1');
//     $routes->get('bpjs-tk/delete/(:num)', 'Upload::deleteBPJSTK/$1');
//     $routes->post('buktibpjs-tk/uploads/(:num)', 'Upload::buktibpjsTK/$1');
//     $routes->get('buktibpjs-tk/delete/(:num)', 'Upload::deletebuktiBPJSTK/$1');
// });


// $routes->group('', ['filter' => 'admin'], function($routes) {
//     $routes->get('/admin', 'Admin::index');

//     // Kelola Users
//     $routes->get('/manage-user', 'DataUser::index');
//     $routes->get('/manage-user/delete/(:num)', 'DataUser::delete/$1');
//     $routes->get('/manage-user-admin', 'DataUser::indexAdmin');
//     $routes->get('/manage-user-admin/delete/(:num)', 'DataUser::deleteAdmin/$1');
//     $routes->post('/manage-user-admin/saveAdmin', 'DataUser::saveAdmin');
//     $routes->post('/manage-user-admin/updateAdmin/(:num)', 'DataUser::updateAdmin/$1');

//     // Kelola Lowongan
//     $routes->get('/kelola-lowongan', 'Lowongan::index');
//     $routes->post('/periode/save', 'Lowongan::periodesave');
//     $routes->post('/periode/update/(:num)', 'Lowongan::update/$1');
    
//     // Kelola Instansi
//     $routes->get('/kelola-instansi', 'Instansi::index');
//     $routes->post('/instansi/save', 'Instansi::save');
//     $routes->post('/instansi/update/(:num)', 'Instansi::update/$1');
//     $routes->post('/instansi/delete/(:num)', 'Instansi::delete/$1');
//     // Kelola Jurusan
//     $routes->get('/kelola-jurusan', 'Jurusan::index');
//     $routes->post('/jurusan/save', 'Jurusan::save');
//     $routes->post('/jurusan/update/(:num)', 'Jurusan::update/$1');
//     $routes->post('/jurusan/delete/(:num)', 'Jurusan::delete/$1');

//     // Kelola Pendaftaran Magang
//     $routes->get('/manage-pendaftaran', 'Admin::index');

//     //Kelola Penelitian
//     $routes->get('/manage-penelitian', 'Penelitian::index');

//     // Kelola Unit
//     $routes->get('/kelola-unit', 'Admin::indexUnit');
//     $routes->post('/kelola-unit/update/(:num)', 'Admin::updateUnit/$1');
//     $routes->post('/unit/save', 'Unitkerja::save');

//     // Kelola Kuota Unit
//     $routes->get('/kuota-unit', 'KuotaUnit::index');
//     $routes->post('/kuota-unit/save', 'KuotaUnit::save');
//     $routes->get('/kelola-kuota-unit', 'Admin::indexKuotaUnit');
//     $routes->post('/kelola-kuota-unit/update/(:num)', 'Admin::updateKelolaUnit/$1');

//     //Kelola Jurusan Unit
//     $routes->get('jurusan-unit', 'JurusanUnit::index');
//     $routes->post('jurusan-unit/save', 'JurusanUnit::save');
//     $routes->post('jurusan-unit/addJurusan', 'JurusanUnit::addJurusan');
//     $routes->post('jurusan-unit/deleteJurusan/(:num)', 'JurusanUnit::deleteJurusan/$1');
//     // $routes->post('jurusan-unit/delete/(:num)', 'JurusanUnit::delete/$1');

//     // Kelola Kuota
//     // $routes->get('/kelola-kuota', 'Admin::indexKuota');

//     // Kelola Seleksi
//     $routes->get('/manage-seleksi', 'Admin::indexSeleksi');
//     $routes->get('/manage-seleksi/pendaftar', 'Admin::pendaftar');
//     $routes->post('/manage-seleksi/terima/(:num)', 'Admin::terimaPendaftar/$1');
//     $routes->post('/manage-seleksi/tolak/(:num)', 'Admin::tolakPendaftar/$1');
//     $routes->post('/manage-seleksi/terima-banyak', 'Admin::terimaBanyak');
//     $routes->post('/manage-seleksi/tolak-banyak', 'Admin::tolakBanyak');

//     // Kelola Kelengkapan Berkas
//     $routes->get('/manage-kelengkapan-berkas', 'Admin::indexBerkas');
//     $routes->post('/manage-kelengkapan-berkas/valid/(:num)', 'Admin::valid/$1');
//     $routes->post('/manage-kelengkapan-berkas/tidakValid/(:num)', 'Admin::tidakValid/$1');

//     // Kelola Magang
//     $routes->get('/manage-magang', 'Admin::indexMagang');

//     $routes->get('detail-pendaftaran/(:num)', 'Admin::detail/$1');
//     $routes->post('manage-pendaftaran/approve/(:num)', 'Admin::approve/$1');
//     $routes->post('manage-pendaftaran/reject/(:num)', 'Admin::reject/$1');
//     $routes->post('manage-pendaftaran/konfirmasi/(:num)', 'User::konfirmasi/$1');
//     $routes->get('generateSuratPenerimaan/(:num)', 'GeneratePDF::suratPenerimaan/$1');
//     $routes->get('generateSuratPenerimaan2/(:num)', 'GeneratePDF::generateAndSavePDF/$1');
// });
