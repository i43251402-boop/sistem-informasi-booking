<?php require 'includes/functions.php'; $title='Beranda';
$sv=$pdo->query('SELECT * FROM services WHERE aktif=1 ORDER BY id')->fetchAll();
$kats=array_values(array_unique(array_column($sv,'kategori')));
$err=$_SESSION['err']??''; unset($_SESSION['err']);
$q=trim($_GET['q']??''); $hasil=null;
if($q!==''){
  $d=preg_replace('/\D/','',$q); $w=strlen($d)>=9 ? norm_wa($q) : '';
  $st=$pdo->prepare('SELECT b.*,s.nama layanan FROM bookings b JOIN services s ON s.id=b.service_id WHERE b.kode=? OR b.wa=? ORDER BY b.id DESC');
  $st->execute([strtoupper($q),$w]); $hasil=$st->fetchAll();
}
include 'includes/header.php'; ?>
<main>
<section class="hero"><h1>Jasa editing video untuk semua kebutuhan</h1>
<p class="muted">Pilih jenis video, tentukan jadwal, dan kirim pesananmu langsung ke WhatsApp. Tanpa perlu daftar akun.</p>
<a class="btn big" href="#layanan">Lihat layanan</a></section>

<section class="sec" id="layanan"><h2>Layanan</h2><p class="muted">Pilih kategori video yang kamu butuhkan.</p>
<?php if($err): ?><p class="alert"><?= e($err) ?></p><?php endif; ?>
<div class="chips"><button class="chipb on" data-k="Semua">Semua</button>
<?php foreach($kats as $k): ?><button class="chipb" data-k="<?= e($k) ?>"><?= e($k) ?></button><?php endforeach; ?></div>
<div class="grid">
<?php foreach($sv as $s): ?>
  <div class="card" data-k="<?= e($s['kategori']) ?>"><div class="ic"><?= ic($s['ikon']) ?></div><span class="tag"><?= e($s['kategori']) ?></span>
    <h3><?= e($s['nama']) ?></h3><p class="muted"><?= e($s['deskripsi']) ?></p><p class="price">Mulai <?= rp($s['harga']) ?></p>
    <button class="btn" onclick="openOrder(<?=$s['id']?>)">Pesan sekarang</button></div>
<?php endforeach; ?></div></section>

<section class="sec" style="background:var(--tint)"><h2>Cara booking</h2>
<div class="stepsg">
  <div class="step"><span class="e"><?= ic('film') ?></span><div class="n">1</div><h3>Pilih layanan</h3><p class="muted">Tentukan jenis video yang kamu butuhkan dari daftar layanan.</p></div>
  <div class="step"><span class="e"><?= ic('calendar') ?></span><div class="n">2</div><h3>Atur jadwal</h3><p class="muted">Isi data, pilih tanggal dan jam yang masih kosong.</p></div>
  <div class="step"><span class="e"><?= ic('card') ?></span><div class="n">3</div><h3>Pilih pembayaran</h3><p class="muted">Bayar lewat QRIS, transfer bank, atau e-wallet.</p></div>
  <div class="step"><span class="e"><?= ic('chat') ?></span><div class="n">4</div><h3>Kirim ke WhatsApp</h3><p class="muted">Pesan otomatis terisi. Tinggal tekan kirim.</p></div>
</div></section>

<section class="sec" id="riwayat"><h2>Riwayat pesanan</h2>
<p class="muted">Masukkan nomor WhatsApp atau kode booking untuk melihat status pesananmu.</p>
<form class="srow" method="get" action="index.php#riwayat"><input name="q" value="<?= e($q) ?>" placeholder="Nomor WhatsApp atau kode (BK-0001)"><button class="btn">Cek status</button></form>
<?php if($hasil!==null && !$hasil): ?><p class="muted">Pesanan tidak ditemukan. Periksa nomor atau kodenya.</p><?php endif; ?>
<div class="grid">
<?php foreach($hasil ?: [] as $b): $t=tahap($b['status']); $x=$b['status']==='Dibatalkan'; ?>
  <div class="card"><h3><?= e($b['kode']) ?> - <?= e($b['layanan']) ?></h3><p><?= tgl($b['tanggal']) ?>, pukul <?= e($b['jam']) ?> WIB</p>
    <p><span class="badge"><?= $x ? 'Dibatalkan' : ['Dalam antrian','Diproses','Selesai'][$t] ?></span></p>
    <?php if(!$x): ?><div class="trk"><span class="on"></span><i class="<?= $t>0?'on':'' ?>"></i><span class="<?= $t>0?'on':'' ?>"></span><i class="<?= $t>1?'on':'' ?>"></i><span class="<?= $t>1?'on':'' ?>"></span></div>
    <div class="trl"><span>Antrian</span><span>Diproses</span><span>Selesai</span></div><?php endif; ?></div>
<?php endforeach; ?></div></section>
</main>

<dialog id="dOrder"><form class="form" method="post" action="pesan.php">
  <h2>Pesan sekarang</h2>
  <label>Layanan<select name="service_id" id="fs" required>
    <?php foreach($sv as $s): ?><option value="<?=$s['id']?>"><?= e($s['nama']) ?> (<?= rp($s['harga']) ?>)</option><?php endforeach; ?></select></label>
  <label>Nama<input name="nama" required></label>
  <label>Nomor WhatsApp<input name="wa" required placeholder="08xxxxxxxxxx" inputmode="tel"></label>
  <div class="two"><label>Tanggal<input type="date" name="tanggal" id="ft" min="<?= date('Y-m-d') ?>" required></label>
  <label>Jam<select name="jam" id="fj" required><?php foreach(JAM as $j): ?><option><?=$j?></option><?php endforeach; ?></select></label></div>
  <label>Catatan<textarea name="catatan" rows="2" placeholder="Gaya, durasi, deadline"></textarea></label>
  <label>Link bahan (Google Drive, dsb.)<input name="link" placeholder="https://"></label>
  <div><b style="font-size:.92rem">Pembayaran</b><div class="pay" style="margin-top:.3rem">
    <?php foreach(PAY as $i=>$p): ?><label><input type="radio" name="pembayaran" value="<?=$p?>" <?= $i?'':'checked' ?>><?=$p?></label><?php endforeach; ?></div></div>
  <div style="display:flex;gap:.6rem"><button class="btn big" style="flex:1">Kirim ke WhatsApp</button><button type="button" class="btn ghost" onclick="dOrder.close()">Batal</button></div>
</form></dialog>

<script>
const dOrder=document.getElementById('dOrder'),ft=document.getElementById('ft'),fj=document.getElementById('fj');
function openOrder(id){ if(id) document.getElementById('fs').value=id; cek(); dOrder.showModal(); }
function cek(){ if(!ft.value) return;
  fetch('slots.php?tanggal='+ft.value).then(r=>r.json()).then(d=>{
    [...fj.options].forEach(o=>o.disabled=!d.tersedia.includes(o.value));
    const ok=[...fj.options].find(o=>!o.disabled); if(fj.selectedOptions[0]&&fj.selectedOptions[0].disabled&&ok) fj.value=ok.value; }); }
ft.addEventListener('change',cek);
document.querySelectorAll('.chipb').forEach(b=>b.onclick=()=>{
  document.querySelectorAll('.chipb').forEach(x=>x.classList.toggle('on',x===b));
  document.querySelectorAll('#layanan .card').forEach(c=>c.hidden=!(b.dataset.k==='Semua'||c.dataset.k===b.dataset.k)); });
document.querySelectorAll('[data-pesan]').forEach(a=>a.onclick=e=>{e.preventDefault();openOrder()});
if(location.search.includes('pesan=1')) openOrder();
</script>
<?php include 'includes/footer.php'; ?>
