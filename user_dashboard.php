<?php
include 'config.php';
checkAuth();

$isAdmin = (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin');

echo '<script>window.kullaniciAdSoyad = "' . strtolower(str_replace(['ç', 'Ç', 'ğ', 'Ğ', 'ı', 'İ', 'ö', 'Ö', 'ş', 'Ş', 'ü', 'Ü', ' '], ['c', 'c', 'g', 'g', 'i', 'i', 'o', 'o', 's', 's', 'u', 'u', ''], trim($_SESSION['ad'] . ' ' . $_SESSION['soyad']))) . '";</script>';

$user_id = $_SESSION['user_id'];
$sql = "SELECT d.* FROM daireler d WHERE d.kullanici_id = $user_id";
$daire_result = $conn->query($sql);
$daire = $daire_result->fetch_assoc();

if (!$daire) {
    die("Daire bilginiz bulunamadı!");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kvkk_onay = isset($_POST['kvkk_onay']) ? 1 : 0;
    $user_id = $_SESSION['user_id'];
    //status değeri 0 sa gözükmesin


    $kvkk_id = 1; // Güncel KVKK metni id'si
    // Önce onay var mı kontrol et
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
}

$kvkk_onay = 0;
$user_id = $_SESSION['user_id'];
$kvkk_id = 1; // Güncel KVKK metni id'si
$kvkk_onay_var = false;
$stmt = $conn->prepare("SELECT 1 FROM user_kvkk_onay WHERE user_id = ? AND kvkk_id = ? LIMIT 1");
$stmt->bind_param("ii", $user_id, $kvkk_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $kvkk_onay_var = true;
}
$stmt->close();



?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Panel - Site Yönetim ERP</title>
    <link rel="stylesheet" href="assets/tablo.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .navbar-brand {
            font-weight: 600;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            transition: transform 0.3s;
            height: fit-content;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .user-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-radius: 15px;
        }

        .stat-card .card-body {
            text-align: center;
            padding: 2rem 1rem;
        }

        .stat-card i {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .table th {
            border-top: none;
            font-weight: 600;
            background-color: #f8f9fa;
        }

        /* Responsive düzenlemeler */
        @media (max-width: 991.98px) {
            .user-header .row {
                flex-direction: column;
                text-align: center;
            }

            .user-header .col-md-8,
            .user-header .col-md-4 {
                text-align: center !important;
            }

            .user-header .col-md-4 {
                margin-top: 1rem;
            }

            .card.user-header {
                padding: 0.5rem;
            }
        }

        @media (max-width: 767.98px) {
            .container {
                padding-left: 5px;
                padding-right: 5px;
            }

            .card.user-header h2 {
                font-size: 1.2rem;
            }

            .card.user-header .fa-user-circle {
                font-size: 2.5rem !important;
            }

            .kanban {
                padding: 0.5rem 0.5rem;
            }

            .card-attachments .thumb-wrapper {
                width: 35px;
                height: 35px;
            }

            .attachment-grid {
                gap: 3px;
            }

            #gelistirmeYilFilter {
                width: 100% !important;
                margin-bottom: 1rem;
            }
        }

        @media (max-width: 575.98px) {
            .user-header .row {
                flex-direction: column;
            }

            .user-header .col-md-8,
            .user-header .col-md-4 {
                width: 100%;
                text-align: center !important;
            }

            .card.user-header h2 {
                font-size: 1rem;
            }

            .kanban {
                font-size: 0.95rem;
            }

            .oncelik-badge {
                font-size: 11px;
            }
        }

        @media (max-width: 576px) {
            #duyuruModal .modal-dialog {
                margin: 0.5rem;
            }

            #duyuruModal .modal-title {
                font-size: 1.1rem;
            }

            #duyuruModal .modal-body {
                padding: 1rem !important;
            }

            #duyuruModalImage {
                max-height: 200px !important;
            }

            #duyuruModalFiles img.thumb {
                max-width: 60px !important;
                max-height: 60px !important;
            }
        }


        /* Sadece KVKK modalı için özel stil */
        .kvkk-modal {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1050;
            display: block;
            width: 100%;
            height: 100%;
            overflow-x: hidden;
            overflow-y: auto;
            outline: 0;
            background-color: rgba(0, 0, 0, .5);
        }

        .kvkk-modal.show .modal-dialog {
            transform: translate(0, 0);
        }

        .kvkk-modal .modal-dialog {
            position: relative;
            width: auto;
            margin: .5rem;
            pointer-events: none;
            margin: 1.75rem auto;
        }

        .kvkk-modal .modal-content {
            position: relative;
            display: flex;
            flex-direction: column;
            width: 100%;
            pointer-events: auto;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid rgba(0, 0, 0, .2);
            border-radius: .3rem;
            outline: 0;
            padding: 20px;
        }

        .kvkk-modal .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1rem;
            border-bottom: 1px solid #dee2e6;
            border-top-left-radius: .3rem;
            border-top-right-radius: .3rem;
        }

        .kvkk-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .kvkk-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 6px 12px;
            font-weight: 600;
            color: white;
            transition: all 0.3s;
        }

        .kvkk-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }



        .oncelik-badge {
            font-weight: 600;
            font-size: 13px;
            margin-right: 0.5rem;
        }

        .oncelik-badge.Yüksek {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: #b91c1c;
            padding: 0.2rem;
            border-radius: 0.4rem;
            border: 1px solid #fecaca;
        }

        .oncelik-badge.Orta {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #d97706;
            border: 1px solid #fde68a;
            padding: 0.2rem;
            border-radius: 0.4rem;
        }

        .oncelik-badge.Düşük {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            color: #047857;
            border: 1px solid #a7f3d0;
            padding: 0.2rem;
            border-radius: 0.4rem;
        }

        .sorumlu {
            font-weight: 600;
            font-size: 13px;
            padding: 0 0.4rem;
            border-radius: 0.4rem;
            background-color: rgb(240, 240, 240);
            width: fit-content;
        }

        /* Kanban */
        .kanban {
            padding: 0.5rem 1rem;
            background-color: rgb(241, 245, 249);
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
        }

        .card-attachments {
            margin-top: 12px;
            padding-top: 10px;
            border-top: 1px dashed #eee;
        }

        .attachment-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .thumb-wrapper {
            width: 45px;
            /* Resimlerin sabit genişliği */
            height: 45px;
            /* Resimlerin sabit yüksekliği */
            overflow: hidden;
            border-radius: 6px;
            border: 1px solid #ddd;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .thumb-wrapper:hover {
            transform: scale(1.1);
            border-color: var(--primary);
        }

        .thumb-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* Resmi bozmadan kırparak sığdırır */
        }

        /* Tablo Alanı */
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .table-blue {
            background-color: var(--primary-blue);
        }

        th {
            background: #fafafa;
            color: #666;
            font-weight: 500;
            padding: 15px 24px;
            font-size: 13px;
            border-bottom: 1px solid #eee !important;
        }

        td {
            padding: 15px 24px;
            border-bottom: 1px solid #f5f5f5ff;
            color: var(--text-dark);
            font-size: 14px;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-building me-2"></i>Site Yönetim ERP
            </a>
            <div class="navbar-nav ms-auto">

                <?php if (!$kvkk_onay_var): ?>
                    <div style="color: black; display: block;" class="modal show kvkk-modal" aria-modal="true" role="dialog">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h6><i class="fas fa-building me-2"></i>Site Yönetim ERP</h6>
                                    <h6 class="modal-title"></i>Hoş geldiniz! <span id="kullanici"><?php echo $_SESSION['ad'] . ' ' . $_SESSION['soyad']; ?></span></h6>
                                </div>
                                <div id="kvkk" class="modal-body flex-column gap-3">
                                    <h5 class="text-center text-decoration-underline mt-3">KVKK Metni</h5>
                                    <?php
                                    $kvkk = mysqli_query($conn, "SELECT * FROM kvkk ORDER BY id DESC");
                                    $row = mysqli_fetch_assoc($kvkk);
                                    if ($row && $row['status'] == 1) {
                                        echo "<p>{$row['metin']}</p> ";
                                    } else {
                                        echo "<p>KVKK metni bulunamadı.</p>";
                                    }
                                    ?>
                                    <div class="mb-3">
                                        <input type="radio" name="kvvk" id="kvkk_onay" required>
                                        <label class="fw-bold" for="kvkk_onay"> KVKK metnini okudum ve kabul ediyorum.</label>
                                    </div>
                                    <div class="modal-footer py-3 px-0 d-flex justify-content-start">
                                        <form id="kvkkSbmt_first">
                                            <input type="hidden" name="kvkk_onay" id="kvkk_onay_hidden" value="0">
                                            <button type="button" id="kvkkBtn" class="kvkk-btn" aria-label="Close"> Gönder</button>
                                            <span id="kvkkBtnText"> </span>
                                        </form>
                                    </div>
                                </div>
                                <div style="display:none; opacity: 0;" id="sifreDegis" class="modal-body flex-column gap-3">
                                    <form action="kvkk_change_password.php" class="w-50 mx-auto" method="POST" id="kvkkSbmt">
                                        <h5 class="text-center text-decoration-underline mt-3">Şifre Değiştir</h5>
                                        <div class="mb-3">
                                            <label for="yeni_sifre" class="form-label">Yeni Şifre</label>
                                            <input type="password" class="form-control" id="yeni_sifre" name="yeni_sifre" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="yeni_sifre_tekrar" class="form-label">Yeni Şifre Tekrar</label>
                                            <input type="password" class="form-control" id="yeni_sifre_tekrar" name="yeni_sifre_tekrar" required>
                                        </div>
                                        <div class="modal-footer d-flex justify-content-center">
                                            <input type="hidden" name="kvkk_onay" id="kvkk_onay_hidden" value="1">
                                            <button type="submit" id="kvkkSifreBtn" class="kvkk-btn" aria-label="Close"> Gönder ve Şifre Değiştir</button>
                                            <span id="kvkkSifreBtnText"> </span>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                <?php endif; ?>


                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i>
                        <?php echo $_SESSION['ad'] . ' ' . $_SESSION['soyad']; ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="change_password.php"><i class="fas fa-key me-2"></i>Şifre Değiştir</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Çıkış Yap</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Image Modal -->
    <div id="imgModal" style="display:none;position:fixed;z-index:2000;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.8);align-items:center;justify-content:center;">
        <span id="closeModal" style="position:absolute;top:30px;right:40px;font-size:3rem;color:white;cursor:pointer;z-index:2001;">&times;</span>
        <img id="modalImage" src="" style="max-width:90vw;max-height:90vh;border-radius:10px;box-shadow:0 0 20px #000;z-index:2000;" />
    </div>
    <div class="container mt-4">

        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card user-header">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <!-- Daire Seçimi -->
                            <div class="col-md-8">
                                <h2 class="card-title">Hoş Geldiniz, <?php echo $_SESSION['ad'] . ' ' . $_SESSION['soyad']; ?></h2>

                                <?php
                                $user_id = $_SESSION['user_id'];
                                $sql = "SELECT d.* FROM daireler d WHERE d.kullanici_id = $user_id";
                                $daire_result = $conn->query($sql);
                                $daireler = [];
                                while ($daire_option = $daire_result->fetch_assoc()) {
                                    $daireler[] = $daire_option;
                                }
                                if (count($daireler) > 1) {
                                ?>
                                    <div class="row g-2 align-items-center card-text">
                                        <div class="col-auto"><i class="fas fa-home"></i></div>
                                        <div class="col-12 col-sm fw-bold">
                                            <label for="daireSec" class="form-label m-0 me-2">Daire Seçiniz</label>
                                            <select class="form-select form-select-m m-0" name="" id="daireSec">
                                                <option value="" selected>Lütfen Daire Seçiniz</option>
                                                <?php
                                                foreach ($daireler as $daire_option) {
                                                    echo "<option value='{$daire_option['id']}'>Blok {$daire_option['blok']} Kat {$daire_option['kat']} Daire {$daire_option['numara']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                <?php } else if (count($daireler) === 1) { ?>
                                    <input type="hidden" id="tekDaireId" value="<?php echo $daireler[0]['id']; ?>">
                                    <div class="row g-2 align-items-center card-text">
                                        <div class="col-auto"><i class="fas fa-home"></i></div>
                                        <div class="col fw-bold">
                                            Blok <?php echo $daireler[0]['blok']; ?> Kat <?php echo $daireler[0]['kat']; ?> Daire <?php echo $daireler[0]['numara']; ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="col-md-4 text-end">
                                <i class="fas fa-user-circle fa-4x"></i><br>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- Kontrol -->
        <div id="kullanici-daire-bilgileri" class="d-none">
            <!-- Stats -->

        </div>
        <!-- Geliştirme Kanban -->
        <!-- Yıla göre filtreleme -->



    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/script.js"></script>
</body>

</html>