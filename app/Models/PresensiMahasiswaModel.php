<?php

namespace App\Models;

use App\Models\PresensiInterface;
use CodeIgniter\I18n\Time;
use CodeIgniter\Model;
use App\Libraries\enums\Kehadiran;

class PresensiMahasiswaModel extends Model implements PresensiInterface
{
   protected $primaryKey = 'id_presensi';

   protected $allowedFields = [
      'id_mahasiswa',
      'id_matkul',
      'tanggal',
      'jam_masuk',
      'jam_keluar',
      'id_kehadiran',
      'keterangan'
   ];

   protected $table = 'tb_presensi_mahasiswa';

   public function cekAbsen(string|int $id, string|Time $date)
   {
      $result = $this->where(['id_mahasiswa' => $id, 'tanggal' => $date])->first();
      if (empty($result)) return false;

      return $result[$this->primaryKey];
   }

   public function absenMasuk(string $id,  $date, $time, $idmatkul = '')
   {
      $this->save([
         'id_mahasiswa' => $id,
         'id_matkul' => $idmatkul,
         'tanggal' => $date,
         'jam_masuk' => $time,
         // 'jam_keluar' => '',
         'id_kehadiran' => Kehadiran::Hadir->value,
         'keterangan' => ''
      ]);
   }

   public function absenKeluar(string $id, $time)
   {
      $this->update($id, [
         'jam_keluar' => $time,
         'keterangan' => ''
      ]);
   }

   public function getPresensiByIdMahasiswaTanggal($idMahasiswa, $date)
   {
      return $this->where(['id_mahasiswa' => $idMahasiswa, 'tanggal' => $date])->first();
   }

   public function getPresensiById(string $idPresensi)
   {
      return $this->where([$this->primaryKey => $idPresensi])->first();
   }

   public function getPresensiByMatkulTanggal($idMatkul, $tanggal)
   {
      return $this->setTable('tb_mahasiswa')
         ->select('*')
         ->join(
            "(SELECT id_presensi, id_mahasiswa AS id_mahasiswa_presensi, tanggal, jam_masuk, jam_keluar, id_kehadiran, keterangan FROM tb_presensi_mahasiswa)tb_presensi_mahasiswa",
            "{$this->table}.id_mahasiswa = tb_presensi_mahasiswa.id_mahasiswa_presensi AND tb_presensi_mahasiswa.tanggal = '$tanggal'",
            'left'
         )
         ->join(
            'tb_kehadiran',
            'tb_presensi_siswa.id_kehadiran = tb_kehadiran.id_kehadiran',
            'left'
         )
         ->where("{$this->table}.id_matkul = $idMatkul")
         ->orderBy("nama_mahasiswa")
         ->findAll();
   }

   public function getPresensiByKehadiran(string $idKehadiran, $tanggal)
   {
      $this->join(
         'tb_mahasiswa',
         "tb_presensi_mahasiswa.id_mahasiswa = tb_mahasiswa.id_mahasiswa AND tb_presensi_mahasiswa.tanggal = '$tanggal'",
         'right'
      );

      if ($idKehadiran == '4') {
         $result = $this->findAll();

         $filteredResult = [];

         foreach ($result as $value) {
            if ($value['id_kehadiran'] != ('1' || '2' || '3')) {
               array_push($filteredResult, $value);
            }
         }

         return $filteredResult;
      } else {
         $this->where(['tb_presensi_mahasiswa.id_kehadiran' => $idKehadiran]);
         return $this->findAll();
      }
   }

   public function updatePresensi(
      $idPresensi,
      $idMahasiswa,
      $idMatkul,
      $tanggal,
      $idKehadiran,
      $jamMasuk,
      $jamKeluar,
      $keterangan
   ) {
      $presensi = $this->getPresensiByIdMahasiswaTanggal($idMahasiswa, $tanggal);

      $data = [
         'id_mahasiswa' => $idMahasiswa,
         'id_matkul' => $idMatkul,
         'tanggal' => $tanggal,
         'id_kehadiran' => $idKehadiran,
         'keterangan' => $keterangan ?? $presensi['keterangan'] ?? ''
      ];

      if ($idPresensi != null) {
         $data[$this->primaryKey] = $idPresensi;
      }

      if ($jamMasuk != null) {
         $data['jam_masuk'] = $jamMasuk;
      }

      if ($jamKeluar != null) {
         $data['jam_keluar'] = $jamKeluar;
      }

      return $this->save($data);
   }
}
