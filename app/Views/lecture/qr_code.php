<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<style>
    .progress-siswa {
        height: 5px;
        border-radius: 0px;
        background-color: rgb(186, 124, 222);
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
                    <div class="card-header card-header-primary">
                        <h4 class="card-title"><b>QR Code Mahasiswa - <?= $matkul['matkul']; ?></b></h4>
                        <p class="card-category">Generate dan download QR Code untuk Mahasiswa di Mata Kuliah Anda</p>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="text-primary"><b>Data Mahasiswa</b></h4>
                                        <p>Total jumlah mahasiswa : <b><?= count($mahasiswa); ?></b></p>
                                        <div class="row px-2">
                                            <div class="col-12 col-xl-3 px-1">
                                                <button onclick="generateAllQrSiswa()"
                                                    class="btn btn-primary p-2 px-md-4 w-100">
                                                    <div class="d-flex align-items-center justify-content-center"
                                                        style="gap: 12px;">
                                                        <div>
                                                            <i class="material-icons"
                                                                style="font-size: 24px;">qr_code</i>
                                                        </div>
                                                        <div>
                                                            <h4 class="d-inline font-weight-bold">Generate All</h4>
                                                            <div id="progressMahasiswa" class="d-none mt-2">
                                                                <span id="progressTextMahasiswa"></span>
                                                                <i id="progressSelesaiMahasiswa"
                                                                    class="material-icons d-none">check</i>
                                                                <div class="progress progress-mahasiswa">
                                                                    <div id="progressBarMahasiswa"
                                                                        class="progress-bar my-progress-bar bg-white"
                                                                        style="width: 0%;" role="progressbar"
                                                                        aria-valuenow="" aria-valuemin=""
                                                                        aria-valuemax=""></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </button>
                                            </div>
                                            <div class="col-12 col-xl-3 px-1">
                                                <a href="<?= base_url('lecture/qr/download'); ?>"
                                                    class="btn btn-success p-2 px-md-4 w-100 h-100">
                                                    <div class="d-flex align-items-center justify-content-center h-100"
                                                        style="gap: 12px;">
                                                        <div>
                                                            <i class="material-icons"
                                                                style="font-size: 24px;">cloud_download</i>
                                                        </div>
                                                        <div>
                                                            <div class="text-start">
                                                                <h4 class="d-inline font-weight-bold">Download All
                                                                    (.zip)</h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="table-responsive mt-4">
                                            <table class="table table-hover">
                                                <thead class="text-primary">
                                                    <th><b>No</b></th>
                                                    <th><b>NIM</b></th>
                                                    <th><b>Nama Mahasiwa</b></th>
                                                    <th class="text-center"><b>Aksi</b></th>
                                                </thead>
                                                <tbody>
                                                    <?php $i = 1;
                                                    foreach ($siswa as $s): ?>
                                                        <tr>
                                                            <td><?= $i++; ?></td>
                                                            <td><?= $s['nim']; ?></td>
                                                            <td><?= $s['nama_mahasiswa']; ?></td>
                                                            <td class="text-center">
                                                                <a href="<?= base_url('admin/qr/mahasiswa/' . $s['id_mahasiswa'] . '/download'); ?>"
                                                                    class="btn btn-info btn-sm">
                                                                    <i class="material-icons">download</i> Download QR
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    const dataMahasiswa = [
        <?php foreach ($mahasiswa as $value) {
            echo "{
              'nama' : `$value[nama_mahasiswa]`,
              'unique_code' : `$value[unique_code]`,
              'id_matkul' : `$value[id_matkul`,
              'nomor' : `$value[nim]`
            },";
        }
        ; ?>
    ];

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
                    id_kelas: element['id_matkul'],
                    nomor: element['nomor']
                },
                success: function (response) {
                    if (!response) return;
                    if (i != dataMahasiswa.length) {
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
</script>
<?= $this->endSection() ?>