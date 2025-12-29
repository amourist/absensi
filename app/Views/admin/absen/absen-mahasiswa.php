<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12 col-md-12">
            <div class="card">
               <div class="card-body">
                  <div class="row justify-content-between">
                     <div class="col">
                        <div class="pt-3 pl-3">
                           <h4><b>Daftar Matkul</b></h4>
                           <p>Silakan pilih matkul</p>
                        </div>
                     </div>
                  </div>

                  <div class="card-body pt-1 px-3">
                     <div class="row">
                        <?php foreach ($matkul as $value): ?>
                           <?php
                           $idMatkul = $value['id_matkul'];
                           $namaMatkul = $value['mamtkul'];
                           ?>
                           <div class="col-md-3">
                              <button id="matkul-<?= $idMatkul; ?>"
                                 onclick="getMahasiswa(<?= $idMatkul; ?>, '<?= $namaMatkul; ?>')" class="btn btn-primary w-100">
                                 <?= $namaMatkul; ?>
                              </button>
                           </div>
                        <?php endforeach; ?>
                     </div>
                  </div>

                  <div class="row">
                     <div class="col-md-3">
                        <div class="pt-3 pl-3 pb-2">
                           <h4><b>Tanggal</b></h4>
                           <input class="form-control" type="date" name="tangal" id="tanggal"
                              value="<?= date('Y-m-d'); ?>" onchange="onDateChange()">
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="card" id="dataMahasiswa">
         <div class="card-body">
            <div class="row justify-content-between">
               <div class="col-auto me-auto">
                  <div class="pt-3 pl-3">
                     <h4><b>Absen Mahasiswa</b></h4>
                     <p>Daftar mahasiswa muncul disini</p>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>

   <!-- Modal ubah kehadiran -->
   <div class="modal fade" id="ubahModal" tabindex="-1" aria-labelledby="modalUbahKehadiran" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="modalUbahKehadiran">Ubah kehadiran</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div id="modalFormUbahMahasiswa"></div>
         </div>
      </div>
   </div>
</div>
<script>
   var lastIdMatkul;
   var lastMatkul;

   function onDateChange() {
      if (lastIdMatkul != null && lastMatkul != null) getMahasiswa(lastIdMatkul, lastMatkul);
   }

   function getMahasiswa(idMatkul, matkul) {
      var tanggal = $('#tanggal').val();

      updateBtn(idMatkul);
      jQuery.ajax({
         url: "<?= base_url('/admin/absen-mahasiswa'); ?>",
         type: 'post',
         data: {
            'matkul': matkul,
            'id_matkul': idMatkul,
            'tanggal': tanggal
         },
         success: function (response, status, xhr) {
            // console.log(status);
            $('#dataMahasiswa').html(response);

            $('html, body').animate({
               scrollTop: $("#dataMahasiswa").offset().top
            }, 500);
         },
         error: function (xhr, status, thrown) {
            console.log(thrown);
            $('#dataMahasiswa').html(thrown);
         }
      });

      lastIdMatkul = idMatkul;
      lastMatkul = matkul;
   }

   function updateBtn(id_btn) {
      for (let index = 1; index <= <?= count($matkul); ?>; index++) {
         if (index != id_btn) {
            $('#matkul-' + index).removeClass('btn-success');
            $('#matkul-' + index).addClass('btn-primary');
         } else {
            $('#matkul-' + index).removeClass('btn-primary');
            $('#matkul-' + index).addClass('btn-success');
         }
      }
   }

   function getDataKehadiran(idPresensi, idMahasiswa) {
      jQuery.ajax({
         url: "<?= base_url('/admin/absen-mahasiswa/kehadiran'); ?>",
         type: 'post',
         data: {
            'id_presensi': idPresensi,
            'id_mahasiswa': idMahasiswa
         },
         success: function (response, status, xhr) {
            // console.log(status);
            $('#modalFormUbahMahasiswa').html(response);
         },
         error: function (xhr, status, thrown) {
            console.log(thrown);
            $('#modalFormUbahMahasiswa').html(thrown);
         }
      });
   }

   function ubahKehadiran() {
      var tanggal = $('#tanggal').val();

      var form = $('#formUbah').serializeArray();

      form.push({
         name: 'tanggal',
         value: tanggal
      });

      console.log(form);

      jQuery.ajax({
         url: "<?= base_url('/admin/absen-mahasiswa/edit'); ?>",
         type: 'post',
         data: form,
         success: function (response, status, xhr) {
            // console.log(status);

            if (response['status']) {
               getMahasiswa(lastIdMatkul, lastMatkul);
               alert('Berhasil ubah kehadiran : ' + response['nama_mahasiswa']);
            } else {
               alert('Gagal ubah kehadiran : ' + response['nama_mahasiswa']);
            }
         },
         error: function (xhr, status, thrown) {
            console.log(thrown);
            alert('Gagal ubah kehadiran\n' + thrown);
         }
      });
   }
</script>
<?= $this->endSection() ?>