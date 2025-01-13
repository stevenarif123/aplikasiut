<?php if (isset($mahasiswa) && count($mahasiswa) > 0) { ?>
  <div class="accordion" id="mahasiswaAccordion">
    <?php $no = 1 + $start; foreach ($mahasiswa as $mhs) { ?>
      <div class="card">
        <div class="card-header" id="heading-<?php echo $mhs['No']; ?>">
          <h2 class="mb-0">
            <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse-<?php echo $mhs['No']; ?>" aria-expanded="true" aria-controls="collapse-<?php echo $mhs['No']; ?>">
              <?php echo $no++; ?>. <span class="nama" data-id="<?php echo $mhs['No']; ?>"><?php echo stripslashes($mhs['NamaLengkap']); ?></span> - <span class="jurusan"><?php echo stripslashes($mhs['Jurusan']); ?></span>
            </button>
          </h2>
        </div>
        <div id="collapse-<?php echo $mhs['No']; ?>" class="collapse" aria-labelledby="heading-<?php echo $mhs['No']; ?>" data-parent="#mahasiswaAccordion">
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <p>
                  <strong>Nama Lengkap:</strong>
                  <span class="nama" data-id="<?php echo $mhs['No']; ?>"><?php echo stripslashes($mhs['NamaLengkap']); ?></span>
                  <i class="fas fa-copy copy-icon"></i>
                </p>
                <p>
                  <strong>Nomor HP:</strong>
                  <span class="nomor-hp" data-id="<?php echo $mhs['No']; ?>"><?php echo stripslashes($mhs['NomorHP']); ?></span>
                  <i class="fas fa-copy copy-icon"></i>
                </p>
                <p>
                  <strong>Email:</strong>
                  <span class="email" data-id="<?php echo $mhs['No']; ?>"><?php echo stripslashes($mhs['Email']); ?></span>
                  <i class="fas fa-copy copy-icon"></i>
                </p>
              </div>
              <div class="col-md-6">
                <p>
                  <strong>Password:</strong>
                  <span class="password" data-id="<?php echo $mhs['No']; ?>"><?php echo stripslashes($mhs['Password']); ?></span>
                  <i class="fas fa-copy copy-icon"></i>
                </p>
                <p>
                  <strong>Status Input SIA:</strong>
                  <span class="status-sia" data-id="<?php echo $mhs['No']; ?>"><?php echo stripslashes($mhs['STATUS_INPUT_SIA']); ?></span>
                  <i class="fas fa-copy copy-icon"></i>
                </p>
                <p>
                  <strong>Status Pembayaran:</strong>
                  <span class="status-pembayaran" data-id="<?php echo isset($mhs['Nim']) ? $mhs['Nim'] : $mhs['NamaLengkap']; ?>" data-nim="<?php echo isset($mhs['Nim']) ? $mhs['Nim'] : ''; ?>" data-nama="<?php echo stripslashes($mhs['NamaLengkap']); ?>">Loading...</span>
                </p>
              </div>
            </div>
            <div class="mt-2">
              <button type="button" class="btn btn-primary edit-btn" data-toggle="modal" data-target="#editModal"
                data-no="<?php echo $mhs['No']; ?>"
                data-namalengkap="<?php echo stripslashes($mhs['NamaLengkap']); ?>"
                data-nomorhp="<?php echo stripslashes($mhs['NomorHP']); ?>"
                data-email="<?php echo stripslashes($mhs['Email']); ?>"
                data-password="<?php echo stripslashes($mhs['Password']); ?>"
                data-statussia="<?php echo stripslashes($mhs['STATUS_INPUT_SIA']); ?>">
                Edit
              </button>
              <a href="lihat_data_mahasiswa.php?No=<?php echo $mhs['No']; ?>" class="btn btn-primary me-2">Detail Data</a>
              <a href="edit_data.php?No=<?php echo $mhs['No']; ?>" class="btn btn-success me-2">Edit Data</a>
              <button type="button" class="btn btn-danger" onclick="confirmDelete(<?php echo $mhs['No']; ?>)">Hapus</button>
            </div>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>
  <div class="d-flex justify-content-center mt-4">
    <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
      <a href="?page=<?php echo $i; ?>&limit=<?php echo $limit; ?>&search_column=<?php echo $search_column; ?>&keyword=<?php echo $keyword; ?>" class="btn <?php echo $i == $page ? 'btn-primary' : 'btn-secondary'; ?> me-2"><?php echo $i; ?></a>
    <?php } ?>
  </div>
<?php } else { ?>
  <p class="text-center py-4">Data mahasiswa tidak ditemukan.</p>
<?php } ?>
