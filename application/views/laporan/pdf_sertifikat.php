<!DOCTYPE html>
<html>
<head>
    <style>
        @page { margin: 0px; }
        body { 
            margin: 0px; 
            font-family: 'Times New Roman', serif;
            background-image: url('<?= $background_path ?>');
            background-position: center;
            background-repeat: no-repeat;
            background-size: 100% 100%;
            width: 100%;
            height: 100%;
        }
        .content {
            position: absolute;
            width: 100%;
            text-align: center;
        }
        /* SESUAIKAN TOP/MARGIN DI BAWAH INI AGAR PAS DENGAN GAMBAR BACKGROUND ANDA */
        .nama {
            margin-top: 280px; /* Atur jarak dari atas */
            font-size: 32px;
            font-weight: bold;
            color: #000;
        }
        .periode {
            margin-top: 20px;
            font-size: 18px;
        }
        .tanggal {
            position: absolute;
            bottom: 150px; /* Jarak dari bawah */
            right: 100px;  /* Jarak dari kanan */
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="nama"><?= $nama ?></div>
        <div class="periode">Telah melaksanakan magang pada periode:<br><?= $periode ?></div>
    </div>
    
    <div class="tanggal"><?= $tanggal_sertifikat ?></div>
</body>
</html>
