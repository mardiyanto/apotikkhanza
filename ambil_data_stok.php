<?php
// Sambungkan ke database
include 'koneksi.php';

// Pastikan kode_barang diterima melalui permintaan GET
if (isset($_GET['kode_brng'])) {
    $kodeBarang = $_GET['kode_brng'];

    // Lakukan sanitasi pada kode_barang sebelum digunakan dalam query
    $kodeBarang = mysqli_real_escape_string($conn, $kodeBarang);

    // Query untuk mengambil data stok berdasarkan kode_barang
    $query = "SELECT gudangbarang.kode_brng, gudangbarang.kd_bangsal, databarang.nama_brng, SUM(gudangbarang.stok) AS total_stok
              FROM gudangbarang, databarang 
              WHERE gudangbarang.kode_brng = databarang.kode_brng 
              AND gudangbarang.kode_brng = '$kodeBarang' 
              GROUP BY gudangbarang.kode_brng, gudangbarang.kd_bangsal";

    $result = mysqli_query($conn, $query);

    if ($result) {
        // Memeriksa apakah ada hasil dari query
        if (mysqli_num_rows($result) > 0) {
            echo "<thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang $kodeBarang</th>
                        <th>Kode Bangsal</th>
                        <th>Stok</th>
                    </tr>
                  </thead>
                  <tbody>";

            $no = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                $no++;
                echo "<tr>
                        <td>$no</td>
                        <td>{$row['nama_brng']}</td>
                        <td>{$row['kd_bangsal']}</td>
                        <td>{$row['total_stok']}</td>
                      </tr>";
            }

            echo "</tbody>";
        } else {
            echo "<tr><td colspan='4'>Data stok tidak ditemukan.</td></tr>";
        }
    } else {
        echo "<tr><td colspan='4'>Gagal mengambil data stok.</td></tr>";
    }
} else {
    echo "<tr><td colspan='4'>Kode barang tidak ditemukan.</td></tr>";
}
?>
