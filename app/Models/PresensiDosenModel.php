<?php

namespace App\Models;

use App\Models\PresensiInterface;
use CodeIgniter\I18n\Time;
use CodeIgniter\Model;
use App\Libraries\enums\Kehadiran;

class PresensiDosenModel extends Model implements PresensiInterface
{
   protected $primaryKey = 'id_presensi';

   protected $allowedFields = [
      'id_dosen',
      'tanggal',
      'jam_masuk',
      'jam_keluar',
      'id_kehadiran',
      'keterangan'
   ];

   protected $table = 'tb_presensi_dosen';

   public function cekAbsen(string|int $id, string|Time $date)
   {
      $result = $this->where(['id_dosen' => $id, 'tanggal' => $date])->first();

      if (empty($result)) return false;

      return $result[$this->primaryKey];
   }

   public function absenMasuk(string $id, $date, $time)
   {
      $this->save([
         'id_dosen' => $id,
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

   public function getPresensiByIdDosenTanggal($idDosen, $date)
   {
      return $this->where(['id_dosen' => $idDosen, 'tanggal' => $date])->first();
   }

   public function getPresensiById(string $idPresensi)
   {
      return $this->where([$this->primaryKey => $idPresensi])->first();
   }

   public function getPresensiByTanggal($tanggal)
   {
      return $this->setTable('tb_dosen')
         ->select('*')
         ->join(
            "(SELECT id_presensi, id_dosen AS id_dosen_presensi, tanggal, jam_masuk, jam_keluar, id_kehadiran, keterangan FROM tb_presensi_dosen) tb_presensi_dosen",
            "{$this->table}.id_dosen = tb_presensi_dosen.id_dosen_presensi AND tb_presensi_dosen.tanggal = '$tanggal'",
            'left'
         )
         ->join(
            'tb_kehadiran',
            'tb_presensi_dosen.id_kehadiran = tb_kehadiran.id_kehadiran',
            'left'
         )
         ->orderBy("nama_dosen")
         ->findAll();
   }

   public function getPresensiByKehadiran(string $idKehadiran, $tanggal)
   {
      $this->join(
         'tb_dosen',
         "tb_presensi_dosen.id_dosen = tb_dosen.id_dosen AND tb_presensi_dosen.tanggal = '$tanggal'",
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
         $this->where(['tb_presensi_dosen.id_kehadiran' => $idKehadiran]);
         return $this->findAll();
      }
   }

   public function updatePresensi(
      $idPresensi,
      $idDosen,
      $tanggal,
      $idKehadiran,
      $jamMasuk,
      $jamKeluar,
      $keterangan
   ) {
      $presensi = $this->getPresensiByIdDosenTanggal($idDosen, $tanggal);

      $data = [
         'id_dosen' => $idDosen,
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
