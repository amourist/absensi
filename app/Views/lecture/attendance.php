<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
   <div class="container-fluid">
      <div class="card">
         <div class="card-body">
            <div class="row">
               <div class="col-md-3">
                  <div class="pt-3 pl-3 pb-2">
                     <h4><b>Tanggal</b></h4>
                     <input class="form-control" type="date" name="tanggal" id="tanggal"
                        value="<?= $date; ?>" onchange="getMahasiswa(<?= $matkul['id_matkul']; ?>, '<?= $matkul['matkul']; ?>')">
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="card" id="dataMahasiswa">
         <div class="card-body">
             <div class="text-center p-5">
                 <div class="spinner-border text-primary" role="status">
                     <span class="sr-only">Loading...</span>
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
    var lastIdMatkul = <?= $matkul['id_matkul']; ?>;
    var lastMatkul = '<?= $matkul['matkul']; ?>';

   $(document).ready(function() {
       getSiswa(lastIdMatkul, lastMatkul);
   });

   function getMahasiswa(lastIdMatkul, matkul) {
      var tanggal = $('#tanggal').val();

      jQuery.ajax({
         url: "<?= base_url('/lecture/attendance/get-list'); ?>",
         type: 'post',
         data: {
            'matkul': matkul,
            'id_matkul': lastIdMatkul,
            'tanggal': tanggal
         },
         success: function (response, status, xhr) {
            $('#dataMahasiswa').html(response);
         },
         error: function (xhr, status, thrown) {
            console.log(thrown);
            $('#dataMahasiswa').html(thrown);
         }
      });
      lastIdMatkuls = idMatkul;
      lastMatkul = matkul;
   }

   function getDataKehadiran(idPresensi, idMahasiswa) {
      jQuery.ajax({
         url: "<?= base_url('/lecture/attendance/get-edit-modal'); ?>",
         type: 'post',
         data: {
            'id_presensi': idPresensi,
            'id_mahasiswa': idMahasiswa
         },
         success: function (response, status, xhr) {
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

      jQuery.ajax({
         url: "<?= base_url('/lecture/attendance/update-single'); ?>",
         type: 'post',
         data: form,
         success: function (response, status, xhr) {
            if (response['status']) {
               getSiswa(lastIdMatkul, lastMatkul);
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