<?php

namespace App\Controllers\Admin;

use App\Models\DosenModel;

use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;

class DataDosen extends BaseController
{
   protected DosenModel $dosenModel;

   protected $dosenValidationRules = [
      'nip' => [
         'rules' => 'required|max_length[20]|min_length[16]',
         'errors' => [
            'required' => 'NIP harus diisi.',
            'is_unique' => 'NIP ini telah terdaftar.',
            'min_length[16]' => 'Panjang NIP minimal 16 karakter'
         ]
      ],
      'nama' => [
         'rules' => 'required|min_length[3]',
         'errors' => [
            'required' => 'Nama harus diisi'
         ]
      ],
      'jk' => ['rules' => 'required', 'errors' => ['required' => 'Jenis kelamin wajib diisi']],
      'no_hp' => 'required|numeric|max_length[20]|min_length[5]',
      'rfid' => [
         'rules' => 'permit_empty|is_rfid_unique[,dosen]',
         'errors' => [
            'is_rfid_unique' => 'RFID code sudah digunakan.'
         ]
      ]
   ];

   public function __construct()
   {
      $this->dosenModel = new DosenModel();
   }

   public function index()
   {
      if (user()->toArray()['is_superadmin'] != '1') {
         return redirect()->to('admin');
      }


      $data = [
         'title' => 'Data Dosen',
         'ctx' => 'dosen',
      ];

      return view('admin/data/data-dosen', $data);
   }

   public function ambilDataDosen()
   {
      $result = $this->dosenModel->getAllDosen();

      $data = [
         'data' => $result,
         'empty' => empty($result)
      ];

      return view('admin/data/list-data-dosen', $data);
   }

   public function formTambahDosen()
   {
      $data = [
         'ctx' => 'dosen',
         'title' => 'Tambah Data Dosen'
      ];

      return view('admin/data/create/create-data-dosen', $data);
   }

   public function saveDosen()
   {
      // validasi
      if (!$this->validate($this->dosenValidationRules)) {
         $data = [
            'ctx' => 'dosen',
            'title' => 'Tambah Data Dosen',
            'validation' => $this->validator,
            'oldInput' => $this->request->getVar()
         ];
         return view('/admin/data/create/create-data-dosen', $data);
      }

      // simpan
      $result = $this->dosenModel->createDosen(
         nip: $this->request->getVar('nip'),
         nama: $this->request->getVar('nama'),
         jenisKelamin: $this->request->getVar('jk'),
         alamat: $this->request->getVar('alamat'),
         noHp: $this->request->getVar('no_hp'),
         rfid: $this->request->getVar('rfid')
      );

      if ($result) {
         session()->setFlashdata([
            'msg' => 'Tambah data berhasil',
            'error' => false
         ]);
         return redirect()->to('/admin/dosen');
      }

      session()->setFlashdata([
         'msg' => 'Gagal menambah data',
         'error' => true
      ]);
      return redirect()->to('/admin/dosen/create/');
   }

   public function formEditDosen($id)
   {
      $dosen = $this->dosenModel->getDosenById($id);

      if (empty($dosen)) {
         throw new PageNotFoundException('Data dosen dengan id ' . $id . ' tidak ditemukan');
      }

      $data = [
         'data' => $dosen,
         'ctx' => 'dosen',
         'title' => 'Edit Data Dosen',
      ];

      return view('admin/data/edit/edit-data-dosen', $data);
   }

   public function updateDosen()
   {
      $idDosen = $this->request->getVar('id');

      $this->dosenValidationRules['rfid']['rules'] = "permit_empty|is_rfid_unique[{$idDosen},dosen]";

      // validasi
      if (!$this->validate($this->dosenValidationRules)) {
         $data = [
            'data' => $this->dosenModel->getDosenById($idDosen),
            'ctx' => 'dosen',
            'title' => 'Edit Data Dosen',
            'validation' => $this->validator,
            'oldInput' => $this->request->getVar()
         ];
         return view('/admin/data/edit/edit-data-dosen', $data);
      }

      // update
      $result = $this->dosenModel->updateDosen(
         id: $idDosen,
         nip: $this->request->getVar('nip'),
         nama: $this->request->getVar('nama'),
         jenisKelamin: $this->request->getVar('jk'),
         alamat: $this->request->getVar('alamat'),
         noHp: $this->request->getVar('no_hp'),
         rfid: $this->request->getVar('rfid')
      );

      if ($result) {
         session()->setFlashdata([
            'msg' => 'Edit data berhasil',
            'error' => false
         ]);
         return redirect()->to('/admin/dosen');
      }

      session()->setFlashdata([
         'msg' => 'Gagal mengubah data',
         'error' => true
      ]);
      return redirect()->to('/admin/dosen/edit/' . $idDosen);
   }

   public function delete($id)
   {
      $result = $this->dosenModel->delete($id);

      if ($result) {
         session()->setFlashdata([
            'msg' => 'Data berhasil dihapus',
            'error' => false
         ]);
         return redirect()->to('/admin/dosen');
      }

      session()->setFlashdata([
         'msg' => 'Gagal menghapus data',
         'error' => true
      ]);
      return redirect()->to('/admin/dosen');
   }
}
