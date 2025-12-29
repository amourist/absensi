<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<style>
  .progress-siswa {
    height: 5px;
    border-radius: 0px;
    background-color: rgb(186, 124, 222);
  }

  .progress-guru {
    height: 5px;
    border-radius: 0px;
    background-color: rgb(58, 192, 85);
  }

  .my-progress-bar {
    height: 5px;
    border-radius: 0px;
  }
</style>
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
        <div class="card">
          <div class="card-header card-header-danger">
            <h4 class="card-title"><b>Generate QR Code</b></h4>
            <p class="card-category">Generate QR berdasarkan kode unik data mahasiswa/dosen</p>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="card">
                  <div class="card-body">
                    <h4 class="text-primary"><b>Data Mahasiswa</b></h4>
                    <p>Total jumlah mahasiswa : <b><?= count($mahasiswa); ?></b>
                      <br>
                      <a href="<?= base_url('admin/mahasiswa'); ?>">Lihat data</a>
                    </p>
                    <div class="row px-2">
                      <div class="col-12 col-xl-6 px-1">
                        <button onclick="generateAllQrMahasiswa()" class="btn btn-primary p-2 px-md-4 w-100">
                          <div class="d-flex align-items-center justify-content-center" style="gap: 12px;">
                            <div>
                              <i class="material-icons" style="font-size: 24px;">qr_code</i>
                            </div>
                            <div>
                              <h4 class="d-inline font-weight-bold">Generate All</h4>
                              <div id="progressMahasiswa" class="d-none mt-2">
                                <span id="progressTextMahasiswa"></span>
                                <i id="progressSelesaiMahasiswa" class="material-icons d-none" class="d-none">check</i>
                                <div class="progress progress-mahasiswa">
                                  <div id="progressBarMahasiswa" class="progress-bar my-progress-bar bg-white"
                                    style="width: 0%;" role="progressbar" aria-valuenow="" aria-valuemin=""
                                    aria-valuemax=""></div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </button>
                      </div>
                      <div class="col-12 col-xl-6 px-1">
                        <a href="<?= base_url('admin/qr/mahasiswa/download'); ?>" class="btn btn-primary p-2 px-md-4 w-100">
                          <div class="d-flex align-items-center justify-content-center" style="gap: 12px;">
                            <div>
                              <i class="material-icons" style="font-size: 24px;">cloud_download</i>
                            </div>
                            <div>
                              <div class="text-start">
                                <h4 class="d-inline font-weight-bold">Download All</h4>
                              </div>
                            </div>
                          </div>
                        </a>
                      </div>
                    </div>
                    <hr>
                    <br>
                    <h4 class="text-primary"><b>Generate per Mata Kuliah</b></h4>
                    <form action="<?= base_url('admin/qr/siswa/download'); ?>" method="get">
                      <select name="id_matkul" id="matkulSelect" class="custom-select mb-3" required>
                        <option value="">--Pilih mata Kuliah--</option>
                        <?php foreach ($matkul as $value): ?>
                          <option id="idMatkul<?= $value['id_matkul']; ?>" value="<?= $value['id_matkul']; ?>">
                            <?= $value['matkul']; ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                      <b class="text-danger mt-2" id="textErrorKelas"></b>
                      <div class="row px-2">
                        <div class="col-12 col-xl-6 px-1">
                          <button type="button" onclick="generateQrMahasiswaByKelas()"
                            class="btn btn-primary p-2 px-md-4 w-100">
                            <div class="d-flex align-items-center justify-content-center" style="gap: 12px;">
                              <div>
                                <i class="material-icons" style="font-size: 24px;">qr_code</i>
                              </div>
                              <div>
                                <div class="text-start">
                                  <h6 class="d-inline">Generate per Mata Kuliah</h6>
                                </div>
                                <div id="progressMatkul" class="d-none">
                                  <span id="progressTextMatkul"></span>
                                  <i id="progressSelesaiMatkul" class="material-icons d-none" class="d-none">check</i>
                                  <div class="progress progress-siswa d-none" id="progressBarBgMatkul">
                                    <div id="progressBarMatkul" class="progress-bar my-progress-bar bg-white"
                                      style="width: 0%;" role="progressbar" aria-valuenow="" aria-valuemin=""
                                      aria-valuemax=""></div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </button>
                        </div>
                        <div class="col-12 col-xl-6 px-1">
                          <button type="submit" class="btn btn-primary p-2 px-md-4 w-100">
                            <div class="d-flex align-items-center justify-content-center" style="gap: 12px;">
                              <div>
                                <i class="material-icons" style="font-size: 24px;">cloud_download</i>
                              </div>
                              <div>
                                <div class="text-start">
                                  <h6 class="d-inline">Download Per Mata Kuliah</h6>
                                </div>
                              </div>
                            </div>
                          </button>
                        </div>
                      </div>
                    </form>
                    <br>
                    <p>
                      Untuk generate/download QR Code per masing-masing mahasiswa kunjungi
                      <a href="<?= base_url('admin/mahasiswa'); ?>"><b>data siswa</b></a>
                    </p>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="card">
                  <div class="card-body">
                    <h4 class="text-success"><b>Data Dosen</b></h4>
                    <p>Total jumlah dosen : <b><?= count($dosen); ?></b>
                      <br>
                      <a href="<?= base_url('admin/dosen'); ?>" class="text-success">Lihat data</a>
                    </p>
                    <div class="row px-2">
                      <div class="col-12 col-xl-6 px-1">
                        <button onclick="generateAllQrGuru()" class="btn btn-success p-2 px-md-4 w-100">
                          <div class="d-flex align-items-center justify-content-center" style="gap: 12px;">
                            <div>
                              <i class="material-icons" style="font-size: 24px;">qr_code</i>
                            </div>
                            <div>
                              <h4 class="d-inline font-weight-bold">Generate All</h4>
                              <div>
                                <div id="progressDosen" class="d-none mt-2">
                                  <span id="progressTextDosen"></span>
                                  <i id="progressSelesaiDosen" class="material-icons d-none" class="d-none">check</i>
                                  <div class="progress progress-dosen">
                                    <div id="progressBarDosen" class="progress-bar my-progress-bar bg-white"
                                      style="width: 0%;" role="progressbar" aria-valuenow="" aria-valuemin=""
                                      aria-valuemax=""></div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </button>
                      </div>
                      <div class="col-12 col-xl-6 px-1">
                        <a href="<?= base_url('admin/qr/dosen/download'); ?>" class="btn btn-success p-2 px-md-4 w-100">
                          <div class="d-flex align-items-center justify-content-center" style="gap: 12px;">
                            <div>
                              <i class="material-icons" style="font-size: 24px;">cloud_download</i>
                            </div>
                            <div>
                              <div class="text-start">
                                <h4 class="d-inline font-weight-bold">Download All</h4>
                              </div>
                            </div>
                          </div>
                        </a>
                      </div>
                    </div>
                    <br>
                    <br>
                    <p>
                      Untuk generate/download QR Code per masing-masing dosen kunjungi
                      <a href="<?= base_url('admin/dosen'); ?>" class="text-success"><b>data dosen</b></a>
                    </p>
                  </div>
                </div>
                <p class="text-danger">
                  <i class="material-icons" style="font-size: 16px;">warning</i>
                  File image QR Code tersimpan di [folder website]/public/uploads/
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  const dataDosen = [
    <?php foreach ($dosen as $value) {
      echo "{
              'nama' : `$value[nama_dosen]`,
              'unique_code' : `$value[unique_code]`,
              'nomor' : `$value[nip]`
            },";
    }
    ; ?>
  ];

  const dataSiswa = [
    <?php foreach ($mahasiswa as $value) {
      echo "{
              'nama' : `$value[nama_mahasiswa]`,
              'unique_code' : `$value[unique_code]`,
              'id_matkul' : `$value[id_matkul]`,
              'nomor' : `$value[nim]`
            },";
    }
    ; ?>
  ];

  var dataSiswaPerKelas = [];

  function generateAllQrSiswa() {
    var i = 1;
    $('#progressMahasiswa').removeClass('d-none');
    $('#progressBarMahasiswa')
      .attr('aria-valuenow', '0')
      .attr('aria-valuemin', '0')
      .attr('aria-valuemax', dataMahasiswa.length)
      .attr('style', 'width: 0%;');

    dataSiswa.forEach(element => {
      jQuery.ajax({
        url: "<?= base_url('admin/generate/mahasiswa'); ?>",
        type: 'post',
        data: {
          nama: element['nama'],
          unique_code: element['unique_code'],
          id_matkul: element['id_matkul'],
          nomor: element['nomor']
        },
        success: function (response) {
          if (!response) return;
          if (i != dataSiswa.length) {
            $('#progressTextMahasiswa').html('Progres: ' + i + '/' + dataMahasiswa.length);
          } else {
            $('#progressTextMahasiswa').html('Progres: ' + i + '/' + dataMahasiswa.length + ' selesai');
            $('#progressSelesaiMahasiswa').removeClass('d-none');
          }

          $('#progressBarMahasiswa')
            .attr('aria-valuenow', i)
            .attr('style', 'width: ' + (i / dataMahasiswa.length) * 100 + '%;');
          i++;
        }
      });
    });
  }

  function generateQrMahasiswaByMatkul() {
    var i = 1;

    idMatkul = $('#matkulSelect').val();

    if (idMatkul == '') {
      $('#progressMatkul').addClass('d-none');
      $('#textErrorMatkul').html('Pilih mata kuliah terlebih dahulu');
      return;
    }

    matkul = $('#idMatkul' + idMatkul).html();

    jQuery.ajax({
      url: "<?= base_url('admin/generate/mahasiswa-by-matkul'); ?>",
      type: 'post',
      data: {
        idMatkul: idMatkul
      },
      success: function (response) {
        dataSiswaPerKelas = response;

        if (dataSiswaPerKelas.length < 1) {
          $('#progressMatkul').addClass('d-none');
          $('#textErrorMatkul').html('Data mahasiswa mata kuliah ' + matkul + ' tidak ditemukan');
          return;
        }

        $('#textErrorMatkul').html('')

        $('#progressMatkul').removeClass('d-none');
        $('#progressBarBgMatkul')
          .removeClass('d-none');
        $('#progressBarMatkul')
          .removeClass('d-none')
          .attr('aria-valuenow', '0')
          .attr('aria-valuemin', '0')
          .attr('aria-valuemax', dataSiswaPerKelas.length)
          .attr('style', 'width: 0%;');

        dataSiswaPerKelas.forEach(element => {
          jQuery.ajax({
            url: "<?= base_url('admin/generate/mahasiswa'); ?>",
            type: 'post',
            data: {
              nama: element['nama_mahasiswa'],
              unique_code: element['unique_code'],
              id_kelas: element['id_matkul'],
              nomor: element['nis']
            },
            success: function (response) {
              if (!response) return;
              if (i != dataMahasiswaperMatkul.length) {
                $('#progressTextMatkul').html('Progres: ' + i + '/' + dataMahasiswaPerMatkul.length);
              } else {
                $('#progressTextMatkul').html('Progres: ' + i + '/' + dataMahasiswaPerMatkul.length + ' selesai');
                $('#progressSelesaiMatkul').removeClass('d-none');
              }

              $('#progressBarMatkul')
                .attr('aria-valuenow', i)
                .attr('style', 'width: ' + (i / dataSiswaPerMatkul.length) * 100 + '%;');
              i++;
            },
            error: function (xhr, status, thrown) {
              console.error(xhr + status + thrown);
            }
          });
        });
      }
    });
  }

  function generateAllQrDosen() {
    var i = 1;
    $('#progressDosen').removeClass('d-none');
    $('#progressBarDosen')
      .attr('aria-valuenow', '0')
      .attr('aria-valuemin', '0')
      .attr('aria-valuemax', dataDosen.length)
      .attr('style', 'width: 0%;');

    dataDosen.forEach(element => {
      jQuery.ajax({
        url: "<?= base_url('admin/generate/dosen'); ?>",
        type: 'post',
        data: {
          nama: element['nama'],
          unique_code: element['unique_code'],
          nomor: element['nomor']
        },
        success: function (response) {
          if (!response) return;
          if (i != dataGuru.length) {
            $('#progressTextGuru').html('Progres: ' + i + '/' + dataDosen.length);
          } else {
            $('#progressTextDosen').html('Progres: ' + i + '/' + dataDosen.length + ' selesai');
            $('#progressSelesaiDosen').removeClass('d-none');
          }

          $('#progressBarDosen')
            .attr('aria-valuenow', i)
            .attr('style', 'width: ' + (i / dataGuru.length) * 100 + '%;');
          i++;
        }
      });
    });
  }
</script>
<?= $this->endSection() ?>