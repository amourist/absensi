<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

use App\Models\DosenModel;
use App\Models\MatkulModel;
use App\Models\MahasiswaModel;

class GenerateQR extends BaseController
{
   protected MahasiswaModel $mahasiswaModel;
   protected MatkulModel $matkulModel;

   protected DosenModel $dosenModel;

   public function __construct()
   {
      $this->mahasiswaModel = new MahasiswaModel();
      $this->matkulModel = new MatkulModel();

      $this->dosenModel = new DosenModel();
   }

   public function index()
   {

      if (user()->toArray()['is_superadmin'] != '1') {
         return redirect()->to('admin');
      }


      $mahasiswa = $this->mahasiswaModel->getAllMahasiswaWithmatkul();
      $matkul = $this->matkulModel->getDataKelas();
      $dosen = $this->dosenModel->getAllDosen();

      $data = [
         'title' => 'Generate QR Code',
         'ctx' => 'qr',
         'mahasiswa' => $mahasiswa,
         'matkul' => $matkul,
         'dosen' => $dosen
      ];

      return view('admin/generate-qr/generate-qr', $data);
   }

   public function getSiswaByKelas()
   {
      $idMatkul = $this->request->getVar('idMatkul');

      $mahasiswa = $this->mahasiswaModel->getMahasiswaByMatkul($idMatkul);
      return $this->response->setJSON($mahasiswa);
   }
}
