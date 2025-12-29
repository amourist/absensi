<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12 col-md-12">
            <?php if (session()->getFlashdata('msg')): ?>
               <div class="pb-2 px-3">
                  <div class="alert alert-<?= session()->getFlashdata('error') == true ? 'danger' : 'success' ?> ">
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="material-icons">close</i>
                     </button>
                     <?= session()->getFlashdata('msg') ?>
                  </div>
               </div>
            <?php endif; ?>
            <a class="btn btn-primary ml-3 pl-3 py-3" href="<?= base_url('admin/mahasiswa/create'); ?>">
               <i class="material-icons mr-2">add</i> Tambah data mahasiswa
            </a>
            <a class="btn btn-primary ml-3 pl-3 py-3" href="<?= base_url('admin/mahasiswa/bulk'); ?>">
               <i class="material-icons mr-2">add</i> Import CSV
            </a>
            <button class="btn btn-danger ml-3 pl-3 py-3 btn-table-delete"
               onclick="deleteSelectedMahasiswa('Data yang sudah dihapus tidak bisa kembalikan');"><i
                  class="material-icons mr-2">delete_forever</i>Bulk Delete</button>
            <div class="card">
               <div class="card-header card-header-tabs card-header-primary">
                  <div class="nav-tabs-navigation">
                     <div class="row">
                        <div class="col-md-2">
                           <h4 class="card-title"><b>Daftar Mahasiswa</b></h4>
                           <p class="card-category">Semester <?= $generalSettings->tahun_ajaran; ?></p>
                        </div>
                        <div class="col-md-4">
                           <div class="nav-tabs-wrapper">
                              <span class="nav-tabs-title">Matkul:</span>
                              <ul class="nav nav-tabs" data-tabs="tabs">
                                 <li class="nav-item">
                                    <a class="nav-link active" onclick="matkul = null; trig()" href="#"
                                       data-toggle="tab">
                                       <i class="material-icons">check</i> Semua
                                       <div class="ripple-container"></div>
                                    </a>
                                 </li>
                                 <?php
                                 $tempMatkul = [];
                                 foreach ($matkul as $value): ?>
                                    <?php if (is_array($value) && isset($value['semester']) && !in_array($value['semester'], $tempMatkul)): ?>
                                       <li class="nav-item">
                                          <a class="nav-link" onclick="matkul = '<?= $value['semester']; ?>'; trig()" href="#"
                                             data-toggle="tab">
                                             <i class="material-icons">prodi</i> <?= $value['semester']; ?>
                                             <div class="ripple-container"></div>
                                          </a>
                                       </li>
                                       <?php array_push($tempMatkul, $value['semester']) ?>
                                    <?php endif; ?>
                                 <?php endforeach; ?>
                              </ul>
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="nav-tabs-wrapper">
                              <span class="nav-tabs-title">Jurusan:</span>
                              <ul class="nav nav-tabs" data-tabs="tabs">
                                 <li class="nav-item">
                                    <a class="nav-link active" onclick="jurusan = null; trig()" href="#"
                                       data-toggle="tab">
                                       <i class="material-icons">check</i> Semua
                                       <div class="ripple-container"></div>
                                    </a>
                                 </li>
                                 <?php foreach ($jurusan as $value): ?>
                                    <li class="nav-item">
                                       <a class="nav-link" onclick="jurusan = '<?= $value['jurusan']; ?>'; trig();"
                                          href="#" data-toggle="tab">
                                          <i class="material-icons">work</i> <?= $value['jurusan']; ?>
                                          <div class="ripple-container"></div>
                                       </a>
                                    </li>
                                 <?php endforeach; ?>
                              </ul>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div id="dataMahasiswa">
                  <p class="text-center mt-3">Daftar Mahasiswa muncul disini</p>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<script>
   var matkul = null;
   var jurusan = null;

   getDataMahasiswa(matkul, jurusan);

   function trig() {
      getDataMahasiswa(matkul, jurusan);
   }

   function getDataMahasiswa(_matkul = null, _matkul = null) {
      jQuery.ajax({
         url: "<?= base_url('/admin/mahasiswa'); ?>",
         type: 'post',
         data: {
            'matkul': _matkul,
            'jurusan': _jurusan
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
   }

   document.addEventListener('DOMContentLoaded', function () {
      $("#checkAll").click(function (e) {
         console.log(e);
         $('input:checkbox').not(this).prop('checked', this.checked);
      });
   });
</script>
<?= $this->endSection() ?>