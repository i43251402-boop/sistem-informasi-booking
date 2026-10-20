<?php require __DIR__.'/_top.php';
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!empty($_POST['tambah']) && preg_match('/^\d{4}-\d{2}-\d{2}$/',$_POST['tanggal']))
    $pdo->prepare('REPLACE INTO closed_days(tanggal,keterangan) VALUES(?,?)')->execute([$_POST['tanggal'],trim($_POST['keterangan'])]);
  if(!empty($_POST['hapus'])) $pdo->prepare('DELETE FROM closed_days WHERE tanggal=?')->execute([$_POST['hapus']]);
  header('Location: jadwal.php'); exit;
}
$libur=$pdo->query('SELECT * FROM closed_days WHERE tanggal>=CURDATE() ORDER BY tanggal')->fetchAll();
$next=$pdo->query("SELECT b.*,s.nama layanan,b.nama pelanggan FROM bookings b JOIN services s ON s.id=b.service_id WHERE b.tanggal>=CURDATE() AND b.status<>'Dibatalkan' ORDER BY b.tanggal,b.jam")->fetchAll(); ?>
<h1>Jadwal dan libur</h1>
<h2>Tutup tanggal tertentu</h2>
<form method="post" class="row card" style="margin-bottom:1rem"><input type="hidden" name="tambah" value="1">
  <label>Tanggal<input type="date" name="tanggal" min="<?= date('Y-m-d') ?>" required></label>
  <label>Keterangan<input name="keterangan" placeholder="Libur nasional, cuti, dll."></label><button class="btn">Tandai libur</button></form>
<table><tr><th>Tanggal libur</th><th>Keterangan</th><th></th></tr>
<?php foreach($libur as $l): ?><tr><td><?= tgl($l['tanggal']) ?></td><td><?= e($l['keterangan']) ?></td>
<td><form method="post"><button class="btn ghost" name="hapus" value="<?=$l['tanggal']?>">Buka lagi</button></form></td></tr>
<?php endforeach; if(!$libur): ?><tr><td colspan="3" class="muted">Tidak ada hari libur terjadwal.</td></tr><?php endif; ?></table>
<h2 style="margin-top:2rem">Jadwal mendatang</h2>
<table><tr><th>Tanggal</th><th>Jam</th><th>Kode</th><th>Pelanggan</th><th>Layanan</th><th>Status</th></tr>
<?php foreach($next as $n): ?><tr><td><?= tgl($n['tanggal']) ?></td><td><?= e($n['jam']) ?></td><td><?= e($n['kode']) ?></td>
<td><?= e($n['pelanggan']) ?></td><td><?= e($n['layanan']) ?></td><td><?= e($n['status']) ?></td></tr>
<?php endforeach; if(!$next): ?><tr><td colspan="6" class="muted">Belum ada jadwal.</td></tr><?php endif; ?></table>
<?php require __DIR__.'/_bottom.php'; ?>
