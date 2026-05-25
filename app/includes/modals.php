<div id="modalSiswa" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalSiswaTitle">Tambah Siswa</h3>
            <span class="modal-close" onclick="closeModal('modalSiswa')">&times;</span>
        </div>
        <form id="formSiswa" method="POST" action="<?= APP_URL ?>/app/index.php?route=admin/prosesSiswa">
            <input type="hidden" name="action" id="siswaAction" value="create">
            <input type="hidden" name="student_id" id="student_id">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-col">
                        <label>NIS</label>
                        <input type="text" name="nis" id="nis" class="form-control" required>
                    </div>
                    <div class="form-col">
                        <label>Nama Depan</label>
                        <input type="text" name="first_name" id="first_name" class="form-control" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-col">
                        <label>Nama Belakang</label>
                        <input type="text" name="last_name" id="last_name" class="form-control">
                    </div>
                    <div class="form-col">
                        <label>Kelas</label>
                        <input type="text" name="class" id="class" class="form-control" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-col">
                        <label>Orang Tua</label>
                        <input type="text" name="parent" id="parent" class="form-control">
                    </div>
                    <div class="form-col">
                        <label>Tag ID</label>
                        <input type="text" name="tag_id" id="tag_id" class="form-control">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-col">
                        <label>Umur</label>
                        <input type="number" name="age" id="age" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('modalSiswa')">Batal</button>
                <button type="submit" class="btn-save">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="modalGuru" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalGuruTitle">Tambah Guru</h3>
            <span class="modal-close" onclick="closeModal('modalGuru')">&times;</span>
        </div>
        <form id="formGuru" method="POST" action="<?= APP_URL ?>/app/index.php?route=admin/prosesGuru">
            <input type="hidden" name="action" id="guruAction" value="create">
            <input type="hidden" name="teacher_id" id="teacher_id">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-col">
                        <label>Username</label>
                        <input type="text" name="username" id="guru_username" class="form-control" required>
                    </div>
                    <div class="form-col">
                        <label>Password</label>
                        <input type="password" name="password" id="guru_password" class="form-control" placeholder="Kosongkan jika tidak diubah">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-col">
                        <label>Nama Depan</label>
                        <input type="text" name="first_name" id="guru_first_name" class="form-control" required>
                    </div>
                    <div class="form-col">
                        <label>Nama Belakang</label>
                        <input type="text" name="last_name" id="guru_last_name" class="form-control">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-col">
                        <label>Gender</label>
                        <select name="gender" id="guru_gender" class="form-control">
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-col">
                        <label>Umur</label>
                        <input type="number" name="age" id="guru_age" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('modalGuru')">Batal</button>
                <button type="submit" class="btn-save">Simpan</button>
            </div>
        </form>
    </div>
</div>