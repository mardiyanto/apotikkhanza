<?php
  include 'koneksi.php';
  date_default_timezone_set('Asia/Jakarta');

///////////////////////////lihat/////////////////////////////////////////////
if($_GET['aksi']=='proseseditpengajuan'){
    $tebaru = mysqli_query($koneksi, "SELECT * FROM detail_pengajuan_barang_medis,databarang  WHERE 
    detail_pengajuan_barang_medis.kode_brng = databarang.kode_brng 
    AND detail_pengajuan_barang_medis.kode_brng = '$_GET[kode_brng]' AND detail_pengajuan_barang_medis.no_pengajuan = '$_GET[no_pengajuan]'"); 
    $t=mysqli_fetch_array($tebaru);

    $jml=$_POST['jumlah']*$t['isi']; //isi barang
    $total=$_POST['jumlah']*$t['h_pengajuan'];

    mysqli_query($koneksi,"UPDATE detail_pengajuan_barang_medis SET jumlah='$_POST[jumlah]',jumlah2='$jml',total='$total' 
    WHERE no_pengajuan='$_GET[no_pengajuan]' AND kode_brng='$_GET[kode_brng]'");
    echo "<script>window.alert('Berhasil edit data $_GET[kode_brng] & $_GET[no_pengajuan]'); window.location=('proses.php?aksi=editdetailpemesanan&no_pengajuan=$_GET[no_pengajuan]')</script>";
}
elseif($_GET['aksi']=='setujuipengajuan'){
	mysqli_query($koneksi,"UPDATE pengajuan_barang_medis SET status='Proses Pengajuan' WHERE no_pengajuan='$_GET[no_pengajuan]'");
	echo "<script>window.alert('Berhasil verikasi data $_GET[no_pengajuan]'); window.location=('proses.php?aksi=editdetailpemesanan&no_pengajuan=$_GET[no_pengajuan]')</script>";
}
elseif($_GET['aksi']=='setujuiprosespengajuan'){
	mysqli_query($koneksi,"UPDATE pengajuan_barang_medis SET status='Pengajuan' WHERE no_pengajuan='$_GET[no_pengajuan]'");
	echo "<script>window.alert('Berhasil verikasi data $_GET[no_pengajuan]'); window.location=('proses.php?aksi=editdetailpemesanan&no_pengajuan=$_GET[no_pengajuan]')</script>";
}
elseif($_GET['aksi']=='hapusobatpengajuan'){
	mysqli_query($koneksi,"DELETE FROM detail_pengajuan_barang_medis WHERE no_pengajuan='$_GET[no_pengajuan]' AND kode_brng='$_GET[kode_brng]'");
	echo "<script>window.alert('Berhasil hapus data obat  $_GET[kode_brng] dan $_GET[no_pengajuan]'); window.location=('proses.php?aksi=editdetailpemesanan&no_pengajuan=$_GET[no_pengajuan]')</script>";
}