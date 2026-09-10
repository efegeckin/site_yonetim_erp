<?php
include 'config.php';
checkAuth();

//mevcut sessiondan email al
$email = $_SESSION['email'];

//sonra buna göre şifre değişikliği yap
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sifre = $conn->real_escape_string($_POST['sifre']);
    $sifre_tekrar = $conn->real_escape_string($_POST['sifre_tekrar']);
    if ($sifre !== $sifre_tekrar) {
        $error = "Şifreler eşleşmiyor!";
    } else {
        //şifreyi hashle
        $sifre_hash = password_hash($sifre, PASSWORD_DEFAULT);
        
        //veritabanında güncelle
        $update_sql = "UPDATE users SET sifre_hash = '" . $conn->real_escape_string($sifre_hash) . "' WHERE email = '" . $conn->real_escape_string($email) . "'";
        if ($conn->query($update_sql) === TRUE) {
            //başarılı ise dashboarda yönlendir
            echo  "<script>
            
                alert('Şifreniz başarıyla güncellendi! Yönlendiriliyorsunuz...');
                window.location.href = 'user_dashboard.php';
            
                 </script>";
            exit();
        } else {
            $error = "Şifre güncellenirken bir hata oluştu: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şifre Değiştir - Site Yönetim ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .login-body {
            padding: 2rem;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px;
            border: 2px solid #e9ecef;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .test-accounts {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="login-card">
                    <div class="login-header">
                        <h2><i class="fas fa-building me-2"></i>Site Yönetim ERP</h2>
                        <p class="mb-0">Site Yönetim Sistemi</p>
                    </div>
                    <div class="login-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">E-posta Adresi</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="email" class="form-control" placeholder="ornek@site.com" value="<?php echo htmlspecialchars($_SESSION['email']); ?>" disabled required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Şifre</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="sifre" class="form-control" placeholder="Şifreniz" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Şifre Tekrar</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="sifre_tekrar" class="form-control" placeholder="Şifreniz" required>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-login w-100 mb-3">
                                <i class="fas fa-sign-in-alt me-2"></i>Giriş Yap
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>