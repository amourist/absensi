<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
    <div class="container-fluid">
        <!-- REKAP JUMLAH DATA -->
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-header card-header-primary card-header-icon">
                        <div class="card-icon">
                            <a href="<?= base_url('admin/mahasiswa'); ?>" class="text-white">
                                <i class="material-icons">person</i>
                            </a>
                        </div>
                        <p class="card-category">Jumlah Mahasiswa</p>
                        <h3 class="card-title"><?= count($mahasiswa); ?></h3>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons text-primary">check</i>
                            Terdaftar
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-header card-header-success card-header-icon">
                        <div class="card-icon">
                            <a href="<?= base_url('admin/dosen'); ?>" class="text-white">
                                <i class="material-icons">person_4</i>
                            </a>
                        </div>
                        <p class="card-category">Jumlah dosen</p>
                        <h3 class="card-title"><?= count($dosen); ?></h3>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons text-success">check</i>
                            Terdaftar
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-header card-header-info card-header-icon">
                        <div class="card-icon">
                            <a href="<?= base_url('admin/matkul'); ?>" class="text-white">
                                <i class="material-icons">grade</i>
                            </a>
                        </div>
                        <p class="card-category">Jumlah matkul</p>
                        <h3 class="card-title"><?= count($matkul); ?></h3>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons">home</i>
                            <?= $generalSettings->nama_prodi; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-header card-header-danger card-header-icon">
                        <div class="card-icon">
                            <a href="<?= base_url('admin/petugas'); ?>" class="text-white">
                                <i class="material-icons">settings</i>
                            </a>
                        </div>
                        <p class="card-category">Jumlah petugas</p>
                        <h3 class="card-title"><?= count($petugas); ?></h3>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons">person</i>
                            Petugas dan Administrator
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title"><b>Absensi Mahasiswa Hari Ini</b></h4>
                        <p class="card-category"><?= $dateNow; ?></p>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <h4 class="text-success"><b>Hadir</b></h4>
                                <h3><?= $jumlahKehadiranMahasiswa['hadir']; ?></h3>
                            </div>
                            <div class="col-md-3">
                                <h4 class="text-warning"><b>Sakit</b></h4>
                                <h3><?= $jumlahKehadiranMahasiswa['sakit']; ?></h3>
                            </div>
                            <div class="col-md-3">
                                <h4 class="text-info"><b>Izin</b></h4>
                                <h3><?= $jumlahKehadiranMahasiswa['izin']; ?></h3>
                            </div>
                            <div class="col-md-3">
                                <h4 class="text-danger"><b>Alfa</b></h4>
                                <h3><?= $jumlahKehadiranMahasiswa['alfa']; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header card-header-success">
                        <h4 class="card-title"><b>Absensi Dosen Hari Ini</b></h4>
                        <p class="card-category"><?= $dateNow; ?></p>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <h4 class="text-success"><b>Hadir</b></h4>
                                <h3><?= $jumlahKehadiranDosen['hadir']; ?></h3>
                            </div>
                            <div class="col-md-3">
                                <h4 class="text-warning"><b>Sakit</b></h4>
                                <h3><?= $jumlahKehadiranDosen['sakit']; ?></h3>
                            </div>
                            <div class="col-md-3">
                                <h4 class="text-info"><b>Izin</b></h4>
                                <h3><?= $jumlahKehadiranDosen['izin']; ?></h3>
                            </div>
                            <div class="col-md-3">
                                <h4 class="text-danger"><b>Alfa</b></h4>
                                <h3><?= $jumlahKehadiranDosen['alfa']; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- GRAFIK CHART -->
        <div class="row">
            <div class="col-md-6">
                <div class="card card-chart">
                    <div class="card-header card-header-primary">
                        <div class="ct-chart" id="kehadiranMahasiswa"></div>
                    </div>
                    <div class="card-body">
                        <h4 class="card-title">Tingkat kehadiran siswa</h4>
                        <p class="card-category">Jumlah kehadiran siswa dalam 7 hari terakhir</p>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons text-primary">checklist</i> <a class="text-primary" href="<?= base_url('admin/absen-siswa'); ?>">Lihat data</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-chart">
                    <div class="card-header card-header-success">
                        <div class="ct-chart" id="kehadiranGuru"></div>
                    </div>
                    <div class="card-body">
                        <h4 class="card-title">Tingkat kehadiran guru</h4>
                        <p class="card-category">Jumlah kehadiran guru dalam 7 hari terakhir</p>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons text-success">checklist</i> <a class="text-success" href="<?= base_url('admin/absen-guru'); ?>">Lihat data</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Chartist JS -->
<script src="<?= base_url('assets/js/plugins/chartist.min.js') ?>"></script>
<script>
    $(document).ready(function() {
        initDashboardPageCharts();
    });

    function initDashboardPageCharts() {

        if ($('#kehadiranMahasiswa').length != 0) {
            /* ----------==========     Chart tingkat kehadiran siswa    ==========---------- */
            const dataKehadiranMahasiswa = [<?php foreach ($grafikKehadiranMahasiswa as $value) echo "$value,"; ?>];

            const chartKehadiranMahasiswa = {
                labels: [
                    <?php
                    foreach ($dateRange as  $value) {
                        echo "'$value',";
                    }
                    ?>
                ],
                series: [dataKehadiranMahasiswa]
            };

            var highestData = 0;

            dataKehadiranMahasiswa.forEach(e => {
                if (e >= highestData) {
                    highestData = e;
                }
            })

            const optionsChart = {
                lineSmooth: Chartist.Interpolation.cardinal({
                    tension: 0
                }),
                low: 0,
                high: highestData + (highestData / 4),
                chartPadding: {
                    top: 0,
                    right: 0,
                    bottom: 0,
                    left: 0
                }
            }

            var kehadiranMahasiswaChart = new Chartist.Line('#kehadiranMahasiswa', chartKehadiranMahasiswa, optionsChart);

            md.startAnimationForLineChart(kehadiranMahasiswaChart);
        }

        if ($('#kehadiranDosen').length != 0) {
            /* ----------==========     Chart tingkat kehadiran dosen    ==========---------- */
            const dataKehadiranDosen = [<?php foreach ($grafikkKehadiranDosen as $value) echo "$value,"; ?>];

            const chartKehadiranDosen = {
                labels: [
                    <?php
                    foreach ($dateRange as  $value) {
                        echo "'$value',";
                    }
                    ?>
                ],
                series: [dataKehadiranDosen]
            };

            var highestData = 0;

            dataKehadiranDosen.forEach(e => {
                if (e >= highestData) {
                    highestData = e;
                }
            })

            const optionsChart = {
                lineSmooth: Chartist.Interpolation.cardinal({
                    tension: 0
                }),
                low: 0,
                high: highestData + (highestData / 4),
                chartPadding: {
                    top: 0,
                    right: 0,
                    bottom: 0,
                    left: 0
                }
            }

            var kehadiranDosenChart = new Chartist.Line('#kehadiranDosen', chartKehadiranDosen, optionsChart);

            md.startAnimationForLineChart(kehadiranDosenChart);
        }
    }
</script>
<?= $this->endSection() ?>