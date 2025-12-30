<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\I18n\Time;
use DateTime;
use DateInterval;
use DatePeriod;

use App\Models\DosenModel;
use App\Models\MatkulModel;
use App\Models\PresensiDosenModel;
use App\Models\MahasiswaModel;
use App\Models\PresensiMahasiswaModel;

class GenerateLaporan extends BaseController
{
   protected MahasiswaModel $mahasiswaModel;
   protected MatkulModel $matkulModel;

   protected DosenModel $dosenModel;

   protected PresensiMahasiswaModel $presensiMahasiswaModel;
   protected PresensiDosenModel $presensiDosenModel;

   public function __construct()
   {
      $this->mahasiswaModel = new MahasiswaModel();
      $this->matkulModel = new MatkulModel();

      $this->dosenModel = new DosenModel();

      $this->presensiMahasiswaModel = new PresensiMahasiswaModel();
      $this->presensiDosenModel = new PresensiDosenModel();
   }

   public function index()
   {
      $matkul = $this->matkulModel->getDataMatkul();
      $dosen = $this->dosenModel->getAllDosen();

      $mahasiswaPerMatkul = [];

      foreach ($matkul as $value) {
         array_push($mahasiswaPerMatkul, $this->mahasiswaModel->getMahasiswaByMatkul($value['id_matkul']));
      }

      $data = [
         'title' => 'Generate Laporan',
         'ctx' => 'laporan',
         'mahasiswaPerMatkul' => $mahasiswaPerMatkul,
         'matkul' => $matkul,
         'dosen' => $dosen
      ];

      return view('admin/generate-laporan/generate-laporan', $data);
   }

   public function generateLaporanMahasiswa()
   {
      $idMatkul = $this->request->getVar('matkul');
      $mahasiswa = $this->mahasiswaModel->getMahasiswaByMatkul($idMatkul);
      $type = $this->request->getVar('type');

      if (empty($mahasiswa)) {
         session()->setFlashdata([
            'msg' => 'Data mahasiswa kosong!',
            'error' => true
         ]);
         return redirect()->to('/admin/laporan');
      }

      $matkul = (array) $this->matkulModel->getMatkul($idMatkul);

      $bulan = $this->request->getVar('tanggalMahasiswa');

      // hari pertama dalam 1 bulan
      $begin = new Time($bulan, locale: 'id');
      // tanggal terakhir dalam 1 bulan
      $end = (new DateTime($begin->format('Y-m-t')))->modify('+1 day');
      // interval 1 hari
      $interval = DateInterval::createFromDateString('1 day');
      // buat array dari semua hari di bulan
      $period = new DatePeriod($begin, $interval, $end);

      $arrayTanggal = [];
      $dataAbsen = [];

      foreach ($period as $value) {
         // kecualikan hari sabtu dan minggu
         if (!($value->format('D') == 'Sat' || $value->format('D') == 'Sun')) {
            $lewat = Time::parse($value->format('Y-m-d'))->isAfter(Time::today());

            $absenByTanggal = $this->presensiMahasiswaModel
               ->getPresensiByMatkulTanggal($idMatkul, $value->format('Y-m-d'));

            $absenByTanggal['lewat'] = $lewat;

            array_push($dataAbsen, $absenByTanggal);
            array_push($arrayTanggal, Time::createFromInstance($value, locale: 'id'));
         }
      }

      $laki = 0;

      foreach ($mahasiswa as $value) {
         if ($value['jenis_kelamin'] != 'Perempuan') {
            $laki++;
         }
      }

      $data = [
         'tanggal' => $arrayTanggal,
         'bulan' => $begin->toLocalizedString('MMMM'),
         'listAbsen' => $dataAbsen,
         'listmahasiswa' => $mahasiswa,
         'rekapMahasiswa' => [
            'laki' => $laki,
            'perempuan' => count($mahasiswa) - $laki
         ],
         'matkul' => $matkul,
         'grup' => "matkul " . $matkul['matkul'],
      ];

      if ($type == 'doc') {
         $this->response->setHeader('Content-type', 'application/vnd.ms-word');
         $this->response->setHeader(
            'Content-Disposition',
            'attachment;Filename=laporan_absen_' . $matkul['matkul'] . '_' . $begin->toLocalizedString('MMMM-Y') . '.doc'
         );

         return view('admin/generate-laporan/laporan-mahasiswa', $data);
      }

      return view('admin/generate-laporan/laporan-mahasiswa', $data) . view('admin/generate-laporan/topdf');
   }

   public function generateLaporanDosen()
   {
      $dosen = $this->dosenModel->getAllDosen();
      $type = $this->request->getVar('type');

      if (empty($dosen)) {
         session()->setFlashdata([
            'msg' => 'Data dosen kosong!',
            'error' => true
         ]);
         return redirect()->to('/admin/laporan');
      }

      $bulan = $this->request->getVar('tanggalDosen');

      // hari pertama dalam 1 bulan
      $begin = new Time($bulan, locale: 'id');
      // tanggal terakhir dalam 1 bulan
      $end = (new DateTime($begin->format('Y-m-t')))->modify('+1 day');
      // interval 1 hari
      $interval = DateInterval::createFromDateString('1 day');
      // buat array dari semua hari di bulan
      $period = new DatePeriod($begin, $interval, $end);

      $arrayTanggal = [];
      $dataAbsen = [];

      foreach ($period as $value) {
         // kecualikan hari sabtu dan minggu
         if (!($value->format('D') == 'Sat' || $value->format('D') == 'Sun')) {
            $lewat = Time::parse($value->format('Y-m-d'))->isAfter(Time::today());

            $absenByTanggal = $this->presensiDosenModel
               ->getPresensiByTanggal($value->format('Y-m-d'));

            $absenByTanggal['lewat'] = $lewat;

            array_push($dataAbsen, $absenByTanggal);
            array_push($arrayTanggal, Time::createFromInstance($value, locale: 'id'));
         }
      }

      $laki = 0;

      foreach ($dosen as $value) {
         if ($value['jenis_kelamin'] != 'Perempuan') {
            $laki++;
         }
      }

      $data = [
         'tanggal' => $arrayTanggal,
         'bulan' => $begin->toLocalizedString('MMMM'),
         'listAbsen' => $dataAbsen,
         'listDosen' => $dosen,
         'jumlahDosen' => [
            'laki' => $laki,
            'perempuan' => count($dosen) - $laki
         ],
         'dosen' => 'dosen',
      ];

      if ($type == 'doc') {
         $this->response->setHeader('Content-type', 'application/vnd.ms-word');
         $this->response->setHeader(
            'Content-Disposition',
            'attachment;Filename=laporan_absen_dosen_' . $begin->toLocalizedString('MMMM-Y') . '.doc'
         );

         return view('admin/generate-laporan/laporan-dosen', $data);
      }

      return view('admin/generate-laporan/laporan-dosen', $data) . view('admin/generate-laporan/topdf');
   }
}
