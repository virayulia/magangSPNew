<?= $this->extend('admin/templates/index');?>

<?= $this->section('content');?>
<div class="container-fluid">
<?php $session = \Config\Services::session(); ?>
<?php if ($session->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $session->getFlashdata('success'); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if ($session->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $session->getFlashdata('error') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<h1 class="h3 mb-4 text-gray-800">Seleksi Pendaftar</h1>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th>No</th>
                        <th>Unit Kerja</th>
                        <th>Tingkat Pendidikan</th>
                        <th>Kuota</th>
                        <th>Jumlah Pendaftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($kuota_unit as $kuota): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= esc($kuota->unit_kerja); ?></td>
                            <td><?= esc($kuota->tingkat_pendidikan); ?></td>
                            <td><?= $kuota->sisa_kuota; ?> / <?= $kuota->kuota; ?></td>
                            <td><?= $kuota->jumlah_pendaftar ?? 0; ?></td>
                            <td>
                                <button class="btn btn-sm btn-info"
                                    onclick="loadPendaftar(<?= $kuota->unit_id; ?>, '<?= esc($kuota->tingkat_pendidikan); ?>', '<?= esc($kuota->unit_kerja); ?>')">
                                    Lihat Pendaftar
                                </button>
                            </td>
                        </tr>


                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Modal (letakkan di luar foreach dan hanya satu!) -->
<div class="modal fade" id="modalPendaftar" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle">Daftar Pendaftar</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body" id="pendaftarContent">
        <!-- Konten via AJAX -->
      </div>
    </div>
  </div>
</div>

<script>
function loadPendaftar(unitId, pendidikan, unitKerja) {
    // Ubah judul modal
    $('#modalTitle').text('Daftar Pendaftar - ' + unitKerja);

    // Tampilkan modal
    $('#modalPendaftar').modal('show');
    $('#pendaftarContent').html('<p class="text-center">Memuat data...</p>');

    // Ambil konten pendaftar via fetch
    fetch(`manage-seleksi/pendaftar?unit_id=${unitId}&pendidikan=${pendidikan}`)
    .then(response => response.text())
    .then(data => {
        const el = document.getElementById('pendaftarContent');
        if (el) {
            el.innerHTML = data;

            // Inisialisasi DataTable
            const table = document.getElementById('tablePendaftar');
            if (table) {
                $(table).DataTable({
                    paging: false,
                    info: false,
                    ordering: false,
                    language: {
                        search: "Cari:",
                        zeroRecords: "Tidak ada data ditemukan",
                        emptyTable: "Belum ada pendaftar.",
                    }
                });
            }

            initKuotaHandler();
        }
    })
    .catch(() => {
        const el = document.getElementById('pendaftarContent');
        if (el) {
            el.innerHTML = '<p class="text-danger">Gagal memuat data.</p>';
        }
    });

}


function initKuotaHandler() {
    const sisaKuotaEl = document.getElementById('sisaKuota');
    const btnTerima = document.querySelector('button[onclick="terimaBeberapa()"]');
    if (!sisaKuotaEl) return;

    let sisaKuota = parseInt(sisaKuotaEl.innerText);
    let mode = 'terima'; // default mode

    // Kalau kuota habis → otomatis ke mode "tolak" dan disable tombol
    if (sisaKuota === 0) {
        mode = 'tolak';
        if (btnTerima) {
            btnTerima.disabled = true;
            btnTerima.title = "Kuota penuh, tidak bisa menerima pendaftar baru";
        }
    }

    function updateKuotaDisplay() {
        if (mode === 'terima') {
            const selectedCount = document.querySelectorAll('.checkbox-pendaftar:checked').length;
            sisaKuotaEl.innerText = sisaKuota - selectedCount;
        } else {
            // kalau mode tolak → tampilkan kuota asli, tidak dihitung ulang
            sisaKuotaEl.innerText = sisaKuota;
        }
    }

    // Event checkbox individu
    document.querySelectorAll('.checkbox-pendaftar').forEach(cb => {
        cb.addEventListener('change', function () {
            if (mode === 'terima') {
                const totalChecked = document.querySelectorAll('.checkbox-pendaftar:checked').length;
                if (totalChecked > sisaKuota) {
                    alert('Melebihi kuota yang tersedia!');
                    this.checked = false;
                }
            }

            // Update selectAll checkbox
            const total = document.querySelectorAll('.checkbox-pendaftar').length;
            const selectAll = document.getElementById('selectAll');
            if (selectAll) {
                selectAll.checked = document.querySelectorAll('.checkbox-pendaftar:checked').length === total;
            }

            updateKuotaDisplay();
        });
    });

    // Event tombol "Select All"
    const selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.addEventListener('change', function () {
            const allCheckboxes = document.querySelectorAll('.checkbox-pendaftar');
            const checked = this.checked;
            let count = 0;

            allCheckboxes.forEach(cb => {
                if (checked) {
                    if (mode === 'terima') {
                        if (count < sisaKuota) {
                            cb.checked = true;
                            count++;
                        } else {
                            cb.checked = false;
                        }
                    } else {
                        cb.checked = true; // mode tolak → semua boleh
                    }
                } else {
                    cb.checked = false;
                }
            });

            updateKuotaDisplay();
        });
    }

    updateKuotaDisplay();

    // Fungsi global untuk ubah mode secara manual
    window.setModeKuota = function(newMode) {
        mode = newMode;

        // Kalau user paksa ke mode terima padahal kuota 0 → tetap disable
        if (sisaKuota === 0 && newMode === 'terima' && btnTerima) {
            btnTerima.disabled = true;
        } else if (btnTerima) {
            btnTerima.disabled = false;
        }

        updateKuotaDisplay();
    };
}



// Fungsi terima banyak
function terimaBeberapa() {
    setModeKuota('terima'); // aktifkan mode terima

    const form = document.getElementById('formTerimaPendaftar');
    if (!form) return alert('Form tidak ditemukan!');

    const formData = new FormData(form);
    const selected = formData.getAll('pendaftar_ids[]');

    if (selected.length === 0) {
        alert('Silakan pilih minimal satu pendaftar.');
        return;
    }

    if (!confirm(`Yakin ingin menerima ${selected.length} pendaftar?`)) return;

    fetch('manage-seleksi/terima-banyak', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(res => {
        alert(res.message);
        $('#modalPendaftar').modal('hide');
        location.reload();
    });
}

// Fungsi tolak banyak
function tolakBeberapa() {
    setModeKuota('tolak'); // aktifkan mode tolak

    const form = document.getElementById('formTerimaPendaftar');
    if (!form) return alert('Form tidak ditemukan!');

    const formData = new FormData(form);
    const selected = formData.getAll('pendaftar_ids[]');

    if (selected.length === 0) {
        alert('Silakan pilih minimal satu pendaftar.');
        return;
    }

    if (!confirm(`Yakin ingin menolak ${selected.length} pendaftar?`)) return;

    fetch('manage-seleksi/tolak-banyak', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(res => {
        alert(res.message);
        $('#modalPendaftar').modal('hide');
        location.reload();
    });
}

</script>


</div>

<?= $this->endSection() ?>
