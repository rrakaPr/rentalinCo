<?php
/**
 * VHRent - Laporan (Report) API
 */

require_once 'config.php';

$db = new Database();
$conn = $db->getConnection();

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'dashboard':
        getDashboardStats($conn);
        break;
    case 'kendaraan-disewa':
        getKendaraanDisewa($conn);
        break;
    case 'riwayat-transaksi':
        getRiwayatLaporan($conn);
        break;
    case 'pendapatan':
        getLaporanPendapatan($conn);
        break;
    default:
        errorResponse('Invalid action', 400);
}

function getDashboardStats($conn)
{
    $stats = [];

    // Total kendaraan
    $stmt = $conn->query('SELECT COUNT(*) as total, SUM(stok) as total_stok, SUM(stok_tersedia) as tersedia FROM kendaraan');
    $kendaraan = $stmt->fetch();
    $stats['total_kendaraan'] = $kendaraan['total'];
    $stats['total_stok_kendaraan'] = $kendaraan['total_stok'];
    $stats['kendaraan_tersedia'] = $kendaraan['tersedia'];

    // Total pelanggan
    $stmt = $conn->query('SELECT COUNT(*) as total FROM pelanggan');
    $stats['total_pelanggan'] = $stmt->fetch()['total'];

    // Transaksi aktif
    $stmt = $conn->query('SELECT COUNT(*) as total FROM transaksi WHERE status = "Disewa"');
    $stats['transaksi_aktif'] = $stmt->fetch()['total'];

    // Total transaksi bulan ini
    $stmt = $conn->query('SELECT COUNT(*) as total, COALESCE(SUM(total_biaya), 0) as pendapatan FROM transaksi WHERE MONTH(tanggal_sewa) = MONTH(CURDATE()) AND YEAR(tanggal_sewa) = YEAR(CURDATE())');
    $transaksi_bulan = $stmt->fetch();
    $stats['transaksi_bulan_ini'] = $transaksi_bulan['total'];
    $stats['pendapatan_bulan_ini'] = $transaksi_bulan['pendapatan'];

    // Denda belum dibayar
    $stmt = $conn->query('SELECT COUNT(*) as total, COALESCE(SUM(total_denda), 0) as total_denda FROM denda WHERE status_pembayaran = "Belum Dibayar"');
    $denda = $stmt->fetch();
    $stats['denda_belum_dibayar'] = $denda['total'];
    $stats['total_denda_belum_dibayar'] = $denda['total_denda'];

    // Kendaraan terlambat dikembalikan
    $stmt = $conn->query('SELECT COUNT(*) as total FROM transaksi WHERE status = "Disewa" AND tanggal_kembali_rencana < CURDATE()');
    $stats['kendaraan_terlambat'] = $stmt->fetch()['total'];

    // Recent transactions
    $stmt = $conn->query('
        SELECT t.kode_transaksi, t.tanggal_sewa, t.total_biaya, t.status,
               p.nama as pelanggan_nama, k.nama as kendaraan_nama
        FROM transaksi t
        LEFT JOIN pelanggan p ON t.pelanggan_id = p.id
        LEFT JOIN kendaraan k ON t.kendaraan_id = k.id
        ORDER BY t.created_at DESC
        LIMIT 5
    ');
    $stats['recent_transactions'] = $stmt->fetchAll();

    // Vehicles by type
    $stmt = $conn->query('SELECT jenis, COUNT(*) as total, SUM(stok) as stok FROM kendaraan GROUP BY jenis');
    $stats['kendaraan_by_jenis'] = $stmt->fetchAll();

    successResponse($stats);
}

function getKendaraanDisewa($conn)
{
    $sql = "SELECT t.*, 
                   p.nama as pelanggan_nama, p.no_telepon as pelanggan_telepon,
                   k.nama as kendaraan_nama, k.plat_nomor, k.jenis as kendaraan_jenis, k.gambar_url,
                   DATEDIFF(t.tanggal_kembali_rencana, CURDATE()) as sisa_hari,
                   CASE WHEN t.tanggal_kembali_rencana < CURDATE() THEN 1 ELSE 0 END as is_terlambat
            FROM transaksi t 
            LEFT JOIN pelanggan p ON t.pelanggan_id = p.id 
            LEFT JOIN kendaraan k ON t.kendaraan_id = k.id 
            WHERE t.status = 'Disewa'
            ORDER BY t.tanggal_kembali_rencana ASC";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll();

    successResponse($data);
}

function getRiwayatLaporan($conn)
{
    $tanggal_dari = $_GET['tanggal_dari'] ?? date('Y-m-01');
    $tanggal_sampai = $_GET['tanggal_sampai'] ?? date('Y-m-d');
    $status = $_GET['status'] ?? '';

    $where = ['t.tanggal_sewa BETWEEN ? AND ?'];
    $params = [$tanggal_dari, $tanggal_sampai];

    if ($status) {
        $where[] = 't.status = ?';
        $params[] = $status;
    }

    $whereClause = 'WHERE ' . implode(' AND ', $where);

    $sql = "SELECT t.*, 
                   p.nama as pelanggan_nama,
                   k.nama as kendaraan_nama, k.plat_nomor, k.jenis as kendaraan_jenis,
                   d.total_denda, d.status_pembayaran as status_denda
            FROM transaksi t 
            LEFT JOIN pelanggan p ON t.pelanggan_id = p.id 
            LEFT JOIN kendaraan k ON t.kendaraan_id = k.id 
            LEFT JOIN denda d ON t.id = d.transaksi_id
            $whereClause 
            ORDER BY t.tanggal_sewa DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $data = $stmt->fetchAll();

    // Summary
    $summary = [
        'total_transaksi' => count($data),
        'total_pendapatan' => array_sum(array_column($data, 'total_biaya')),
        'total_denda' => array_sum(array_column($data, 'total_denda'))
    ];

    successResponse([
        'items' => $data,
        'summary' => $summary,
        'periode' => [
            'dari' => $tanggal_dari,
            'sampai' => $tanggal_sampai
        ]
    ]);
}

function getLaporanPendapatan($conn)
{
    $tahun = $_GET['tahun'] ?? date('Y');

    // Monthly revenue
    $sql = "SELECT 
                MONTH(tanggal_sewa) as bulan,
                COUNT(*) as total_transaksi,
                SUM(total_biaya) as pendapatan
            FROM transaksi 
            WHERE YEAR(tanggal_sewa) = ? AND status != 'Dibatalkan'
            GROUP BY MONTH(tanggal_sewa)
            ORDER BY bulan";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$tahun]);
    $monthly = $stmt->fetchAll();

    // Fill missing months
    $fullMonthly = [];
    for ($i = 1; $i <= 12; $i++) {
        $found = false;
        foreach ($monthly as $m) {
            if ($m['bulan'] == $i) {
                $fullMonthly[] = $m;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $fullMonthly[] = [
                'bulan' => $i,
                'total_transaksi' => 0,
                'pendapatan' => 0
            ];
        }
    }

    // Yearly summary
    $stmt = $conn->prepare("SELECT COUNT(*) as total, SUM(total_biaya) as pendapatan FROM transaksi WHERE YEAR(tanggal_sewa) = ? AND status != 'Dibatalkan'");
    $stmt->execute([$tahun]);
    $yearly = $stmt->fetch();

    successResponse([
        'tahun' => $tahun,
        'monthly' => $fullMonthly,
        'summary' => $yearly
    ]);
}
?>