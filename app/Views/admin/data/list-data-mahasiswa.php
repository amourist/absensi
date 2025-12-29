<div class="card-body table-responsive">
   <?php if (!$empty): ?>
      <table class="table table-hover">
         <thead class="text-primary">
            <th width="20"><input type="checkbox" class="checkbox-table" id="checkAll"></th>
            <th><b>No</b></th>
            <th><b>NIM</b></th>
            <th><b>Nama Mahasiswa</b></th>
            <th><b>Jenis Kelamin</b></th>
            <th><b>Kelas</b></th>
            <th><b>No HP</b></th>
            <th width="1%"><b>Aksi</b></th>
         </thead>
         <tbody>
            <?php $i = 1;
            foreach ($data as $value): ?>
               <tr>
                  <td><input type="checkbox" name="checkbox-table" class="checkbox-table" value="<?= $value['id_mahasiswa']; ?>">
                  </td>
                  <td><?= $i; ?></td>
                  <td><?= $value['nim']; ?></td>
                  <td><b><?= $value['nama_mahasiswa']; ?></b></td>
                  <td><?= $value['jenis_kelamin']; ?></td>
                  <td><?= $value['matkul']; ?></td>
                  <td><?= $value['no_hp']; ?></td>
                  <td>
                     <div class="d-flex justify-content-center">
                        <a title="Edit" href="<?= base_url('admin/mahasiswa/edit/' . $value['id_mahasiswa']); ?>"
                           class="btn btn-primary p-2" id="<?= $value['nim']; ?>">
                           <i class="material-icons">edit</i>
                        </a>
                        <form action="<?= base_url('admin/mahasiswa/delete/' . $value['id_mahasiswa']); ?>" method="post"
                           class="d-inline">
                           <?= csrf_field(); ?>
                           <input type="hidden" name="_method" value="DELETE">
                           <button title="Delete" onclick="return confirm('Konfirmasi untuk menghapus data');" type="submit"
                              class="btn btn-danger p-2" id="<?= $value['nis']; ?>">
                              <i class="material-icons">delete_forever</i>
                           </button>
                        </form>
                        <a title="Download QR Code"
                           href="<?= base_url('admin/qr/mahasiswa/' . $value['id_mahasiswa'] . '/download'); ?>"
                           class="btn btn-success p-2">
                           <i class="material-icons">qr_code</i>
                        </a>
                     </div>
                  </td>
               </tr>
               <?php $i++;
            endforeach; ?>
         </tbody>
      </table>
   <?php else: ?>
      <div class="row">
         <div class="col">
            <h4 class="text-center text-danger">Data tidak ditemukan</h4>
         </div>
      </div>
   <?php endif; ?>
</div>