<?php require 'includes/functions.php';
ini_set('display_errors',1); error_reporting(E_ALL);
if($_SERVER['REQUEST_METHOD']!=='POST'){ header('Location: index.php'); exit; }
$sid=(int)$_POST['service_id']; $tg=$_POST['tanggal']; $jam=$_POST['jam']; $nama=trim($_POST['nama']);
$wa=norm_wa($_POST['wa']); $cat=trim($_POST['catatan']); $link=trim($_POST['link']); $pay=$_POST['pembayaran']; $err='';
$s=$pdo->prepare('SELECT id FROM services WHERE id=? AND aktif=1'); $s->execute([$sid]);
if(!$s->fetch() || !$nama || strlen($wa)<10 || !in_array($jam,JAM) || !in_array($pay,PAY) || !preg_match('/^\d{4}-\d{2}-\d{2}$/',$tg) || $tg<date('Y-m-d'))
  $err='Data pesanan belum lengkap atau tidak valid. Periksa lagi.';
elseif($link && !filter_var($link,FILTER_VALIDATE_URL)) $err='Link bahan harus diawali https://';
else {
  $pdo->beginTransaction();
  $m=slot_map($pdo,$tg,$tg);
  $t=$pdo->prepare("SELECT id FROM bookings WHERE tanggal=? AND jam=? AND status<>'Dibatalkan' FOR UPDATE"); $t->execute([$tg,$jam]);
  if(isset($m[$tg])) $err='Tanggal itu libur. Pilih tanggal lain.';
  elseif($t->fetch() || !slot_free($m,$tg,$jam)) $err='Slot itu sudah terisi atau lewat. Pilih jam lain.';
  else {
    $pdo->prepare('INSERT INTO bookings(nama,wa,service_id,tanggal,jam,catatan,link,pembayaran) VALUES(?,?,?,?,?,?,?,?)')->execute([$nama,$wa,$sid,$tg,$jam,$cat,$link,$pay]);
    $id=$pdo->lastInsertId(); $kode='BK-'.str_pad($id,4,'0',STR_PAD_LEFT);
    $pdo->prepare('UPDATE bookings SET kode=? WHERE id=?')->execute([$kode,$id]); $pdo->commit();
    header('Location: '.($pay==='QRIS' ? 'bayar.php?k='.$kode : wa_booking($pdo,$kode))); exit;
  }
  $pdo->rollBack();
}
$_SESSION['err']=$err; header('Location: index.php#layanan');
