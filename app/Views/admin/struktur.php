<?= $this->extend('admin/templates/index'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
<h1 class="h3 mb-4 text-gray-800">Kelola Struktur Organisasi</h1>
<button class="btn btn-primary mb-4" onclick="openPositionModal()">
    <i class="fas fa-plus"></i> Tambah Posisi
</button>

<?php if (empty($positions)): ?>
    <div class="alert alert-info">
        Belum ada struktur organisasi.
    </div>
<?php else: ?>

<?php
    // group posisi per unit_id
    $grouped = [];
    foreach ($positions as $p) {
        $grouped[$p['unit_id']]['name'] = $p['unit_kerja'];
        $grouped[$p['unit_id']]['items'][] = $p;
    }
?>

<?php foreach ($grouped as $unitId => $unit): 

    // cari kepala unit (parent_id NULL)
    $kepala = null;
    foreach ($unit['items'] as $p) {
        if (empty($p['parent_id'])) {
            $kepala = $p;
            break;
        }
    }
?>

<!-- ================= UNIT CARD ================= -->
<div class="card border shadow-sm mb-4">
    <div class="card-body py-3">

        <!-- HEADER UNIT -->
        <div class="d-flex align-items-center">

            <strong class="text-gray-800">
                <?= esc($unit['name']) ?>
            </strong>

            <?php if ($kepala): ?>
                <span class="text-muted ml-2">
                    — <?= esc($kepala['name']) ?>
                    (
                    <?= !empty($kepala['users'])
                        ? esc($kepala['users'][0]['fullname'])
                        : 'Kosong'
                    ?>
                    )
                </span>
            <?php endif; ?>

            <!-- ACTION -->
            <div class="ml-auto d-flex align-items-center">

                <?php if ($kepala): ?>
                    <!-- EDIT ORANG -->
                    <i class="fas fa-user-edit text-info mx-2"
                    style="cursor:pointer"
                    onclick='openUserModal(
                            <?= $kepala['position_id'] ?>,
                            <?= !empty($kepala["users"]) ? $kepala["users"][0]["id"] : "null" ?>
                    )'></i>

                    <!-- HAPUS ORANG -->
                    <?php if (!empty($kepala['users'])): ?>
                        <a href="<?= base_url('admin/struktur/remove-user/'.$kepala['position_id']) ?>"
                        onclick="return confirm('Hapus orang dari posisi ini?')">
                            <i class="fas fa-user-times text-warning mx-2"></i>
                        </a>
                    <?php endif; ?>

                    <!-- HAPUS POSISI -->
                    <a href="<?= base_url('admin/struktur/delete-position/'.$kepala['position_id']) ?>"
                    onclick="return confirm('Hapus posisi ini?')">
                        <i class="fas fa-trash text-danger mx-2"></i>
                    </a>
                <?php endif; ?>

                <!-- TOGGLE -->
                <i class="fas fa-chevron-right text-primary ml-2"
                id="icon-unit-<?= $unitId ?>"
                style="cursor:pointer"
                onclick="toggleUnit(<?= $unitId ?>)"></i>
            </div>
        </div>


        <!-- BAWAHAN -->
        <div class="ml-4 mt-3 d-none" id="unit-body-<?= $unitId ?>">

            <?php foreach ($unit['items'] as $pos): ?>
                <?php if (!empty($pos['parent_id'])): ?>

                <!-- POSISI -->
                <div class="card border-0 shadow-sm mb-2">
                    <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center">

                        <div>
                            <?= esc($pos['name']) ?>
                            <span class="text-muted ml-1">
                                (
                                <?= !empty($pos['users'])
                                    ? esc($pos['users'][0]['fullname'])
                                    : 'Kosong'
                                ?>
                                )
                            </span>
                        </div>

                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-light"
                                onclick='openPositionModal(<?= json_encode($pos) ?>)'>
                                <i class="fas fa-edit text-warning"></i>
                            </button>

                            <button class="btn btn-light"
                                onclick="openUserModal(
                                    <?= $pos['position_id'] ?>,
                                    <?= !empty($pos['users']) ? $pos['users'][0]['id'] : 'null' ?>
                                )">
                                <i class="fas fa-user-edit text-info"></i>
                            </button>
                            
                            <a href="<?= base_url('admin/struktur/delete-position/'.$pos['position_id']) ?>"
                               class="btn btn-light text-danger"
                               onclick="return confirm('Hapus posisi ini?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>

                    </div>
                </div>

                <?php endif; ?>
            <?php endforeach; ?>

        </div>

    </div>
</div>

<?php endforeach; ?>
<?php endif; ?>
</div>

<!-- Modal Posisi -->
<div class="modal fade" id="modalPosition">
    <div class="modal-dialog">
        <form method="post" action="<?= base_url('admin/struktur/save-position') ?>">
            <?= csrf_field() ?>

            <input type="hidden" name="id" id="pos_id">

            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Posisi</h5>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>Unit Kerja</label>
                        <select name="unit_id" id="pos_unit" class="form-control select2" required>
                            <option value="">-- Pilih Unit --</option>
                            <?php foreach ($units as $u): ?>
                                <option value="<?= $u['unit_id'] ?>">
                                    <?= esc($u['unit_kerja']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Nama Posisi</label>
                        <input type="text" name="name" id="pos_name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Level / Eselon</label>
                        <input type="number" name="level" id="pos_level" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Atasan / Parent Posisi</label>
                        <select name="parent_id" id="pos_parent" class="form-control select2">
                            <option value="">-- Tanpa Atasan (Posisi Utama) --</option>
                            <?php foreach ($allPositions as $p): ?>
                                <option value="<?= $p['position_id'] ?>" data-unit="<?= $p['unit_id'] ?>">
                                    <?= esc($p['name'].' '.$p['unit_kerja']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">
                            Kosongkan jika posisi ini berada di level tertinggi unit
                        </small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Assign User -->
 <div class="modal fade" id="userModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="post" action="<?= base_url('admin/struktur/assign-user') ?>">
      <?= csrf_field() ?>

      <input type="hidden" name="position_id" id="modal_position_id">

      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Isi Posisi</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">

          <div class="form-group">
            <label>Pilih Pegawai</label>
            <select name="user_id" id="modal_user_id" class="form-control" required>
              <option value="">-- Pilih --</option>
              <?php foreach ($users as $u): ?>
                <option value="<?= $u->id ?>">
                    <?= esc($u->fullname) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

        </div>

        <div class="modal-footer">
          <button class="btn btn-primary">Simpan</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        </div>

      </div>
    </form>
  </div>
</div>



<?= $this->endSection(); ?>
<?= $this->section('scripts') ?>
<script>
function toggleUnit(unitId) {
    const body = document.getElementById('unit-body-' + unitId);
    const icon = document.getElementById('icon-unit-' + unitId);

    if (!body || !icon) return;

    if (body.classList.contains('d-none')) {
        body.classList.remove('d-none');
        icon.classList.remove('fa-chevron-right');
        icon.classList.add('fa-chevron-down');
    } else {
        body.classList.add('d-none');
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-right');
    }
}
</script>
<script>
function openUserModal(positionId, userId = null) {
    document.getElementById('modal_position_id').value = positionId;

    const userSelect = document.getElementById('modal_user_id');
    userSelect.value = userId ? userId : '';

    $('#userModal').modal('show');
}
</script>
<script>
function openPositionModal(data = null) {
    $('#pos_id').val(data ? data.id : '');
    $('#pos_name').val(data ? data.name : '');
    $('#pos_level').val(data ? data.level : '');
    $('#pos_unit').val(data ? data.unit_id : '');
    $('#modalPosition').modal('show');
}

// function openUserModal(data = null, positionId = null) {
//     $('#user_id').val(data ? data.id : '');
//     $('#user_name').val(data ? data.fullname : '');
//     $('#user_email').val(data ? data.email : '');
//     $('#user_position').val(data ? data.position_id : positionId);
//     $('#modalUser').modal('show');
// }
</script>

<?= $this->endSection(); ?>

