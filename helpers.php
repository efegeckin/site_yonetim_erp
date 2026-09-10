<?php
// Ortak yardımcı fonksiyonlar

// Dosya yükleme (tekil veya çoklu)
function upload_files($input_name, $target_dir, $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'])
{
    $uploaded = [];
    if (!isset($_FILES[$input_name]) || empty($_FILES[$input_name]['name'][0])) return $uploaded;
    if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);
    foreach ($_FILES[$input_name]['tmp_name'] as $key => $tmp_name) {
        if ($_FILES[$input_name]['error'][$key] == 0) {
            $file_name = basename($_FILES[$input_name]['name'][$key]);
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            if (in_array($file_ext, $allowed_ext)) {
                $new_name = time() . '_' . uniqid() . '.' . $file_ext;
                $upload_path = rtrim($target_dir, '/') . '/' . $new_name;
                if (move_uploaded_file($tmp_name, $upload_path)) {
                    $uploaded[] = $new_name;
                }
            }
        }
    }
    return $uploaded;
}

// Dosya yolu oluşturucu (sadece dosya adı verilirse tam yol döndürür)
function get_gelistirme_file_path($filename)
{
    if (empty($filename)) return '';
    if (strpos($filename, 'uploads/gelistirmeler/') === 0 || strpos($filename, 'upload/gelistirmeler/') === 0) {
        return $filename;
    }
    if (basename($filename) === $filename) {
        return 'uploads/gelistirmeler/' . $filename;
    }
    return $filename;
}

// Yetki kontrolleri (gerekirse tekrar kullanılabilir)
function is_admin()
{
    return (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin');
}
function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

// Duyuru dosya yolu oluşturucu (sadece dosya adı verilirse tam yol döndürür)
function get_announcement_file_path($filename)
{
    if (empty($filename)) return '';
    if (strpos($filename, 'uploads/announcements/') === 0 || strpos($filename, 'upload/announcements/') === 0) {
        return $filename;
    }
    if (basename($filename) === $filename) {
        return 'uploads/announcements/' . $filename;
    }
    return $filename;
}
