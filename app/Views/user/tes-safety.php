<!-- app/Views/safety/quiz.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tes Safety Induction</title>
    <link rel="shortcut icon" href="<?= base_url('assets/img/SP_logo.png'); ?>" type="image/png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4">🦺 Tes Safety Induction</h2>

    <form id="formTesSafety" action="<?= base_url('safety/submit') ?>" method="post">
        <?php foreach ($soal as $index => $s): ?>
            <div class="mb-4">
                <p><strong><?= ($index + 1) ?>.</strong> <?= esc($s['pertanyaan']) ?></p>
                <?php foreach (['a', 'b', 'c', 'd'] as $opt): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" 
                               name="jawaban[<?= $s['soal_id'] ?>]" 
                               value="<?= $opt ?>" 
                               id="soal<?= $s['soal_id'] ?>_<?= $opt ?>" required>
                        <label class="form-check-label" for="soal<?= $s['soal_id'] ?>_<?= $opt ?>">
                            <?= strtoupper($opt) ?>. <?= esc($s['opsi_' . $opt]) ?>
                        </label>
                    </div>
                <?php endforeach ?>
            </div>
        <?php endforeach ?>

        <button type="submit" class="btn btn-primary">Kirim Jawaban</button>
    </form>
</div>

<script>
document.getElementById('formTesSafety').addEventListener('submit', function(e) {
    e.preventDefault(); // Stop default submit

    Swal.fire({
        title: 'Kirim Jawaban?',
        text: 'Jawaban tidak dapat diubah setelah dikirim.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#0d6efd',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, kirim!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            this.submit(); // Submit form kalau dikonfirmasi
        }
    });
});
</script>
</body>
</html>