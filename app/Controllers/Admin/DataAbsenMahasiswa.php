<?php

namespace App\Controllers\Admin;

use App\Models\MatkulModel;

use App\Models\MahasiswaModel;

use App\Controllers\BaseController;
use App\Models\KehadiranModel;
use App\Models\PresensiMahasiswaModel;
use CodeIgniter\I18n\Time;

class DataAbsenSiswa extends BaseController
{
   protected MatkulModel $matkulModel;

   protected MahasiswaModel $mahasiswaModel;

   protected KehadiranModel $kehadiranModel;

   protected PresensiMahasiswaModel $presensiMahasiswa;

   protected string $currentDate;

   public function __construct()
   {
      $this->currentDate = Time::today()->toDateString();

      $this->mahasiswaModel = new MahasiswaModel();

      $this->kehadiranModel = new KehadiranModel();

      $this->matkulModel = new MatkulModel();

      $this->presensiMahasiswa = new PresensiMahasiswaModel();
   }

   public function index()
   {
      $matkul = $this->matkulModel->getDataKelas();

      $data = [
         'title' => 'Data Absen Mahasiswa',
         'ctx' => 'absen-Mahasiswa',
         'matkul' => $matkul
      ];

      return view('admin/absen/absen-mahasiswa', $data);
   }

   public function ambilDataMahasiswa()
   {
      // ambil variabel POST
      $matkul = $this->request->getVar('matkul');
      $idmatkul = $this->request->getVar('id_matkul');
      $tanggal = $this->request->getVar('tanggal');

      $lewat = Time::parse($tanggal)->isAfter(Time::today());

      $result = $this->presensiMahasiswa->getPresensiByKelasTanggal($idmatkul, $tanggal);

      $data = [
         'matkul' => $matkul,
         'data' => $result,
         'listKehadiran' => $this->kehadiranModel->getAllKehadiran(),
         'lewat' => $lewat
      ];

      return view('admin/absen/list-absen-mahasiswa', $data);
   }

   public function ambilKehadiran()
   {
      $idPresensi = $this->request->getVar('id_presensi');
      $idMahasiswa = $this->request->getVar('id_mahasiswa');

      $data = [
         'presensi' => $this->presensiMahasiswa->getPresensiById($idPresensi),
         'listKehadiran' => $this->kehadiranModel->getAllKehadiran(),
         'data' => $this->mahasiswaModel->getMahasiswaById($idMahasiswa)
      ];

      return view('admin/absen/ubah-kehadiran-modal', $data);
   }

   public function ubahKehadiran()
   {
      // ambil variabel POST
      $idKehadiran = $this->request->getVar('id_kehadiran');
      $idMahasiswa = $this->request->getVar('id_mahasiswa');
      $idmatkul = $this->request->getVar('id_matkul');
      $tanggal = $this->request->getVar('tanggal');
      $jamMasuk = $this->request->getVar('jam_masuk');
      $jamKeluar = $this->request->getVar('jam_keluar');
      $keterangan = $this->request->getVar('keterangan');

      $cek = $this->presensiMahasiswa->cekAbsen($idMahasiswa, $tanggal);

      $result = $this->presensiMahasiswa->updatePresensi(
         $cek == false ? NULL : $cek,
         $idMahasiswa,
         $idmatkul,
         $tanggal,
         $idKehadiran,
         $jamMasuk ?? NULL,
         $jamKeluar ?? NULL,
         $keterangan
      );

      $response['nama_mahasiswa'] = $this->mahasiswaModel->getMahasiswaById($idMahasiswa)['nama_mahasiswa'];

      if ($result) {
         $response['status'] = TRUE;
      } else {
         $response['status'] = FALSE;
      }

      return $this->response->setJSON($response);
   }
}
