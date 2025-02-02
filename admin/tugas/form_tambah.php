<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-body">
                <h5 class="card-title">Tambah Tugas Baru</h5>
                <form method="post" action="index.php">
                    <div class="form-group">
                        <label for="judul_tugas">Judul Tugas</label>
                        <input type="text" class="form-control" id="judul_tugas" name="judul_tugas" required>
                    </div>
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="deadline">Deadline</label>
                        <input type="date" class="form-control" id="deadline" name="deadline" required>
                    </div>
                    <div class="form-group">
                        <label for="admin_id">Assignee</label>
                        <select class="form-control" id="admin_id" name="admin_id">
                            <?php
                                $result = mysqli_query($koneksi, "SELECT * FROM admin");
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<option value='".$row['id_admin']."'>".$row['nama_lengkap']."</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="prioritas">Prioritas</label>
                        <select class="form-control" id="prioritas" name="prioritas">
                            <option value="tinggi">Tinggi</option>
                            <option value="sedang">Sedang</option>
                            <option value="rendah">Rendah</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="pending">Pending</option>
                            <option value="proses">Proses</option>
                            <option value="selesai">Selesai</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tag_tugas">Tag Tugas</label>
                        <input type="text" class="form-control" id="tag_tugas" name="tag_tugas">
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
