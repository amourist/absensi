<?php

namespace App\Models;

use CodeIgniter\Model;

class SiswaModel extends Model
{
   protected function initialize()
   {
      $this->allowedFields = [
         'nim',
         'nama_mahasiswa',
         'id_matkul',
         'jenis_kelamin',
         'no_hp',
         'unique_code',
         'rfid_code'
      ];
   }

   protected $table = 'tb_mahasiswa';

   protected $primaryKey = 'id_mahasiswa';

   public function cekMahasiswa(string $unique_code)
   {
      $this->select('tb_mahasiswa.*, tb_matkul.tingkat, tb_matkul.index_matkul, tb_jurusan.jurusan, CONCAT(tb_matkul.tingkat, " ", tb_jurusan.jurusan, " ", tb_matkul.index_matkul) as matkul')
         ->join(
            'tb_matkul',
            'tb_matkul.id_matkul = tb_mahasiswa.id_matkul',
            'LEFT'
         )->join(
            'tb_jurusan',
            'tb_jurusan.id = tb_matkul.id_jurusan',
            'LEFT'
         );
      return $this->where(['unique_code' => $unique_code])
         ->orWhere(['rfid_code' => $unique_code])
         ->first();
   }

   public function getMahasiswaById($id)
   {
      return $this->where([$this->primaryKey => $id])->first();
   }

   public function getAllMahasiswaWithmatkul($matkul = null, $jurusan = null)
   {
      $query = $this->select('tb_mahasiswa.*, tb_matkul.tingkat, tb_matkul.index_matkul, tb_jurusan.jurusan, CONCAT(tb_matkul.tingkat, " ", tb_jurusan.jurusan, " ", tb_matkul.index_matkul) as matkul')
         ->join(
            'tb_matkul',
            'tb_matkul.id_matkul = tb_mahasiswa.id_matkul',
            'LEFT'
         )->join(
            'tb_jurusan',
            'tb_matkul.id_jurusan = tb_jurusan.id',
            'LEFT'
         );

      if (!empty($matkul) && !empty($jurusan)) {
         $query = $this->where(['tb_jurusan.jurusan' => $jurusan, 'tb_matkul.tingkat' => $matkul]);
      } else if (empty($matkul) && !empty($jurusan)) {
         $query = $this->where(['tb_jurusan.jurusan' => $jurusan]);
      } else if (!empty($matkul) && empty($jurusan)) {
         $query = $this->where(['tb_matkul.tingkat' => $matkul]);
      } else {
         $query = $this;
      }

      return $query->orderBy('nama_mahasiswa')->findAll();
   }

   public function getMahasiswaByMatkul($id_matkul)
   {
      return $this->select('tb_mahasiswa.*, tb_matkul.tingkat, tb_matkul.index_matkul, tb_jurusan.jurusan, CONCAT(tb_matkul.tingkat, " ", tb_jurusan.jurusan, " ", tb_matkul.index_matkul) as matkul')
         ->join(
            'tb_matkul',
            'tb_matkul.id_matkul = tb_mahasiswa.id_matkul',
            'LEFT'
         )
         ->join('tb_jurusan', 'tb_matkul.id_jurusan = tb_jurusan.id', 'left')
         ->where(['tb_mahasiswa.id_matkul' => $id_matkul])
         ->orderBy('nama_mahasiswa')
         ->findAll();
   }

   public function createMahasiswa($nim, $nama, $idMatkul, $jenisKelamin, $noHp, $rfid = null)
   {
      return $this->save([
         'nim' => $nim,
         'nama_mahasiswa' => $nama,
         'id_matkul' => $idMatkul,
         'jenis_kelamin' => $jenisKelamin,
         'no_hp' => $noHp,
         'unique_code' => generateToken(),
         'rfid_code' => $rfid
      ]);
   }

   public function updateMahasiswa($id, $nim, $nama, $idMatkul, $jenisKelamin, $noHp, $rfid = null)
   {
      return $this->save([
         $this->primaryKey => $id,
         'nim' => $nim,
         'nama_mahasiswa' => $nama,
         'id_matkul' => $idMatkul,
         'jenis_kelamin' => $jenisKelamin,
         'no_hp' => $noHp,
         'rfid_code' => $rfid
      ]);
   }

   public function getSiswaCountByMatkul($matkulId)
   {
      $tree = array();
      $matkulId = cleanNumber($matkulId);
      if (!empty($matkulId)) {
         array_push($tree, $matkulId);
      }

      $matkulIds = $tree;
      if (countItems($matkulIds) < 1) {
         return array();
      }

      return $this->whereIn('tb_mahasiswa.id_matkul', $matkulIds, false)->countAllResults();
   }

   //generate CSV object
   public function generateCSVObject($filePath)
   {
      $array = array();
      $fields = array();
      $txtName = uniqid() . '.txt';
      $i = 0;
      $handle = fopen($filePath, 'r');
      if ($handle) {
         while (($row = fgetcsv($handle)) !== false) {
            if (empty($fields)) {
               $fields = $row;
               continue;
            }
            foreach ($row as $k => $value) {
               $array[$i][$fields[$k]] = $value;
            }
            $i++;
         }
         if (!feof($handle)) {
            return false;
         }
         fclose($handle);
         if (!empty($array)) {
            $txtFile = fopen(FCPATH . 'uploads/tmp/' . $txtName, 'w');
            fwrite($txtFile, serialize($array));
            fclose($txtFile);
            $obj = new \stdClass();
            $obj->numberOfItems = countItems($array);
            $obj->txtFileName = $txtName;
            @unlink($filePath);
            return $obj;
         }
      }
      return false;
   }

   //import csv item
   public function importCSVItem($txtFileName, $index)
   {
      $filePath = FCPATH . 'uploads/tmp/' . $txtFileName;
      $file = fopen($filePath, 'r');
      $content = fread($file, filesize($filePath));
      $array = @unserialize($content);
      if (!empty($array)) {
         $i = 1;
         foreach ($array as $item) {
            if ($i == $index) {
               $data = array();
               $data['nim'] = getCSVInputValue($item, 'nim', 'int');
               $data['nama_mahasiswa'] = getCSVInputValue($item, 'nama_mahasiswa');
               $data['id_matkul'] = getCSVInputValue($item, 'id_matkul', 'int');
               $data['jenis_kelamin'] = getCSVInputValue($item, 'jenis_kelamin');
               $data['no_hp'] = getCSVInputValue($item, 'no_hp');
               $data['unique_code'] = generateToken();

               $this->insert($data);
               return $data;
            }
            $i++;
         }
      }
   }

   public function getmahasiswa($id)
   {
      return $this->where('id_mahasiswa', cleanNumber($id))->get()->getRow();
   }

   //delete post
   public function deletemahasiswa($id)
   {
      $mahasiswa = $this->getmahasiswa($id);
      if (!empty($mahasiswa)) {
         //delete mahasiswa
         return $this->where('id_mahasiswa', $mahasiswa->id_mahasiswa)->delete();
      }
      return false;
   }

   //delete multi post
   public function deleteMultiSelected($mahasiswaIds)
   {
      if (!empty($mahasiswaIds)) {
         foreach ($mahasiswaIds as $id) {
            $this->deleteMahasiswa($id);
         }
      }
   }
}
