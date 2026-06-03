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
                        <label>Tag ID <span id="nfcStatusText" class="text-blink" style="display:none;"><i class="fas fa-wifi"></i> Pemindaian aktif...</span></label>
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

<div id="modalPairing" class="modal">
    <div class="modal-content" style="text-align: center; max-width: 400px;">
        <div class="modal-header" style="justify-content: center;">
            <h3 id="modalPairingTitle"><i class="fas fa-qrcode"></i> Pairing Scanner NFC</h3>
            <span class="modal-close" onclick="tutupModalPairing()" style="position: absolute; right: 20px;">&times;</span>
        </div>
        <div class="modal-body">
            <p style="margin-bottom: 15px; color: #64748b; font-size: 0.9rem;">
                Masukkan kode berikut pada aplikasi Android SP24 Scanner Anda untuk menghubungkan.
            </p>
            <div id="pairingCodeContainer" style="background: #EBF4F6; padding: 20px; border-radius: 12px; margin-bottom: 20px;">
                <h1 id="pairingCodeDisplay" style="font-size: 2.5rem; letter-spacing: 5px; color: #09637E; margin: 0;">----</h1>
            </div>
            <div id="pairingStatus" style="font-size: 0.85rem; color: #059669; display: flex; align-items: center; justify-content: center; gap: 8px;">
                <i class="fas fa-spinner fa-spin"></i> Menghubungkan ke server...
            </div>
            <div id="pairingResult" style="margin-top: 15px; display: none; background: #d1fae5; color: #065f46; padding: 10px; border-radius: 8px; font-size: 0.85rem;">
            </div>
        </div>
        <div class="modal-footer" style="justify-content: center;">
            <button type="button" class="btn-cancel" onclick="tutupModalPairing()">Batal / Tutup</button>
        </div>
    </div>
</div>

<div id="modalDisconnectNFC" class="modal">
    <div class="modal-content" style="text-align: center; max-width: 350px;">
        <div class="modal-header" style="justify-content: center; border-bottom: none;">
            <h3 style="color: #ef4444;"><i class="fas fa-exclamation-triangle"></i> Putuskan Koneksi</h3>
            <span class="modal-close" onclick="closeModal('modalDisconnectNFC')" style="position: absolute; right: 20px;">&times;</span>
        </div>
        <div class="modal-body">
            <p style="margin-bottom: 20px; color: #475569; font-size: 0.95rem;">
                Perangkat Android saat ini terhubung sebagai Scanner NFC. Anda yakin ingin memutuskan koneksi ini?
            </p>
        </div>
        <div class="modal-footer" style="justify-content: center; gap: 15px; border-top: none;">
            <button type="button" class="btn-cancel" onclick="closeModal('modalDisconnectNFC')">Batal</button>
            <button type="button" class="btn-delete" onclick="putuskanKoneksiNfc()"><i class="fas fa-unlink"></i> Putuskan</button>
        </div>
    </div>
</div>