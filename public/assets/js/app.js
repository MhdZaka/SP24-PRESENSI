function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function showModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
}

function showTambahSiswa() {
    document.getElementById('siswaAction').value = 'create';
    document.getElementById('formSiswa').reset();
    document.getElementById('modalSiswaTitle').innerText = 'Tambah Siswa';
    showModal('modalSiswa');
}

function editSiswa(id) {
    fetch('index.php?route=admin/prosesSiswa&action=get&id=' + id)
        .then(response => response.json())
        .then(data => {
            document.getElementById('siswaAction').value = 'update';
            document.getElementById('student_id').value = data.student_id;
            document.getElementById('nis').value = data.nis;
            document.getElementById('first_name').value = data.first_name;
            document.getElementById('last_name').value = data.last_name || '';
            document.getElementById('class').value = data.class || '';
            document.getElementById('parent').value = data.parent || '';
            document.getElementById('tag_id').value = data.tag_id || '';
            document.getElementById('age').value = data.age || '';
            document.getElementById('modalSiswaTitle').innerText = 'Edit Siswa';
            showModal('modalSiswa');
        })
        .catch(error => alert('Gagal mengambil data siswa: ' + error));
}

function hapusSiswa(id) {
    if (confirm('Yakin ingin menghapus siswa ini?')) {
        window.location.href = 'index.php?route=admin/prosesSiswa&action=delete&id=' + id;
    }
}

function showTambahGuru() {
    document.getElementById('guruAction').value = 'create';
    document.getElementById('formGuru').reset();
    document.getElementById('modalGuruTitle').innerText = 'Tambah Guru';
    showModal('modalGuru');
}

function editGuru(id) {
    fetch('index.php?route=admin/prosesGuru&action=get&id=' + id)
        .then(response => response.json())
        .then(data => {
            document.getElementById('guruAction').value = 'update';
            document.getElementById('teacher_id').value = data.teacher_id;
            document.getElementById('guru_username').value = data.username;
            document.getElementById('guru_first_name').value = data.first_name;
            document.getElementById('guru_last_name').value = data.last_name || '';
            document.getElementById('guru_gender').value = data.gender || 'L';
            document.getElementById('guru_age').value = data.age || '';
            document.getElementById('modalGuruTitle').innerText = 'Edit Guru';
            showModal('modalGuru');
        })
        .catch(error => alert('Gagal mengambil data guru: ' + error));
}

function hapusGuru(id) {
    if (confirm('Yakin ingin menghapus guru ini?')) {
        window.location.href = 'index.php?route=admin/prosesGuru&action=delete&id=' + id;
    }
}

function uploadFotoSiswa(id) {
    let input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/jpeg,image/png,image/jpg,image/webp';
    input.onchange = function(e) {
        let file = e.target.files[0];
        let formData = new FormData();
        formData.append('student_id', id);
        formData.append('photo', file);
        
        if (confirm('Upload foto untuk siswa ini?')) {
            fetch('index.php?route=admin/uploadFotoSiswa', {
                method: 'POST',
                body: formData
            }).then(response => {
                if (response.ok) {
                    alert('Foto berhasil diupload!');
                    location.reload();
                } else {
                    alert('Gagal upload foto');
                }
            });
        }
    };
    input.click();
}

function uploadFotoGuru(id) {
    let input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/jpeg,image/png,image/jpg,image/webp';
    input.onchange = function(e) {
        let file = e.target.files[0];
        let formData = new FormData();
        formData.append('teacher_id', id);
        formData.append('photo', file);
        
        if (confirm('Upload foto untuk guru ini?')) {
            fetch('index.php?route=admin/uploadFotoGuru', {
                method: 'POST',
                body: formData
            }).then(response => {
                if (response.ok) {
                    alert('Foto berhasil diupload!');
                    location.reload();
                } else {
                    alert('Gagal upload foto');
                }
            });
        }
    };
    input.click();
}

window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}

window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    const msg = urlParams.get('msg');
    const error = urlParams.get('error');
    
    if (msg === 'photo_uploaded') {
        alert('Foto berhasil diupload!');
        window.history.replaceState({}, document.title, window.location.pathname + '?tab=' + urlParams.get('tab'));
    }
    
    if (error) {
        alert('Error: ' + decodeURIComponent(error));
        window.history.replaceState({}, document.title, window.location.pathname + '?tab=' + urlParams.get('tab'));
    }
}

// --- NFC PAIRING LOGIC ---
let nfcSocket = null;

function bukaModalPairing() {
    showModal('modalPairing');
    document.getElementById('pairingCodeDisplay').innerText = '----';
    document.getElementById('pairingStatus').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghubungkan ke server...';
    document.getElementById('pairingResult').style.display = 'none';

    if (!nfcSocket) {
        // Menginisialisasi socket client
        nfcSocket = io("https://sp24api.wind.my.id");

        nfcSocket.on("connect", () => {
            console.log("Terhubung ke Server dengan ID:", nfcSocket.id);
            // LANGKAH 1: Meminta kode pairing ke server
            nfcSocket.emit("request-pairing-code");
        });

        // LANGKAH 2: Menerima kode pairing yang dihasilkan server
        nfcSocket.on("pairing-code-generated", (code) => {
            document.getElementById('pairingCodeDisplay').innerText = code;
            document.getElementById('pairingStatus').innerHTML = '<i class="fas fa-check-circle"></i> Menunggu scan NFC dari Android...';
        });

        // LANGKAH 3: Menerima data NFC yang diteruskan oleh server
        nfcSocket.on("nfc-received", (data) => {
            console.log("DATA NFC DITERIMA!", data);
            let resultDiv = document.getElementById('pairingResult');
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = '<strong>Data NFC Diterima!</strong><br>Tag ID: <code>' + data.tagId + '</code><br>Waktu: ' + new Date(data.timestamp).toLocaleTimeString();
            
            // Catat presensi otomatis jika Tag ID sesuai dengan data siswa
            catatPresensiDariNFC(data.tagId);
        });

        nfcSocket.on("disconnect", () => {
            document.getElementById('pairingStatus').innerHTML = '<i class="fas fa-exclamation-triangle" style="color:#ef4444;"></i> Terputus dari server.';
        });

        nfcSocket.on("connect_error", (err) => {
            document.getElementById('pairingStatus').innerHTML = '<i class="fas fa-exclamation-triangle" style="color:#ef4444;"></i> Gagal terhubung ke server.';
        });
    } else {
        if (nfcSocket.connected) {
            nfcSocket.emit("request-pairing-code");
        } else {
            nfcSocket.connect();
        }
    }
}

function tutupModalPairing() {
    closeModal('modalPairing');
    if (nfcSocket) {
        nfcSocket.disconnect();
        nfcSocket = null;
    }
}

function catatPresensiDariNFC(tagId) {
    // Fungsi ini bisa dikembangkan untuk langsung mengirim POST request ke server Anda
    // Misalnya menggunakan fetch API untuk absen otomatis
    document.getElementById('pairingStatus').innerHTML = '<i class="fas fa-check"></i> Proses pencatatan presensi...';
    
    // Asumsi ada API endpoint untuk catat presensi via NFC di backend SP24
    let formData = new FormData();
    formData.append('tag_id', tagId);
    
    /* 
    fetch('index.php?route=admin/catatPresensiNFC', {
        method: 'POST',
        body: formData
    }).then(res => {
        // refresh data jika berhasil
        location.reload();
    });
    */
}

