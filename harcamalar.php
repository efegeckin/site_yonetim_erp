<?php
include 'config.php';
checkAuth();
checkAdmin();

// Harcama ekleme
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['harcama_ekle'])) {
    $tarih = $conn->real_escape_string($_POST['tarih']);
    $miktar = floatval($_POST['miktar']);
    $kategori = $conn->real_escape_string($_POST['kategori']);
    $aciklama = $conn->real_escape_string($_POST['aciklama']);

    // Dosya yükleme
    $belge_path = '';
    if (isset($_FILES['belge']) && $_FILES['belge']['error'] == 0) {
        $uploads_dir = 'uploads/harcamalar/';
        if (!is_dir($uploads_dir)) {
            mkdir($uploads_dir, 0777, true);
        }

        $file_name = time() . '_' . basename($_FILES['belge']['name']);
        $target_file = $uploads_dir . $file_name;

        if (move_uploaded_file($_FILES['belge']['tmp_name'], $target_file)) {
            $belge_path = $target_file;
        }
    }

    $ay = date('m', strtotime($tarih));
    $yil = date('Y', strtotime($tarih));
    $kontrol_sql = "SELECT COUNT(*) as adet FROM harcamalar WHERE kategori = '$kategori' AND MONTH(tarih) = $ay AND YEAR(tarih) = $yil";
    $kontrol_result = $conn->query($kontrol_sql);
    $adet = $kontrol_result ? $kontrol_result->fetch_assoc()['adet'] : 0;

    if ($adet > 0 && $ay != '12') {
        header("Location: harcamalar.php?error=Bu ay ve kategori için zaten harcama var!");
        exit();
    }

    $sql = "INSERT INTO harcamalar (tarih, miktar, kategori, aciklama, belge_path) 
            VALUES ('$tarih', $miktar, '$kategori', '$aciklama', '$belge_path')";

    if ($conn->query($sql)) {
        // Kasa güncelleme
        $kasa_sql = "INSERT INTO kasa (islem_tipi, miktar, aciklama, tarih) 
                     VALUES ('gider', $miktar, '$kategori - $aciklama', '$tarih')";
        $conn->query($kasa_sql);

        header("Location: harcamalar.php?success=harcama_eklendi");
    } else {
        header("Location: harcamalar.php?error=harcama_eklenemedi");
    }
    exit();
}

// Harcama silme
if (isset($_GET['harcama_sil'])) {
    $harcama_id = intval($_GET['harcama_sil']);

    // Belgeyi sil
    $sql = "SELECT belge_path FROM harcamalar WHERE id = $harcama_id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['belge_path'] && file_exists($row['belge_path'])) {
            unlink($row['belge_path']);
        }
    }

    $sql = "DELETE FROM harcamalar WHERE id = $harcama_id";
    if ($conn->query($sql)) {
        header("Location: harcamalar.php?success=harcama_silindi");
    } else {
        header("Location: harcamalar.php?error=harcama_silinemedi");
    }
    exit();
}

// Toplam istatistikler
$toplam_harcama_result = $conn->query("SELECT SUM(miktar) as total FROM harcamalar");
$toplam_harcama = $toplam_harcama_result->fetch_assoc()['total'] ?? 0;

$aylik_harcama_result = $conn->query("SELECT SUM(miktar) as total FROM harcamalar WHERE MONTH(tarih) = MONTH(CURDATE()) AND YEAR(tarih) = YEAR(CURDATE())");
$aylik_harcama = $aylik_harcama_result->fetch_assoc()['total'] ?? 0;

$kategori_dagilim_result = $conn->query("SELECT kategori, SUM(miktar) as total FROM harcamalar GROUP BY kategori");
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Harcama Yönetimi - Site Yönetim ERP</title>
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

        .stat-card.gelir {
            border-left-color: #28a745;
        }

        .stat-card.gider {
            border-left-color: #dc3545;
        }

        .stat-card.bakiye {
            border-left-color: #007bff;
        }

        .belge-link {
            font-size: 0.8rem;
        }

        .category-badge {
            font-size: 0.75rem;
        }

        .alert {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 1050;
        }
    </style>
</head>

<body>
    <?php include 'admin_navbar.php'; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_GET['error'] ?? ''); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <div class="container-fluid mt-4">
        <!-- Başlık -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-receipt me-2"></i>Harcama Yönetimi</h1>
            <a href="kasa.php" class="btn btn-primary">
                <i class="fas fa-piggy-bank me-2"></i>Kasa Durumu
            </a>
        </div>

        <!-- İstatistik Kartları -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card gider">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title text-muted">Toplam Harcama</h6>
                                <h3 class="text-danger"><?php echo number_format($toplam_harcama, 2); ?> TL</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-money-bill-wave fa-2x text-danger"></i>
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
                                <h6 class="card-title text-muted">Aylık Harcama</h6>
                                <h3 class="text-warning"><?php echo number_format($aylik_harcama, 2); ?> TL</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-calendar-alt fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title text-muted">Kategori Sayısı</h6>
                                <h3 class="text-info"><?php echo $kategori_dagilim_result->num_rows; ?></h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-tags fa-2x text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title text-muted">Ort. Harcama</h6>
                                <h3 class="text-success">
                                    <?php
                                    $avg_result = $conn->query("SELECT AVG(miktar) as avg FROM harcamalar");
                                    $ortalama = $avg_result->fetch_assoc()['avg'] ?? 0;
                                    echo number_format($ortalama, 2);
                                    ?> TL
                                </h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-chart-line fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Harcama Ekleme Formu -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-plus-circle me-2"></i>Yeni Harcama Ekle
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Tarih</label>
                                <input type="date" name="tarih" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Miktar (TL)</label>
                                <input type="number" step="0.01" name="miktar" class="form-control" placeholder="0.00" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Kategori</label>
                                <select name="kategori" class="form-select" required>
                                    <option value="">Kategori Seçin</option>
                                    <option value="Temizlik">Temizlik</option>
                                    <option value="Bakım">Bakım & Onarım</option>
                                    <option value="Yakıt">Yakıt</option>
                                    <option value="Elektrik">Elektrik</option>
                                    <option value="Su">Su</option>
                                    <option value="Personel">Personel Giderleri</option>
                                    <option value="Vergi">Vergi & Sigorta</option>
                                    <option value="Diğer">Diğer</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Açıklama</label>
                                <textarea name="aciklama" class="form-control" rows="3" placeholder="Harcama detayını yazın..."></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Belge (Fatura/Makbuz)</label>
                                <input type="file" name="belge" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                <div class="form-text">PDF, JPG, PNG formatlarını yükleyebilirsiniz (Max: 5MB)</div>
                            </div>

                            <button type="submit" name="harcama_ekle" class="btn btn-success w-100">
                                <i class="fas fa-save me-2"></i>Harcamayı Kaydet
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Kategori Dağılımı -->
                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-pie me-2"></i>Kategori Dağılımı
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $kategori_result = $conn->query("SELECT kategori, SUM(miktar) as total FROM harcamalar GROUP BY kategori ORDER BY total DESC");
                        while ($row = $kategori_result->fetch_assoc()) {
                            $yuzde = ($row['total'] / $toplam_harcama) * 100;
                            echo '
                            <div class="mb-2">
                                <div class="d-flex justify-content-between">
                                    <span>' . $row['kategori'] . '</span>
                                    <span>' . number_format($row['total'], 2) . ' TL</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar" role="progressbar" style="width: ' . $yuzde . '%"></div>
                                </div>
                                <small class="text-muted">%' . number_format($yuzde, 1) . '</small>
                            </div>';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Harcama Listesi -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-list me-2"></i>Harcama Geçmişi
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Tarih</th>
                                        <th>Kategori</th>
                                        <th>Açıklama</th>
                                        <th>Miktar</th>
                                        <th>Belge</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT * FROM harcamalar ORDER BY tarih DESC, id DESC";
                                    $result = $conn->query($sql);

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $belge_icon = $row['belge_path'] ?
                                                '<a href="' . $row['belge_path'] . '" target="_blank" class="btn btn-sm btn-outline-success">
                                                    <i class="fas fa-file-invoice"></i>
                                                </a>' :
                                                '<span class="text-muted">Yok</span>';

                                            echo "<tr>
                                                <td>" . date('d.m.Y', strtotime($row['tarih'])) . "</td>
                                                <td>
                                                    <span class='badge bg-primary category-badge'>{$row['kategori']}</span>
                                                </td>
                                                <td>{$row['aciklama']}</td>
                                                <td class='text-danger fw-bold'>" . number_format($row['miktar'], 2) . " TL</td>
                                                <td>{$belge_icon}</td>
                                                <td>
                                                    <a href='harcamalar.php?harcama_sil={$row['id']}' 
                                                       class='btn btn-sm btn-danger' 
                                                       onclick='return confirm(\"Bu harcamayı silmek istediğinizden emin misiniz?\")'>
                                                        <i class='fas fa-trash'></i>
                                                    </a>
                                                </td>
                                            </tr>";
                                        }
                                    } else {
                                        echo "<tr>
                                            <td colspan='6' class='text-center text-muted'>
                                                <i class='fas fa-receipt fa-2x mb-2'></i><br>
                                                Henüz harcama kaydı bulunmuyor
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>