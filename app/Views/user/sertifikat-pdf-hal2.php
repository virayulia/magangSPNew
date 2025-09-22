<style>
    body{
        padding-top: 500px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 50px;
    }
    th, td {
        border: 1px solid #333;
        padding: 6px;
        font-size: 12px;
    }
    th { background: #eee; }
    .center { text-align: center; }
</style>

<table>
    <tbody>
        <tr><td class="center"><?= $penilaian['nilai_disiplin'] ?></td></tr>
        <tr><td class="center"><?= $penilaian['nilai_kerajinan'] ?></td></tr>
        <tr><td class="center"><?= $penilaian['nilai_tingkahlaku'] ?></td></tr>
        <tr><td class="center"><?= $penilaian['nilai_kerjasama'] ?></td></tr>
        <tr><td class="center"><?= $penilaian['nilai_kreativitas'] ?></td></tr>
        <tr><td class="center"><?= $penilaian['nilai_kemampuankerja'] ?></td></tr>
        <tr><td class="center"><?= $penilaian['nilai_tanggungjawab'] ?></td></tr>
        <tr><td class="center"><?= $penilaian['nilai_penyerapan'] ?></td></tr>
        <tr>
            <td class="center"><strong><?= $rataRata ?></strong></td>
        </tr>
        <tr>
            <td class="center"><strong><?= $kategori ?></strong></td>
        </tr>
    </tbody>
</table>

