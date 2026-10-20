<?php require 'includes/functions.php'; header('Content-Type: application/json');
$d=$_GET['tanggal']??''; $out=['libur'=>false,'tersedia'=>[]];
if(preg_match('/^\d{4}-\d{2}-\d{2}$/',$d)){
  $m=slot_map($pdo,$d,$d); $out['libur']=isset($m[$d]);
  foreach(JAM as $j) if(slot_free($m,$d,$j)) $out['tersedia'][]=$j;
}
echo json_encode($out);
