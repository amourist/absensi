<div class="card-body table-responsive">
  <table class="table table-hover">
    <thead class="text-primary">
      <th><b>No</b></th>
      <th><b>Mata Kuliah</b></th>
      <th><b>Jurusan</b></th>
      <th><b>Dosen</b></th>
      <th><b>Aksi</b></th>
    </thead>
    <tbody>
      <?php $i = 1;
      foreach ($data as $value): ?>
        <tr>
          <td><?= $i; ?></td>
          <td><b><?= $value['matkul']; ?></b></td>
          <td><?= $value['jurusan']; ?></td>
          <td><?= $value['nama_dosen'] ?? '-'; ?></td>
          <td>
            <a href="<?= base_url('admin/matkul/edit/' . $value['id_matkul']); ?>" type="button"
              class="btn btn-primary p-2" id="<?= $value['id_matkul']; ?>">
              <i class="material-icons">edit</i>
              Edit
            </a>
            <button
              onclick='deleteItem("admin/matkul/deleteMatkulPost","<?= $value["id_matkul"]; ?>","Konfirmasi untuk menghapus data");'
              class="btn btn-danger p-2" id="<?= $value['id_kelas']; ?>">
              <i class="material-icons">delete_forever</i>
              Delete
            </button>
          </td>
        </tr>
        <?php $i++;
      endforeach; ?>
    </tbody>
  </table>
</div>