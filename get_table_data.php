

<?php

include 'config.php';
include_once 'helpers.php';

checkAuth();

$type = $_POST['type'] ?? '';

// Sadece borç tablosu ve duyuru detay için admin kontrolü yapılmasın
if ($type !== 'stats' && $type !== 'duyuru_detay') {
    checkAdmin();
}

// --- DAİRE TABLOSU ---
// --- MEVCUT 'daireler' BLOĞUNU BUNUNLA GÜNCELLE ---
if ($type == 'daireler') {
    // Telefon sayısını da çekelim (COUNT)
    $sql = "SELECT d.*, u.ad, u.soyad, 
            (SELECT COUNT(*) FROM daire_telefonlar WHERE daire_id = d.id) as tel_sayisi 
            FROM daireler d 
            LEFT JOIN users u ON d.kullanici_id = u.id 
            ORDER BY d.blok ASC, d.numara ASC";

    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $kullanici = $row['ad'] ? $row['ad'] . ' ' . $row['soyad'] : '<span class="text-muted">Boş</span>';
        $daire_bilgi = "Blok {$row['blok']} No {$row['numara']}";
        $kullanici_bilgi = $row['ad'] ? "<br><small class='text-primary'>({$row['ad']} {$row['soyad']})</small>" : "";


        // Telefon varsa buton yeşil, yoksa gri olsun
        $tel_btn_class = $row['tel_sayisi'] > 0 ? 'btn-success' : 'btn-secondary';

        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['blok']}</td>
            <td>{$row['kat']}</td>
            <td>{$row['numara']}</td>
            <td>{$kullanici}</td>
            <td>
                <div class='btn-group'>
                    <button class='btn btn-sm {$tel_btn_class} text-white btn-telefon-yonet' 
                            data-id='{$row['id']}' 
                            data-info='{$daire_bilgi}' 
                            title='Telefon Numaraları ({$row['tel_sayisi']})'>
                        <i class='fas fa-phone-alt'></i> 
                        <span class='badge bg-white text-dark ms-1' style='font-size:0.6rem;'>{$row['tel_sayisi']}</span>
                    </button>

                    <button class='btn btn-sm btn-info text-white btn-arac-ekle-modal' 
                            data-id='{$row['id']}' data-info='{$daire_bilgi}' title='Araç Ekle'>
                        <i class='fas fa-car'></i>
                    </button>
                    
                    <button class='btn btn-sm btn-warning btn-daire-duzenle-modal' 
                            data-id='{$row['id']}' data-blok='{$row['blok']}' data-kat='{$row['kat']}' 
                            data-numara='{$row['numara']}' data-kullanici='{$row['kullanici_id']}' title='Düzenle'>
                        <i class='fas fa-edit'></i>
                    </button>
                    
                    <button class='btn btn-sm btn-danger btn-sil' data-type='daire' data-id='{$row['id']}' title='Sil'>
                        <i class='fas fa-trash'></i>
                    </button>
                </div>
            </td>
        </tr>";
    }
}

//Telefon Listesi (Modal İçin) ---
if ($type == 'telefon_listesi') {
    $daire_id = intval($_POST['daire_id']);
    $sql = "SELECT * FROM daire_telefonlar WHERE daire_id = $daire_id ORDER BY id DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<ul class="list-group list-group-flush">';
        while ($row = $result->fetch_assoc()) {
            echo '<li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-user-circle text-muted me-2"></i>
                        <strong>' . $row['ad_soyad'] . '</strong>
                        <br>
                        <small class="text-muted ms-4"><i class="fas fa-mobile-alt me-1"></i>' . $row['telefon'] . '</small>
                    </div>
                    <button class="btn btn-sm btn-outline-danger btn-telefon-sil" data-id="' . $row['id'] . '" title="Sil">
                        <i class="fas fa-times"></i>
                    </button>
                  </li>';
        }
        echo '</ul>';
    } else {
        echo '<div class="alert alert-warning py-2"><small>Kayıtlı numara bulunamadı.</small></div>';
    }
}

// --- BORÇ TABLOSU ---
if ($type == 'borclar') {
    $sql = "SELECT b.*, d.blok, d.numara, u.ad, u.soyad FROM borclar b JOIN daireler d ON b.daire_id = d.id LEFT JOIN users u ON d.kullanici_id = u.id ORDER BY b.id"; // Son 100 kayıt
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $kullanici = $row['ad'] ? $row['ad'] . ' ' . $row['soyad'] : '<span class="text-muted">Boş</span>';
        $durum = $row['durum'] == 1 ? '<span class="badge bg-success badge-success">Ödendi</span>' : '<span class="badge bg-danger">Ödenmedi</span>';
        $btn = $row['durum'] == 0 ? "<button class='btn btn-sm btn-success btn-ode' data-id='{$row['id']}' title='Öde'><i class='fas fa-check'></i></button>" : "";

        echo "<tr>
            <td>Blok {$row['blok']} D:{$row['numara']}</td>
            <td>{$kullanici}</td>
            <td>{$row['ay']}/{$row['yil']}</td>
            <td>{$row['kalem']}</td>
            <td>{$row['tutat']} TL</td>
            <td>{$durum}</td>
            <td>
                {$btn}
                <button class='btn btn-sm btn-danger btn-sil' data-type='borc' data-id='{$row['id']}'><i class='fas fa-trash'></i></button>
            </td>
        </tr>";
    }
}

// --- DAİRE SELECT OPTIONS (SELECT2 İÇİN) ---
if ($type == 'daire_options') {
    $sql = "SELECT * FROM daireler ORDER BY blok ASC, numara ASC";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        echo "<option value='{$row['id']}'>Blok {$row['blok']} - Daire {$row['numara']}</option>";
    }
}

// --- KULLANICI TABLOSU ---
if ($type == 'kullanicilar') {
    $sql = "SELECT * FROM users WHERE rol='kullanici' ORDER BY id DESC";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['ad']} {$row['soyad']}</td>
            <td>{$row['email']}</td>
            <td>{$row['rol']}</td>
        </tr>";
    }
}

// --- ARAÇ TABLOSU ---
if ($type == 'araclar') {
    $sql = "SELECT a.*, d.blok, d.numara, u.ad, u.soyad FROM araclar a JOIN daireler d ON a.daire_id=d.id LEFT JOIN users u ON d.kullanici_id=u.id ORDER BY a.id DESC";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $sahip = $row['ad'] ? $row['ad'] . ' ' . $row['soyad'] : '-';
        echo "<tr>
            <td><strong>{$row['plaka']}</strong></td>
            <td>{$row['blok']} / {$row['numara']}</td>
            <td>{$sahip}</td>
            <td><button class='btn btn-sm btn-danger btn-sil' data-type='arac' data-id='{$row['id']}'><i class='fas fa-trash'></i></button></td>
        </tr>";
    }
}

// --- DASHBOARD İSTATİSTİKLERİ ---
if ($type == 'stats') {
    $d = $conn->query("SELECT COUNT(*) as c FROM daireler")->fetch_assoc()['c'];
    $k = $conn->query("SELECT COUNT(*) as c FROM users WHERE rol='kullanici'")->fetch_assoc()['c'];
    $b = $conn->query("SELECT SUM(tutat) as t FROM borclar WHERE durum=0")->fetch_assoc()['t'] ?? 0;

    // Kasa Bakiyesi
    $g = $conn->query("SELECT SUM(miktar) as t FROM kasa WHERE islem_tipi='gelir'")->fetch_assoc()['t'] ?? 0;
    $gid = $conn->query("SELECT SUM(miktar) as t FROM kasa WHERE islem_tipi='gider'")->fetch_assoc()['t'] ?? 0;
    $kasa = $g - $gid;

    echo '
    <div class="row">
        <div class="col-md-3 mb-3"><div class="card p-3 text-center border-primary"><h3 class="text-primary">' . $d . '</h3><small>Toplam Daire</small></div></div>
        <div class="col-md-3 mb-3"><div class="card p-3 text-center border-success"><h3 class="text-success">' . $k . '</h3><small>Kullanıcı</small></div></div>
        <div class="col-md-3 mb-3"><div class="card p-3 text-center border-danger"><h3 class="text-danger">' . number_format($b, 2) . ' TL</h3><small>Ödenmemiş Borç</small></div></div>
        <div class="col-md-3 mb-3"><div class="card p-3 text-center border-info"><h3 class="text-info">' . number_format($kasa, 2) . ' TL</h3><small>Kasa Bakiyesi</small></div></div>
    </div>';
}

// --- DAİRE GÜNCELLEME ---
if ($type == 'daire_guncelle') {
    $id = intval($_POST['daire_id']);
    $blok = $conn->real_escape_string($_POST['blok']);
    $kat = intval($_POST['kat']);
    $numara = intval($_POST['numara']);
    $kullanici_id = intval($_POST['kullanici_id']);

    // Kullanıcı ID 0 geldiyse NULL yapacağız (SQL için)
    $kullanici_sql_val = ($kullanici_id > 0) ? $kullanici_id : "NULL";

    // Kontrol: Eğer kullanıcı seçildiyse, bu kullanıcı BAŞKA bir dairede oturuyor mu?
    if ($kullanici_id > 0) {
        $check = $conn->query("SELECT id FROM daireler WHERE kullanici_id = $kullanici_id AND id != $id");
        if ($check->num_rows > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Bu kullanıcı zaten başka bir daireye atanmış!']);
            exit;
        }
    }

    $sql = "UPDATE daireler SET blok='$blok', kat=$kat, numara=$numara, kullanici_id=$kullanici_sql_val WHERE id=$id";

    if ($conn->query($sql)) {
        echo json_encode(['status' => 'success', 'message' => 'Daire bilgileri güncellendi.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Güncelleme başarısız.']);
    }
    exit;
}

// --- GELİŞTİRMELER TABLOSU ---
if ($type == 'gelistirmeler') {
    $sql = "SELECT * FROM gelistirmeler ORDER BY oncelik ASC, id DESC";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        // Dosyaları çek
        $dosyalar = [];
        $gelistirme_id = intval($row['id']);
        $sql_dosya = "SELECT * FROM gelistirme_dosyalar WHERE gelistirme_id = $gelistirme_id";
        $result_dosya = $conn->query($sql_dosya);
        if ($result_dosya && $result_dosya->num_rows > 0) {
            while ($dosya = $result_dosya->fetch_assoc()) {
                $dosya_path = htmlspecialchars(get_gelistirme_file_path($dosya['dosya']), ENT_QUOTES);
                $dosya_adi = basename($dosya['dosya']);
                $dosyalar[] = "<a href='{$dosya_path}' target='_blank'>{$dosya_adi}</a>";
            }
        }
        $dosya_html = empty($dosyalar) ? '-' : implode('<br>', $dosyalar);

        // Durum metni
        $durum_text = '';
        if ($row['durum'] === 'Planlandı' || $row['durum'] === 0 || $row['durum'] === '0') {
            $durum_text = 'Planlandı';
        } elseif ($row['durum'] === 'Devam Ediyor' || $row['durum'] === 1 || $row['durum'] === '1') {
            $durum_text = 'Devam Ediyor';
        } elseif ($row['durum'] === 'Tamamlandı' || $row['durum'] === 2 || $row['durum'] === '2') {
            $durum_text = 'Tamamlandı';
        } else {
            $durum_text = $row['durum'];
        }

        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['baslik']}</td>
            <td>{$row['aciklama']}</td>
            <td>{$durum_text}</td>
            <td>{$row['oncelik']}</td>
            <td>{$row['sorumlu']}</td>
            <td>" . date('d.m.Y', strtotime($row['hedef_tarih'])) . "</td>
            <td>" . date('d.m.Y', strtotime($row['created_at'])) . "</td>
            <td>{$dosya_html}</td>
            <td>
                <button class='btn btn-sm btn-warning btn-gelistirme-duzenle-modal' 
                    data-id='{$row['id']}'
                    data-baslik='" . htmlspecialchars($row['baslik'], ENT_QUOTES) . "'
                    data-aciklama='" . htmlspecialchars($row['aciklama'], ENT_QUOTES) . "'
                    data-durum='{$row['durum']}'
                    data-oncelik='{$row['oncelik']}'
                    data-sorumlu='" . htmlspecialchars($row['sorumlu'], ENT_QUOTES) . "'
                    data-hedef_tarih='{$row['hedef_tarih']}'
                    title='Düzenle'>
                    <i class='fas fa-edit'></i>
                </button>
                <button class='btn btn-danger btn-sm btn-sil' data-type='gelistirme' data-id='{$row['id']}'>
                    <i class='fas fa-trash'></i>
                </button>
            </td>
        </tr>";
    }
}

// --- GELİŞTİRME EKLEME ---
if ($type == 'gelistirme_ekle') {
    $baslik = $conn->real_escape_string($_POST['baslik']);
    $aciklama = $conn->real_escape_string($_POST['aciklama']);
    $durum = intval($_POST['durum']);
    $oncelik = intval($_POST['oncelik']);
    $sorumlu = $conn->real_escape_string($_POST['sorumlu']);
    $hedef_tarih = $conn->real_escape_string($_POST['hedef_tarih']);

    $sql = "INSERT INTO gelistirmeler (baslik, aciklama, durum, oncelik, sorumlu, hedef_tarih, created_at) 
            VALUES ('$baslik', '$aciklama', $durum, $oncelik, '$sorumlu', '$hedef_tarih', NOW())";

    if ($conn->query($sql)) {
        $gelistirme_id = $conn->insert_id;


        // Dosyalar varsa ekle (helpers.php fonksiyonu ile)
        $uploaded_files = upload_files('dosyalar', 'uploads/gelistirmeler/');
        foreach ($uploaded_files as $fname) {
            $conn->query("INSERT INTO gelistirme_dosyalar (gelistirme_id, dosya) VALUES ($gelistirme_id, '$fname')");
        }

        echo json_encode(['status' => 'success', 'message' => 'Geliştirme eklendi.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Ekleme başarısız.']);
    }
    exit;
}
// --- GELİŞTİRME GÜNCELLEME ---
if ($type == 'gelistirme_guncelle') {
    $id = intval($_POST['gelistirme_id']);
    $baslik = $conn->real_escape_string($_POST['baslik']);
    $aciklama = $conn->real_escape_string($_POST['aciklama']);
    $durum = intval($_POST['durum']);
    $oncelik = intval($_POST['oncelik']);
    $sorumlu = $conn->real_escape_string($_POST['sorumlu']);
    $hedef_tarih = $conn->real_escape_string($_POST['hedef_tarih']);

    $sql = "UPDATE gelistirmeler SET 
            baslik='$baslik', 
            aciklama='$aciklama', 
            durum=$durum, 
            oncelik=$oncelik, 
            sorumlu='$sorumlu', 
            hedef_tarih='$hedef_tarih' 
            WHERE id=$id";

    if ($conn->query($sql)) {
        // Dosyalar varsa ekle (admin_actions2.php fonksiyonu ile)
        if (function_exists('upload_gelistirme_files')) {
            upload_gelistirme_files($id, $conn);
        }
        echo json_encode(['status' => 'success', 'message' => 'Geliştirme güncellendi.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Güncelleme başarısız.']);
    }
    exit;
}
// --- GELİŞTİRME DETAY (Düzenle modalı için dosya listesi) ---
if ($type == 'gelistirme_detay' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $gelistirme = $conn->query("SELECT * FROM gelistirmeler WHERE id = $id LIMIT 1");
    if ($gelistirme && $gelistirme->num_rows > 0) {
        $g = $gelistirme->fetch_assoc();
        $dosyalar = [];
        $dosya_sorgu = $conn->query("SELECT id, dosya FROM gelistirme_dosyalar WHERE gelistirme_id = $id");
        if ($dosya_sorgu && $dosya_sorgu->num_rows > 0) {
            include_once 'helpers.php';
            while ($f = $dosya_sorgu->fetch_assoc()) {
                $dosyalar[] = [
                    'id' => $f['id'],
                    'file_name' => basename($f['dosya']),
                    'file_path' => get_gelistirme_file_path($f['dosya'])
                ];
            }
        }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'baslik' => $g['baslik'],
            'aciklama' => $g['aciklama'],
            'dosyalar' => $dosyalar
        ]);
    } else {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => 'Geliştirme bulunamadı.']);
    }
    exit;
}


// --- Duyurular Tablosu ---
if ($type == 'duyurular') {
    $sql = "SELECT * FROM announcements ORDER BY id DESC";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        // Dosyaları çek
        $dosyalar = [];
        $announcement_id = intval($row['id']);
        $sql_dosya = "SELECT * FROM announcement_dosyalar WHERE announcement_id = $announcement_id";
        $result_dosya = $conn->query($sql_dosya);
        if ($result_dosya && $result_dosya->num_rows > 0) {
            while ($dosya = $result_dosya->fetch_assoc()) {
                $file_path = htmlspecialchars($dosya['file_path'], ENT_QUOTES);
                $file_name = htmlspecialchars(basename($dosya['file_path']), ENT_QUOTES);
                $dosyalar[] = "<a href='{$file_path}' target='_blank'>{$file_name}</a>";
            }
        }
        $dosya_html = empty($dosyalar) ? '-' : implode('<br>', $dosyalar);

        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['baslik']}</td>
            <td>{$row['aciklama']}</td>    
            <td>" . date('d.m.Y H:i', strtotime($row['modified'])) . "</td>
            <td>{$row['modified_by']}</td>
            <td>{$row['status']}</td>
            <td>{$dosya_html}</td>
            <td>
                <button class='btn btn-sm btn-warning btn-duyuru-duzenle' 
                    data-id='{$row['id']}'
                    data-baslik='" . htmlspecialchars($row['baslik'], ENT_QUOTES) . "'
                    data-aciklama='" . htmlspecialchars($row['aciklama'], ENT_QUOTES) . "'
                    data-status='{$row['status']}'
                    data-modified_by='" . htmlspecialchars($row['modified_by'], ENT_QUOTES) . "'
                    title='Düzenle'>
                    <i class='fas fa-edit'></i>
                </button>
                <button class='btn btn-sm btn-danger btn-sil' data-type='duyuru' data-id='{$row['id']}' title='Sil'>
                    <i class='fas fa-trash'></i>
                </button>
            </td>
        </tr>";
    }
}

// --- DUYURU DETAY (KULLANICI MODALI İÇİN) ---
if ($type == 'duyuru_detay' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $duyuru = $conn->query("SELECT * FROM announcements WHERE id = $id LIMIT 1");
    if ($duyuru && $duyuru->num_rows > 0) {
        $d = $duyuru->fetch_assoc();
        $dosyalar = [];
        $dosya_sorgu = $conn->query("SELECT id, file_path FROM announcement_dosyalar WHERE announcement_id = $id");
        if ($dosya_sorgu && $dosya_sorgu->num_rows > 0) {
            include_once 'helpers.php';
            while ($f = $dosya_sorgu->fetch_assoc()) {
                $dosyalar[] = [
                    'id' => $f['id'],
                    'file_name' => basename($f['file_path']),
                    'file_path' => get_announcement_file_path($f['file_path'])
                ];
            }
        }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'baslik' => $d['baslik'],
            'aciklama' => $d['aciklama'],
            'modified' => date('d.m.Y H:i', strtotime($d['modified'])),
            'modified_by' => $d['modified_by'],
            'image_path' => isset($d['image_path']) ? $d['image_path'] : '',
            'dosyalar' => $dosyalar
        ]);
    } else {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => 'Duyuru bulunamadı.']);
    }
    exit;
}
