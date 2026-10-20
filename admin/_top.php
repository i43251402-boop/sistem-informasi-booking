<?php require __DIR__.'/../includes/functions.php'; need_admin(); $cur=basename($_SERVER['SCRIPT_NAME']); ?>
<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin - PinkCut Studio</title>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/style.css"></head><body>
<div class="adm"><aside><b>PinkCut Admin</b>
  <a href="index.php" class="<?= $cur=='index.php'?'on':'' ?>">Booking</a>
  <a href="layanan.php" class="<?= $cur=='layanan.php'?'on':'' ?>">Layanan</a>
  <a href="jadwal.php" class="<?= $cur=='jadwal.php'?'on':'' ?>">Jadwal dan libur</a>
  <a href="logout.php">Keluar</a></aside><main>
