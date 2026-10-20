<?php require __DIR__.'/_top.php';
if($_SERVER['REQUEST_METHOD']==='POST' && in_array($_POST['status'],STATUS)){
  $pdo->prepare('UPDATE bookings SET status=? WHERE id=?')->execute([$_POST['status'],(int)$_POST['id']]);
  header('Location: index.php'); exit;
}
$baru=$pdo->query("SELECT COUNT(*) FROM bookings WHERE status='Menunggu konfirmasi'")->fetchColumn();
$hari=$pdo->query("SELECT COUNT(*) FROM bookings WHERE tanggal=CURDATE() AND status<>'Dibatalkan'")->fetchColumn();
$proses=$pdo->query("SELECT COUNT(*) FROM bookings WHERE status IN ('Dikonfirmasi','Dalam pengerjaan','Revisi')")->fetchColumn();
$omzet=$pdo->query("SELECT COALESCE(SUM(s.harga),0) FROM bookings b JOIN services s ON s.id=b.service_id WHERE b.status='Selesai' AND MONTH(b.tanggal)=MONTH(CURDATE()) AND YEAR(b.tanggal)=YEAR(CURDATE())")->fetchColumn();
$f=$_GET['status']??''; $sql='SELECT b.*,s.nama layanan FROM bookings b JOIN services s ON s.id=b.service_id';
if(in_array($f,STATUS)){ $q=$pdo->prepare($sql.' WHERE b.status=? ORDER BY b.tanggal,b.jam'); $q->execute([$f]); } else { $q=$pdo->query($sql.' ORDER BY b.id DESC'); }
$rows=$q->fetchAll(); ?>
<h1>Booking</h1>
<div class="stats">
  <div class="stat"><b><?=$baru?></b>Menunggu konfirmasi</div><div class="stat"><b><?=$hari?></b>Jadwal hari ini</div>
  <div class="stat"><b><?=$proses?></b>Sedang dikerjakan</div><div class="stat"><b><?= rp($omzet) ?></b>Pendapatan bulan ini (selesai)</div>
</div>
<form method="get" class="row" style="margin-bottom:1rem"><label>Filter status
  <select name="status" onchange="this.form.submit()"><option value="">Semua</option>
  <?php foreach(STATUS as $s): ?><option <?= $f===$s?'selected':'' ?>><?=$s?></option><?php endforeach; ?></select></label></form>
<table><tr><th>Kode</th><th>Pelanggan</th><th>Layanan</th><th>Jadwal</th><th>Catatan</th><th>Bayar</th><th>Status</th></tr>
<?php foreach($rows as $r): ?>
<tr><td><?= e($r['kode']) ?></td>
<td><?= e($r['nama']) ?><br><a target="_blank" rel="noopener" href="<?= e(wa_to($r['wa'])) ?>">Chat <?= e($r['wa']) ?></a></td>
<td><?= e($r['layanan']) ?></td><td><?= tgl($r['tanggal']) ?><br><?= e($r['jam']) ?></td>
<td><?= e($r['catatan']) ?><?php if($r['link']): ?><br><a target="_blank" rel="noopener" href="<?= e($r['link']) ?>">Link bahan</a><?php endif; ?></td>
<td><?= e($r['pembayaran']) ?><br><?= $r['lunas']?'Lunas':'Belum' ?></td>
<td><form method="post"><input type="hidden" name="id" value="<?=$r['id']?>">
  <select name="status"><?php foreach(STATUS as $s): ?><option <?= $r['status']===$s?'selected':'' ?>><?=$s?></option><?php endforeach; ?></select>
  <button class="btn">Simpan</button></form></td></tr>
<?php endforeach; if(!$rows): ?><tr><td colspan="7" class="muted">Belum ada booking.</td></tr><?php endif; ?>
</table>
<?php require __DIR__.'/_bottom.php'; ?>
