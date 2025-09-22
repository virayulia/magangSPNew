<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Pendaftaran Magang - Halaman 1</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: url('assets/img/page1.png');
            background-size: cover;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: rgba(255,255,255,0.9);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(99, 67, 67, 0.2);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            display: block;
            margin: 20px auto 0;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background: #007bff;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Form Pendaftaran Magang - Halaman 1</h2>
        <form action="page2.php" method="post">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" required>
            </div>

            <div class="form-group">
                <label>NIM / NIS</label>
                <input type="text" name="nim" required>
            </div>

            <div class="form-group">
                <label>Asal Universitas / Sekolah</label>
                <input type="text" name="asal" required>
            </div>

            <button type="submit">Lanjut ke Halaman 2</button>
        </form>
    </div>
</body>
</html>
