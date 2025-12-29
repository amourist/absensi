<?php

namespace App\Models;

use CodeIgniter\Model;

class MatkulModel extends BaseModel
{
   protected $builder;

   public function __construct()
   {
      parent::__construct();
      $this->builder = $this->db->table('tb_matkul');
   }

   //input values
   public function inputValues()
   {
      return [
         'semester' => inputPost('semester'),
         'id_jurusan' => inputPost('id_jurusan'),
         'index_matkul' => inputPost('index_matkul'),
         'id_dosen' => inputPost('id_dosen'),
      ];
   }

   public function addMatkul()
   {
      $data = $this->inputValues();
      return $this->builder->insert($data);
   }

   public function editMatkul($id)
   {
      $matkul = $this->getMatkul($id);
      if (!empty($matkul)) {
         $data = $this->inputValues();
         return $this->builder->where('id_matkul', $matkul->id_matkul)->update($data);
      }
      return false;
   }

   public function getDataMatkul()
   {
      return $this->builder->select('tb_matkul.*, tb_jurusan.jurusan, tb_dosen.nama_dosen as nama_dosen, CONCAT(tb_matkul.semester, " ", tb_jurusan.jurusan, " ", tb_matkul.index_matkul) as matkul')
         ->join('tb_jurusan', 'tb_matkul.id_jurusan = tb_jurusan.id')
         ->join('tb_dosen', 'tb_matkul.id_dosen = tb_dosen.id_dosen', 'left')
         ->orderBy('tb_matkul.id_matkul')
         ->get()->getResult('array');
   }

   public function getMatkul($id)
   {
      return $this->builder->select('tb_matkul.*, tb_jurusan.jurusan, tb_dosen.nama_dosen as nama_dosen, CONCAT(tb_matkul.semester, " ", tb_jurusan.jurusan, " ", tb_matkul.index_matkul) as matkul')
         ->join('tb_jurusan', 'tb_matkul.id_jurusan = tb_jurusan.id')
         ->join('tb_dosen', 'tb_matkul.id_dosen = tb_dosen.id_dosen', 'left')
         ->where('id_matkul', cleanNumber($id))
         ->get()->getRow();
   }

   public function getMatkulByDosen($id_dosen)
   {
      return $this->builder->select('tb_matkul.*, tb_jurusan.jurusan, tb_dosen.nama_dosen as nama_dosen, CONCAT(tb_matkul.semester, " ", tb_jurusan.jurusan, " ", tb_matkul.index_matkul) as matkul')
         ->join('tb_jurusan', 'tb_matkul.id_jurusan = tb_jurusan.id')
         ->join('tb_dosen', 'tb_matkul.id_dosen = tb_dosen.id_dosen', 'left')
         ->where('id_dosen', cleanNumber($id_dosen))
         ->get()->getRowArray();
   }

   public function getCategoryTree($categoryId, $categories)
   {
      $tree = array();
      $categoryId = cleanNumber($categoryId);
      if (!empty($categoryId)) {
         array_push($tree, $categoryId);
      }
      return $tree;
   }

   public function getMatkulCountByJurusan($jurusanId)
   {
      $tree = array();
      $jurusanId = cleanNumber($jurusanId);
      if (!empty($jurusanId)) {
         array_push($tree, $jurusanId);
      }

      $jurusanIds = $tree;
      if (countItems($jurusanIds) < 1) {
         return array();
      }

      return $this->builder->whereIn('tb_matkul.id_jurusan', $jurusanIds, false)->countAllResults();
   }

   public function deleteMatkul($id)
   {
      $matkul = $this->getMatkul($id);
      if (!empty($matkul)) {
         return $this->builder->where('id_matkul', $matkul->id_matkul)->delete();
      }
      return false;
   }

   public function getAllMatkul()
   {
      return $this->select('tb_matkul.*, tb_jurusan.jurusan, CONCAT(tb_matkul.semester, " ", tb_jurusan.jurusan, " ", tb_matkul.index_matkul) as matkul')
         ->join('tb_jurusan', 'tb_matkul.id_jurusan = tb_jurusan.id', 'left')
         ->findAll();
   }
}
