<?php require __DIR__.'/../includes/functions.php'; $err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $q=$pdo->prepare('SELECT * FROM admins WHERE email=?'); $q->execute([trim($_POST['email'])]); $r=$q->fetch();
  if($r && hash_equals($r['password'],$_POST['password'])){ $_SESSION['admin']=['id'=>$r['id'],'nama'=>$r['nama']]; header('Location: index.php'); exit; }
  $err='Email atau password admin salah.';
}?>
<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login Admin</title><link rel="stylesheet" href="../assets/style.css"></head><body>
<main class="narrow"><form class="card form" method="post"><h1>Login admin</h1>
<?php if($err): ?><p class="alert"><?= e($err) ?></p><?php endif; ?>
<label>Email<input type="email" name="email" required></label>
<label>Password<input type="password" name="password" required></label>
<button class="btn big">Masuk</button></form></main></body></html>
