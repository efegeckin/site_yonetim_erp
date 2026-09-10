<?php
include 'config.php';
checkAuth();


// Kasa bakiyesi hesapla
$gelir_result = $conn->query("SELECT SUM(miktar) as total FROM kasa WHERE islem_tipi = 'gelir'");
$toplam_gelir = $gelir_result->fetch_assoc()['total'] ?? 0;

$gider_result = $conn->query("SELECT SUM(miktar) as total FROM kasa WHERE islem_tipi = 'gider'");
$toplam_gider = $gider_result->fetch_assoc()['total'] ?? 0;

$bakiye = $toplam_gelir - $toplam_gider;

// Aylık istatistikler
$current_month = date('m');
$current_year = date('Y');

$aylik_gelir_result = $conn->query("SELECT SUM(miktar) as total FROM kasa WHERE islem_tipi = 'gelir' AND MONTH(tarih) = $current_month AND YEAR(tarih) = $current_year");
$aylik_gelir = $aylik_gelir_result->fetch_assoc()['total'] ?? 0;

$aylik_gider_result = $conn->query("SELECT SUM(miktar) as total FROM kasa WHERE islem_tipi = 'gider' AND MONTH(tarih) = $current_month AND YEAR(tarih) = $current_year");
$aylik_gider = $aylik_gider_result->fetch_assoc()['total'] ?? 0;

$aylik_bakiye = $aylik_gelir - $aylik_gider;
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasa Durumu - Site Yönetim ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .stat-card {
            border-left: 4px solid;
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-card.gelir { border-left-color: #28a745; }
        .stat-card.gider { border-left-color: #dc3545; }
        .stat-card.bakiye { border-left-color: #007bff; }
        .positive { color: #28a745; }
        .negative { color: #dc3545; }
        .kasa-table th { background-color: #f8f9fa; }
    </style>
</head>
<body>
    <?php include 'admin_navbar.php'; ?>

    <div class="container-fluid mt-4">
        <!-- Başlık -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-piggy-bank me-2"></i>Kasa Durumu</h1>
            <div>
                <a href="harcamalar.php" class="btn btn-outline-primary me-2">
                    <i class="fas fa-receipt me-2"></i>Harcama Yönetimi
                </a>
                <?php 
                    if(checkAdmin()===true){
                ?>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#gelirEkleModal">
                    <i class="fas fa-plus me-2"></i>Gelir Ekle
                </button>
                <?php 
                    }
                ?>
            </div>
        </div>

        <!-- İstatistik Kartları -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card gelir">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title text-muted">Toplam Gelir</h6>
                                <h3 class="positive"><?php echo number_format($toplam_gelir, 2); ?> TL</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-arrow-down fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card gider">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title text-muted">Toplam Gider</h6>
                                <h3 class="negative"><?php echo number_format($toplam_gider, 2); ?> TL</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-arrow-up fa-2x text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card bakiye">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title text-muted">Toplam Bakiye</h6>
                                <h3 class="<?php echo $bakiye >= 0 ? 'positive' : 'negative'; ?>">
                                    <?php echo number_format($bakiye, 2); ?> TL
                                </h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-balance-scale fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card bakiye">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title text-muted">Aylık Bakiye</h6>
                                <h3 class="<?php echo $aylik_bakiye >= 0 ? 'positive' : 'negative'; ?>">
                                    <?php echo number_format($aylik_bakiye, 2); ?> TL
                                </h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-calendar-alt fa-2x text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kasa Hareketleri -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-exchange-alt me-2"></i>Kasa Hareketleri
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped kasa-table">
                                <thead>
                                    <tr>
                                        <th>Tarih</th>
                                        <th>İşlem Tipi</th>
                                        <th>Açıklama</th>
                                        <th>Miktar</th>
                                        <th>Bakiye</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT * FROM kasa ORDER BY tarih DESC, id DESC";
                                    $result = $conn->query($sql);
                                    
                                    $running_balance = $bakiye;
                                    
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $islem_class = $row['islem_tipi'] == 'gelir' ? 'positive' : 'negative';
                                            $islem_icon = $row['islem_tipi'] == 'gelir' ? 
                                                '<i class="fas fa-arrow-down text-success me-1"></i>' : 
                                                '<i class="fas fa-arrow-up text-danger me-1"></i>';
                                            
                                            // Bakiye hesaplaması (ters sırada)
                                            if ($row['islem_tipi'] == 'gelir') {
                                                $running_balance -= $row['miktar'];
                                            } else {
                                                $running_balance += $row['miktar'];
                                            }
                                            
                                            echo "<tr>
                                                <td>".date('d.m.Y', strtotime($row['tarih']))."</td>
                                                <td>
                                                    <span class='badge bg-".($row['islem_tipi'] == 'gelir' ? 'success' : 'danger')."'>
                                                        {$islem_icon}".ucfirst($row['islem_tipi'])."
                                                    </span>
                                                </td>
                                                <td>{$row['aciklama']}</td>
                                                <td class='{$islem_class} fw-bold'>
                                                    ".($row['islem_tipi'] == 'gelir' ? '+' : '-').number_format($row['miktar'], 2)." TL
                                                </td>
                                                <td class='fw-bold'>".number_format($running_balance, 2)." TL</td>
                                            </tr>";
                                        }
                                    } else {
                                        echo "<tr>
                                            <td colspan='5' class='text-center text-muted'>
                                                <i class='fas fa-exchange-alt fa-2x mb-2'></i><br>
                                                Henüz kasa hareketi bulunmuyor
                                            </td>
                                        </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gelir Ekle Modal -->
    <div class="modal fade" id="gelirEkleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle me-2"></i>Yeni Gelir Ekle
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="kasa_actions.php">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tarih</label>
                            <input type="date" name="tarih" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Miktar (TL)</label>
                            <input type="number" step="0.01" name="miktar" class="form-control" placeholder="0.00" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Açıklama</label>
                            <textarea name="aciklama" class="form-control" rows="3" placeholder="Gelir kaynağını açıklayın..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" name="gelir_ekle" class="btn btn-success">Gelir Ekle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>