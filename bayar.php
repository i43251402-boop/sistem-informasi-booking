<?php require 'includes/functions.php'; $title='Pembayaran'; $k=$_GET['k']??'';
$q=$pdo->prepare('SELECT b.*,s.nama layanan,s.harga FROM bookings b JOIN services s ON s.id=b.service_id WHERE b.kode=?'); $q->execute([$k]); $b=$q->fetch();
if(!$b){ header('Location: index.php'); exit; }
if($_SERVER['REQUEST_METHOD']==='POST'){
  $pdo->prepare('UPDATE bookings SET lunas=1 WHERE kode=?')->execute([$k]);
  header('Location: '.wa_booking($pdo,$k)); exit;
}
mt_srand(crc32($k)); $svg=''; $F=[[0,0],[14,0],[0,14]];
for($y=0;$y<21;$y++) for($x=0;$x<21;$x++){
  $f=null; foreach($F as $c) if($x>=$c[0]&&$x<$c[0]+7&&$y>=$c[1]&&$y<$c[1]+7) $f=$c;
  if($f){ $dx=$x-$f[0]; $dy=$y-$f[1]; $on=$dx==0||$dx==6||$dy==0||$dy==6||($dx>1&&$dx<5&&$dy>1&&$dy<5); } else $on=mt_rand(0,1);
  if($on) $svg.='<rect x="'.$x.'" y="'.$y.'" width="1" height="1"/>';
}
include 'includes/header.php'; ?>
<main class="narrow"><div class="card qrbox">
  <h1>Bayar dengan QRIS</h1>
  <p class="muted">Ini demo, tidak perlu scan. Tekan tombol di bawah untuk menyelesaikan.</p>
  <svg viewBox="0 0 21 21" width="220" height="220"><?= $svg ?></svg>
  <p><b><?= rp($b['harga']) ?></b> untuk <?= e($b['layanan']) ?> (<?= e($b['kode']) ?>)</p>
  <form method="post"><button class="btn big">Selesai bayar (demo)</button></form>
</div></main>
<?php include 'includes/footer.php'; ?>
