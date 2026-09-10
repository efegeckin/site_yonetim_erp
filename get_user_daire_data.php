<?php
// announcements tablosuna resim yolu için alan eklemek:
// ALTER TABLE announcements ADD COLUMN image_path VARCHAR(255) DEFAULT NULL;
// Örnek veri eklemek:
// UPDATE announcements SET image_path = 'uploads/announcements/duyuru1.jpg' WHERE id = 1;

include 'config.php';
checkAuth();

if (!isset($_SESSION['user_id'])) {
    echo '<div class="alert alert-danger">Oturum süresi doldu. Lütfen tekrar giriş yapın.</div>';
    exit;
}

$daire_id = intval($_POST['daire_id'] ?? 0);
if ($daire_id <= 0) {
    echo '<div class="alert alert-warning">Daire seçimi hatalı.</div>';
    exit;
}

// Toplam borç
$result = $conn->query("SELECT SUM(tutat) as total FROM borclar WHERE daire_id = $daire_id AND durum = 0");
$toplam_borc = $result->fetch_assoc()['total'] ?? 0;

// Toplam ödenmiş
$result = $conn->query("SELECT SUM(tutat) as total FROM borclar WHERE daire_id = $daire_id AND durum = 1");
$toplam_odenmis = $result->fetch_assoc()['total'] ?? 0;

// Araç sayısı
$result = $conn->query("SELECT COUNT(*) as total FROM araclar WHERE daire_id = $daire_id");
$toplam_arac = $result->fetch_assoc()['total'] ?? 0;

// Borç listesi
$borc_rows = '';
$sql = "SELECT * FROM borclar WHERE daire_id = $daire_id ORDER BY yil DESC, ay DESC";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $durum_badge = $row['durum'] == 1 ?
        '<span class="status-badge status-paid">Ödendi</span>' :
        '<span class="status-badge status-unpaid">Ödenmedi</span>';
    $borc_rows .= "<tr>
        <td>{$row['yil']}-{$row['ay']}</td>
        <td><span class='badge-1 badge-blue'>{$row['kalem']}</span></td>
        <td><strong>{$row['tutat']} TL</strong></td>
        <td>{$durum_badge}</td>
    </tr>";
}
if ($borc_rows === '') {
    $borc_rows = '<tr><td colspan="4" class="text-center text-muted">Borç bulunamadı.</td></tr>';
}

// Araç listesi
$arac_rows = '';
$sql = "SELECT * FROM araclar WHERE daire_id = $daire_id";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $arac_rows .= "<div class='list-group-item d-flex justify-content-between align-items-center'>
            {$row['plaka']}
            <span class='badge-1 bg-primary rounded-pill'><i class='fas fa-car'></i></span>
        </div>";
    }
} else {
    $arac_rows = "<div class='text-center text-muted'><i class='fas fa-car fa-2x mb-2'></i><p>Kayıtlı aracınız bulunmamaktadır</p></div>";
}

// Kasa bakiyesi (tüm kasa)
$gelir_result = $conn->query("SELECT SUM(miktar) as total FROM kasa WHERE islem_tipi = 'gelir'");
$toplam_gelir = $gelir_result->fetch_assoc()['total'] ?? 0;
$gider_result = $conn->query("SELECT SUM(miktar) as total FROM kasa WHERE islem_tipi = 'gider'");
$toplam_gider = $gider_result->fetch_assoc()['total'] ?? 0;
$bakiye = $toplam_gelir - $toplam_gider;

$sorumlular = $conn->query("SELECT DISTINCT sorumlu FROM gelistirmeler WHERE sorumlu IS NOT NULL AND sorumlu != ''");


function getir($durum, $conn)
{
    return $conn->query("
        SELECT * FROM gelistirmeler 
        WHERE durum='$durum' 
        ORDER BY created_at DESC
    ");
}

// HTML çıktısı
echo '<script>window.kullaniciAdSoyad = "' . strtolower(str_replace(["ç", "Ç", "ğ", "Ğ", "ı", "İ", "ö", "Ö", "ş", "Ş", "ü", "Ü", " "], ["c", "c", "g", "g", "i", "i", "o", "o", "s", "s", "u", "u", ""], trim($_SESSION['ad'] . ' ' . $_SESSION['soyad']))) . '";</script>';
?>
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card stat-card border-left-primary">
            <div class="card-body text-primary">
                <i class="fas fa-money-bill-wave"></i>
                <h5>Toplam Borç</h5>
                <h2><?= number_format($toplam_borc, 2) ?> TL</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card border-left-success">
            <div class="card-body text-success">
                <i class="fas fa-check-circle"></i>
                <h5>Ödenen Tutar</h5>
                <h2><?= number_format($toplam_odenmis, 2) ?> TL</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card border-left-info">
            <div class="card-body text-info">
                <i class="fas fa-car"></i>
                <h5>Kayıtlı Araç</h5>
                <h2><?= $toplam_arac ?></h2>
            </div>
        </div>
    </div>
</div>
<!-- Borç Listesi -->
<div class="row mb-4">

    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list"></i>Borç Listesi
                </h5>
                <div class="d-flex gap-2 filter-container">
                    <div>
                        <input type="radio" class="btn-check" name="borcTarih" id="son10" autocomplete="off" checked>
                        <label class="filter-btn" for="son10">Son 10</label>

                        <input type="radio" class="btn-check" name="borcTarih" id="son3ay" autocomplete="off">
                        <label class="filter-btn" for="son3ay">Son 3 Ay</label>

                        <input type="radio" class="btn-check" name="borcTarih" id="son1yil" autocomplete="off">
                        <label class="filter-btn" for="son1yil">Son 1 Yıl</label>
                    </div>
                    <div class="filter-divider"></div>
                    <div>
                        <input type="radio" class="btn-check" name="borcDurum" id="tumu" autocomplete="off" checked>
                        <label class="filter-btn" for="tumu">Tümü</label>

                        <input type="radio" class="btn-check" name="borcDurum" id="odendi" autocomplete="off">
                        <label class="filter-btn" for="odendi">Ödendi</label>

                        <input type="radio" class="btn-check" name="borcDurum" id="odenmedi" autocomplete="off">
                        <label class="filter-btn" for="odenmedi">Ödenmedi</label>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="" id="borcTable">
                        <thead>
                            <tr>
                                <th>Yıl/Ay</th>
                                <th>Kalem</th>
                                <th>Tutar</th>
                                <th>Durum</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?= $borc_rows ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Araç Bilgileri -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-car"></i>Araç Bilgilerim
                </h5>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <?= $arac_rows ?>
                </div>
            </div>
        </div>
        <!-- Kasa Bilgileri -->
        <div class="card mt-4">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-wallet"></i> Kasa
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-muted">Toplam Bakiye</h6>
                        <h3 class="<?= $bakiye >= 0 ? 'positive' : 'negative' ?>">
                            <?= number_format($bakiye, 2) ?> TL
                        </h3>
                    </div>
                </div>
            </div>
        </div>
        <!-- Hızlı İşlemler -->
        <div class="card mt-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt"></i>Hızlı İşlemler
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button id="borc-indir" class="btn btn-outline-primary">
                        <i class="fas fa-download me-2"></i>Borç Listesini İndir
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-receipt"></i> Site Harcama Listesi
                </h5>
                <div class="d-flex gap-2 filter-container">
                    <input type="radio" class="btn-check-warning" name="harcamaTarih" id="harcamaSon10" autocomplete="off" checked>
                    <label class="filter-btn" for="harcamaSon10">Son 10</label>

                    <input type="radio" class="btn-check-warning" name="harcamaTarih" id="harcamaSon3ay" autocomplete="off">
                    <label class="filter-btn" for="harcamaSon3ay">Son 3 Ay</label>

                    <input type="radio" class="btn-check-warning" name="harcamaTarih" id="harcamaSon1yil" autocomplete="off">
                    <label class="filter-btn" for="harcamaSon1yil">Son 1 Yıl</label>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <div class="table-responsive">
                        <table class="">
                            <thead>
                                <tr>
                                    <th>Tarih</th>
                                    <th>Kategori</th>
                                    <th>Açıklama</th>
                                    <th>Miktar</th>
                                    <th>Belge</th>
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
                                                        <span class='badge-1 badge-blue'>{$row['kategori']}</span>
                                                    </td>
                                                    <td>{$row['aciklama']}</td>
                                                    <td class='text-danger fw-bold'>" . number_format($row['miktar'], 2) . " TL</td>
                                                    <td>{$belge_icon}</td>
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
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-danger text-white" style="cursor:pointer;" data-bs-toggle="collapse" data-bs-target="#aracListesiCollapse" aria-expanded="false" aria-controls="aracListesiCollapse">
                <h5 class="card-title mb-0">
                    <i class="fas fa-receipt"></i> Tüm Araç Listesi
                    <span class="float-end"><i class="fas fa-chevron-down"></i></span>
                </h5>
            </div>
            <div id="aracListesiCollapse" class="collapse show">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <div class="table-responsive">
                            <table class="table-blue">
                                <thead>
                                    <tr>
                                        <th>Plaka</th>
                                        <th>Ad Soyad</th>
                                        <th>Telefon Numarası</th>
                                        <th>Blok</th>
                                        <th>Daire No</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT a.plaka, u.ad, u.soyad, t.telefon, d.blok, d.numara
                                        FROM araclar a
                                        JOIN daireler d ON a.daire_id = d.id
                                        JOIN users u ON d.kullanici_id = u.id
                                        LEFT JOIN daire_telefonlar t ON t.daire_id = d.id
                                        ORDER BY d.blok";

                                    $result = $conn->query($sql);
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>
                                                    <td><span class='badge-1 badge-red'>{$row['plaka']}</span></td>
                                                    <td>{$row['ad']} {$row['soyad']}</td>
                                                    <td>{$row['telefon']}</td>
                                                    <td>{$row['blok']}</td>
                                                    <td>{$row['numara']}</td>
                                                </tr>";
                                        }
                                    } else {
                                        echo "<tr>
                                                <td colspan='4' class='text-center text-muted'>
                                                    <i class='fas fa-car fa-2x mb-2'></i><br>
                                                    Henüz araç kaydı bulunmuyor
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
</div>

<!-- DUYURULAR (Kullanıcıya gösterilecek, modal ana sayfada olacak) -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header mb-0 bg-primary text-white" style="cursor:pointer;" data-bs-toggle="collapse" data-bs-target="#duyuruCollapse" aria-expanded="false" aria-controls="duyuruCollapse">
                <h5 class="card-title"><i class="fas fa-bullhorn"></i>Duyurular <span class="float-end"><i class="fas fa-chevron-down"></i></span></h5>
            </div>
            <div id="duyuruCollapse" class="collapse show">
                <div class="card-body" id="duyuruCardBody">
                    <ul class="list-group" id="duyuruListesi">
                        <?php
                        $duyurular = $conn->query("SELECT * FROM announcements WHERE status=1 ORDER BY modified DESC");
                        if ($duyurular && $duyurular->num_rows > 0) {
                            while ($d = $duyurular->fetch_assoc()) {
                                // Dosya ekleri
                                $dosyalar = [];
                                $dosya_query = $conn->query("SELECT * FROM announcement_dosyalar WHERE announcement_id = " . intval($d['id']));
                                if ($dosya_query && $dosya_query->num_rows > 0) {
                                    while ($f = $dosya_query->fetch_assoc()) {
                                        $dosyalar[] = [
                                            'name' => basename($f['file_path']),
                                            'path' => $f['file_path']
                                        ];
                                    }
                                }
                                $dosyalar_json = htmlspecialchars(json_encode($dosyalar));

                                echo '<li class="list-group-item duyuru-item" style="cursor:pointer;"'
                                    . ' data-id="' . $d['id'] . '"'
                                    . ' data-baslik="' . htmlspecialchars($d['baslik']) . '"'
                                    . ' data-aciklama="' . htmlspecialchars($d['aciklama']) . '"'
                                    . ' data-modified="' . htmlspecialchars(date('d.m.Y H:i', strtotime($d['modified']))) . '"'
                                    . ' data-modified_by="' . htmlspecialchars($d['modified_by']) . '"'
                                    . ' data-image_path="' . (isset($d['image_path']) ? htmlspecialchars($d['image_path']) : '') . '"'
                                    . ' data-dosyalar="' . $dosyalar_json . '"'
                                    . '>'
                                    . '<b>' . htmlspecialchars($d['baslik']) . '</b> '
                                    . '<span class="text-muted" style="font-size:12px;">(' . date('d.m.Y H:i', strtotime($d['modified'])) . ')</span>'
                                    . ((isset($d['image_path']) && !empty($d['image_path'])) ? '<img src="' . htmlspecialchars($d['image_path']) . '" alt="" style="max-width:100px; display:block; margin-top:10px;">' : '')
                                    . '</li>';
                            }
                        } else {
                            echo '<li class="list-group-item text-muted">Henüz duyuru bulunmamaktadır.</li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="search" placeholder="Yıl Giriniz (Örn: 2024)" aria-label="Yıl Giriniz (Örn: 2024)" id="gelistirmeYilFilter" class="form-control mb-3" style="max-width:300px;">

<div class="row g-3">

    <?php
    $columns = [
        'Planlandı' => [
            'icon' => 'fas fa-calendar-alt',
            'class' => 'planlanan',
            'color' => 'bg-warning',
            'text_color' => 'text-dark'
        ],
        'Devam Ediyor' => [
            'icon' => 'fas fa-spinner fa-spin',
            'class' => 'devam',
            'color' => 'bg-info',
            'text_color' => 'text-dark'
        ],
        'Tamamlandı' => [
            'icon' => 'fas fa-check-circle',
            'class' => 'tamamlanan',
            'color' => 'bg-success',
            'text_color' => 'text-white'
        ]
    ];

    foreach ($columns as $durum => $info) {
        $collapseId = 'gelistirmeCollapse_' . strtolower(str_replace(' ', '', $durum));
        echo "<div class='col-12 col-md-4'>";
        echo "<div class='card mb-4 w-100 {$info['class']}'>";
        echo "<div class='card-header {$info['color']} {$info['text_color']}' style='cursor:pointer;' data-bs-toggle='collapse' data-bs-target='#{$collapseId}' aria-expanded='true' aria-controls='{$collapseId}'>
                    <h5 class='card-title mb-0'>
                    <i class='{$info['icon']}'></i> {$durum} <span class='float-end'><i class='fas fa-chevron-down'></i></span></h5>
                </div>";
        echo "<div id='{$collapseId}' class='collapse show'>";
        echo "<div class='p-3'>";
        $result = getir($durum, $conn);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='kanban'>";
                echo "<p class='mb-1'><b>{$row['baslik']}</b></p>";
                echo "<p class='mb-1'>{$row['aciklama']}</p>";
                echo "<p class='mb-1'>
                            <small class='oncelik-badge {$row['oncelik']}'><i class='fas fa-flag me-2'></i>{$row['oncelik']}</small>
                            <small class='text-muted'><i class='far fa-calendar-alt me-2'></i>" . date('d.m.Y', strtotime($row['hedef_tarih'])) . "</small>
                        </p>";
                echo "<p class='mb-1 sorumlu'><i class='fas fa-user me-2'></i>{$row['sorumlu']}</p>";
                // Fotoğraflar
                $imgs = $conn->query("SELECT * FROM gelistirme_dosyalar WHERE gelistirme_id=" . $row['id']);
                if ($imgs && $imgs->num_rows > 0) {
                    echo "<div class='card-attachments mb-2'>";
                    echo "<div class='attachment-grid'>";
                    while ($img = $imgs->fetch_assoc()) {
                        $dosyaYolu = $img['dosya'];
                        if (basename($dosyaYolu) === $dosyaYolu) {
                            $dosyaYolu = 'uploads/gelistirmeler/' . $dosyaYolu;
                        }
                        echo "<div class='thumb-wrapper'><img src='{$dosyaYolu}' class='thumb' data-full='{$dosyaYolu}'></div>";
                    }
                    echo "</div>";
                    echo "</div>";
                }
                echo "</div>";
            }
        } else {
            echo "<li class='list-group-item'>Henüz geliştirme bulunmamaktadır.</li>";
        }
        echo "</div>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }
    ?>
</div>
<!-- Modal -->
<!-- Tek bir modal -->
<div class="modal fade" id="duyuruModal" tabindex="-1" aria-labelledby="duyuruModalBaslik" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content" style="background-color: #eeeeeeff;">
            <div class="modal-header">
                <h6 class="mb-0"><i class="fas fa-bullhorn me-2"></i>Duyuru</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>

            <div class="modal-body p-3 bg-white">
                <div class="mb-2">
                    <small class="text-muted"><i class="fas fa-building me-1"></i>Site Yönetim ERP</small>
                </div>
                <h5 class="modal-title mb-3" id="duyuruModalBaslik"></h5>
                <p id="duyuruModalAciklama" style="word-break: break-word;"></p>
                <div class="mb-2">
                    <small class="text-muted d-block" id="duyuruModalmodified"></small>
                    <small class="text-muted d-block" id="duyuruModalmodifiedBy"></small>
                </div>
                <img id="duyuruModalImage" src="" alt="" class="img-fluid rounded mb-2" style="display:none; max-height:300px; object-fit:cover;">
                <div id="duyuruModalFiles" class="mt-2"></div>
            </div>
        </div>
    </div>
</div>