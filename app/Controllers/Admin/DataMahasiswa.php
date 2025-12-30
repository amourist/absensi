<?php

namespace App\Controllers\Admin;

use App\Models\MahasiswaModel;
use App\Models\MatkulModel;

use App\Controllers\BaseController;
use App\Models\JurusanModel;
use App\Models\UploadModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class DataMahasiswa extends BaseController
{
   protected MahasiswaModel $mahasiswaModel;
   protected MatkulModel $matkulModel;
   protected JurusanModel $jurusanModel;

   protected $mahasiswaValidationRules = [
      'nis' => [
         'rules' => 'required|max_length[20]|min_length[4]',
         'errors' => [
            'required' => 'NIM harus diisi.',
            'is_unique' => 'NIM ini telah terdaftar.',
            'min_length[4]' => 'Panjang NIM minimal 4 karakter'
         ]
      ],
      'nama' => [
         'rules' => 'required|min_length[3]',
         'errors' => [
            'required' => 'Nama harus diisi'
         ]
      ],
      'id_matkul' => [
         'rules' => 'required',
         'errors' => [
            'required' => 'matkul harus diisi'
         ]
      ],
      'jk' => ['rules' => 'required', 'errors' => ['required' => 'Jenis kelamin wajib diisi']],
      'no_hp' => 'required|numeric|max_length[20]|min_length[5]',
      'rfid' => [
         'rules' => 'permit_empty|is_rfid_unique[,siswa]',
         'errors' => [
            'is_rfid_unique' => 'RFID code sudah digunakan.'
         ]
      ]
   ];

   public function __construct()
   {
      $this->mahasiswaModel = new MahasiswaModel();
      $this->matkulModel = new matkulModel();
      $this->jurusanModel = new JurusanModel();
   }

   public function index()
   {

      // hanya super admin
      if (user()->toArray()['is_superadmin'] != 1) {
         return redirect()->to('admin');
      }

      $data = [
         'title' => 'Data Siswa',
         'ctx' => 'siswa',
         'matkul' => $this->matkulModel->getDataMatkul(),
         'jurusan' => $this->jurusanModel->getDataJurusan()
      ];

      return view('admin/data/data-matkul', $data);
   }

   public function ambilDataMahasiswa()
   {
      $matkul = $this->request->getVar('matkul') ?? null;
      $jurusan = $this->request->getVar('jurusan') ?? null;

      $result = $this->mahasiswaModel->getAllMahasiswaWithKelas($matkul, $jurusan);

      $data = [
         'data' => $result,
         'empty' => empty($result)
      ];

      return view('admin/data/list-data-mahasiswa', $data);
   }

   public function formTambahMahasiswa()
   {
      $matkul = $this->matkulModel->getDataMatkul();

      $data = [
         'ctx' => 'mahasiswa',
         'matkul' => $matkul,
         'title' => 'Tambah Data Mahasiswa'
      ];

      return view('admin/data/create/create-data-mahasiswa', $data);
   }

   public function saveMahasiswa()
   {
      // validasi
      if (!$this->validate($this->mahasiswaValidationRules)) {
         $matkul = $this->matkulModel->getDataMatkul();

         $data = [
            'ctx' => 'mahasiswa',
            'kelas' => $matkul,
            'title' => 'Tambah Data Mahasiswa',
            'validation' => $this->validator,
            'oldInput' => $this->request->getVar()
         ];
         return view('/admin/data/create/create-data-mahasiswa', $data);
      }

      // simpan
      $result = $this->mahasiswaModel->createMahasiswa(
         nim: $this->request->getVar('nim'),
         nama: $this->request->getVar('nama'),
         idMatkul: intval($this->request->getVar('id_matkul')),
         jenisKelamin: $this->request->getVar('jk'),
         noHp: $this->request->getVar('no_hp'),
         rfid: $this->request->getVar('rfid')
      );

      if ($result) {
         session()->setFlashdata([
            'msg' => 'Tambah data berhasil',
            'error' => false
         ]);
         return redirect()->to('/admin/mahasiswa');
      }

      session()->setFlashdata([
         'msg' => 'Gagal menambah data',
         'error' => true
      ]);
      return redirect()->to('/admin/mahasiswa/create');
   }

   public function formEditMahasiswa($id)
   {
      $mahasiswa = $this->mahasiswaModel->getMahasiswaById($id);
      $matkul = $this->matkulModel->getDataMatkul();

      if (empty($mahasiswa) || empty($matkul)) {
         throw new PageNotFoundException('Data mahasiswa dengan id ' . $id . ' tidak ditemukan');
      }

      $data = [
         'data' => $mahasiswa,
         'matkul' => $matkul,
         'ctx' => 'mahasiswa',
         'title' => 'Edit Mahasiswa',
      ];

      return view('admin/data/edit/edit-data-mahasiswa', $data);
   }

   public function updateMahasiswa()
   {
      $idMahasiswa = $this->request->getVar('id');

      $this->mahasiswaValidationRules['rfid']['rules'] = "permit_empty|is_rfid_unique[{$idMahasiswa},mahasiswa]";

      $mahasiswaLama = $this->mahasiswaModel->getMahasiswaById($idMahasiswa);

      if ($mahasiswaLama['nim'] != $this->request->getVar('nis')) {
         $this->mahasiswaValidationRules['nim']['rules'] = 'required|max_length[20]|min_length[4]|is_unique[tb_mahasiswa.nim]';
      }

      $this->mahasiswaValidationRules['rfid']['rules'] = "permit_empty|is_rfid_unique[{$idMahasiswa},mahasiswa]";

      // validasi
      if (!$this->validate($this->mahasiswaValidationRules)) {
         $mahasiswa = $this->mahasiswaModel->getMahasiswaById($idMahasiswa);
         $matkul = $this->matkulModel->getDataMatkul();

         $data = [
            'data' => $mahasiswa,
            'matkul' => $matkul,
            'ctx' => 'mahasiswa',
            'title' => 'Edit Mahasiswa',
            'validation' => $this->validator,
            'oldInput' => $this->request->getVar()
         ];
         return view('/admin/data/edit/edit-data-mahasiswa', $data);
      }

      // update
      $result = $this->mahasiswaModel->updateMahasiswa(
         id: $idMahasiswa,
         nim: $this->request->getVar('nim'),
         nama: $this->request->getVar('nama'),
         idMatkul: intval($this->request->getVar('id_matkul')),
         jenisKelamin: $this->request->getVar('jk'),
         noHp: $this->request->getVar('no_hp'),
         rfid: $this->request->getVar('rfid')
      );

      if ($result) {
         session()->setFlashdata([
            'msg' => 'Edit data berhasil',
            'error' => false
         ]);
         return redirect()->to('/admin/mahasiswa');
      }

      session()->setFlashdata([
         'msg' => 'Gagal mengubah data',
         'error' => true
      ]);
      return redirect()->to('/admin/mahasiswa/edit/' . $idMahasiswa);
   }

   public function delete($id)
   {
      $result = $this->mahasiswaModel->delete($id);

      if ($result) {
         session()->setFlashdata([
            'msg' => 'Data berhasil dihapus',
            'error' => false
         ]);
         return redirect()->to('/admin/mahasiswa');
      }

      session()->setFlashdata([
         'msg' => 'Gagal menghapus data',
         'error' => true
      ]);
      return redirect()->to('/admin/mahasiswa');
   }

   /**
    * Delete Selected Posts
    */
   public function deleteSelectedMahasiswa()
   {
      $mahasiswaIds = inputPost('mahasiswa_ids');
      $this->mahasiswaModel->deleteMultiSelected($mahasiswaIds);
   }

   /*
    *-------------------------------------------------------------------------------------------------
    * IMPORT MAHASISWA
    *-------------------------------------------------------------------------------------------------
    */

   /**
    * Bulk Post Upload
    */
   public function bulkPostMahasiswa()
   {
      $data['title'] = 'Import Mahasiswa';
      $data['ctx'] = 'mahasiswa';
      $data['matkul'] = $this->matkulModel->getDataMatkul();

      return view('/admin/data/import-mahasiswa', $data);
   }

   /**
    * Generate CSV Object Post
    */
   public function generateCSVObjectPost()
   {
      $uploadModel = new UploadModel();
      //delete old txt files
      $files = glob(FCPATH . 'uploads/tmp/*.txt');
      if (!empty($files)) {
         foreach ($files as $item) {
            @unlink($item);
         }
      }
      $file = $uploadModel->uploadCSVFile('file');
      if (!empty($file) && !empty($file['path'])) {
         $obj = $this->mahasiswaModel->generateCSVObject($file['path']);
         if (!empty($obj)) {
            $data = [
               'result' => 1,
               'numberOfItems' => $obj->numberOfItems,
               'txtFileName' => $obj->txtFileName,
            ];
            echo json_encode($data);
            exit();
         }
      }
      echo json_encode(['result' => 0]);
   }

   /**
    * Import CSV Item Post
    */
   public function importCSVItemPost()
   {
      $txtFileName = inputPost('txtFileName');
      $index = inputPost('index');
      $mahasiswa = $this->mahasiswaModel->importCSVItem($txtFileName, $index);
      if (!empty($mahasiswa)) {
         $data = [
            'result' => 1,
            'mahasiswa' => $mahasiswa,
            'index' => $index
         ];
         echo json_encode($data);
      } else {
         $data = [
            'result' => 0,
            'index' => $index
         ];
         echo json_encode($data);
      }
   }

   /**
    * Download CSV File Post
    */
   public function downloadCSVFilePost()
   {
      $submit = inputPost('submit');
      $response = \Config\Services::response();
      if ($submit == 'csv_mahasiswa_template') {
         return $response->download(FCPATH . 'assets/file/csv_mahasiswa_template.csv', null);
      } elseif ($submit == 'csv_dosen_template') {
         return $response->download(FCPATH . 'assets/file/csv_dosen_template.csv', null);
      }
   }
}
