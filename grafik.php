<script src="chart.js"></script>
    <div class='box box-default'>
        <div class='box-header with-border'>
        <h3 class='box-title'>Cari Data</h3>
        <div class='box-tools pull-right'>
            <button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
            <button class='btn btn-box-tool' data-widget='remove'><i class='fa fa-remove'></i></button>
        </div>
        </div><!-- /.box-header -->
        <div class='box-body'>
        
        <div class='row'>
            <div class='col-md-6'>
                <div >
                    <canvas id="barChart1"></canvas>
                </div>
            </div><!-- /.col -->
    
            <div class='col-md-6'>
                <div >
                    <canvas id="lineChart"></canvas>
                </div>
            </div><!-- /.col -->

            <div class='col-md-6'>
                <div >
                    <canvas id="barChart2"></canvas>
                </div>
            </div><!-- /.col -->
            
            <div class='col-md-6'>
                <div >
                    <canvas id="lineChart1"></canvas>
                </div>
            </div><!-- /.col -->
     
        </div><!-- /.row -->
     
        </div><!-- /.box-body -->
    
        </div><!-- /.box -->
        
        <?php
         // Data untuk grafik bar pertama (barData1) 
        // $sql = "SELECT MONTH(pengajuan_barang_medis.tanggal) AS bulan, 
        //         SUM(detail_pengajuan_barang_medis.total) AS total_per_bulan
        //         FROM detail_pengajuan_barang_medis
        //         INNER JOIN pengajuan_barang_medis 
        //         ON detail_pengajuan_barang_medis.no_pengajuan = pengajuan_barang_medis.no_pengajuan
        //         GROUP BY MONTH(pengajuan_barang_medis.tanggal)";
        //$tahun_sekarang = '2023';
       
        // // Kueri untuk mendapatkan data total per bulan untuk tahun sekarang
        // $sql = "SELECT MONTH(pengajuan_barang_medis.tanggal) AS bulan, 
        //                SUM(detail_pengajuan_barang_medis.total) AS total_per_bulan
        //         FROM detail_pengajuan_barang_medis
        //         INNER JOIN pengajuan_barang_medis 
        //         ON detail_pengajuan_barang_medis.no_pengajuan = pengajuan_barang_medis.no_pengajuan
        //         WHERE YEAR(pengajuan_barang_medis.tanggal) = $tahun_sekarang
        //         GROUP BY MONTH(pengajuan_barang_medis.tanggal)";
       
       // $tahun_sekarang = date('Y');
        // Kueri untuk mendapatkan data total per bulan untuk tahun sekarang dengan status Disetujui
        //$tahun_sekarang = date('Y');
        $sql = "SELECT MONTH(pengajuan_barang_medis.tanggal) AS bulan, 
                       SUM(detail_pengajuan_barang_medis.total) AS total_per_bulan
                FROM detail_pengajuan_barang_medis
                INNER JOIN pengajuan_barang_medis 
                ON detail_pengajuan_barang_medis.no_pengajuan = pengajuan_barang_medis.no_pengajuan
                WHERE YEAR(pengajuan_barang_medis.tanggal) = $tahun_sekarang
                AND pengajuan_barang_medis.status = 'Disetujui'
                GROUP BY MONTH(pengajuan_barang_medis.tanggal)";
        
        $result = mysqli_query($koneksi, $sql);

        // Array untuk menyimpan data bulan dan total
        $bulan = [];
        $total_per_bulan = [];

        while ($row = mysqli_fetch_assoc($result)) {
            // Mengisi array bulan dan total
            $nama_bulan = date("F", mktime(0, 0, 0, $row['bulan'], 1)); // Mengonversi angka bulan menjadi nama bulan
            $bulan[] = $nama_bulan;
            $total_per_bulan[] = $row['total_per_bulan'];
        }

        // 2. Query data total per bulan dari tabel pemesanan dengan status Belum Dibayar
        $data = "SELECT MONTH(tgl_faktur) AS bulan, SUM(tagihan) AS total_per_bulan
        FROM pemesanan
        WHERE (status = 'Belum Dibayar' OR status = 'Belum Lunas') AND YEAR(tgl_faktur) = '$tahun_sekarang'
        GROUP BY MONTH(tgl_faktur)";

        $result = mysqli_query($koneksi, $data);

        // 3. Proses hasil kueri untuk persiapan data
        $mont = [];
        $total_per_mont = [];

        while ($t = mysqli_fetch_assoc($result)) {
        $nama_mont = date("F", mktime(0, 0, 0, $t['bulan'], 1));    
        $mont[] = $nama_mont;
        $total_per_mont[] = $t['total_per_bulan'];
        }
        ?>
 
	
    <script>
        // Data untuk grafik bar pertama
        var bulan = <?php echo json_encode($bulan); ?>;
        var total_per_bulan = <?php echo json_encode($total_per_bulan); ?>;

    // Data untuk grafik bar pertama (barData1)
    var barData1 = {
        labels: bulan, // Menggunakan label bulan dari data yang diambil dari database
        datasets: [{
            label: 'Pengajuan Obat <?php echo $tahun_sekarang ;?>',
            data: total_per_bulan, // Menggunakan data total per bulan dari data yang diambil dari database
            backgroundColor: [
            'rgba(255, 99, 132, 0.2)',
            'rgba(255, 159, 64, 0.2)',
            'rgba(255, 205, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(201, 203, 207, 0.2)'
            ],
            borderColor: [
            'rgb(255, 99, 132)',
            'rgb(255, 159, 64)',
            'rgb(255, 205, 86)',
            'rgb(75, 192, 192)',
            'rgb(54, 162, 235)',
            'rgb(153, 102, 255)',
            'rgb(201, 203, 207)'
             ],
            borderWidth: 1
        }]
    };

        // Data untuk grafik bar kedua
        var barData2 = {
            labels: <?php echo json_encode($mont); ?>,
            datasets: [{
                label: 'Pemesanan Obat <?php echo $tahun_sekarang ;?>',
                data: <?php echo json_encode($total_per_mont); ?>,
                backgroundColor: [
                'rgba(201, 203, 207, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(255, 205, 86, 0.2)',
                'rgba(255, 159, 64, 0.2)',
                'rgba(255, 99, 132, 0.2)'
                ],
                borderColor: [
                'rgb(255, 99, 132)',
                'rgb(255, 159, 64)',
                'rgb(255, 205, 86)',
                'rgb(75, 192, 192)',
                'rgb(54, 162, 235)',
                'rgb(153, 102, 255)',
                'rgb(201, 203, 207)'
                ],
                borderWidth: 1
            }]
        };

        // Data untuk grafik line
        var lineData = {
            labels: bulan,
            datasets: [{
                label: 'Grafik Line',
                data: total_per_bulan,
                fill: false,
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2
            }]
        };
        // Data untuk grafik line1
        var lineData1 = {
                    labels: <?php echo json_encode($mont); ?>,
                    datasets: [{
                        label: 'Grafik Line',
                        data: <?php echo json_encode($total_per_mont); ?>,
                        fill: false,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 2,
                        tension: 2
                    }]
        };

        // Mendapatkan konteks canvas untuk grafik bar pertama
        var barCtx1 = document.getElementById('barChart1').getContext('2d');
        var barChart1 = new Chart(barCtx1, {
            type: 'bar',
            data: barData1,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Mendapatkan konteks canvas untuk grafik bar kedua
        var barCtx2 = document.getElementById('barChart2').getContext('2d');
        var barChart2 = new Chart(barCtx2, {
            type: 'bar',
            data: barData2,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Mendapatkan konteks canvas untuk grafik line
        var lineCtx = document.getElementById('lineChart').getContext('2d');
        var lineChart = new Chart(lineCtx, {
            type: 'line',
            data: lineData,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
         // Mendapatkan konteks canvas untuk grafik line
        var lineCtx1 = document.getElementById('lineChart1').getContext('2d');
        var lineChart1 = new Chart(lineCtx1, {
            type: 'line',
            data: lineData1,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });       
		// Data untuk grafik pie chart
        var pieData = {
            labels: ['A', 'B', 'C', 'D'],
            datasets: [{
                data: [30, 20, 25, 25],
                backgroundColor: ['rgba(255, 99, 132, 0.5)', 'rgba(54, 162, 235, 0.5)', 'rgba(255, 206, 86, 0.5)', 'rgba(75, 192, 192, 0.5)'],
                borderColor: ['rgba(255,99,132,1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)', 'rgba(75, 192, 192, 1)'],
                borderWidth: 1
            }]
        };

        // Mendapatkan konteks canvas untuk grafik pie chart
        var pieCtx = document.getElementById('pieChart').getContext('2d');
        var pieChart = new Chart(pieCtx, {
            type: 'pie',
            data: pieData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>