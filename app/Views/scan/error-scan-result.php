<h3 class="text-danger"><?= $msg; ?></h3>

<?php

use App\Libraries\enums\TipeUser;

if (empty($type)) {
   return;
} else {
   switch ($type) {
      case TipeUser::Mahasiswa: ?>
         <div class="row w-100">
            <div class="col">
               <p>Nama : <b><?= $data['nama_mahasiswa']; ?></b></p>
               <p>NIM : <b><?= $data['nim']; ?></b></p>
               <p>Matkul : <b><?= $data['matkul']; ?></b></p>
            </div>
            <div class="col">
               <?= jam($presensi ?? []); ?>
            </div>
         </div>
         <?php break;

      case TipeUser::Dosen: ?>
         <div class="row w-100">
            <div class="col">
               <p>Nama : <b><?= $data['nama_dosen']; ?></b></p>
               <p>NIP : <b><?= $data['nip']; ?></b></p>
               <p>No HP : <b><?= $data['no_hp']; ?></b></p>
            </div>
            <div class="col">
               <?= jam($presensi ?? []); ?>
            </div>
         </div>
         <?php break;

      default: ?>
         <p class="text-danger">Tipe tidak valid</p>
         <?php break;
   }
}

function jam($presensi)
{
   ?>
   <p>Jam masuk : <b class="text-info"><?= $presensi['jam_masuk'] ?? '-'; ?></b></p>
   <p>Jam pulang : <b class="text-info"><?= $presensi['jam_keluar'] ?? '-'; ?></b></p>
   <?php
}

?>