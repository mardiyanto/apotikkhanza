
<?php
///////////////////////////lihat/////////////////////////////////////////////
if($_GET['aksi']=='index'){
    $tahun_sekarang = date('Y');
    $bulan_sekarang = date('m');
    $sql1 = "SELECT  SUM(detail_pengajuan_barang_medis.total) AS total_per_bulan
            FROM detail_pengajuan_barang_medis
            INNER JOIN pengajuan_barang_medis 
            ON detail_pengajuan_barang_medis.no_pengajuan = pengajuan_barang_medis.no_pengajuan
            WHERE YEAR(pengajuan_barang_medis.tanggal) = $tahun_sekarang AND MONTH(pengajuan_barang_medis.tanggal) = $bulan_sekarang
            AND pengajuan_barang_medis.status = 'Disetujui'
            GROUP BY MONTH(pengajuan_barang_medis.tanggal)";
    
    $result1 = mysqli_query($koneksi, $sql1);
    $tx = mysqli_fetch_assoc($result1);

    $sql2 = "SELECT  COUNT(no_pengajuan) AS hasil
            FROM pengajuan_barang_medis  WHERE YEAR(tanggal) = $tahun_sekarang AND MONTH(tanggal) = $bulan_sekarang
            AND status = 'Proses Pengajuan'
            GROUP BY MONTH(tanggal)";
    
    $result2 = mysqli_query($koneksi, $sql2);
    $tj = mysqli_fetch_assoc($result2);
    $nama_bulan = date("F", mktime(0, 0, 0, $bulan_sekarang, 1));
      // 2. Query data total per bulan dari tabel pemesanan dengan status Belum Dibayar
      $sql3 = "SELECT SUM(tagihan) AS total_per_bulan, COUNT(no_faktur) AS hasil
      FROM pemesanan
      WHERE (status = 'Belum Dibayar' OR status = 'Belum Lunas') AND YEAR(tgl_faktur) = '$tahun_sekarang' AND MONTH(tgl_faktur) = '$bulan_sekarang'
      GROUP BY MONTH(tgl_faktur)";
      $result3 = mysqli_query($koneksi, $sql3);
      $tr = mysqli_fetch_assoc($result3); 
      $hasil_bayar=$tr['total_per_bulan'];
      // 3. Query data total per bulan dari tabel pemesanan dengan status Belum Dibayar
      $sql4 = "SELECT SUM(bayar_pemesanan.besar_bayar) AS total_bayar FROM pemesanan,bayar_pemesanan WHERE 
      pemesanan.no_faktur = bayar_pemesanan.no_faktur AND YEAR(pemesanan.tgl_faktur) = '$tahun_sekarang' AND MONTH(pemesanan.tgl_faktur) = '$bulan_sekarang'
      GROUP BY bayar_pemesanan.no_faktur";
      $result4 = mysqli_query($koneksi, $sql4);
      $ts = mysqli_fetch_assoc($result4);   
      $besar_bayar=$ts['total_bayar'];
      $hasil_bayar1=$besar_bayar-$hasil_bayar;
 echo"  <div class='row'>
 <div class='col-md-3 col-sm-6 col-xs-12'>
   <div class='info-box'>
     <span class='info-box-icon bg-aqua'><i class='fa fa-money'></i></span>

     <div class='info-box-content'> 
       <span class='info-box-text'>Pengajuan $nama_bulan $tahun_sekarang</span>
       <span class='info-box-number'>"; echo "Rp." . number_format($tx['total_per_bulan'], 0, ',', '.'); echo"</small></span>
       <a href='proses.php?aksi=pengajuanobat' type='button' class='btn btn-success btn-sm'>Detail</a>
     </div>
     <!-- /.info-box-content -->
   </div>
   <!-- /.info-box -->
 </div>
 <!-- /.col -->
 <div class='col-md-3 col-sm-6 col-xs-12'>
   <div class='info-box'>
     <span class='info-box-icon bg-red'><i class='fa  fa-credit-card'></i></span>

     <div class='info-box-content'>
       <span class='info-box-text'>Penerimaan $nama_bulan $tahun_sekarang</span>
       <span class='info-box-number'>"; echo "Rp." . number_format($hasil_bayar, 0, ',', '.'); echo"</span>
       
     </div>
     <!-- /.info-box-content -->
   </div>
   <!-- /.info-box -->
 </div>
 <!-- /.col -->

 <!-- fix for small devices only -->
 <div class='clearfix visible-sm-block'></div>

 <div class='col-md-3 col-sm-6 col-xs-12'>
   <div class='info-box'>
     <span class='info-box-icon bg-green'><i class='fa fa-suitcase'></i></span>

     <div class='info-box-content'>
       <span class='info-box-text'>Pengajuan Baru $nama_bulan $tahun_sekarang</span>
       <span class='info-box-number'>$tj[hasil]</span>
       <a href='proses.php?aksi=pengajuanobat' type='button' class='btn btn-success btn-sm'>Detail</a>
     </div>
     <!-- /.info-box-content -->
   </div>
   <!-- /.info-box -->
 </div>
 <!-- /.col -->
 <div class='col-md-3 col-sm-6 col-xs-12'>
   <div class='info-box'>
     <span class='info-box-icon bg-yellow'><i class='fa fa-shopping-cart'></i></span>

     <div class='info-box-content'>
       <span class='info-box-text'>Penerimaan Obat $nama_bulan $tahun_sekarang</span>
       <span class='info-box-number'>$tr[hasil]</span>
     </div>
     <!-- /.info-box-content -->
   </div>
   <!-- /.info-box -->
 </div>
 <!-- /.col -->
</div>
<!-- /.row -->"; 
include "grafik.php";
}
elseif($_GET['aksi']=='pengajuanobat'){
    echo"<div class='box box-default'>
    <div class='box-header with-border'>
    <h3 class='box-title'>Cari Data</h3>
    <div class='box-tools pull-right'>
        <button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
        <button class='btn btn-box-tool' data-widget='remove'><i class='fa fa-remove'></i></button>
    </div>
    </div><!-- /.box-header -->
    <div class='box-body'>
    <form method='get' action='proses.php?aksi=tampildata'> 
    <div class='row'>
        <div class='col-md-4'>
        <div class='form-group'>
            <label>Tgl Awal</label>
            <input type='date' class='form-control' id='startDate' name='startDate' placeholder='Tanggal Awal'>
        </div><!-- /.form-group -->
        </div><!-- /.col -->

        <div class='col-md-4'>
        <div class='form-group'>
            <label>Tgl Akhir</label>
            <input type='date' class='form-control' id='endDate' name='endDate' placeholder='Tanggal Akhir'>
        </div><!-- /.form-group -->
        </div><!-- /.col -->

        <div class='col-md-4'>
        <div class='form-group'>
    </br>
        <input type='submit' class='btn btn-info'  name='submit' value='Tampilkan Data'>
        <input type='hidden' name='aksi' value='tampildata'>
        </div><!-- /.form-group -->
        
    </div><!-- /.col -->
    </div><!-- /.row -->
    </form>
    </div><!-- /.box-body -->

    </div><!-- /.box --> ";

    echo"<div class=row>

    <div class='col-lg-12'>
                        <div class='panel panel-default'>
                            <div class='panel-heading'>
                Data 
                            </div>
                            
                            <div class='panel-body'>
                               <a class='btn btn-info' href='proses.php?aksi=prosespemesanan&p=Proses Pengajuan'>Proses Pengajuan</a>
                               <a class='btn btn-info' href='proses.php?aksi=prosespemesanan&p=Disetujui'>Di Setujui</a>
                               <a class='btn btn-info' href='proses.php?aksi=prosespemesanan&p=Ditolak' >Di Tolak</a> <br> <br>
                              
                              
                           
                                <div class='tab-content'>
                                        <h4>Data Proses Pengadaan Gudang Obat $k_k[nama_instansi] </h4>
                                       
                       <div class='panel-body'>
                                <div class='table-responsive'>

                                
                                    <table id='example1' class='table table-bordered table-striped'>
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>No Pengajuan</th>
                                                <th>Pegawai</th>
                                                <th>Tanggal</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                        ";
                        $no=0;
                    $tebaru=mysqli_query($koneksi," SELECT * FROM pengajuan_barang_medis,pegawai 
                    WHERE pengajuan_barang_medis.nip=pegawai.nik and pengajuan_barang_medis.status='Proses Pengajuan'
                    ORDER BY pengajuan_barang_medis.no_pengajuan DESC ");
                    while ($t=mysqli_fetch_array($tebaru)){          
                    $no++;
                    $tgl_indo = date('d F Y', strtotime($t['tanggal']));     
                                        echo"<tbody>
                                            <tr>
                                                <td>$no</td>
                                                <td><button type='button' class='btn btn-info' data-toggle='modal' data-target='#$t[no_pengajuan]'>$t[no_pengajuan]</button></td> 
                                                <td>$t[nama]</td>
                                                <td>$tgl_indo</td>
                                                <td><a class='btn btn-info' href='proses.php?aksi=detailpemesanan&no_pengajuan=$t[no_pengajuan]'>$t[status]</a></td>
                       
                                            </tr>";
                                            ///MODEL VIEW
                                           
                                echo"
                                <div class='modal fade' id='$t[no_pengajuan]' role='dialog'>
                                    <div class='modal-dialog modal-lg'>
                                      <div class='modal-content'>
                                        <div class='modal-header'>
                                          <button type='button' class='close' data-dismiss='modal'>&times;</button>
                                          <h4 class='modal-title'>NO=$t[no_pengajuan]</h4>
                                        </div>
                                        <div class='modal-body'>
                                        <div class='row'>
                                    <div class='col-md-12'>
                                    <div class='box box-primary'>
                                        <div class='box-body box-profile'>
                                          <h3 class='profile-username text-center'>$t[nama]</h3>
                                          <p class='text-muted text-center'>$t[no_pengajuan]</p>
                                          <p class='text-muted text-center'>$t[keterangan]</p>
                                          <button type='button' class='btn btn-primary' data-dismiss='modal'>Close</button>
                                          <button type='button' class='btn btn-primary' data-dismiss='modal'>$t[status]</button>
                                          <a class='btn btn-info' href='proses.php?aksi=detailpemesanan&no_pengajuan=$t[no_pengajuan]' target='_blank'>Total Harga</a>
                                        </div><!-- /.box-body -->
                                      </div><!-- /.box -->
                                
                                      <!-- About Me Box -->
                                      <div class='box box-primary'>
                                        <div class='box-header with-border'>
                                          <h3 class='box-title'>Detail Pengajuan Obat</h3>
                                        </div><!-- /.box-header -->";
                                        $total_semua = 0;
                                        $no=0;
                                        $sub = mysqli_query($koneksi, "SELECT * FROM detail_pengajuan_barang_medis, pengajuan_barang_medis, databarang  
                                        WHERE detail_pengajuan_barang_medis.no_pengajuan = pengajuan_barang_medis.no_pengajuan
                                        AND detail_pengajuan_barang_medis.kode_brng = databarang.kode_brng AND detail_pengajuan_barang_medis.no_pengajuan = '$t[no_pengajuan]' "); 
                                         
                                        while ($w = mysqli_fetch_array($sub)) {
                                            $no++;
                                            $nilai_total = $w['total']; // Mengakses nilai dari kolom 'total'
                                            $total_semua += $nilai_total; // Menambahkan nilai total ke variabel akumulasi
                                        
                                            $subtotal       = $subtotal + $w['total']; 
                                       echo" <div class='box-body'>
                                          <strong><i class='fa fa-book margin-r-5'></i>$no . $w[nama_brng] ($w[kode_satbesar])</strong>
                                          <div class='tablediv'>
                                    <div class='tablediv-row header'>
                                        <div class='tablediv-cell'>Jumlah = $w[jumlah2]</div>
                                    </div>
                                   <div class='tablediv-row header'>
                                   <div class='tablediv-cell'>Harga ="; echo "Rp." . number_format($w['h_pengajuan'], 0, ',', '.'); echo"</div>
                                    </div>
                                
                                    <div class='tablediv-row header'>
                                   <div class='tablediv-cell'>Total Harga = "; echo "Rp." . number_format($w['total'], 0, ',', '.'); echo"</div>
                                    </div>
                                    
                                    <!-- Tambahkan baris baru (tablediv-row) jika diperlukan -->
                                </div>   
                                          ";
                                          $tebaru2 = mysqli_query($koneksi, "SELECT bangsal.nm_bangsal, gudangbarang.kode_brng, gudangbarang.kd_bangsal, SUM(gudangbarang.stok) AS total_stok
                                                      FROM gudangbarang, bangsal 
                                                      WHERE gudangbarang.kd_bangsal = bangsal.kd_bangsal 
                                                      AND kode_brng='$w[kode_brng]' 
                                                      GROUP BY kode_brng, kd_bangsal");
                                          while ($row = mysqli_fetch_array($tebaru2)) {
                                              echo "<div class='tablediv-row'>
                                              <div class='tablediv-cell'><i class='fa fa-pencil margin-r-5'></i> STOK</strong> $row[nm_bangsal] = $row[total_stok]</div>
                                          </div>";
                                          }
                                         echo" 
                                
                                         
                                        </div>
                                        
                                        <!-- /.box-body -->";
                                        }
                                        
                                        echo"
                                      </div></a><!-- /.box -->
                                    </div><!-- /.col -->
                                    
                                  </div><!-- /.row -->
                                        </div>
                                        <div class='modal-footer'>
                                        <button type='button' class='btn btn-info' data-dismiss='modal'>"; echo "Rp." . number_format($total_semua, 0, ',', '.'); echo"</button>
                                          <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div> ";
                                //AKHIR MODEL
                                        echo"   
                                        </tbody>";
    }
                                    echo"</table>
                                    </div>
                                                   
                            </div>
                     </div>
                 
                                </div>
                            </div>
                        </div>
</div>
<div class='box-footer'>
                        <button class='btn btn-danger btn-lg'> "; echo "Subtotal : Rp." . number_format($subtotal , 0, ',', '.');  echo" </button>
                        </div>";
}
elseif($_GET['aksi']=='grafik'){
include "grafik.php";
}
elseif($_GET['aksi']=='pemesanan'){
    echo"
    <div class='col-lg-12'>
                        <div class='panel panel-default'>
                            <div class='panel-heading'>
                Data paslon
                            </div>
                            <div class='panel-body'>
                                <ul class='nav nav-pills'>
                                    <li class='active'><a href='#home-pills' data-toggle='tab'>Proses Pengajuan</a></li>
                                    <li><a href='#setuju-pills' data-toggle='tab'>Di Setujui</a> </li>
                                    <li><a href='#tolak-pills' data-toggle='tab'>Di Tolak</a> </li>
                                </ul>
                                <div class='tab-content'>
                                    <div class='tab-pane fade in active' id='home-pills'>
                                        <h4>Data Proses Pengadaan Gudang Obat $k_k[nama_instansi] </h4>
                                       
                       <div class='panel-body'>
                                <div class='table-responsive'>

                                
                                    <table id='example1' class='table table-bordered table-striped'>
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>No Pengajuan</th>
                                                <th>Pegawai</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                        ";
                        $no=0;
                    $tebaru=mysqli_query($koneksi," SELECT * FROM pengajuan_barang_medis,pegawai 
                    WHERE pengajuan_barang_medis.nip=pegawai.nik and pengajuan_barang_medis.status='Proses Pengajuan'
                    ORDER BY pengajuan_barang_medis.no_pengajuan DESC ");
                    while ($t=mysqli_fetch_array($tebaru)){          
                    $no++;    
                                        echo"<tbody>
                                            <tr>
                                                <td>$no</td>
                                                <td><a href='proses.php?aksi=detailpemesanan&no_pengajuan=$t[no_pengajuan]' class='btn  btn-success'>$t[no_pengajuan]</a>  <button class='btn btn-primary'>$t[tanggal]</button><br>";
                                                $sub=mysqli_query($koneksi,"SELECT * FROM detail_pengajuan_barang_medis,pengajuan_barang_medis,databarang  
                                                WHERE detail_pengajuan_barang_medis.no_pengajuan=pengajuan_barang_medis.no_pengajuan
                                                and detail_pengajuan_barang_medis.kode_brng=databarang.kode_brng 
                                                AND detail_pengajuan_barang_medis.no_pengajuan='$t[no_pengajuan]'"); 
                                                $jml=mysqli_num_rows($sub);
                                                 // apabila sub menu ditemukan
                                                 if ($jml > 0){
                                                 while($w=mysqli_fetch_array($sub)){
                                                    echo "<p>$w[nama_brng] ($w[kode_satbesar]), qty : $w[jumlah], harga :"; 
                                                    echo "Rp." . number_format($w['h_pengajuan'], 0, ',', '.'); 
                                                    echo ", total : Rp." . number_format($w['total'], 0, ',', '.'); 
                                                    $subtotal       = $subtotal + $w['total']; 
                                                    $sql=mysqli_query($koneksi," SELECT bangsal.nm_bangsal, gudangbarang.kode_brng, gudangbarang.kd_bangsal, SUM(gudangbarang.stok) AS total_stok
                                                    FROM gudangbarang,bangsal WHERE gudangbarang.kd_bangsal = bangsal.kd_bangsal 
                                                    AND kode_brng='$w[kode_brng]' 
                                                    GROUP BY kode_brng, kd_bangsal");
                                                    while ($row=mysqli_fetch_array($sql)){ 
                                                        
                                                        echo" Bangsal: $row[nm_bangsal] (Stok : $row[total_stok]),";
                                                    }
                                                }     
                                            }
                                            else{
                                              echo "tidak ada data";
                                            }      
                                             echo"  </td> 
                                                <td>$t[nama]</td> 
                                                <td><a class='btn btn-info' href='proses.php?aksi=detailpemesanan&no_pengajuan=$t[no_pengajuan]'>$t[status]</a></td>
                       
                                            </tr>
                                           
                                        </tbody>";
    }
                                    echo"</table>
                                    </div>
                                    <button class='btn btn-danger btn-lg'> "; echo "Subtotal : Rp." . number_format($subtotal , 0, ',', '.');  echo" </button>
                                   
                
                            </div>
                     </div>
                     <div class='tab-pane fade' id='setuju-pills'>
                     <h4>Data Pengaddan Gudang Obat $k_k[nama_instansi]</h4>
                        
                     <div class='panel-body'>
                                <div class='table-responsive'>
                               <table id='example2' class='table table-bordered table-striped'>
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>No Pengajuan</th>
                                                    <th>Pegawai</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>";
                                    $tebaru = mysqli_query($koneksi, "SELECT * FROM pengajuan_barang_medis, pegawai 
                                                    WHERE pengajuan_barang_medis.nip = pegawai.nik 
                                                    AND pengajuan_barang_medis.status = 'Disetujui'
                                                    ORDER BY pengajuan_barang_medis.no_pengajuan DESC");
                                                    $no = 0;
                                                    while ($t = mysqli_fetch_array($tebaru)) {
                                                    $no++;
                                        echo "<tr>";
                                        echo "<td>$no</td>";
                                        echo "<td><a href='proses.php?aksi=detailpemesanan&no_pengajuan=$t[no_pengajuan]' class='btn  btn-success'>$t[no_pengajuan]</a>  <button class='btn btn-primary'>$t[tanggal]</button><br>";
                                        $sub = mysqli_query($koneksi, "SELECT * FROM detail_pengajuan_barang_medis, pengajuan_barang_medis, databarang  
                                                        WHERE detail_pengajuan_barang_medis.no_pengajuan = pengajuan_barang_medis.no_pengajuan
                                                        AND detail_pengajuan_barang_medis.kode_brng = databarang.kode_brng 
                                                        AND detail_pengajuan_barang_medis.no_pengajuan = '$t[no_pengajuan]'");
                                        $jml = mysqli_num_rows($sub);
                                        if ($jml > 0) {
                                            while ($w = mysqli_fetch_array($sub)) {
                                                echo "<p>$w[nama_brng] ($w[kode_satbesar]), qty : $w[jumlah], harga :"; 
                                                echo "Rp." . number_format($w['h_pengajuan'], 0, ',', '.'); 
                                                echo ", total : Rp." . number_format($w['total'], 0, ',', '.');
                                                $tt1      = $tt1+ $w['total']; 
                                                $tebaru2 = mysqli_query($koneksi, "SELECT bangsal.nm_bangsal, gudangbarang.kode_brng, gudangbarang.kd_bangsal, SUM(gudangbarang.stok) AS total_stok
                                                            FROM gudangbarang, bangsal 
                                                            WHERE gudangbarang.kd_bangsal = bangsal.kd_bangsal 
                                                            AND kode_brng='$w[kode_brng]' 
                                                            GROUP BY kode_brng, kd_bangsal");

                                                while ($row = mysqli_fetch_array($tebaru2)) {
                                                    echo " Bangsal: $row[nm_bangsal] (Stok : $row[total_stok]),";
                                                }
                                            }
                                        } else {
                                            echo "tidak ada data";
                                        }

                                        echo "</td>";
                                        echo "<td>$t[nama]</td>";
                                        echo "<td><a class='btn btn-info' href='proses.php?aksi=detailpemesanan&no_pengajuan=$t[no_pengajuan]'>$t[status]</a></td>";
                                        echo "</tr>";
                                               }

                                    echo "</tbody>
                               </table>
                                </div>
                                <button class='btn  btn-danger btn-lg'> "; echo "Subtotal : Rp." . number_format($tt1 , 0, ',', '.');  echo" </button>
                            </div>
    
                    </div>

                    <div class='tab-pane fade' id='tolak-pills'>
                     <h4>Data Pengaddan Gudang Obat $k_k[nama_instansi]</h4>
                                       
                     <div class='panel-body'>
                     <div class='table-responsive'>
                     <table id='example3' class='table table-bordered table-striped'>
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>No Pengajuan</th>
                                                    <th>Pegawai</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>";
                                    $tebaru = mysqli_query($koneksi, "SELECT * FROM pengajuan_barang_medis, pegawai 
                                                    WHERE pengajuan_barang_medis.nip = pegawai.nik 
                                                    AND pengajuan_barang_medis.status = 'Ditolak'
                                                    ORDER BY pengajuan_barang_medis.no_pengajuan DESC");
                                                    $no = 0;
                                                    while ($t = mysqli_fetch_array($tebaru)) {
                                                    $no++;
                                        echo "<tr>";
                                        echo "<td>$no</td>";
                                        echo "<td><a href='proses.php?aksi=detailpemesanan&no_pengajuan=$t[no_pengajuan]' class='btn  btn-success'>$t[no_pengajuan]</a>  <button class='btn btn-primary'>$t[tanggal]</button><br><br>";
                                        $sub = mysqli_query($koneksi, "SELECT * FROM detail_pengajuan_barang_medis, pengajuan_barang_medis, databarang  
                                                        WHERE detail_pengajuan_barang_medis.no_pengajuan = pengajuan_barang_medis.no_pengajuan
                                                        AND detail_pengajuan_barang_medis.kode_brng = databarang.kode_brng 
                                                        AND detail_pengajuan_barang_medis.no_pengajuan = '$t[no_pengajuan]'");
                                        $jml = mysqli_num_rows($sub);
                                        if ($jml > 0) {
                                            while ($w = mysqli_fetch_array($sub)) {
                                                echo "<p>$w[nama_brng] ($w[kode_satbesar]), qty : $w[jumlah], harga :"; 
                                                echo "Rp." . number_format($w['h_pengajuan'], 0, ',', '.'); 
                                                echo ", total : Rp." . number_format($w['total'], 0, ',', '.');
                                                $tt      = $tt+ $w['total']; 
                                                $tebaru2 = mysqli_query($koneksi, "SELECT bangsal.nm_bangsal, gudangbarang.kode_brng, gudangbarang.kd_bangsal, SUM(gudangbarang.stok) AS total_stok
                                                            FROM gudangbarang, bangsal 
                                                            WHERE gudangbarang.kd_bangsal = bangsal.kd_bangsal 
                                                            AND kode_brng='$w[kode_brng]' 
                                                            GROUP BY kode_brng, kd_bangsal");

                                                while ($row = mysqli_fetch_array($tebaru2)) {
                                                    echo " Bangsal: $row[nm_bangsal] (Stok : $row[total_stok]),";
                                                }
                                            }
                                        } else {
                                            echo "tidak ada data";
                                        }

                                        echo "</td>";
                                        echo "<td>$t[nama]</td>";
                                        echo "<td><a class='btn btn-info' href='proses.php?aksi=detailpemesanan&no_pengajuan=$t[no_pengajuan]'>$t[status]</a></td>";
                                        echo "</tr>";
                                               }

                                    echo "</tbody>
                               </table>
                                </div>
                                <button class='btn  btn-danger btn-lg'> "; echo "Subtotal : Rp." . number_format($tt , 0, ',', '.');  echo" </button>
                 </div>
    
                    </div>
                    
                    </div>
                                </div>
                            </div>
                        </div>

    "; 
}
elseif($_GET['aksi']=='prosespemesanan'){
    echo"<div class='box box-default'>
    <div class='box-header with-border'>
    <h3 class='box-title'>Cari Data</h3>
    <div class='box-tools pull-right'>
        <button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
        <button class='btn btn-box-tool' data-widget='remove'><i class='fa fa-remove'></i></button>
    </div>
    </div><!-- /.box-header -->
    <div class='box-body'>
    <form method='get' action='proses.php?aksi=tampildata'> 
    <div class='row'>
        <div class='col-md-4'>
        <div class='form-group'>
            <label>Tgl Awal</label>
            <input type='date' class='form-control' id='startDate' name='startDate' placeholder='Tanggal Awal'>
        </div><!-- /.form-group -->
        </div><!-- /.col -->

        <div class='col-md-4'>
        <div class='form-group'>
            <label>Tgl Akhir</label>
            <input type='date' class='form-control' id='endDate' name='endDate' placeholder='Tanggal Akhir'>
        </div><!-- /.form-group -->
        </div><!-- /.col -->

        <div class='col-md-4'>
        <div class='form-group'>
    </br>
        <input type='submit' class='btn btn-info'  name='submit' value='Tampilkan Data'>
        <input type='hidden' name='aksi' value='tampildata'>
        </div><!-- /.form-group -->
        
    </div><!-- /.col -->
    </div><!-- /.row -->
    </form>
    </div><!-- /.box-body -->

    </div><!-- /.box --> ";
    echo"<div class='box box-default'>
    <div class='box-header with-border'>
    <h3 class='box-title'>Data</h3>
    <div class='box-tools pull-right'>
        <button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
        <button class='btn btn-box-tool' data-widget='remove'><i class='fa fa-remove'></i></button>
    </div>
    </div><!-- /.box-header -->
    <div class='box-body'>
    
    <div class='row'>
    <div class='panel-body'>
    <a class='btn btn-info' href='proses.php?aksi=prosespemesanan&p=Proses Pengajuan'>Proses Pengajuan</a>
    <a class='btn btn-info' href='proses.php?aksi=prosespemesanan&p=Disetujui'>Di Setujui</a>
    <a class='btn btn-info' href='proses.php?aksi=prosespemesanan&p=Ditolak' >Di Tolak</a> <br> <br>
     <div class='tab-content'>
             <h4>Data Proses Pengadaan Gudang Obat $k_k[nama_instansi] </h4> 
<div class='panel-body'>
     <div class='table-responsive'>

     
         <table id='example1' class='table table-bordered table-striped'>
             <thead>
                 <tr>
                     <th>No</th>
                     <th>No Pengajuan</th>
                     <th>Pegawai</th>
                     <th>Tanggal</th>
                     <th>Status</th>
                 </tr>
             </thead>
";
$no=0;
$tebaru=mysqli_query($koneksi," SELECT * FROM pengajuan_barang_medis,pegawai 
WHERE pengajuan_barang_medis.nip=pegawai.nik and pengajuan_barang_medis.status='$_GET[p]'
ORDER BY pengajuan_barang_medis.no_pengajuan DESC ");
while ($t=mysqli_fetch_array($tebaru)){ 
    $tgl_indo = date('d F Y', strtotime($t['tanggal']));          
    $no++;    
    echo"<tbody>
        <tr>
            <td>$no</td>
            <td><button type='button' class='btn btn-info' data-toggle='modal' data-target='#$t[no_pengajuan]'>$t[no_pengajuan]</button></td> 
            <td>$t[nama]</td>
            <td>$tgl_indo</td>
            <td><a class='btn btn-info' href='proses.php?aksi=detailpemesanan&no_pengajuan=$t[no_pengajuan]'>$t[status]</a></td>

        </tr>";
        ///MODEL VIEW
       
echo"
<div class='modal fade' id='$t[no_pengajuan]' role='dialog'>
<div class='modal-dialog modal-lg'>
  <div class='modal-content'>
    <div class='modal-header'>
      <button type='button' class='close' data-dismiss='modal'>&times;</button>
      <h4 class='modal-title'>NO=$t[no_pengajuan]</h4>
    </div>
    <div class='modal-body'>
    <div class='row'>
<div class='col-md-12'>
<div class='box box-primary'>
    <div class='box-body box-profile'>
      <h3 class='profile-username text-center'>$t[nama]</h3>
      <p class='text-muted text-center'>$t[no_pengajuan]</p>
      <p class='text-muted text-center'>$t[keterangan]</p>
      <button type='button' class='btn btn-primary' data-dismiss='modal'>Close</button>
      <button type='button' class='btn btn-primary' data-dismiss='modal'>$t[status]</button>
      <a class='btn btn-info' href='proses.php?aksi=detailpemesanan&no_pengajuan=$t[no_pengajuan]' target='_blank'>Total Harga</a>
    </div><!-- /.box-body -->
  </div><!-- /.box -->

  <!-- About Me Box -->
  <div class='box box-primary'>
    <div class='box-header with-border'>
      <h3 class='box-title'>Detail Pengajuan Obat</h3>
    </div><!-- /.box-header -->";
    $total_semua = 0;
    $no=0;
    $sub = mysqli_query($koneksi, "SELECT * FROM detail_pengajuan_barang_medis, pengajuan_barang_medis, databarang  
    WHERE detail_pengajuan_barang_medis.no_pengajuan = pengajuan_barang_medis.no_pengajuan
    AND detail_pengajuan_barang_medis.kode_brng = databarang.kode_brng AND detail_pengajuan_barang_medis.no_pengajuan = '$t[no_pengajuan]' "); 
     
    while ($w = mysqli_fetch_array($sub)) {
        $no++;
        $nilai_total = $w['total']; // Mengakses nilai dari kolom 'total'
        $total_semua += $nilai_total; // Menambahkan nilai total ke variabel akumulasi
    
        $subtotal       = $subtotal + $w['total']; 
   echo" <div class='box-body'>
      <strong><i class='fa fa-book margin-r-5'></i>$no . $w[nama_brng] ($w[kode_satbesar])</strong>
      <div class='tablediv'>
<div class='tablediv-row header'>
    <div class='tablediv-cell'>Jumlah = $w[jumlah]</div>
</div>
<div class='tablediv-row header'>
<div class='tablediv-cell'>Harga ="; echo "Rp." . number_format($w['h_pengajuan'], 0, ',', '.'); echo"</div>
</div>

<div class='tablediv-row header'>
<div class='tablediv-cell'>Total Harga = "; echo "Rp." . number_format($w['total'], 0, ',', '.'); echo"</div>
</div>

<!-- Tambahkan baris baru (tablediv-row) jika diperlukan -->
</div>   
      ";
      $tebaru2 = mysqli_query($koneksi, "SELECT bangsal.nm_bangsal, gudangbarang.kode_brng, gudangbarang.kd_bangsal, SUM(gudangbarang.stok) AS total_stok
                  FROM gudangbarang, bangsal 
                  WHERE gudangbarang.kd_bangsal = bangsal.kd_bangsal 
                  AND kode_brng='$w[kode_brng]' 
                  GROUP BY kode_brng, kd_bangsal");
      while ($row = mysqli_fetch_array($tebaru2)) {
          echo "<div class='tablediv-row'>
          <div class='tablediv-cell'><i class='fa fa-pencil margin-r-5'></i> STOK</strong> $row[nm_bangsal] = $row[total_stok]</div>
      </div>";
      }
     echo" 

     
    </div>
    
    <!-- /.box-body -->";
    }
    
    echo"
  </div></a><!-- /.box -->
</div><!-- /.col -->

</div><!-- /.row -->
    </div>
    <div class='modal-footer'>
    <button type='button' class='btn btn-info' data-dismiss='modal'>"; echo "Rp." . number_format($total_semua, 0, ',', '.'); echo"</button>
      <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
    </div>
  </div>
</div>
</div>
</div> ";
//AKHIR MODEL
    echo"   
    </tbody>";
}
echo"</table>
         </div>
         
        

 </div>
</div>

     </div>
    </div><!-- /.row -->
  
    </div><!-- /.box-body -->
    <div class='box-footer'>
    <button class='btn btn-danger btn-lg'> "; echo "Subtotal : Rp." . number_format($subtotal , 0, ',', '.');  echo" </button>
    </div>
    </div><!-- /.box --> ";
}

elseif($_GET['aksi']=='detailpemesanan'){
    $tebaru = mysqli_query($koneksi, "SELECT * FROM pengajuan_barang_medis, pegawai 
    WHERE pengajuan_barang_medis.nip = pegawai.nik  AND  pengajuan_barang_medis.no_pengajuan='$_GET[no_pengajuan]'");
    $t=mysqli_fetch_array($tebaru);
    $sub = mysqli_query($koneksi, "SELECT * FROM detail_pengajuan_barang_medis, pengajuan_barang_medis, databarang  
                                                        WHERE detail_pengajuan_barang_medis.no_pengajuan = pengajuan_barang_medis.no_pengajuan
                                                        AND detail_pengajuan_barang_medis.kode_brng = databarang.kode_brng 
                                                        AND detail_pengajuan_barang_medis.no_pengajuan = '$_GET[no_pengajuan]'");
 
    echo" <div class='row'>
    <div class='col-md-12'>

      <!-- Profile Image -->
      <div class='box box-primary'>
        <div class='box-body box-profile'>
          <h3 class='profile-username text-center'>$t[nama]</h3>
          <p class='text-muted text-center'>$t[no_pengajuan]</p>
          <a href='proses.php?aksi=pengajuanobat' class='btn btn-primary'>Kembali</a> <a href='proses.php?aksi=pengajuanobat' class='btn btn-primary'>$t[status]</a> 
        </div><!-- /.box-body -->
      </div><!-- /.box -->

      <!-- About Me Box -->
      <div class='box box-primary'>
        <div class='box-header with-border'>
          <h3 class='box-title'>Detail Pengajuan Obat</h3>
        </div><!-- /.box-header -->";
        $no=0;
        while ($w = mysqli_fetch_array($sub)) {
            $no++;
            $tt       = $tt + $w['total']; 
            echo" <div class='box-body'>
            <strong><i class='fa fa-book margin-r-5'></i>$no . $w[nama_brng] ($w[kode_satbesar])</strong>
            <div class='tablediv'>
      <div class='tablediv-row header'>
          <div class='tablediv-cell'>Jumlah = $w[jumlah]</div>
      </div>
     <div class='tablediv-row header'>
     <div class='tablediv-cell'>Harga ="; echo "Rp." . number_format($w['h_pengajuan'], 0, ',', '.'); echo"</div>
      </div>
  
      <div class='tablediv-row header'>
     <div class='tablediv-cell'>Total Harga = "; echo "Rp." . number_format($w['total'], 0, ',', '.'); echo"</div>
      </div>
      
      <!-- Tambahkan baris baru (tablediv-row) jika diperlukan -->
  </div>   
            ";
          
          $tebaru2 = mysqli_query($koneksi, "SELECT bangsal.nm_bangsal, gudangbarang.kode_brng, gudangbarang.kd_bangsal, SUM(gudangbarang.stok) AS total_stok
                      FROM gudangbarang, bangsal 
                      WHERE gudangbarang.kd_bangsal = bangsal.kd_bangsal 
                      AND kode_brng='$w[kode_brng]' 
                      GROUP BY kode_brng, kd_bangsal");
          while ($row = mysqli_fetch_array($tebaru2)) {
            echo "<div class='tablediv-row'>
            <div class='tablediv-cell'><i class='fa fa-pencil margin-r-5'></i> STOK</strong> $row[nm_bangsal] = $row[total_stok]</div>
        </div>";
          }
         echo" 
          <hr>
          
        </div><!-- /.box-body -->";
        }
        
        echo"
      </div></a><!-- /.box -->
    </div><!-- /.col -->
    <div class='col-md-12'>
      <div class='nav-tabs-custom'>
      <a href='#' class='btn btn-primary'>"; echo "Subtotal : Rp." . number_format($tt , 0, ',', '.');  echo"</a>
      </div><!-- /.nav-tabs-custom -->
    </div><!-- /.col -->
  </div><!-- /.row -->
";    
}
elseif ($_GET['aksi'] == 'caripengajuan') {
    // Tampilkan Form untuk Pencarian
    echo '
        <form method="get" action="proses.php?aksi=tampildata"> 
            <label for="startDate">Tanggal Awal:</label>
            <input type="date" id="startDate" name="startDate">

            <label for="endDate">Tanggal Akhir:</label>
            <input type="date" id="endDate" name="endDate">

            <input type="submit" name="submit" value="Tampilkan Data">
            <input type="hidden" name="aksi" value="tampildata">
        </form>
    ';
} elseif ($_GET['aksi'] == 'tampildata') {
    echo"<div class='box box-default'>
    <div class='box-header with-border'>
    <h3 class='box-title'>Cari Data</h3>
    <div class='box-tools pull-right'>
        <button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
        <button class='btn btn-box-tool' data-widget='remove'><i class='fa fa-remove'></i></button>
    </div>
    </div><!-- /.box-header -->
    <div class='box-body'>
    <form method='get' action='proses.php?aksi=tampildata'> 
    <div class='row'>
        <div class='col-md-4'>
        <div class='form-group'>
            <label>Tgl Awal</label>
            <input type='date' class='form-control' id='startDate' name='startDate' placeholder='Tanggal Awal'>
        </div><!-- /.form-group -->
        </div><!-- /.col -->

        <div class='col-md-4'>
        <div class='form-group'>
            <label>Tgl Akhir</label>
            <input type='date' class='form-control' id='endDate' name='endDate' placeholder='Tanggal Akhir'>
        </div><!-- /.form-group -->
        </div><!-- /.col -->

        <div class='col-md-4'>
        <div class='form-group'>
    </br>
        <input type='submit' class='btn btn-info'  name='submit' value='Tampilkan Data'>
        <input type='hidden' name='aksi' value='tampildata'>
        </div><!-- /.form-group -->
        
    </div><!-- /.col -->
    </div><!-- /.row -->
    </form>
    </div><!-- /.box-body -->

    </div><!-- /.box --> ";
    
///PENCARIAN DATA

$startDate = mysqli_real_escape_string($koneksi, $_GET['startDate']);
$endDate = mysqli_real_escape_string($koneksi, $_GET['endDate']);
$tanggal_indo1 = date('d F Y', strtotime($startDate));
$tanggal_indo2 = date('d F Y', strtotime($endDate));
$query = " SELECT * FROM pengajuan_barang_medis, pegawai 
WHERE pengajuan_barang_medis.nip = pegawai.nik and tanggal BETWEEN '$startDate' AND '$endDate'";
$result = mysqli_query($koneksi, $query);
    echo"<div class='box box-default'>
    <div class='box-header with-border'>
    <h3 class='box-title'>Cari Data Pengajuan Obat $tanggal_indo1 S/D $tanggal_indo2 </h3>
    <div class='box-tools pull-right'>
        <button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
        <button class='btn btn-box-tool' data-widget='remove'><i class='fa fa-remove'></i></button>
    </div>
    </div><!-- /.box-header -->
    <div class='box-body'>
    
    <div class='row'>
    <div class='panel-body'>";// Tampilkan Data Pengajuan
    if ($result) {
        echo "<table id='example1' class='table table-bordered table-striped'>
                <thead>
                    <tr>
                        <th>No Pengajuan</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>";

        while ($t = mysqli_fetch_assoc($result)) {
         $tgl_indo = date('d F Y', strtotime($t['tanggal']));   
            echo "<tr>";
            echo "<td><button type='button' class='btn btn-info' data-toggle='modal' data-target='#$t[no_pengajuan]'>$t[no_pengajuan]</button></td>";
            echo "<td>$tgl_indo</td>";
            echo "<td>$t[status]</td>";
            echo "</tr>";

///MODEL VIEW
            $sub = mysqli_query($koneksi, "SELECT * FROM detail_pengajuan_barang_medis, pengajuan_barang_medis, databarang  
            WHERE detail_pengajuan_barang_medis.no_pengajuan = pengajuan_barang_medis.no_pengajuan
            AND detail_pengajuan_barang_medis.kode_brng = databarang.kode_brng AND detail_pengajuan_barang_medis.no_pengajuan = '$t[no_pengajuan]' "); 
echo"
<div class='modal fade' id='$t[no_pengajuan]' role='dialog'>
    <div class='modal-dialog modal-lg'>
      <div class='modal-content'>
        <div class='modal-header'>
          <button type='button' class='close' data-dismiss='modal'>&times;</button>
          <h4 class='modal-title'>NO=$t[no_pengajuan]</h4>
        </div>
        <div class='modal-body'>
        <div class='row'>
    <div class='col-md-12'>

      <!-- Profile Image -->
      <div class='box box-primary'>
        <div class='box-body box-profile'>
          <h3 class='profile-username text-center'>$t[nama]</h3>
          <p class='text-muted text-center'>$t[no_pengajuan]</p>
          <p class='text-muted text-center'>$t[keterangan]</p>
          <button type='button' class='btn btn-primary' data-dismiss='modal'>Close</button>
          <button type='button' class='btn btn-primary' data-dismiss='modal'>$t[status]</button>
          <a class='btn btn-info' href='proses.php?aksi=detailpemesanan&no_pengajuan=$t[no_pengajuan]' target='_blank'>Total Harga</a>
        </div><!-- /.box-body -->
      </div><!-- /.box -->

      <!-- About Me Box -->
      <div class='box box-primary'>
        <div class='box-header with-border'>
          <h3 class='box-title'>Detail Pengajuan Obat</h3>
        </div><!-- /.box-header -->";
        $total_semua = 0; // Variabel untuk menyimpan jumlah total dari kolom 'total'
        $no=0;
while ($w = mysqli_fetch_array($sub)) {
    $no++;
    $nilai_total = $w['total']; // Mengakses nilai dari kolom 'total'
    $total_semua += $nilai_total; // Menambahkan nilai total ke variabel akumulasi
        $no=0;
       echo" <div class='box-body'>
          <strong><i class='fa fa-book margin-r-5'></i>$no . $w[nama_brng] ($w[kode_satbesar])</strong>
          <div class='tablediv'>
    <div class='tablediv-row header'>
        <div class='tablediv-cell'>Jumlah = $w[jumlah]</div>
    </div>
   <div class='tablediv-row header'>
   <div class='tablediv-cell'>Harga ="; echo "Rp." . number_format($w['h_pengajuan'], 0, ',', '.'); echo"</div>
    </div>

    <div class='tablediv-row header'>
   <div class='tablediv-cell'>Total Harga = "; echo "Rp." . number_format($w['total'], 0, ',', '.'); echo"</div>
    </div>
    
    <!-- Tambahkan baris baru (tablediv-row) jika diperlukan -->
</div>   
          ";
          $tebaru2 = mysqli_query($koneksi, "SELECT bangsal.nm_bangsal, gudangbarang.kode_brng, gudangbarang.kd_bangsal, SUM(gudangbarang.stok) AS total_stok
                      FROM gudangbarang, bangsal 
                      WHERE gudangbarang.kd_bangsal = bangsal.kd_bangsal 
                      AND kode_brng='$w[kode_brng]' 
                      GROUP BY kode_brng, kd_bangsal");
          while ($row = mysqli_fetch_array($tebaru2)) {
              echo "<div class='tablediv-row'>
              <div class='tablediv-cell'><i class='fa fa-pencil margin-r-5'></i> STOK</strong> $row[nm_bangsal] = $row[total_stok]</div>
          </div>";
          }
         echo" 

         
        </div>
        <!-- /.box-body -->";
        }
        
        echo"
      </div></a><!-- /.box -->
    </div><!-- /.col -->
    
  </div><!-- /.row -->
        </div>
        <div class='modal-footer'>
          <button type='button' class='btn btn-info' data-dismiss='modal'>"; echo "Rp." . number_format($total_semua, 0, ',', '.'); echo"</button>
          <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
        </div>
      </div>
    </div>
  </div>
</div> ";
//AKHIR MODEL
        }

        echo "</tbody></table>";
    } else {
        echo "Gagal mengambil data.";
    }
    echo"</div> 
    </div><!-- /.row -->
  
    </div><!-- /.box-body -->
    <div class='box-footer'>
    Visit <a href='https://select2.github.io/'>Select2 documentation</a> for more examples and information about the plugin.
    </div>
    </div><!-- /.box --> ";
}
elseif ($_GET['aksi'] == 'home') {
    echo"<div class='box box-default'>
    <div class='box-header with-border'>
    <h3 class='box-title'>Cari Data</h3>
    <div class='box-tools pull-right'>
        <button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
        <button class='btn btn-box-tool' data-widget='remove'><i class='fa fa-remove'></i></button>
    </div>
    </div><!-- /.box-header -->
    <div class='box-body'>
    <form method='get' action='proses.php?aksi=tampildata'> 
    <div class='row'>
        <div class='col-md-4'>
        <div class='form-group'>
            <label>Tgl Awal</label>
            <input type='date' class='form-control' id='startDate' name='startDate' placeholder='Tanggal Awal'>
        </div><!-- /.form-group -->
        </div><!-- /.col -->

        <div class='col-md-4'>
        <div class='form-group'>
            <label>Tgl Akhir</label>
            <input type='date' class='form-control' id='endDate' name='endDate' placeholder='Tanggal Akhir'>
        </div><!-- /.form-group -->
        </div><!-- /.col -->

        <div class='col-md-4'>
        <div class='form-group'>
    </br>
        <input type='submit' class='btn btn-info'  name='submit' value='Tampilkan Data'>
        <input type='hidden' name='aksi' value='tampildata'>
        </div><!-- /.form-group -->
        
    </div><!-- /.col -->
    </div><!-- /.row -->
    </form>
    </div><!-- /.box-body -->

    </div><!-- /.box --> ";

///PENCARIAN DATA
    echo"<div class='box box-default'>
    <div class='box-header with-border'>
    <h3 class='box-title'>Cari Data</h3>
    <div class='box-tools pull-right'>
        <button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
        <button class='btn btn-box-tool' data-widget='remove'><i class='fa fa-remove'></i></button>
    </div>
    </div><!-- /.box-header -->
    <div class='box-body'>
    
    <div class='row'>
    <div class='panel-body'>";// Tampilkan Data Pengajuan
    $startDate = mysqli_real_escape_string($koneksi, $_GET['startDate']);
    $endDate = mysqli_real_escape_string($koneksi, $_GET['endDate']);

    $query = "SELECT no_pengajuan, tanggal, status, keterangan 
              FROM pengajuan_barang_medis 
              WHERE tanggal BETWEEN '$startDate' AND '$endDate'";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        echo "<table id='example1' class='table table-bordered table-striped'>
                <thead>
                    <tr>
                        <th>No Pengajuan</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>";

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td><a href='proses.php?aksi=detailpemesanan&no_pengajuan=$row[no_pengajuan]' class='btn  btn-success'>$row[no_pengajuan]</a></td>";
            echo "<td><button type='button' class='btn btn-info btn-lg' data-toggle='modal' data-target='#$row[no_pengajuan]'>$row[tanggal]</button> </td>";
            echo "<td>$row[status]</td>";
            echo "<td>$row[keterangan]</td>";
            echo "</tr>";
        }
///MODEL VIEW
echo"
<div class='modal fade' id='$row[no_pengajuan]' role='dialog'>
    <div class='modal-dialog modal-lg'>
      <div class='modal-content'>
        <div class='modal-header'>
          <button type='button' class='close' data-dismiss='modal'>&times;</button>
          <h4 class='modal-title'>NO=$row[no_pengajuan]</h4>
        </div>
        <div class='modal-body'>
          <p>This is a large modal.</p>
        </div>
        <div class='modal-footer'>
          <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
        </div>
      </div>
    </div>
  </div>
</div>";
        echo "</tbody></table>";
    } else {
        echo "Gagal mengambil data.";
    }
    echo"</div> 
    </div><!-- /.row -->
  
    </div><!-- /.box-body -->
    <div class='box-footer'>
    tes
    </div>
    </div><!-- /.box --> ";
}
?>