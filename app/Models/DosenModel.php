<?php

namespace App\Models;

use CodeIgniter\Model;

class DosenModel extends Model
{
   protected $allowedFields = [
      'nip',
      'nama_dosen',
      'jenis_kelamin',
      'alamat',
      'no_hp',
      'unique_code',
      'rfid_code'
   ];

   protected $table = 'tb_dosen';

   protected $primaryKey = 'id_dosen';

   public function cekDosen(string $unique_code)
   {
      return $this->where(['unique_code' => $unique_code])
         ->orWhere(['rfid_code' => $unique_code])
         ->first();
   }

   public function getAllDosen()
   {
      return $this->orderBy('nama_dosen')->findAll();
   }

   public function getDosenById($id)
   {
      return $this->where([$this->primaryKey => $id])->first();
   }

   public function createDosen($nip, $nama, $jenisKelamin, $alamat, $noHp, $rfid = null)
   {
      return $this->save([
         'nip' => $nip,
         'nama_dosen' => $nama,
         'jenis_kelamin' => $jenisKelamin,
         'alamat' => $alamat,
         'no_hp' => $noHp,
         'no_hp' => $noHp,
         'unique_code' => sha1($nama . md5($nip . $nama . $noHp)) . substr(sha1($nip . rand(0, 100)), 0, 24),
         'rfid_code' => $rfid
      ]);
   }

   public function updateDosen($id, $nip, $nama, $jenisKelamin, $alamat, $noHp, $rfid = null)
   {
      return $this->save([
         $this->primaryKey => $id,
         'nip' => $nip,
         'nama_dosen' => $nama,
         'jenis_kelamin' => $jenisKelamin,
         'alamat' => $alamat,
         'no_hp' => $noHp,
         'rfid_code' => $rfid,
      ]);
   }
}
