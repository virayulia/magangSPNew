<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ID Card Magang</title>
    <link rel="shortcut icon" href="<?= base_url('assets/img/SP_logo.png'); ?>" type="image/png">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #eaeaea;
            display: block;
            height: auto;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .card {
            width: 5.4cm;
            height: 8.6cm;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            position: relative;
            background: linear-gradient(160deg, #ffffff 0%, #dfe3e8 100%);
            color: #222;
            text-align: center;
        }

        .card::after {
            content: "";
            position: absolute;
            top: 32px;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('<?= base_url("assets/img/SP_logo.png") ?>');
            background-size: 85%;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.05;
            z-index: 0;
        }

        .logo-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 10px;
            background: #fff;
            border-bottom: 1px solid #ccc;
            position: relative;
            z-index: 2;
        }

        .logo {
            width: 27px;
            height: auto;
        }

        .header,
        .foto,
        .info,
        .footer {
            position: relative;
            z-index: 1;
        }

        .header {
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 3px 0;  /* lebih kecil supaya naik */
            line-height: 1.2;
            color: #e30613;
        }

        .foto {
            width: 125px;
            height: 125px;
            object-fit: cover;
            border-radius: 6px;
            border: 2px solid #e30613;
            margin: 2px auto 3px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .info {
            font-size: 10px;
            text-align: center;
        }

        .info h3 {
            margin: 1px 0 2px; /* rapatkan ke bawah */
            font-size: 13px;
        }

        .info p {
            margin: 0;
            line-height: 1.3;
        }

        .info b {
            display: inline-block;
            width: 60px;
            color: #444;
        }

        .footer {
            position: absolute;
            bottom: 6px;
            width: 100%;
            font-size: 8px;
            color: #666;
        }

        @media print {
            body {
                background: none;
                margin: 0;
                padding: 0;
            }

            .card {
                box-shadow: none;
                margin: 0;
                page-break-after: always;
            }
        }
    </style>
</head>
<body>

    <div class="card">
        <div class="logo-container">
            <img src="<?= base_url('assets/img/SIG_Logo.png') ?>" alt="SIG Logo" class="logo">
            <img src="<?= base_url('assets/img/SP_logo2.png') ?>" alt="SP Logo" class="logo">
        </div>

        <div class="header">Magang</div>

        <img src="<?= base_url('/uploads/user-image/' . $user->user_image) ?>" alt="Foto" class="foto">

        <div class="info">
            <h3><strong><?= esc($user->fullname) ?></strong></h3>
            <p><?= esc($user->nisn_nim) ?>
            <br><?= esc($user->nama_instansi) ?>
            <br><?= esc($magang['unit_kerja']) ?>
            <br><?= format_tanggal_singkat($magang['tanggal_masuk']) ?> - <?= format_tanggal_singkat($magang['tanggal_selesai']) ?></p>
        </div>

        <div class="footer">© PT. Semen Padang</div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
