<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */


$routes->get('/', 'HomeController::index', ['filter' => 'roleRedirect']);
$routes->get('/magang', 'HomeController::lowongan');
$routes->get('/tentang-kami', 'HomeController::tentang_kami');
$routes->get('get-images', 'HomeController::getImages');


$routes->get('sertifikat/verify/(:segment)', 'SertifikatController::verify/$1');
$routes->get('surat/verify/(:segment)', 'SertifikatController::verifySurat/$1');



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
// $routes->get('reset-password-default', 'AuthController::resetPasswordDefault');
// $routes->post('reset-password-default', 'AuthController::attemptResetDefault');

//Cronjob
$routes->get('cron/remind-unit/(:segment)', 'CronController::remindUnit/$1');
$routes->get('cron/autoTolakTidakKonfirmasi/(:segment)', 'CronController::autoTolakTidakKonfirmasi/$1');
// $routes->get('cron/autoTolakTidakValidasiBerkas/(:segment)', 'CronController::autoTolakTidakValidasiBerkas/$1');
$routes->get('cron/reminderLengkapiBerkas/(:segment)', 'CronController::reminderLengkapiBerkas/$1');
$routes->get('cron/autoKirimEmailAkhirMagang/(:segment)', 'CronController::autoKirimEmailAkhirProgram/$1');
$routes->get('cron/remind-pembimbing/(:segment)', 'CronController::remindSetPembimbing/$1');
$routes->get('cron/remind-upload-laporan/(:segment)', 'CronController::reminderUploadLaporan/$1');

$routes->get('must-change-password', 'ForcePassResetController::index');
$routes->post('must-change-password', 'ForcePassResetController::update');



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
$routes->group('admin', ['namespace' => 'App\Controllers\Admin','filter' => ['admin','forcePassReset'],], function($routes) {
    // Dashboard 
    $routes->get('dashboard', 'DashboardController::index');
    
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

    //Kelola Struktur
    $routes->get('manage-struktur', 'StrukturController::index');
    // $routes->get('manage-strukturnew', 'StrukturController::indexnew');
    $routes->post('struktur/save-position', 'StrukturController::savePosition');
    $routes->post('struktur/assign-user', 'StrukturController::assignUser');
    $routes->get('struktur/remove-user/(:num)', 'StrukturController::removeUser/$1');
    $routes->get('struktur/delete-position/(:num)', 'StrukturController::deletePOsition/$1');


    //Kelola Unit Kerja
    $routes->get('kelola-unit', 'UnitKerjaController::index');
    $routes->post('unit/save', 'UnitKerjaController::save');
    $routes->post('kelola-unit/update/(:num)', 'UnitKerjaController::update/$1');
    $routes->post('unit/delete/(:num)', 'UnitKerjaController::delete/$1');

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
    $routes->get('export-rfid', 'RfidController::export');

    // Kelola keyword
    $routes->get('kelola-keyword', 'KeywordController::index');
    $routes->post('keyword/save', 'KeywordController::save');
    $routes->post('keyword/update/(:num)', 'KeywordController::update/$1');
    $routes->post('keyword/delete/(:num)', 'KeywordController::delete/$1');
    $routes->get('export-rfid', 'KeywordController::export');


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
    
    //Kelola Logs
    $routes->get('access-log', 'LogsController::index');
    $routes->get('access-log/chart-data', 'LogsController::chartData');
    $routes->get('change-log', 'ChangeLogsController::index');
    $routes->post('change-log/save', 'ChangeLogsController::save');
    $routes->post('change-log/update/(:num)', 'ChangeLogsController::update/$1');
    $routes->post('change-log/delete/(:num)', 'ChangeLogsController::delete/$1');
    $routes->get('user-manual', 'ChangeLogsController::userManual');




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

    //Kelola Pendaftaran Ditolak/Gagal
    $routes->get('manage-pendaftaran-gagal', 'MagangController::indexGagal');


    // Kelola Peserta Magang 
    $routes->get('manage-magang', 'MagangController::pesertaMagang');
    $routes->post('manage-magang/getPesertaAjax', 'MagangController::getPesertaAjax');
    $routes->post('manage-magang/tolakLaporan/(:num)', 'MagangController::tolakLaporan/$1');
    $routes->post('manage-magang/tolakAbsensi/(:num)', 'MagangController::tolakAbsensi/$1');
    $routes->get('manage-magang/bukaUpload/(:num)', 'MagangController::bukaUpload/$1');
    $routes->get('manage-magang/tutupUpload/(:num)', 'MagangController::tutupUpload/$1');
    $routes->get('manage-magang/detailNilai/(:num)', 'MagangController::getDetailNilai/$1');
    $routes->get('manage-magang/detailPeserta/(:num)', 'MagangController::getDetailMagang/$1');
    $routes->get('manage-magang/getEditData/(:num)', 'MagangController::getEditData/$1');
    $routes->post('manage-magang/updateMagang/(:num)', 'MagangController::updateMagang/$1');
    $routes->get('manage-magang/getAvailableRfid', 'MagangController::getAvailableRfid');
    $routes->get('getDataPembimbing/(:num)', 'MagangController::getDataPembimbing/$1');
    $routes->post('setPembimbing/(:num)', 'MagangController::setPembimbing/$1');


    $routes->post('updateMagang/(:num)', 'MagangController::updateMagang/$1');
    $routes->post('batalkanMagang', 'MagangController::batalkanMagang');
    $routes->post('setRFID', 'MagangController::setRFID');
    $routes->post('returnRFID', 'MagangController::returnRFID');
    

    // $routes->post('tolakLaporan', 'MagangController::tolakLaporan');
    // $routes->post('tolakAbsensi', 'MagangController::tolakAbsensi');

    $routes->post('finalisasi/(:num)', 'MagangController::finalisasi/$1');

    $routes->get('export-peserta', 'MagangController::exportPeserta');
    $routes->get('export-alumni', 'MagangController::exportAlumni');

    // Kelola Feedback
    $routes->get('manage-feedback', 'FeedbackController::index');
    // $routes->post('feedback/save', 'InstansiController::save');
    // $routes->post('instansi/update/(:num)', 'InstansiController::update/$1');
    // $routes->post('instansi/delete/(:num)', 'InstansiController::delete/$1');

    //Alumni
    $routes->get('manage-alumni', 'MagangController::alumniMagang');
    $routes->get('cetak-sertifikat/(:num)', 'MagangController::cetakSertifikat/$1');


    //Kelola Hasil Tes Safety
    $routes->get('manage-hasil-tes', 'MagangController::safety');

    //Kelola Penelitian
    $routes->get('manage-penelitian', 'PenelitianController::index');
    $routes->get('penelitian/get-rekomendasi-unit/(:num)', 'PenelitianController::getRekomendasiUnit/$1');
    $routes->post('penelitian/terima-satu', 'PenelitianController::terimaSatu');

    $routes->get('penelitian/validasi-konfirmasi', 'PenelitianController::validasi');
    $routes->post('penelitian/validasi-konfirmasi/bulk', 'PenelitianController::bulkValidasi');
    $routes->get('manage-peserta-penelitian', 'PenelitianController::pesertaPenelitian');

    $routes->get('penelitian/kelengkapan-berkas', 'PenelitianController::berkas');
    $routes->get('penelitian/kelengkapan-berkas/(:num)', 'PenelitianController::berkas/$1');
    $routes->post('penelitian/kelengkapan-berkas/valid/(:num)', 'PenelitianController::valid/$1');
    $routes->post('penelitian/kelengkapan-berkas/tidakValid/(:num)', 'PenelitianController::tidakValid/$1');

    $routes->post('penelitian/setRFID', 'PenelitianController::setRFID');
    $routes->post('penelitian/returnRFID', 'PenelitianController::returnRFID');
    $routes->post('penelitian/tolakFormulir/(:num)', 'PenelitianController::tolakFormulir/$1');
    $routes->post('penelitian/tolakAbsensi/(:num)', 'PenelitianController::tolakAbsensi/$1');
    $routes->post('finalisasi-penelitian/(:num)', 'PenelitianController::finalisasi/$1');

    $routes->get('penelitian/detailPeserta/(:num)', 'PenelitianController::getDetailPenelitian/$1');
    $routes->get('penelitian/getEditData/(:num)', 'PenelitianController::getEditData/$1');
    $routes->post('penelitian/updatePenelitian/(:num)', 'PenelitianController::updatePenelitian/$1');
    $routes->post('batalkanPenelitian', 'PenelitianController::batalkanPenelitian');

    $routes->get('getDataPembimbingPenelitian/(:num)', 'PenelitianController::getDataPembimbing/$1');
    $routes->post('setPembimbingPenelitian/(:num)', 'PenelitianController::setPembimbing/$1');

    $routes->get('export-peserta-penelitian', 'PenelitianController::exportPeserta');



    $routes->post('penelitian/terima-banyak', 'PenelitianController::terimaBanyak');
    $routes->post('penelitian/tolak-banyak', 'PenelitianController::tolakBanyak');
    

    
    
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
$routes->group('pembimbing', ['namespace' => 'App\Controllers\Pembimbing', 'filter' => ['pembimbing','forcePassReset'],], function($routes) {
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

    $routes->get('approve-unit-penelitian', 'PenelitianController::approveUnit');
    $routes->post('approve-unit-penelitian/save', 'PenelitianController::approveUnitSave');

    $routes->get('approve-penelitian', 'PenelitianController::approvePenelitian');

    $routes->get('peserta-penelitian', 'PenelitianController::pesertaPenelitian');
    $routes->get('penelitian/detailPeserta/(:num)', 'PenelitianController::getDetailPenelitian/$1');
    $routes->post('assignPembimbingPenelitian/(:num)', 'PenelitianController::assignPembimbing/$1');
    $routes->post('updatePembimbingPenelitian/(:num)', 'PenelitianController::updatePembimbing/$1');
    $routes->post('approve-formulir', 'PenelitianController::approveFormulir');
    $routes->post('reject-formulir', 'PenelitianController::rejectFormulir');
    $routes->post('setApprovePenelitian', 'PenelitianController::setApprovePenelitian');








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
    $routes->get('magang/cetak-presensi/(:num)', 'MagangController::cetak_absensi/$1');

    $routes->get('sertifikat-magang', 'MagangController::sertifikatIndex');
    $routes->get('unggah-laporan', 'MagangController::unggahIndex');
    $routes->post('unggah-laporan/(:num)', 'MagangController::uploadLaporanAbsensi/$1');
    $routes->post('sertifikat/saveFeedback', 'MagangController::saveFeedback');

    $routes->get('cetak-sertifikat', 'MagangController::cetakSertifikat');


    //Penelitian
    $routes->get('penelitian', 'PenelitianController::index');
    $routes->post('penelitian/daftar', 'PenelitianController::daftar');
    $routes->get('status-penelitian', 'PenelitianController::statusPenelitian');
    $routes->post('penelitian/konfirmasi', 'PenelitianController::konfirmasi');
    $routes->post('penelitian/validasi-berkas', 'PenelitianController::validasiBerkas');
    $routes->get('pelaksanaan-penelitian', 'PenelitianController::pelaksanaan');
    $routes->get('penelitian/cetak-tanda-pengenal/(:num)', 'PenelitianController::cetakTandaPengenal/$1');
    $routes->get('penelitian/surat-pernyataan', 'PenelitianController::suratPernyataan');
    $routes->post('penelitian/setujui-surat-pernyataan', 'PenelitianController::setujuiPernyataan');
    $routes->get('safety-tesP', 'PenelitianController::safetyTes');
    $routes->post('safetyPenelitian/submit', 'PenelitianController::submitTes');
    $routes->get('unggah-form-penelitian', 'PenelitianController::unggahIndex');
    $routes->post('unggah-form-penelitian/(:num)', 'PenelitianController::uploadFormPenelitian/$1');
    $routes->get('surat-keterangan', 'PenelitianController::suratKeterangan');
    $routes->post('penelitian/saveFeedback', 'PenelitianController::saveFeedback');
    $routes->get('cetak-surat-keterangan', 'PenelitianController::cetakSuratKeterangan');
    $routes->get('penelitian/cetak-presensi/(:num)', 'PenelitianController::cetak_absensi/$1');
    $routes->get('penelitian/cetak-formulir/(:num)', 'PenelitianController::cetak_formulir_data/$1');






    //Old
    $routes->post('/pendaftaran/save', 'User::savedaftar');

    
    // $routes->post('penelitian/daftar', 'Penelitian::daftar');
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
