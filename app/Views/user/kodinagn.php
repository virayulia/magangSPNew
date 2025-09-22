<!-- Dokumen -->
        <div class="tab-pane fade" id="dokumen" role="tabpanel">
            <div class="card p-4 shadow-sm rounded-4">
                <h5 class="fw-bold mb-3">Kelengkapan Dokumen</h5>
                <p class="text-muted mb-4">Lengkapi dokumen untuk mempermudah proses pendaftaran magang. <br><small class="text-danger">*Wajib diisi</small></p>

                <?php 
                $docs = [
                    ['cv', 'Curriculum Vitae', true],
                    ['proposal', 'Proposal', false],
                    ['surat_permohonan', 'Surat Permohonan', true],
                    ['ktp_kk', 'KTP/KK', false],
                    ['bpjs_kes', 'BPJS Kesehatan', false],
                    ['bpjs_tk', 'BPJS Ketenagakerjaan', false],
                    ['buktibpjs_tk', 'Bukti Pembayaran BPJS TK', false],
                ];
                foreach ($docs as [$field, $label, $wajib]) :
                    $is_uploaded = !empty($user_data->$field);
                    $uploadId = ucfirst($field) . "File";
                ?>
                <div class="border rounded p-3 mb-3 bg-light">
                    <h6 class="fw-semibold mb-2"><?= $label ?><?= $wajib ? '<span class="text-danger">*</span>' : '' ?></h6>
                    <?php if ($is_uploaded): ?>
                        <div class="d-flex justify-content-between align-items-center">
                            <div><i class="bi bi-file-earmark-text me-2"></i><?= esc($user_data->$field) ?></div>
                            <div>
                                <a href="<?= base_url('uploads/' . $field . '/' . $user_data->$field) ?>" target="_blank" class="btn btn-sm btn-outline-info">Lihat</a>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete<?= ucfirst($field) ?>(<?= $user_data->id ?>)">Delete</button>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-muted">Dokumen belum diupload</div>
                        <button type="button" class="btn btn-sm btn-primary mt-2" onclick="document.getElementById('<?= $uploadId ?>').click();">Upload file</button>
                        <input type="file" name="<?= $field ?>" id="<?= $uploadId ?>" style="display:none;" onchange="upload<?= ucfirst($field) ?>(this)">
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>

            </div>
        </div>

        
            <!-- Surat Pernyataan -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">📝 Surat Pernyataan</h5>
                    <p>Unduh, isi, dan unggah kembali surat pernyataan berikut sebagai salah satu syarat pelaksanaan magang.</p>

                    <a href="<?= base_url('template/surat-pernyataan.pdf') ?>" class="btn btn-outline-primary mb-3" download>
                        <i class="bi bi-download"></i> Unduh Template Surat Pernyataan
                    </a>

                    <!-- Status Unggah -->
                    <?php if (!empty($pendaftaran['file_pernyataan'])): ?>
                        <div class="alert alert-success p-2">
                            ✅ Surat pernyataan telah diunggah pada <strong><?= date('d M Y', strtotime($pendaftaran['tgl_upload_pernyataan'])) ?></strong>.
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('magang/upload-surat-pernyataan') ?>" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="file_pernyataan" class="form-label">Unggah Surat Pernyataan (PDF)</label>
                            <input class="form-control" type="file" name="file_pernyataan" id="file_pernyataan" accept="application/pdf" required>
                        </div>
                        <button type="submit" class="btn btn-success">Unggah Surat</button>
                    </form>
                </div>
            </div>