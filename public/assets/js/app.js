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
    
    const nfcStatus = document.getElementById('nfcStatusText');
    if (nfcStatus) {
        nfcStatus.style.display = window.isDeviceConnected ? 'inline-flex' : 'none';
    }
    
    showModal('modalSiswa');
}

function editSiswa(id) {
    fetch('index.php?route=admin/prosesSiswa&action=get&id=' + id, { credentials: 'same-origin' })
        .then(async response => {
            const text = await response.text();
            if (!text) throw new Error('Response dari server kosong (kemungkinan sesi Anda terputus). Silakan muat ulang halaman.');
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error("Server response:", text);
                throw new Error('Format data tidak valid dari server (Session Expired). Silakan refresh browser Anda.');
            }
        })
        .then(data => {
            if (data.error) throw new Error(data.error);
            document.getElementById('siswaAction').value = 'update';
            document.getElementById('student_id').value = data.student_id || '';
            document.getElementById('nis').value = data.nis || '';
            document.getElementById('first_name').value = data.first_name || '';
            document.getElementById('last_name').value = data.last_name || '';
            document.getElementById('class').value = data.class || '';
            document.getElementById('parent').value = data.parent || '';
            document.getElementById('tag_id').value = data.tag_id || '';
            document.getElementById('age').value = data.age || '';
            document.getElementById('modalSiswaTitle').innerText = 'Edit Siswa';
            
            const nfcStatus = document.getElementById('nfcStatusText');
            if (nfcStatus) {
                nfcStatus.style.display = window.isDeviceConnected ? 'inline-flex' : 'none';
            }
            
            showModal('modalSiswa');
        })
        .catch(error => alert('Gagal mengambil data siswa: ' + error.message));
}

function hapusSiswa(id) {
    if (confirm('Yakin ingin menghapus siswa ini?')) {
        fetch('index.php?route=admin/prosesSiswa&action=delete&id=' + id, { credentials: 'same-origin' })
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTable = doc.querySelector('.data-table');
            const oldTable = document.querySelector('.data-table');
            if (newTable && oldTable) {
                oldTable.innerHTML = newTable.innerHTML;
                const toast = document.getElementById('toastNotification');
                if (toast) {
                    toast.innerHTML = '<i class="fas fa-trash"></i> Data Siswa dihapus!';
                    toast.style.background = '#ef4444';
                    toast.classList.add('show');
                    setTimeout(() => { toast.classList.remove('show'); }, 3000);
                }
            } else {
                location.reload();
            }
        })
        .catch(() => location.reload());
    }
}

function showTambahGuru() {
    document.getElementById('guruAction').value = 'create';
    document.getElementById('formGuru').reset();
    document.getElementById('modalGuruTitle').innerText = 'Tambah Guru';
    
    // Make password required for new users
    const pwdInput = document.getElementById('guru_password');
    pwdInput.required = true;
    pwdInput.placeholder = 'Wajib diisi (Password Baru)';
    
    showModal('modalGuru');
}

function editGuru(id) {
    fetch('index.php?route=admin/prosesGuru&action=get&id=' + id, { credentials: 'same-origin' })
        .then(async response => {
            const text = await response.text();
            if (!text) throw new Error('Response dari server kosong (kemungkinan sesi Anda terputus). Silakan muat ulang halaman.');
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error("Server response:", text);
                throw new Error('Format data tidak valid dari server (Session Expired). Silakan refresh browser Anda.');
            }
        })
        .then(data => {
            if (data.error) throw new Error(data.error);
            document.getElementById('guruAction').value = 'update';
            document.getElementById('teacher_id').value = data.teacher_id || '';
            document.getElementById('guru_username').value = data.username || '';
            document.getElementById('guru_first_name').value = data.first_name || '';
            document.getElementById('guru_last_name').value = data.last_name || '';
            document.getElementById('guru_gender').value = data.gender || 'L';
            document.getElementById('guru_age').value = data.age || '';
            document.getElementById('modalGuruTitle').innerText = 'Edit Guru';
            
            // Logika UI Password
            const pwdInput = document.getElementById('guru_password');
            if (pwdInput) {
                pwdInput.removeAttribute('required');
                pwdInput.setAttribute('placeholder', 'Kosongkan jika tidak diubah');
            }

            showModal('modalGuru');
        })
        .catch(error => alert('Gagal mengambil data guru: ' + error.message));
}

function hapusGuru(id) {
    if (confirm('Yakin ingin menghapus guru ini?')) {
        fetch('index.php?route=admin/prosesGuru&action=delete&id=' + id, { credentials: 'same-origin' })
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTable = doc.querySelector('.guru-grid-container');
            const oldTable = document.querySelector('.guru-grid-container');
            if (newTable && oldTable) {
                oldTable.innerHTML = newTable.innerHTML;
                const toast = document.getElementById('toastNotification');
                if (toast) {
                    toast.innerHTML = '<i class="fas fa-trash"></i> Data Guru dihapus!';
                    toast.style.background = '#ef4444';
                    toast.classList.add('show');
                    setTimeout(() => { toast.classList.remove('show'); }, 3000);
                }
            } else {
                location.reload();
            }
        })
        .catch(() => location.reload());
    }
}

function uploadFotoSiswa(id) {
    let input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/jpeg,image/png,image/jpg,image/webp';
    input.onchange = function (e) {
        let file = e.target.files[0];
        let formData = new FormData();
        formData.append('student_id', id);
        formData.append('photo', file);

        if (confirm('Upload foto untuk siswa ini?')) {
            fetch('index.php?route=admin/uploadFotoSiswa', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTable = doc.querySelector('.data-table');
                const oldTable = document.querySelector('.data-table');
                if (newTable && oldTable) {
                    oldTable.innerHTML = newTable.innerHTML;
                    const toast = document.getElementById('toastNotification');
                    if (toast) {
                        toast.innerHTML = '<i class="fas fa-image"></i> Foto Siswa berhasil diupload!';
                        toast.style.background = '#22c55e';
                        toast.classList.add('show');
                        setTimeout(() => { toast.classList.remove('show'); }, 3000);
                    }
                } else {
                    location.reload();
                }
            })
            .catch(() => alert('Gagal upload foto'));
        }
    };
    input.click();
}

function uploadFotoGuru(id) {
    let input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/jpeg,image/png,image/jpg,image/webp';
    input.onchange = function (e) {
        let file = e.target.files[0];
        let formData = new FormData();
        formData.append('teacher_id', id);
        formData.append('photo', file);

        if (confirm('Upload foto untuk guru ini?')) {
            fetch('index.php?route=admin/uploadFotoGuru', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTable = doc.querySelector('.guru-grid-container');
                const oldTable = document.querySelector('.guru-grid-container');
                if (newTable && oldTable) {
                    oldTable.innerHTML = newTable.innerHTML;
                    const toast = document.getElementById('toastNotification');
                    if (toast) {
                        toast.innerHTML = '<i class="fas fa-image"></i> Foto Guru berhasil diupload!';
                        toast.style.background = '#22c55e';
                        toast.classList.add('show');
                        setTimeout(() => { toast.classList.remove('show'); }, 3000);
                    }
                } else {
                    location.reload();
                }
            })
            .catch(() => alert('Gagal upload foto'));
        }
    };
    input.click();
}

window.onclick = function (event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}

window.onload = function () {
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

let nfcSocket = null;

function initNfcSocket() {
    if (!nfcSocket) {
        nfcSocket = io("https://sp24api.wind.my.id");

        nfcSocket.on("connect", () => {
            console.log("Terhubung ke Server dengan ID:", nfcSocket.id);
        });

        nfcSocket.on("pairing-code-generated", (code) => {
            let display = document.getElementById('pairingCodeDisplay');
            if (display) display.innerText = code;
            
            let status = document.getElementById('pairingStatus');
            if (status) status.innerHTML = '<i class="fas fa-check-circle"></i> Menunggu scan NFC dari Android...';
        });

        nfcSocket.on("device-connected", (data) => {
            console.log("Device Connected!", data);
            window.isDeviceConnected = true;
            localStorage.setItem('nfc_paired', 'true'); // Simpan permanen
            
            const btnPairing = document.getElementById('btnPairingScanner');
            if (btnPairing) {
                btnPairing.innerHTML = '<i class="fas fa-check-circle"></i> Perangkat Terhubung';
                btnPairing.style.background = '#0284c7';
                btnPairing.style.opacity = '0.9';
                btnPairing.classList.add('btn-pulsing');
            }
            
            let status = document.getElementById('pairingStatus');
            if (status) status.innerHTML = '<i class="fas fa-mobile-alt"></i> Perangkat Android berhasil terhubung!';
            
            const toast = document.getElementById('toastNotification');
            if (toast) {
                toast.classList.add('show');
                setTimeout(() => { toast.classList.remove('show'); }, 3000);
            }
            
            tutupModalPairing();
        });

        nfcSocket.on("device-disconnected", () => {
            console.log("Device Disconnected!");
            window.isDeviceConnected = false;
            kembalikanTombolPairing();
            
            const toast = document.getElementById('toastNotification');
            if (toast) {
                toast.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Perangkat Android Terputus!';
                toast.style.background = '#ef4444';
                toast.classList.add('show');
                setTimeout(() => { 
                    toast.classList.remove('show'); 
                    setTimeout(() => {
                        toast.innerHTML = '<i class="fas fa-check-circle"></i> Perangkat Berhasil Terhubung!';
                        toast.style.background = '#22c55e';
                    }, 500);
                }, 3000);
            }
        });

        nfcSocket.on("nfc-received", (data) => {
            console.log("DATA NFC DITERIMA!", data);
            
            const modalSiswa = document.getElementById('modalSiswa');
            const isModalSiswaOpen = modalSiswa && (modalSiswa.style.display === 'block' || window.getComputedStyle(modalSiswa).display === 'block');
            
            if (isModalSiswaOpen) {
                const tagIdInput = document.getElementById('tag_id');
                if (tagIdInput) {
                    tagIdInput.value = data.tagId;
                    const originalBg = tagIdInput.style.backgroundColor;
                    tagIdInput.style.backgroundColor = '#d1fae5';
                    setTimeout(() => { tagIdInput.style.backgroundColor = originalBg; }, 1500);
                }
            } else {
                let resultDiv = document.getElementById('pairingResult');
                if (resultDiv) {
                    resultDiv.style.display = 'block';
                    resultDiv.innerHTML = '<strong>Data NFC Diterima!</strong><br>Tag ID: <code>' + data.tagId + '</code><br>Waktu: ' + new Date(data.timestamp).toLocaleTimeString();
                }
                catatPresensiDariNFC(data.tagId);
            }
        });

        nfcSocket.on("disconnect", () => {
            let status = document.getElementById('pairingStatus');
            if (status) status.innerHTML = '<i class="fas fa-exclamation-triangle" style="color:#ef4444;"></i> Terputus dari server.';
            window.isDeviceConnected = false;
            kembalikanTombolPairing();
        });

        nfcSocket.on("connect_error", (err) => {
            let status = document.getElementById('pairingStatus');
            if (status) status.innerHTML = '<i class="fas fa-exclamation-triangle" style="color:#ef4444;"></i> Gagal terhubung ke server.';
            window.isDeviceConnected = false;
            kembalikanTombolPairing();
        });
    }
}

function kembalikanTombolPairing() {
    const btnPairing = document.getElementById('btnPairingScanner');
    if (btnPairing) {
        btnPairing.innerHTML = '<i class="fas fa-mobile-alt"></i> Pairing Scanner';
        btnPairing.style.background = '#059669';
        btnPairing.style.opacity = '1';
        btnPairing.classList.remove('btn-pulsing');
    }
    
    const nfcStatus = document.getElementById('nfcStatusText');
    if (nfcStatus) {
        nfcStatus.style.display = 'none';
    }
}

function bukaModalPairing() {
    showModal('modalPairing');
    document.getElementById('pairingCodeDisplay').innerText = '----';
    document.getElementById('pairingStatus').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghubungkan ke server...';
    document.getElementById('pairingResult').style.display = 'none';

    // Pastikan socket sudah diinisialisasi
    initNfcSocket();

    if (nfcSocket.connected) {
        nfcSocket.emit("request-pairing-code");
    } else {
        nfcSocket.once("connect", () => {
            nfcSocket.emit("request-pairing-code");
        });
    }
}

function tutupModalPairing() {
    closeModal('modalPairing');
}

function catatPresensiDariNFC(tagId) {

    let status = document.getElementById('pairingStatus');
    if (status) status.innerHTML = '<i class="fas fa-check"></i> Proses pencatatan presensi...';

    let formData = new FormData();
    formData.append('tag_id', tagId);
}

function handlePairingClick() {
    if (localStorage.getItem('nfc_paired') === 'true') {
        showModal('modalDisconnectNFC');
    } else {
        bukaModalPairing();
    }
}

function putuskanKoneksiNfc() {
    localStorage.removeItem('nfc_paired');
    window.isDeviceConnected = false;
    kembalikanTombolPairing();
    closeModal('modalDisconnectNFC');
    
    // Putuskan websocket sementara agar server API tau bahwa kita disconnect
    if (nfcSocket) {
        nfcSocket.disconnect();
        setTimeout(() => {
            initNfcSocket(); // Konek kembali untuk standby session baru
        }, 500);
    }
    
    const toast = document.getElementById('toastNotification');
    if (toast) {
        toast.innerHTML = '<i class="fas fa-unlink"></i> Koneksi NFC Diputuskan!';
        toast.style.background = '#64748b';
        toast.classList.add('show');
        setTimeout(() => { toast.classList.remove('show'); }, 3000);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    initNfcSocket();
    
    if (localStorage.getItem('nfc_paired') === 'true') {
        window.isDeviceConnected = true;
        const btnPairing = document.getElementById('btnPairingScanner');
        if (btnPairing) {
            btnPairing.innerHTML = '<i class="fas fa-check-circle"></i> Perangkat Terhubung';
            btnPairing.style.background = '#0284c7';
            btnPairing.style.opacity = '0.9';
            btnPairing.classList.add('btn-pulsing');
        }
    }
    
    const formSiswa = document.getElementById('formSiswa');
    if (formSiswa) {
        formSiswa.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerText;
            submitBtn.innerText = 'Menyimpan...';
            submitBtn.disabled = true;
            
            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTable = doc.querySelector('.data-table');
                const oldTable = document.querySelector('.data-table');
                
                if (newTable && oldTable) {
                    oldTable.innerHTML = newTable.innerHTML;
                    closeModal('modalSiswa');
                    formSiswa.reset();
                    
                    const toast = document.getElementById('toastNotification');
                    if (toast) {
                        toast.innerHTML = '<i class="fas fa-check-circle"></i> Data Siswa berhasil disimpan!';
                        toast.style.background = '#22c55e';
                        toast.classList.add('show');
                        setTimeout(() => { toast.classList.remove('show'); }, 3000);
                    }
                } else {
                    location.reload();
                }
            })
            .catch(err => {
                console.error("Error submitting form:", err);
                location.reload();
            })
            .finally(() => {
                submitBtn.innerText = originalText;
                submitBtn.disabled = false;
            });
        });
    }

    const formGuru = document.getElementById('formGuru');
    if (formGuru) {
        formGuru.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerText;
            submitBtn.innerText = 'Menyimpan...';
            submitBtn.disabled = true;
            
            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTable = doc.querySelector('.guru-grid-container');
                const oldTable = document.querySelector('.guru-grid-container');
                
                if (newTable && oldTable) {
                    oldTable.innerHTML = newTable.innerHTML;
                    closeModal('modalGuru');
                    formGuru.reset();
                    
                    const toast = document.getElementById('toastNotification');
                    if (toast) {
                        toast.innerHTML = '<i class="fas fa-check-circle"></i> Data Guru berhasil disimpan!';
                        toast.style.background = '#22c55e';
                        toast.classList.add('show');
                        setTimeout(() => { toast.classList.remove('show'); }, 3000);
                    }
                } else {
                    location.reload();
                }
            })
            .catch(err => {
                console.error("Error submitting form:", err);
                location.reload();
            })
            .finally(() => {
                submitBtn.innerText = originalText;
                submitBtn.disabled = false;
            });
        });
    }
});
