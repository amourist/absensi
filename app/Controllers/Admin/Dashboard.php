<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

use App\Models\DosenModel;
use App\Models\MahasiswaModel;
use App\Models\MatkulModel;
use App\Models\PetugasModel;
use App\Models\PresensiDosenModel;
use App\Models\PresensiMahasiswaModel;
use CodeIgniter\I18n\Time;
use Config\AbsensiKampus as ConfigAbsensiKampus;

class Dashboard extends BaseController
{
   protected MahasiswaModel $mahasiswaModel;
   protected DosenModel $dosenModel;

   protected MatkulModel $MatkulModel;

   protected PresensiMahasiswaModel $presensiMahasiswaModel;
   protected PresensiDosenModel $presensiDosenModel;

   protected PetugasModel $petugasModel;

   public function __construct()
   {
      $this->mahasiswaModel = new MahasiswaModel();
      $this->dosenModel = new DosenModel();
      $this->MatkulModel = new MatkulModel();
      $this->presensiMahasiswaModel = new PresensiMahasiswaModel();
      $this->presensiDosenModel = new PresensiDosenModel();
      $this->petugasModel = new PetugasModel();
   }

   public function index()
   {

      // jika bukan admin dan kepala sekolah, maka hentikan

      $user = user();
      $userRole = (int) $user->is_superadmin;

      if (!empty($user->id_dosen)) {
         return redirect()->to('lecturer/dashboard');
      }

      if ($userRole < 1) {
         return redirect()->to('scan');
      }



      $now = Time::now();

      $dateRange = [];
      $mahasiswaKehadiranArray = [];
      $dosenKehadiranArray = [];

      for ($i = 6; $i >= 0; $i--) {
         $date = $now->subDays($i)->toDateString();
         if ($i == 0) {
            $formattedDate = "Hari ini";
         } else {
            $t = $now->subDays($i);
            $formattedDate = "{$t->getDay()} " . substr($t->toFormattedDateString(), 0, 3);
         }
         array_push($dateRange, $formattedDate);
         array_push(
            $mahasiswaKehadiranArray,
            count($this->presensiMahasiswaModel
               ->join('tb_mahasiswa', 'tb_presensi_mahasiswa.id_mahasiswa = tb_mahasiswa.id_mahasiswa', 'left')
               ->where(['tb_presensi_mahasiswa.tanggal' => "$date", 'tb_presensi_mahasiswa.id_kehadiran' => '1'])->findAll())
         );
         array_push(
            $dosenKehadiranArray,
            count($this->presensiDosenModel
               ->join('tb_dosen', 'tb_presensi_dosen.id_dosen = tb_dosen.id_dosen', 'left')
               ->where(['tb_presensi_dosen.tanggal' => "$date", 'tb_presensi_dosen.id_kehadiran' => '1'])->findAll())
         );
      }

      $today = $now->toDateString();

      $data = [
         'title' => 'Dashboard',
         'ctx' => 'dashboard',

         'siswa' => $this->mahasiswaModel->getAllMahasiswaWithmatkul(),
         'guru' => $this->dosenModel->getAllDosen(),

         'kelas' => $this->MatkulModel->getDataMatkul(),

         'dateRange' => $dateRange,
         'dateNow' => $now->toLocalizedString('d MMMM Y'),

         'grafikKehadiranMahasiswa' => $mahasiswaKehadiranArray,
         'grafikkKehadiranDosen' => $dosenKehadiranArray,

         'jumlahKehadiranMahasiswa' => [
            'hadir' => count($this->presensiMahasiswaModel->getPresensiByKehadiran('1', $today)),
            'sakit' => count($this->presensiMahasiswaModel->getPresensiByKehadiran('2', $today)),
            'izin' => count($this->presensiMahasiswaModel->getPresensiByKehadiran('3', $today)),
            'alfa' => count($this->presensiMahasiswaModel->getPresensiByKehadiran('4', $today))
         ],

         'jumlahKehadiranGuru' => [
            'hadir' => count($this->presensiDosenModel->getPresensiByKehadiran('1', $today)),
            'sakit' => count($this->presensiDosenModel->getPresensiByKehadiran('2', $today)),
            'izin' => count($this->presensiDosenModel->getPresensiByKehadiran('3', $today)),
            'alfa' => count($this->presensiDosenModel->getPresensiByKehadiran('4', $today))
         ],

         'petugas' => $this->petugasModel->getAllPetugas(),
      ];

      return view('admin/dashboard', $data);
   }
}
