<div class="card-body table-responsive">
   <?php if (!$empty) : ?>
      <table class="table table-hover">
         <thead class="text-success">
            <th><b>No</b></th>
            <th><b>NIP</b></th>
            <th><b>Nama Dosen</b></th>
            <th><b>Jenis Kelamin</b></th>
            <th><b>No HP</b></th>
            <th><b>Alamat</b></th>
            <th width="1%"><b>Aksi</b></th>
         </thead>
         <tbody>
            <?php $i = 1;
            foreach ($data as $value) : ?>
               <tr>
                  <td><?= $i; ?></td>
                  <td><?= $value['nip']; ?></td>
                  <td><b><?= $value['nama_dosen']; ?></b></td>
                  <td><?= $value['jenis_kelamin']; ?></td>
                  <td><?= $value['no_hp']; ?></td>
                  <td><?= $value['alamat']; ?></td>
                  <td>
                     <div class="d-flex justify-content-center">
                        <a title="Edit" href="<?= base_url('admin/dosen/edit/' . $value['id_dosen']); ?>" class="btn btn-success p-2" id="<?= $value['nip']; ?>">
                           <i class="material-icons">edit</i>
                        </a>
                        <form action="<?= base_url('admin/dosen/delete/' . $value['id_dosen']); ?>" method="post" class="d-inline">
                           <?= csrf_field(); ?>
                           <input type="hidden" name="_method" value="DELETE">
                           <button title="Delete" onclick="return confirm('Konfirmasi untuk menghapus data');" type="submit" class="btn btn-danger p-2" id="<?= $value['nip']; ?>">
                              <i class="material-icons">delete_forever</i>
                           </button>
                        </form>
                        <a title="Download QR Code" href="<?= base_url('admin/qr/dosen/' . $value['id_dosen'] . '/download'); ?>" class="btn btn-info p-2">
                           <i class="material-icons">qr_code</i>
                        </a>
                     </div>
                  </td>
               </tr>
            <?php $i++;
            endforeach; ?>
         </tbody>
      </table>
   <?php else : ?>
      <div class="row">
         <div class="col">
            <h4 class="text-center text-danger">Data tidak ditemukan</h4>
         </div>
      </div>
   <?php endif; ?>
</div>