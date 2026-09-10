<?php
include 'config.php';
checkAuth();
checkAdmin();

// Gelir ekleme
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['gelir_ekle'])) {
    $tarih = $conn->real_escape_string($_POST['tarih']);
    $miktar = floatval($_POST['miktar']);
    $aciklama = $conn->real_escape_string($_POST['aciklama']);
    
    $sql = "INSERT INTO kasa (islem_tipi, miktar, aciklama, tarih) 
            VALUES ('gelir', $miktar, '$aciklama', '$tarih')";
    
    if ($conn->query($sql)) {
        header("Location: kasa.php?success=gelir_eklendi");
    } else {
        header("Location: kasa.php?error=gelir_eklenemedi");
    }
    exit();
}

header("Location: kasa.php");
?>