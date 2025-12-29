<?php

namespace App\Controllers\Admin;

use App\Models\DosenModel;

use App\Controllers\BaseController;
use App\Models\KehadiranModel;
use App\Models\PresensiDosenModel;
use CodeIgniter\I18n\Time;

class DataAbsenDosen extends BaseController
{
   protected DosenModel $dosenModel;

   protected PresensiDosenModel $presensiDosen;

   protected KehadiranModel $kehadiranModel;

   public function __construct()
   {
      $this->dosenModel = new DosenModel();

      $this->presensiDosen = new PresensiDosenModel();

      $this->kehadiranModel = new KehadiranModel();
   }

   public function index()
   {
      $data = [
         'title' => 'Data Absen Dosen',
         'ctx' => 'absen-dosen',
      ];

      return view('admin/absen/absen-dosen', $data);
   }

   public function ambilDataDosen()
   {
      // ambil variabel POST
      $tanggal = $this->request->getVar('tanggal');

      $lewat = Time::parse($tanggal)->isAfter(Time::today());

      $result = $this->presensiDosen->getPresensiByTanggal($tanggal);

      $data = [
         'data' => $result,
         'listKehadiran' => $this->kehadiranModel->getAllKehadiran(),
         'lewat' => $lewat
      ];

      return view('admin/absen/list-absen-dosen', $data);
   }

   public function ambilKehadiran()
   {
      $idPresensi = $this->request->getVar('id_presensi');
      $idDosen = $this->request->getVar('id_dosen');

      $data = [
         'presensi' => $this->presensiDosen->getPresensiById($idPresensi),
         'listKehadiran' => $this->kehadiranModel->getAllKehadiran(),
         'data' => $this->dosenModel->getDosenById($idDosen)
      ];

      return view('admin/absen/ubah-kehadiran-modal', $data);
   }

   public function ubahKehadiran()
   {
      // ambil variabel POST
      $idKehadiran = $this->request->getVar('id_kehadiran');
      $idDosen = $this->request->getVar('id_dosen');
      $tanggal = $this->request->getVar('tanggal');
      $jamMasuk = $this->request->getVar('jam_masuk');
      $jamKeluar = $this->request->getVar('jam_keluar');
      $keterangan = $this->request->getVar('keterangan');

      $cek = $this->presensiDosen->cekAbsen($idDosen, $tanggal);

      $result = $this->presensiDosen->updatePresensi(
         $cek == false ? NULL : $cek,
         $idDosen,
         $tanggal,
         $idKehadiran,
         $jamMasuk ?? NULL,
         $jamKeluar ?? NULL,
         $keterangan
      );

      $response['nama_dosen'] = $this->dosenModel->getDosenById($idDosen)['nama_dosen'];

      if ($result) {
         $response['status'] = TRUE;
      } else {
         $response['status'] = FALSE;
      }

      return $this->response->setJSON($response);
   }
}
