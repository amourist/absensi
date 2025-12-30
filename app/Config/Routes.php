<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

// Scan
$routes->get('/', 'Scan::index');

$routes->group('scan', function (RouteCollection $routes) {
   $routes->get('', 'Scan::index');
   $routes->get('masuk', 'Scan::index/Masuk');
   $routes->get('pulang', 'Scan::index/Pulang');

   $routes->post('cek', 'Scan::cekKode');
});



// Admin
$routes->group('admin', function (RouteCollection $routes) {
   // Admin dashboard
   $routes->get('', 'Admin\Dashboard::index');
   $routes->get('dashboard', 'Admin\Dashboard::index');

   // Matkul
   $routes->group('matkul', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
      $routes->get('/', 'MatkulController::index');
      $routes->get('tambah', 'MatkulController::tambahMatkul');
      $routes->post('tambahMatkulPost', 'MatkulController::tambahMatkulPost');
      $routes->get('edit/(:any)', 'MatkulController::editMatkul/$1');
      $routes->post('editMatkulPost', 'MatkulController::editMatkulPost');
      $routes->post('deleteMatkulPost', 'MatkulController::deleteMatkulPost');
      $routes->post('list-data', 'MatkulController::listData');
   });

   // Jurusan
   $routes->group('jurusan', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
      $routes->get('/', 'JurusanController::index');
      $routes->get('tambah', 'JurusanController::tambahJurusan');
      $routes->post('tambahJurusanPost', 'JurusanController::tambahJurusanPost');
      $routes->get('edit/(:any)', 'JurusanController::editJurusan/$1');
      $routes->post('editJurusanPost', 'JurusanController::editJurusanPost');
      $routes->post('deleteJurusanPost', 'JurusanController::deleteJurusanPost');
      $routes->post('list-data', 'JurusanController::listData');
   });

   // admin lihat data mahasiswa
   $routes->get('mahasiswa', 'Admin\DataMahasiswa::index');
   $routes->post('mahasiswa', 'Admin\DataMahasiswa::ambilDataMahasiswa');
   // admin tambah data siswa
   $routes->get('mahasiswa/create', 'Admin\DataMahasiswa::formTambahMahasiswa');
   $routes->post('mahasiswa/create', 'Admin\DataMahasiswa::saveMahasiswa');
   // admin edit data siswa
   $routes->get('mahasiswa/edit/(:any)', 'Admin\DataMahasiswa::formEditMahasiswa/$1');
   $routes->post('mahasiswa/edit', 'Admin\DataMahasiswa::updateMahasiswa');
   // admin hapus data siswa
   $routes->delete('mahasiswa/delete/(:any)', 'Admin\DataMahasiswa::delete/$1');
   $routes->get('mahasiswa/bulk', 'Admin\DataMahasiswa::bulkPostMahasiswa');

   // POST Data Mahasiswa

   $routes->group('mahasiswa', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
      $routes->post('downloadCSVFilePost', 'DataMahasiswa::downloadCSVFilePost');
      $routes->post('generateCSVObjectPost', 'DataMahasiswa::generateCSVObjectPost');
      $routes->post('importCSVItemPost', 'DataMahasiswa::importCSVItemPost');
      $routes->post('deleteSelectedMahasiswa', 'DataMahasiswa::deleteSelectedMahasiswa');
   });


   // admin lihat data dosen
   $routes->get('dosen', 'Admin\DataDosen::index');
   $routes->post('dosen', 'Admin\DataDosen::ambilDataDosen');
   // admin tambah data dosen
   $routes->get('dosen/create', 'Admin\DataDosen::formTambahDosen');
   $routes->post('dosen/create', 'Admin\DataDosen::saveDosen');
   // admin edit data dosen
   $routes->get('dosen/edit/(:any)', 'Admin\DataDosen::formEditDosen/$1');
   $routes->post('dosen/edit', 'Admin\DataDosen::updateDosen');
   // admin hapus data dosen
   $routes->delete('dosen/delete/(:any)', 'Admin\DataDosen::delete/$1');


   // admin lihat data absen mahasiswa
   $routes->get('absen-mahasiswa', 'Admin\DataAbsenMahasiswa::index');
   $routes->post('absen-mahasiswa', 'Admin\DataAbsenMahasiswa::ambilDataMahasiswa'); // ambil mahasiswa berdasarkan kelas dan tanggal
   $routes->post('absen-mahasiswa/kehadiran', 'Admin\DataAbsenMahasiswa::ambilKehadiran'); // ambil kehadiran mahasiswa
   $routes->post('absen-mahasiswa/edit', 'Admin\DataAbsenMahasiswa::ubahKehadiran'); // ubah kehadiran mahasiswa

   // admin lihat data absen dosen
   $routes->get('absen-dosen', 'Admin\DataAbsenDosen::index');
   $routes->post('absen-dosen', 'Admin\DataAbsenDosen::ambilDataDosen'); // ambil dosen berdasarkan tanggal
   $routes->post('absen-dosen/kehadiran', 'Admin\DataAbsenDosen::ambilKehadiran'); // ambil kehadiran dosen
   $routes->post('absen-dosen/edit', 'Admin\DataAbsenDosen::ubahKehadiran'); // ubah kehadiran dosen

   // admin generate QR
   $routes->get('generate', 'Admin\GenerateQR::index');
   $routes->post('generate/mahasiswa-by-matkul', 'Admin\GenerateQR::getMahasiswaByMatkul'); // ambil siswa berdasarkan kelas

   // Generate QR
   $routes->post('generate/mahasiswa', 'Admin\QRGenerator::generateQrMahasiswa');
   $routes->post('generate/dosen', 'Admin\QRGenerator::generateQrDosen');

   // Download QR
   $routes->get('qr/mahasiswa/download', 'Admin\QRGenerator::downloadAllQrMahasiswa');
   $routes->get('qr/mahasiswa/(:any)/download', 'Admin\QRGenerator::downloadQrMahasiswa/$1');
   $routes->get('qr/dosen/download', 'Admin\QRGenerator::downloadAllQrDosen');
   $routes->get('qr/dosen/(:any)/download', 'Admin\QRGenerator::downloadQrDosen/$1');

   // admin buat laporan
   $routes->get('laporan', 'Admin\GenerateLaporan::index');
   $routes->post('laporan/mahasiswa', 'Admin\GenerateLaporan::generateLaporanMahasiswa');
   $routes->post('laporan/dosen', 'Admin\GenerateLaporan::generateLaporanDosen');

   // superadmin lihat data petugas
   $routes->get('petugas', 'Admin\DataPetugas::index');
   $routes->post('petugas', 'Admin\DataPetugas::ambilDataPetugas');
   // superadmin tambah data petugas
   $routes->get('petugas/register', 'Admin\DataPetugas::registerPetugas');
   $routes->post('petugas/register', 'Admin\DataPetugas::registerPetugasPost');
   // superadmin edit data petugas
   $routes->get('petugas/edit/(:any)', 'Admin\DataPetugas::formEditPetugas/$1');
   $routes->post('petugas/edit', 'Admin\DataPetugas::updatePetugas');
   // superadmin hapus data petugas
   $routes->delete('petugas/delete/(:any)', 'Admin\DataPetugas::delete/$1');
   $routes->get('petugas/activate/(:any)', 'Admin\DataPetugas::toggleActivation/$1');

   // Settings
   $routes->group('general-settings', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
      $routes->get('/', 'GeneralSettings::index');
      $routes->post('update', 'GeneralSettings::generalSettingsPost');
   });
});

// Teacher
$routes->group('lecture', ['namespace' => 'App\Controllers\Lecture', 'filter' => 'login'], function (RouteCollection $routes) {
   $routes->get('/', 'Dashboard::index');
   $routes->get('dashboard', 'Dashboard::index');
   $routes->get('laporan', 'Reports::index');
   $routes->post('laporan/generate', 'Reports::generate');

   // QR Code Siswa สำหรับ Wali Kelas
   $routes->get('qr', 'QRCode::index');
   $routes->get('qr/download', 'QRCode::download');
   $routes->get('attendance', 'Dashboard::attendance');
   $routes->get('attendance/(:any)', 'Dashboard::attendance/$1');
   $routes->post('attendance/get-list', 'Dashboard::getAttendanceList');
   $routes->post('attendance/get-edit-modal', 'Dashboard::getEditModal');
   $routes->post('attendance/update-single', 'Dashboard::updateSingleAttendance');
});


/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
   require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
