<?php
include 'config.php';
checkAuth();

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sifre = $conn->real_escape_string($_POST['yeni_sifre']);
    $sifre_tekrar = $conn->real_escape_string($_POST['yeni_sifre_tekrar']);
    $kvkk_onay = isset($_POST['kvkk_onay']) ? 1 : 0;
    $password = password_hash($_POST['yeni_sifre'], PASSWORD_DEFAULT);
    //$sifre_hash = password_hash($sifre, PASSWORD_DEFAULT);

    // Eski şifre ile yeni şifre aynı mı kontrolü
    $stmt = $conn->prepare("SELECT sifre_hash FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($eski_sifre_hash);
    $stmt->fetch();
    $stmt->close();
    if (password_verify($sifre, $eski_sifre_hash)) {
        echo "<script>alert('Yeni şifre, eski şifre ile aynı olamaz. Lütfen farklı bir şifre girin.'); window.history.back();</script>";
        exit();
    }

    if ($sifre !== $sifre_tekrar) {
        echo "<script>alert('Şifreler eşleşmiyor. Lütfen tekrar deneyin.'); window.history.back();</script>";
        exit();
    }

    if (strlen($sifre) < 8) {
        echo "<script>alert('Şifre en az 8 karakter olmalıdır.'); window.history.back();</script>";
        exit();
    }




    // KVKK işlemleri
    $kvkk_id = 1; // Güncel KVKK metni id'si
    $stmt = $conn->prepare("SELECT 1 FROM user_kvkk_onay WHERE user_id = ? AND kvkk_id = ? LIMIT 1");
    $stmt->bind_param("ii", $user_id, $kvkk_id);
    $stmt->execute();
    $stmt->store_result();
    $onay_var = $stmt->num_rows > 0;
    $stmt->close();
    if ($kvkk_onay && !$onay_var) {
        $stmt = $conn->prepare("REPLACE INTO user_kvkk_onay (user_id, kvkk_id, onay_tarihi) VALUES (?, ?, NOW())");
        $stmt->bind_param("ii", $user_id, $kvkk_id);
        $stmt->execute();
    }

    // Şifreyi güncelle (prepared statement ile)
    $stmt = $conn->prepare("UPDATE users SET sifre_hash = ? WHERE id = ?");
    $stmt->bind_param("si", $password, $user_id);
    if ($stmt->execute()) {
        $stmt->close();
        echo  "<script>alert('Şifreniz başarıyla güncellendi! Yönlendiriliyorsunuz...'); window.location.href = 'user_dashboard.php';</script>";
        exit();
    } else {
        $stmt->close();
        echo "<script>alert('Şifre güncellenirken bir hata oluştu: Lütfen tekrar deneyin.'); window.history.back();</script>";
        exit();
    }
}
