<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Pernyataan Magang</title>
    <link rel="shortcut icon" href="<?= base_url('assets/img/SP_logo.png'); ?>" type="image/png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            line-height: 1.7;
            padding: 50px 100px;
        }
        ul.informasi-mahasiswa {
            list-style-type: none;
            padding-left: 0;
            margin-left: 0;
        }
        .footer {
            float: right;
            padding-bottom: 30px;
        }
    </style>
</head>
<body>

    <h3 class="mb-4 text-center">SURAT PERNYATAAN <br>
    KERJA PRAKTEK DAN PENELITIAN DI PT SEMEN PADANG</h3>

    <p>Yang bertanda tangan di bawah ini:</p>
    <ul class="informasi-mahasiswa">
        <li><strong>Nama:</strong> <?= esc(user()->fullname) ?></li>
        <li><strong>NISN/NIM:</strong> <?= esc(user()->nisn_nim) ?></li>
        <li><strong>Perguruan Tinggi/Sekolah:</strong> <?= esc($user_data->nama_instansi ?? '-') ?></li>
        <li><strong>Alamat sesuai KTP:</strong> <?= esc(user()->alamat ?? '-') ?></li>
    </ul>

    <p>Dengan ini menyatakan bahwa saya adalah mahasiswa/siswa yang melakukan Kerja Praktek/Penelitian di Unit <strong><?= esc($pendaftaran['unit_kerja']) ?></strong> PT Semen Padang sejak tanggal <strong><?= format_tanggal_indonesia(date('d M Y', strtotime($pendaftaran['tanggal_masuk']))) ?></strong> sampai dengan <strong><?= format_tanggal_indonesia(date('d M Y', strtotime($pendaftaran['tanggal_selesai']))) ?></strong>.</p>

    <p>Saya menyatakan hal-hal sebagai berikut:</p>
    <ol>
        <li><strong>Kepatuhan terhadap Peraturan Perusahaan dan K3</strong>
            <ul>
                <li>Saya akan mematuhi semua peraturan dan tata tertib yang berlaku di PT Semen Padang, serta menjaga nama baik Perguruan Tinggi/Sekolah dan Perusahaan.</li>
                <li>Selama melaksanakan Kerja Praktek/Penelitian di area kerja PT Semen Padang (Produksi, Pemeliharaan, Tambang, SP Inventory, dan SHE), saya akan memakai sepatu safety, helm warna biru dengan tali pengaman, serta rompi scotlight/safety vest sesuai dengan standar keselamatan kerja.</li>
            </ul>
        </li>
        <li><strong>Tanggung Jawab dan Perawatan Fasilitas Perusahaan</strong>
            <ul>
                <li>Saya akan melaksanakan tugas dan tanggung jawab yang diberikan dengan penuh tanggung jawab.</li>
                <li>Saya akan menjaga kondisi barang, peralatan, dan fasilitas milik Perusahaan agar tetap dalam keadaan yang baik dan berfungsi dengan optimal.</li>
            </ul>
        </li>
        <li><strong>Kerahasiaan Data dan Informasi</strong>
            <ul>
                <li>Saya memahami bahwa semua data dan informasi yang diperoleh selama kegiatan Kerja Praktek/Penelitian adalah sepenuhnya milik PT Semen Padang.</li>
                <li>Saya berkomitmen untuk menjaga kerahasiaan data dan informasi milik PT Semen Padang, serta tidak akan memberikan dan/atau menyebarkannya kepada pihak yang tidak berkepentingan atau pihak lain yang dapat memanfaatkan data tersebut untuk kepentingan pribadi/kelompok yang dapat atau berpotensi merugikan PT Semen Padang.</li>
                <li>Seluruh data dan informasi yang diterima dari PT Semen Padang hanya akan digunakan untuk keperluan penulisan hasil Kerja Praktek/Penelitian dan tidak akan dipublikasikan secara umum atau digunakan untuk kepentingan lain tanpa izin dari PT Semen Padang.</li>
                <li>Pernyataan mengenai kerahasiaan data dan informasi ini tetap berlaku dan mengikat meskipun periode Kerja Praktek/Penelitian telah berakhir. Untuk penggunaan data dan informasi yang akan dipublikasikan atau digunakan di kemudian hari, saya akan memperoleh persetujuan ulang dari PT Semen Padang.</li>
            </ul>
        </li>
        <li><strong>Sanksi</strong>
            <ul>
                <li>Saya bersedia dikenakan sanksi sesuai peraturan yang berlaku dan akan bertanggung jawab secara hukum apabila melakukan pelanggaran pada poin 1, 2, dan 3 di atas. Saya siap menanggung biaya kerusakan atau kerugian yang ditimbulkan oleh pelanggaran tersebut.</li>
            </ul>
        </li>
    </ol>

   
    <p>Demikian surat pernyataan ini saya buat dengan sebenar-benarnya tanpa adanya tekanan dari pihak manapun.</p>
    <div class="footer">
   
    <?php if(empty($pendaftaran['tanggal_setujui_pernyataan'])): ?>
    <p><strong>Padang, <?= format_tanggal_indonesia(date('d M Y')) ?></strong></p>
    <p><em>Yang membuat pernyataan</em></p>
    <form action="<?= base_url('magang/setujui-surat-pernyataan') ?>" method="post">
        <button type="submit" class="btn btn-success btn-approve">✅ Setujui & Simpan</button>
    </form>
    <?php else:?>
    <div style="text-align: right;">
        <p>Padang, <?= date('d M Y', strtotime($pendaftaran['tanggal_setujui_pernyataan'])) ?></p>
        <p><strong>Pernyataan ini telah disetujui secara digital oleh:</strong></p>
        <p style="margin-top: 30px;"><strong><?= esc(user()->fullname) ?></strong></p>
        <p><em>(Tertanda secara digital)</em></p>
    </div>
    <?php endif; ?>
    </div>
    

</body>
</html>
