<?php require __DIR__.'/_top.php'; $err='';
$IK=['film','phone','play','mic','heart','calendar','tag'];
if($_SERVER['REQUEST_METHOD']==='POST'){
  $a=$_POST['aksi']; $ik=in_array($_POST['ikon']??'',$IK)?$_POST['ikon']:'film';
  if($a==='tambah' && trim($_POST['nama'])) $pdo->prepare('INSERT INTO services(nama,kategori,ikon,deskripsi,harga) VALUES(?,?,?,?,?)')->execute([trim($_POST['nama']),trim($_POST['kategori'])?:'Lainnya',$ik,trim($_POST['deskripsi']),(int)$_POST['harga']]);
  if($a==='simpan') $pdo->prepare('UPDATE services SET nama=?,kategori=?,ikon=?,deskripsi=?,harga=?,aktif=? WHERE id=?')->execute([trim($_POST['nama']),trim($_POST['kategori']),$ik,trim($_POST['deskripsi']),(int)$_POST['harga'],isset($_POST['aktif'])?1:0,(int)$_POST['id']]);
  if($a==='hapus'){ try{ $pdo->prepare('DELETE FROM services WHERE id=?')->execute([(int)$_POST['id']]); }catch(Exception $x){ $err='Layanan sudah punya booking, jadi tidak bisa dihapus. Nonaktifkan saja.'; } }
  if(!$err){ header('Location: layanan.php'); exit; }
}
$sv=$pdo->query('SELECT * FROM services ORDER BY id')->fetchAll();
function opt($IK,$cur=''){ foreach($IK as $i) echo '<option '.($i===$cur?'selected':'').'>'.$i.'</option>'; } ?>
<h1>Layanan</h1>
<?php if($err): ?><p class="alert"><?= e($err) ?></p><?php endif; ?>
<form method="post" class="row card" style="margin-bottom:1.5rem"><input type="hidden" name="aksi" value="tambah">
  <label>Nama<input name="nama" required></label><label>Kategori<input name="kategori" placeholder="Sosmed, Event, ..."></label>
  <label>Ikon<select name="ikon"><?php opt($IK); ?></select></label><label>Deskripsi<input name="deskripsi"></label>
  <label>Harga mulai (Rp)<input type="number" name="harga" min="0" required></label><button class="btn">Tambah layanan</button></form>
<table><tr><th>Nama</th><th>Kategori</th><th>Ikon</th><th>Deskripsi</th><th>Harga</th><th>Aktif</th><th></th></tr>
<?php foreach($sv as $s): ?>
<tr><form method="post"><input type="hidden" name="id" value="<?=$s['id']?>">
<td><input name="nama" value="<?= e($s['nama']) ?>"></td><td><input name="kategori" value="<?= e($s['kategori']) ?>"></td>
<td><select name="ikon"><?php opt($IK,$s['ikon']); ?></select></td><td><input name="deskripsi" value="<?= e($s['deskripsi']) ?>"></td>
<td><input type="number" name="harga" value="<?=$s['harga']?>"></td><td><input type="checkbox" name="aktif" <?= $s['aktif']?'checked':'' ?>></td>
<td style="display:flex;gap:.4rem"><button class="btn" name="aksi" value="simpan">Simpan</button>
<button class="btn ghost" name="aksi" value="hapus" onclick="return confirm('Hapus layanan ini?')">Hapus</button></td></form></tr>
<?php endforeach; ?></table>
<?php require __DIR__.'/_bottom.php'; ?>
