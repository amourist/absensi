<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12 col-md-12">
        <?= view('admin/_messages'); ?>
        <div class="card">
          <div class="card-header card-header-primary">
            <h4 class="card-title"><b>Form Tambah Mata Kuliah</b></h4>
          </div>
          <div class="card-body mx-5 my-3">

            <form action="<?= base_url('admin/matkul/tambahMatkulPost'); ?>" method="post">
              <?= csrf_field() ?>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group mt-4">
                    <label for="semester">Semester</label>
                    <input type="text" id="semester"
                      class="form-control <?= invalidFeedback('semester') ? 'is-invalid' : ''; ?>" name="semester"
                      placeholder="'X', 'XI', 'XII'" value="<?= old('semester') ?>" required>
                    <div class="invalid-feedback">
                      <?= invalidFeedback('semester'); ?>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group mt-4">
                    <label for="index_matkul">Index Mata Kuliah</label>
                    <input type="text" id="index_matkul"
                      class="form-control <?= invalidFeedback('index_matkul') ? 'is-invalid' : ''; ?>" name="index_matkul"
                      placeholder="'1', '2', 'A'" value="<?= old('index_matkul') ?>" required>
                    <div class="invalid-feedback">
                      <?= invalidFeedback('index_matkul'); ?>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <label for="id_jurusan">Jurusan</label>
                <select class="custom-select <?= invalidFeedback('id_jurusan') ? 'is-invalid' : ''; ?>" id="id_jurusan"
                  name="id_jurusan">
                  <option value="">--Pilih Jurusan--</option>
                  <?php foreach ($jurusan as $value): ?>
                    <option value="<?= $value['id']; ?>" <?= old('id_jurusan') == $value['id'] ? 'selected' : ''; ?>>
                      <?= $value['jurusan']; ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">
                  <?= invalidFeedback('id_jurusan'); ?>
                </div>
              </div>
              <div class="col-md-6">
                <label for="id_dosbing">Dsen Pembimbing</label>
                <select class="custom-select <?= invalidFeedback('id_dosbing') ? 'is-invalid' : ''; ?>"
                  id="id_dosbing" name="id_dosbing">
                  <option value="">--Pilih Dosen Pembimbing--</option>
                  <?php foreach ($dosen as $value): ?>
                    <option value="<?= $value['id_dosen']; ?>" <?= old('id_dosbing') == $value['id_dosen'] ? 'selected' : ''; ?>>
                      <?= $value['nama_dosen']; ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">
                  <?= invalidFeedback('id_dosbing'); ?>
                </div>
              </div>
          </div>
          <button type="submit" class="btn btn-primary mt-4">Simpan</button>
          </form>

          <hr>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
<?= $this->endSection() ?>