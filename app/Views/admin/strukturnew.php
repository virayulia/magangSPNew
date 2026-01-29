<?= $this->extend('admin/templates/index'); ?>
<?= $this->section('content'); ?>
<?php

use function PHPUnit\Framework\isNull;

function renderNode($node, $level = 0)
{
    $hasChildren = !empty($node['children']);
    $nodeId = 'node-' . $node['position_id'];
?>
    <div class="card border shadow-sm mb-2 ml-<?= $level * 3 ?>">
        <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center">

            <div>
                <strong><?= esc($node['name']) ?>
                <?php if(isNull($node['unit_kerja'])):?>
                    <?= esc($node['unit_kerja'])  ?>
                <?php endif;?>
                </strong>

                <span class="text-muted ml-1">
                    (
                    <?= !empty($node['users'])
                        ? esc($node['users'][0]['fullname'])
                        : 'Kosong'
                    ?>
                    )
                </span>
            </div>

            <div class="d-flex align-items-center">

                <!-- EDIT POSISI -->
                 <i class="fas fa-edit text-warning mx-2"
                   style="cursor:pointer"
                   onclick='openPositionModal(<?= json_encode($node) ?>)'></i>

                <!-- EDIT USER -->
                <i class="fas fa-user-edit text-info mx-2"
                   style="cursor:pointer"
                   onclick="openUserModal(
                        <?= $node['position_id'] ?>,
                        <?= !empty($node['users']) ? $node['users'][0]['id'] : 'null' ?>
                   )"></i>

                <!-- DELETE POSITION -->
                <a href="<?= base_url('admin/struktur/delete-position/'.$node['position_id']) ?>"
                   onclick="return confirm('Hapus posisi ini?')">
                    <i class="fas fa-trash text-danger mx-2"></i>
                </a>

                <?php if ($hasChildren): ?>
                    <i class="fas fa-chevron-right text-primary"
                       id="icon-<?= $nodeId ?>"
                       style="cursor:pointer"
                       onclick="toggleNode('<?= $nodeId ?>')"></i>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($hasChildren): ?>
            <div class="ml-3 mt-2 d-none" id="<?= $nodeId ?>">
                <?php foreach ($node['children'] as $child): ?>
                    <?php renderNode($child, $level + 1); ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
<?php } ?>


<div class="container-fluid">
<h1 class="h3 mb-4 text-gray-800">Kelola Struktur Organisasi</h1>
<button class="btn btn-primary mb-4" onclick="openPositionModal()">
    <i class="fas fa-plus"></i> Tambah Posisi
</button>
<?php if (empty($tree)): ?>
    <div class="alert alert-info">Belum ada struktur organisasi</div>
<?php else: ?>
    <?php foreach ($tree as $root): ?>
        <?php renderNode($root); ?>
    <?php endforeach; ?>
<?php endif; ?>

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
                        <label>Nama Posisi</label>
                        <input type="text" name="name" id="pos_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Level / Eselon</label>
                        <input type="number" name="level" id="pos_level" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Unit Kerja</label>
                        <select name="unit_id" id="pos_unit" class="form-control select2">
                            <option value="">-- Pilih Unit --</option>
                            <?php foreach ($units as $u): ?>
                                <option value="<?= $u['unit_id'] ?>">
                                    <?= esc($u['unit_kerja']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    
                    <div class="form-group">
                        <label>Atasan / Parent Posisi</label>
                        <select name="parent_id" id="pos_parent" class="form-control select2">
                            <option value="">-- Tanpa Atasan (Posisi Utama) --</option>
                            <?php foreach ($allPositions as $p): ?>
                                <option value="<?= $p['position_id'] ?>" data-unit="<?= $p['unit_id'] ?? '' ?>">
                                    <?= esc($p['name'].' '.($p['unit_kerja'] ?? '')) ?>
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
    $('.select2').select2({
    width: '100%',
    dropdownParent: $('#modalPosition')
});
</script>
<script>
function toggleNode(id) {
    const body = document.getElementById(id);
    const icon = document.getElementById('icon-' + id);

    if (!body) return;

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
<!-- <script>
$('#pos_parent').on('change', function () {
    const selected = $('#pos_parent option:selected');
    const unitId = selected.data('unit');

    if (unitId !== undefined) {
        if (unitId) {
            $('#pos_unit')
                .val(unitId)
                .prop('disabled', true)
                .trigger('change');
        } else {
            // parent TANPA unit (direktur / puncak)
            $('#pos_unit')
                .val('')
                .prop('disabled', false)
                .trigger('change');
        }
    }
});
</script> -->




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

    // reset
    $('#pos_id').val('');
    $('#pos_name').val('');
    $('#pos_level').val('');
    $('#pos_unit').val('').trigger('change');
    $('#pos_parent').val('').trigger('change');

    if (data) {
        $('#pos_id').val(data.position_id);
        $('#pos_name').val(data.name);
        $('#pos_level').val(data.level);

        if (data.unit_id !== null && data.unit_id !== '') {
            $('#pos_unit')
                .val(String(data.unit_id))
                .trigger('change');
        }

        if (data.parent_id) {
            $('#pos_parent')
                .val(String(data.parent_id))
                .trigger('change');
        }
    }

    $('.modal-title').text(data ? 'Edit Posisi' : 'Tambah Posisi');
    $('#modalPosition').modal('show');
}
</script>




<?= $this->endSection(); ?>

