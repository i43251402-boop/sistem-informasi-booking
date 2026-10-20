<!DOCTYPE html>
<html lang="id"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($title ?? 'PinkCut Studio') ?> - PinkCut Studio</title>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head><body>
<header class="nav">
  <a class="brand" href="index.php">PinkCut Studio</a>
  <nav>
    <a href="index.php#layanan">Layanan</a>
    <a href="index.php?pesan=1" data-pesan>Pesan</a>
    <a href="index.php#riwayat">Riwayat pesanan</a>
    <a class="btn" target="_blank" rel="noopener" href="<?= e(wa_link('Halo, saya mau bertanya soal jasa editing.')) ?>">Tanyakan di sini</a>
  </nav>
</header>
