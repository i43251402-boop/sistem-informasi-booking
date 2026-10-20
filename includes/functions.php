<?php
session_start();
date_default_timezone_set('Asia/Jakarta');

const WA = '6288213330433';   // nomor WhatsApp admin (format 62)
const JAM = ['09:00','11:00','13:00','15:00','17:00'];
const PAY = ['QRIS','Transfer Bank','E-Wallet'];
const STATUS = ['Menunggu konfirmasi','Dikonfirmasi','Dalam pengerjaan','Revisi','Selesai','Dibatalkan'];

try {
  $pdo = new PDO('mysql:host=localhost;dbname=pinkcut_booking;charset=utf8mb4','root','',   // user & password MySQL
    [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
} catch (Exception $x) {
  die('Database belum terhubung. Import database.sql lewat phpMyAdmin, lalu cek pengaturan di includes/functions.php.');
}

function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function rp($n){ return 'Rp '.number_format((int)$n,0,',','.'); }
function tgl($d){ return date('d-m-Y', strtotime($d)); }
function need_admin(){ if(empty($_SESSION['admin'])){ header('Location: login.php'); exit; } }
function norm_wa($no){ $no=preg_replace('/\D/','',$no); if(strpos($no,'0')===0) $no='62'.substr($no,1); return $no; }
function wa_link($msg=''){ return 'https://wa.me/'.WA.($msg ? '?text='.rawurlencode($msg) : ''); }
function wa_to($no){ return 'https://wa.me/'.norm_wa($no); }
function wa_msg($b){
  return "Halo, saya mau booking jasa editing.\nKode: {$b['kode']}\nLayanan: {$b['layanan']}\nTanggal: ".tgl($b['tanggal']).", pukul {$b['jam']}\nNama: {$b['nama']}\nPembayaran: {$b['pembayaran']}"
    .(!empty($b['catatan']) ? "\nCatatan: {$b['catatan']}" : '');
}
function wa_booking($pdo,$kode){
  $q=$pdo->prepare('SELECT b.*,s.nama layanan FROM bookings b JOIN services s ON s.id=b.service_id WHERE b.kode=?');
  $q->execute([$kode]); $b=$q->fetch(); return $b ? wa_link(wa_msg($b)) : wa_link();
}
function slot_map($pdo,$from,$to){
  $m=[];
  $q=$pdo->prepare("SELECT tanggal,jam FROM bookings WHERE tanggal BETWEEN ? AND ? AND status<>'Dibatalkan'");
  $q->execute([$from,$to]); foreach($q as $r) $m[$r['tanggal'].'|'.$r['jam']]=1;
  $q=$pdo->prepare('SELECT tanggal FROM closed_days WHERE tanggal BETWEEN ? AND ?');
  $q->execute([$from,$to]); foreach($q as $r) $m[$r['tanggal']]='libur';
  return $m;
}
function slot_free($m,$d,$j){
  if(isset($m[$d]) || isset($m[$d.'|'.$j])) return false;
  if($d===date('Y-m-d') && $j<=date('H:i')) return false;
  return true;
}
function tahap($s){ return $s==='Selesai' ? 2 : (in_array($s,['Dalam pengerjaan','Revisi']) ? 1 : 0); }
function ic($n){
  $P=['phone'=>'<rect x="7" y="2" width="10" height="20" rx="2"/><path d="M11 18h2"/>','play'=>'<rect x="2" y="5" width="20" height="14" rx="3"/><path d="M10 9l5 3-5 3z"/>',
  'mic'=>'<rect x="9" y="2" width="6" height="12" rx="3"/><path d="M5 11a7 7 0 0014 0M12 18v4"/>','heart'=>'<path d="M12 21s-8-5-8-11a4.5 4.5 0 018-2.8A4.5 4.5 0 0120 10c0 6-8 11-8 11z"/>',
  'calendar'=>'<rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 10h18M8 2v4M16 2v4"/>','tag'=>'<path d="M3 12V4h8l10 10-8 8z"/><circle cx="7.5" cy="8.5" r="1.2"/>',
  'film'=>'<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 4v16M17 4v16M3 9h4M3 15h4M17 9h4M17 15h4"/>','card'=>'<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h4"/>',
  'chat'=>'<path d="M21 12a8 8 0 01-11.6 7.1L4 20l1-4.6A8 8 0 1121 12z"/>'];
  return '<svg viewBox="0 0 24 24" aria-hidden="true">'.($P[$n] ?? $P['film']).'</svg>';
}
