
<?php
// Hata ayıklama için hata raporlamayı aç
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'config.php';


// Oturum kontrolü (AJAX isteği olsa bile)
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'error', 'message' => 'Yetkisiz erişim!']);
    exit;
}

$action = $_POST['type'] ?? '';

// --- BORÇ EKLEME (TOPLU VE KONTROLLÜ) ---
if ($action == 'borc_ekle') {
    $daire_ids = $_POST['daire_ids'] ?? []; // Array gelecek
    $yil = intval($_POST['yil']);
    $ay = intval($_POST['ay']);
    $kalem = $conn->real_escape_string($_POST['kalem']);
    $tutar = floatval($_POST['tutar']);

    if (empty($daire_ids) || empty($kalem) || $tutar <= 0) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'error', 'message' => 'Lütfen tüm alanları doldurun ve en az bir daire seçin.']);
        exit;
    }

    $eklenen = 0;
    $hatali = 0;
    $mukerrer = 0;

    foreach ($daire_ids as $daire_id) {
        $daire_id = intval($daire_id);

        // 1. Mükerrer Kontrol (Aynı yıl, aynı ay, aynı kalem, aynı daire) - 12. ay hariç
        if ($ay != 12) {
            $check_sql = "SELECT id FROM borclar WHERE daire_id = $daire_id AND yil = $yil AND ay = $ay AND kalem = '$kalem'";
            $check_result = $conn->query($check_sql);
            if ($check_result->num_rows > 0) {
                $mukerrer++;
                continue; // Bu daireyi atla
            }
        }

        // 2. Ekleme
        $sql = "INSERT INTO borclar (daire_id, yil, ay, kalem, tutat, durum) VALUES ($daire_id, $yil, $ay, '$kalem', $tutar, 0)";
        if ($conn->query($sql)) {
            $eklenen++;
        } else {
            $hatali++;
        }
    }

    $msg = "$eklenen kayıt başarıyla eklendi.";
    if ($mukerrer > 0) $msg .= " ($mukerrer adet mükerrer kayıt atlandı)";
    if ($hatali > 0) $msg .= " ($hatali adet hata)";

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'success', 'message' => $msg]);
    exit;
}

// --- BORÇ ÖDEME VE KASA GİRİŞİ ---
if ($action == 'borc_ode') {
    $borc_id = intval($_POST['id']);

    // Borç bilgilerini al
    $sql_borc = "SELECT b.*, d.blok, d.numara FROM borclar b JOIN daireler d ON b.daire_id = d.id WHERE b.id = $borc_id AND b.durum = 0";
    $result_borc = $conn->query($sql_borc);

    if ($result_borc->num_rows > 0) {
        $borc = $result_borc->fetch_assoc();
        $tutar = $borc['tutat'];
        $aciklama = "Aidat Tahsilatı: Blok {$borc['blok']} Daire {$borc['numara']} ({$borc['ay']}/{$borc['yil']} - {$borc['kalem']})";
        $tarih = date('Y-m-d');

        // Transaction (İşlem bütünlüğü) başlat
        $conn->begin_transaction();

        try {
            // 1. Borcu güncelle
            $conn->query("UPDATE borclar SET durum = 1, odeme_tarihi = '$tarih' WHERE id = $borc_id");

            // 2. Kasaya işle
            $sql_kasa = "INSERT INTO kasa (islem_tipi, miktar, aciklama, tarih) VALUES ('gelir', $tutar, '$aciklama', '$tarih')";
            $conn->query($sql_kasa);

            $conn->commit();
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['status' => 'success', 'message' => 'Borç ödendi ve kasaya işlendi.']);
        } catch (Exception $e) {
            $conn->rollback();
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['status' => 'error', 'message' => 'İşlem sırasında hata oluştu: ' . $e->getMessage()]);
        }
    } else {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'error', 'message' => 'Borç bulunamadı veya zaten ödenmiş.']);
    }
    exit;
}

// --- DAİRE EKLEME ---
if ($action == 'daire_ekle') {
    $blok = $conn->real_escape_string($_POST['blok']);
    $kat = intval($_POST['kat']);
    $numara = intval($_POST['numara']);

    $sql = "INSERT INTO daireler (blok, kat, numara) VALUES ('$blok', $kat, $numara)";
    if ($conn->query($sql)) {
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'success', 'message' => 'Daire eklendi.']);
    } else {
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası.']);
    }
    exit;
}

// --- SİLME İŞLEMLERİ ---
if (strpos($action, '_sil') !== false) {
    $id = intval($_POST['id']);
    $table = '';

    if ($action == 'borc_sil') $table = 'borclar';
    if ($action == 'daire_sil') $table = 'daireler';
    if ($action == 'arac_sil') $table = 'araclar';
    if ($action == 'duyuru_sil') $table = 'announcements';
    if ($action == 'gelistirme_sil') $table = 'gelistirmeler';

    if ($action == 'gelistirme_sil') {
        // Geliştirmeye bağlı dosyaları sil (gelistirme_dosyalar tablosu ve diskten)
        $dosya_sorgu = $conn->query("SELECT dosya FROM gelistirme_dosyalar WHERE gelistirme_id = $id");
        while ($d = $dosya_sorgu->fetch_assoc()) {
            $dosya_path = 'uploads/gelistirmeler/' . $d['dosya'];
            if (file_exists($dosya_path)) @unlink($dosya_path);
        }
        $conn->query("DELETE FROM gelistirme_dosyalar WHERE gelistirme_id = $id");
    }

    if ($action == 'duyuru_sil') {
        // Duyuruya bağlı dosyaları sil (announcement_dosyalar tablosu ve diskten)
        $dosya_sorgu = $conn->query("SELECT file_path FROM announcement_dosyalar WHERE announcement_id = $id");
        while ($d = $dosya_sorgu->fetch_assoc()) {
            $dosya_path = $d['file_path'];
            if (file_exists($dosya_path)) @unlink($dosya_path);
        }
        $conn->query("DELETE FROM announcement_dosyalar WHERE announcement_id = $id");
    }

    if ($table) {
        // Daire silinirse bağlı veriler temizlenmeli
        if ($table == 'daireler') {
            $conn->query("DELETE FROM borclar WHERE daire_id = $id");
            $conn->query("DELETE FROM araclar WHERE daire_id = $id");
        }

        $sql = "DELETE FROM $table WHERE id = $id";
        if ($conn->query($sql)) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['status' => 'success', 'message' => 'Kayıt silindi.']);
        } else {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['status' => 'error', 'message' => 'Silme başarısız.']);
        }
    }
    exit;
}

// --- KULLANICI EKLEME ---
if ($action == 'kullanici_ekle') {
    $ad = $conn->real_escape_string($_POST['ad']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['sifre'], PASSWORD_DEFAULT);

    // Email kontrol
    $check = $conn->query("SELECT id FROM users WHERE email='$email'");
    if ($check->num_rows > 0) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'error', 'message' => 'Bu e-posta zaten kayıtlı.']);
        exit;
    }

    $sql = "INSERT INTO users (ad, soyad, email, sifre_hash, rol) VALUES ('$ad', '{$_POST['soyad']}', '$email', '$password', 'kullanici')";
    if ($conn->query($sql)) {
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'success', 'message' => 'Kullanıcı eklendi.']);
    } else {
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'error', 'message' => 'Hata oluştu.']);
    }
    exit;
}

// --- ARAÇ EKLEME ---
if ($action == 'arac_ekle') {
    $daire_id = intval($_POST['daire_id']);
    $plaka = strtoupper(trim($conn->real_escape_string($_POST['plaka']))); // Plakayı büyük harf yap ve boşlukları temizle

    if ($daire_id <= 0 || empty($plaka)) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'error', 'message' => 'Lütfen daire seçin ve plaka girin.']);
        exit;
    }

    // Aynı plaka var mı kontrolü
    $check = $conn->query("SELECT id FROM araclar WHERE plaka = '$plaka'");
    if ($check->num_rows > 0) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'error', 'message' => 'Bu plaka zaten sistemde kayıtlı!']);
        exit;
    }

    $sql = "INSERT INTO araclar (daire_id, plaka) VALUES ($daire_id, '$plaka')";

    if ($conn->query($sql)) {
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'success', 'message' => 'Araç başarıyla eklendi.']);
    } else {
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası oluştu.']);
    }
    exit;
}

// Daire Bilgilerini Güncelleme (Blok, Kat, No veya Kullanıcı Atama)
if ($action == 'daire_guncelle') {
    $daire_id = intval($_POST['daire_id']);
    $blok = $conn->real_escape_string($_POST['blok']);
    $kat = intval($_POST['kat']);
    $numara = intval($_POST['numara']);
    $kullanici_id = intval($_POST['kullanici_id']) > 0 ? intval($_POST['kullanici_id']) : 'NULL';

    $sql = "UPDATE daireler SET blok = '$blok', kat = $kat, numara = $numara, kullanici_id = $kullanici_id WHERE id = $daire_id";

    if ($conn->query($sql)) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'success', 'message' => 'Daire güncellendi.']);
        exit;
    } else {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'error', 'message' => 'Daire güncellenemedi.']);
        exit;
    }
    exit();
}

// --- TELEFON EKLEME ---
if ($action == 'telefon_ekle') {
    $daire_id = intval($_POST['daire_id']);
    $ad_soyad = $conn->real_escape_string($_POST['ad_soyad']);
    $telefon = $conn->real_escape_string($_POST['telefon']);

    if ($daire_id <= 0 || empty($telefon)) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'error', 'message' => 'Lütfen tüm alanları doldurun.']);
        exit;
    }

    $sql = "INSERT INTO daire_telefonlar (daire_id, ad_soyad, telefon) VALUES ($daire_id, '$ad_soyad', '$telefon')";

    if ($conn->query($sql)) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'success', 'message' => 'Numara eklendi.']);
    } else {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası.']);
    }
    exit;
}

// --- TELEFON SİLME ---
if ($action == 'telefon_sil') {
    $id = intval($_POST['id']);
    $sql = "DELETE FROM daire_telefonlar WHERE id = $id";

    if ($conn->query($sql)) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'success', 'message' => 'Numara silindi.']);
    } else {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'error', 'message' => 'Hata oluştu.']);
    }
    exit;
}

// --- DUYURU EKLEME ---
if ($action == 'duyuru_ekle') {
    $baslik = $conn->real_escape_string($_POST['baslik']);
    $aciklama = $conn->real_escape_string($_POST['aciklama']);
    $status = intval($_POST['status']);
    $modified_by = $_SESSION['ad'] . ' ' . $_SESSION['soyad'];

    $sql = "INSERT INTO announcements (baslik, aciklama, status, modified_by) VALUES ('$baslik', '$aciklama', $status, '$modified_by')";
    if ($conn->query($sql)) {
        $announcement_id = $conn->insert_id;

        // Dosya yükleme işlemi
        if (!empty($_FILES['files']['name'][0])) {
            $upload_dir = 'uploads/announcements/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            foreach ($_FILES['files']['tmp_name'] as $index => $tmp_name) {
                $file_name = basename($_FILES['files']['name'][$index]);
                $file_path = $upload_dir . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file_name);
                if (move_uploaded_file($tmp_name, $file_path)) {
                    $file_path_sql = $conn->real_escape_string($file_path);
                    $conn->query("INSERT INTO announcement_dosyalar (announcement_id, file_path) VALUES ($announcement_id, '$file_path_sql')");
                }
            }
        }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'success', 'message' => 'Duyuru başarıyla eklendi.']);
    } else {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'error', 'message' => 'Duyuru eklenemedi.']);
    }
    exit;
}


// --- DUYURU GÜNCELLEME ---
if ($action == 'duyuru_guncelle') {
    $id = intval($_POST['id']);
    $baslik = $conn->real_escape_string($_POST['baslik']);
    $aciklama = $conn->real_escape_string($_POST['aciklama']);
    $status = intval($_POST['status']);
    $modified_by = $_SESSION['ad'] . ' ' . $_SESSION['soyad'];
    $sql = "UPDATE announcements SET baslik='$baslik', aciklama='$aciklama', status=$status, modified_by='$modified_by', modified=NOW() WHERE id=$id";
    $success = $conn->query($sql);

    // 1. Mevcut dosyaları sil (hem veritabanı hem diskten)
    if (isset($_POST['dosya_sil']) && is_array($_POST['dosya_sil'])) {
        foreach ($_POST['dosya_sil'] as $dosya_id) {
            $dosya_id = intval($dosya_id);
            $q = $conn->query("SELECT file_path FROM announcement_dosyalar WHERE id = $dosya_id AND announcement_id = $id");
            if ($q && $row = $q->fetch_assoc()) {
                $file_path = $row['file_path'];
                if ($file_path && file_exists($file_path)) @unlink($file_path);
            }
            $conn->query("DELETE FROM announcement_dosyalar WHERE id = $dosya_id AND announcement_id = $id");
        }
    }

    // 2. Yeni dosya ekleme
    if (!empty($_FILES['files']['name'][0])) {
        $upload_dir = 'uploads/announcements/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        foreach ($_FILES['files']['tmp_name'] as $index => $tmp_name) {
            if ($_FILES['files']['error'][$index] == 0) {
                $file_name = basename($_FILES['files']['name'][$index]);
                $file_path = $upload_dir . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file_name);
                if (move_uploaded_file($tmp_name, $file_path)) {
                    $file_path_sql = $conn->real_escape_string($file_path);
                    $conn->query("INSERT INTO announcement_dosyalar (announcement_id, file_path) VALUES ($id, '$file_path_sql')");
                }
            }
        }
    }

    if ($success) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'success', 'message' => 'Duyuru güncellendi.']);
    } else {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'error', 'message' => 'Güncelleme başarısız.']);
    }
    exit;
}

// --- GELİŞTİRME DOSYA YÜKLEME FONKSİYONU ---
function upload_gelistirme_files($id, $conn)
{
    if (!empty($_FILES['dosyalar']['name'][0])) {
        $allowed_ext = array('jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx');
        $upload_dir = 'uploads/gelistirmeler/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        foreach ($_FILES['dosyalar']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['dosyalar']['error'][$key] == 0) {
                $file_name = basename($_FILES['dosyalar']['name'][$key]);
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                if (in_array($file_ext, $allowed_ext)) {
                    $new_name = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file_name);
                    $upload_path = $upload_dir . $new_name;
                    if (move_uploaded_file($tmp_name, $upload_path)) {
                        $conn->query("INSERT INTO gelistirme_dosyalar (gelistirme_id, dosya) VALUES ($id, '$new_name')");
                    }
                }
            }
        }
    }
}

if ($action == 'gelistirme_ekle') {
    $baslik = $conn->real_escape_string($_POST['baslik']);
    $aciklama = $conn->real_escape_string($_POST['aciklama']);
    $durum = $conn->real_escape_string($_POST['durum']); // enum: Planlandı, Devam Ediyor, Tamamlandı
    $oncelik = $conn->real_escape_string($_POST['oncelik']); // enum: Düşük, Orta, Yüksek
    $sorumlu = $conn->real_escape_string($_POST['sorumlu']);
    $hedef_tarih = $conn->real_escape_string($_POST['hedef_tarih']);

    $sql = "INSERT INTO gelistirmeler (baslik, aciklama, durum, oncelik, sorumlu, hedef_tarih, created_at) VALUES ('$baslik', '$aciklama', '$durum', '$oncelik', '$sorumlu', '$hedef_tarih', NOW())";
    if ($conn->query($sql)) {
        $gelistirme_id = $conn->insert_id;
        upload_gelistirme_files($gelistirme_id, $conn);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'success', 'message' => 'Geliştirme başarıyla eklendi.']);
    } else {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'error', 'message' => 'Geliştirme eklenemedi.']);
    }
    exit;
}


// --- GELİŞTİRME GÜNCELLEME ---
if ($action == 'gelistirme_guncelle') {
    $id = intval($_POST['gelistirme_id']);
    $baslik = $conn->real_escape_string($_POST['baslik']);
    $aciklama = $conn->real_escape_string($_POST['aciklama']);
    $durum = $conn->real_escape_string($_POST['durum']);
    $oncelik = $conn->real_escape_string($_POST['oncelik']);
    $sorumlu = $conn->real_escape_string($_POST['sorumlu']);
    $hedef_tarih = $conn->real_escape_string($_POST['hedef_tarih']);

    $sql = "UPDATE gelistirmeler SET baslik='$baslik', aciklama='$aciklama', durum='$durum', oncelik='$oncelik', sorumlu='$sorumlu', hedef_tarih='$hedef_tarih' WHERE id=$id";
    if ($conn->query($sql)) {
        // 1. Seçili dosyaları sil (hem veritabanı hem diskten)
        if (isset($_POST['dosya_sil']) && is_array($_POST['dosya_sil'])) {
            foreach ($_POST['dosya_sil'] as $dosya_id) {
                $dosya_id = intval($dosya_id);
                $q = $conn->query("SELECT dosya FROM gelistirme_dosyalar WHERE id = $dosya_id AND gelistirme_id = $id");
                if ($q && $row = $q->fetch_assoc()) {
                    $file_path = 'uploads/gelistirmeler/' . $row['dosya'];
                    if ($file_path && file_exists($file_path)) @unlink($file_path);
                }
                $conn->query("DELETE FROM gelistirme_dosyalar WHERE id = $dosya_id AND gelistirme_id = $id");
            }
        }
        // 2. Dosya ekleme
        if (!empty($_FILES['dosyalar']['name'][0])) {
            $upload_dir = 'uploads/gelistirmeler/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            foreach ($_FILES['dosyalar']['tmp_name'] as $index => $tmp_name) {
                $file_name = basename($_FILES['dosyalar']['name'][$index]);
                $file_path = $upload_dir . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file_name);
                if (move_uploaded_file($tmp_name, $file_path)) {
                    $file_path_sql = $conn->real_escape_string(basename($file_path));
                    $conn->query("INSERT INTO gelistirme_dosyalar (gelistirme_id, dosya) VALUES ($id, '$file_path_sql')");
                }
            }
        }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'success', 'message' => 'Geliştirme güncellendi.']);
    } else {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'error', 'message' => 'Güncelleme başarısız.']);
    }
    exit;
}

// Geçersiz işlem

header('Content-Type: application/json; charset=utf-8');
echo json_encode(['status' => 'error', 'message' => 'Geçersiz işlem.']);
