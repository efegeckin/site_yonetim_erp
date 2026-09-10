      <?php
        include 'config.php';
        checkAuth();
        checkAdmin();

        ?>
      <!DOCTYPE html>
      <html lang="tr">

      <head>
          <meta charset="UTF-8">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <title>Admin Panel - Site Yönetim ERP</title>
          <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
          <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
          <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
          <style>
              .sidebar {
                  background: #2c3e50;
                  height: 100vh;
                  color: white;
                  transition: all 0.3s;
              }

              .sidebar .nav-link {
                  color: #bdc3c7;
                  padding: 15px 20px;
                  border-left: 3px solid transparent;
                  cursor: pointer;
              }

              .sidebar .nav-link:hover,
              .sidebar .nav-link.active {
                  color: white;
                  background: #34495e;
                  border-left-color: #3498db;
              }

              .card {
                  border: none;
                  border-radius: 10px;
                  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                  margin-bottom: 20px;
                  height: fit-content;
              }

              /* Yükleniyor ikonu için */
              .loading-overlay {
                  position: fixed;
                  top: 0;
                  left: 0;
                  width: 100%;
                  height: 100%;
                  background: rgba(255, 255, 255, 0.8);
                  z-index: 9999;
                  display: none;
                  justify-content: center;
                  align-items: center;
              }

              /* Mobil Menü için*/
              .offcanvas {
                  background-color: #2c3e50;
                  color: white;
              }

              .offcanvas-body .nav-link {
                  color: white;
                  border-left: 3px solid transparent;
              }

              .offcanvas-body .nav-link.active {
                  background-color: #34495e;
                  border-left-color: #3498db;
              }
          </style>
      </head>

      <body>
          <div class="loading-overlay" id="loader">
              <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Yükleniyor...</span>
              </div>
          </div>

          <!-- Mobil Menü -->
          <?php include 'admin_mobile_sidebar.php'; ?>

          <div class="container-fluid">
              <div class="row">
                  <!-- Masaüstü Menü -->
                  <nav class="position-fixed col-md-3 col-lg-2 d-none d-md-block sidebar">
                      <div class="pt-3">
                          <h4 class="text-center mb-4"><i class="fas fa-building me-2"></i>Site ERP</h4>
                          <ul class="nav flex-column">
                              <li class="nav-item"><a class="nav-link active" onclick="showSection('dashboard')"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                              <li class="nav-item"><a class="nav-link" onclick="showSection('duyurular')"><i class="fas fa-bullhorn me-2"></i>Duyurular</a></li>
                              <li class="nav-item"><a class="nav-link" onclick="showSection('gelistirmeler')"><i class="fas fa-circle-up me-2"></i>İş Planı</a></li>
                              <li class="nav-item"><a class="nav-link" onclick="showSection('daireler')"><i class="fas fa-home me-2"></i>Daireler</a></li>
                              <li class="nav-item"><a class="nav-link" onclick="showSection('borclar')"><i class="fas fa-money-bill-wave me-2"></i>Borçlar</a></li>
                              <li class="nav-item"><a class="nav-link" onclick="showSection('kullanicilar')"><i class="fas fa-users me-2"></i>Kullanıcılar</a></li>
                              <li class="nav-item"><a class="nav-link" onclick="showSection('araclar')"><i class="fas fa-car me-2"></i>Araçlar</a></li>
                              <li class="nav-item"><a class="nav-link" href="kasa.php"><i class="fas fa-cash-register me-2"></i>Kasa</a></li>
                          </ul>
                      </div>
                  </nav>

                  <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                      <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom mb-4">
                          <div class="container-fluid">
                              <span class="navbar-text">Hoşgeldin, <b><?php echo $_SESSION['ad'] . ' ' . $_SESSION['soyad']; ?></b></span>
                              <a href="logout.php" class="btn btn-sm btn-outline-danger"><i class="fas fa-sign-out-alt"></i> Çıkış</a>
                          </div>
                      </nav>
                      <div id="alert-area"></div>

                      <div id="section-dashboard" class="page-section">
                          <h3>Dashboard</h3>
                          <div id="dashboard-stats-container">
                          </div>
                      </div>

                      <div id="section-daireler" class="page-section" style="display:none;">
                          <div class="card">
                              <div class="card-header bg-primary text-white">Daire Yönetimi</div>
                              <div class="card-body">
                                  <form id="form-daire-ekle" class="row g-3 mb-4">
                                      <input type="hidden" name="type" value="daire_ekle">
                                      <div class="col-md-3"><input type="text" name="blok" class="form-control" placeholder="Blok" required></div>
                                      <div class="col-md-3"><input type="number" name="kat" class="form-control" placeholder="Kat" required></div>
                                      <div class="col-md-3"><input type="number" name="numara" class="form-control" placeholder="No" required></div>
                                      <div class="col-md-3"><button type="submit" class="btn btn-primary w-100">Ekle</button></div>
                                  </form>
                                  <hr>
                                  <div class="table-responsive">
                                      <table class="table table-striped" id="table-daireler">
                                          <thead>
                                              <tr>
                                                  <th>ID</th>
                                                  <th>Blok</th>
                                                  <th>Kat</th>
                                                  <th>No</th>
                                                  <th>Kullanıcı</th>
                                                  <th>İşlemler</th>
                                              </tr>
                                          </thead>
                                          <tbody></tbody>
                                      </table>
                                  </div>
                              </div>
                          </div>
                      </div>

                      <div id="section-borclar" class="page-section" style="display:none;">
                          <div class="card">
                              <div class="card-header bg-success text-white">Borç Yönetimi</div>
                              <div class="card-body">
                                  <form id="form-borc-ekle" class="mb-4">
                                      <input type="hidden" name="type" value="borc_ekle">
                                      <div class="row g-3">
                                          <div class="col-12">
                                              <label>Daireler (Çoklu Seçim)</label>
                                              <select name="daire_ids[]" class="form-select select2-daireler" multiple="multiple" style="width: 100%;" required>
                                              </select>
                                          </div>
                                          <div class="col-md-2">
                                              <input type="number" name="yil" class="form-control" value="<?php echo date('Y'); ?>" placeholder="Yıl" required>
                                          </div>
                                          <div class="col-md-2">
                                              <select name="ay" class="form-select" required>
                                                  <option value="">Ay</option>
                                                  <?php for ($i = 1; $i <= 12; $i++) echo "<option value='$i'>$i</option>"; ?>
                                              </select>
                                          </div>
                                          <div class="col-md-3">
                                              <select name="kalem" class="form-select" required>
                                                  <option value="">Kalem Seçin</option>
                                                  <option value="aidat">Aidat</option>
                                                  <option value="su">Su</option>
                                                  <option value="elektrik">Elektrik</option>
                                                  <option value="internet">İnternet</option>
                                                  <option value="demirbas">Demirbaş</option>
                                                  <option value="Garaj Kumanda">Garaj Kumanda</option>
                                                  <option value="Yuvarlama Farkı">Yuvarlama Farkı</option>
                                              </select>
                                          </div>
                                          <div class="col-md-3">
                                              <input type="number" step="0.01" name="tutar" class="form-control" placeholder="Tutar" required>
                                          </div>
                                          <div class="col-md-2">
                                              <button type="submit" class="btn btn-success w-100">Borçlandır</button>
                                          </div>
                                      </div>
                                  </form>
                                  <hr>
                                  <div class="row g-2 mb-3">
                                      <div class="col-md-3"><select id="filter-borc-durum" class="form-select">
                                              <option value="">Tüm Durumlar</option>
                                              <option value="0">Ödenmedi</option>
                                              <option value="1">Ödendi</option>
                                          </select></div>
                                      <div class="col-md-3"><input type="text" id="filter-borc-ara" class="form-control" placeholder="Daire/Blok Ara..."></div>
                                  </div>
                                  <div class="table-responsive">
                                      <table class="table table-striped" id="table-borclar">
                                          <thead>
                                              <tr>
                                                  <th>Daire</th>
                                                  <th>Kullanıcı</th>
                                                  <th>Dönem</th>
                                                  <th>Kalem</th>
                                                  <th>Tutar</th>
                                                  <th>Durum</th>
                                                  <th>İşlem</th>
                                              </tr>
                                          </thead>
                                          <tbody></tbody>
                                      </table>
                                  </div>
                              </div>
                          </div>
                      </div>

                      <div id="section-kullanicilar" class="page-section" style="display:none;">
                          <div class="card">
                              <div class="card-header bg-danger text-white">Kullanıcılar</div>
                              <div class="card-body">
                                  <form id="form-kullanici-ekle" class="row g-3 mb-4">
                                      <input type="hidden" name="type" value="kullanici_ekle">
                                      <div class="col-md-3"><input type="text" name="ad" class="form-control" placeholder="Ad" required></div>
                                      <div class="col-md-3"><input type="text" name="soyad" class="form-control" placeholder="Soyad" required></div>
                                      <div class="col-md-3"><input type="email" name="email" class="form-control" placeholder="E-posta" required></div>
                                      <div class="col-md-2"><input type="password" name="sifre" class="form-control" placeholder="Şifre" required></div>
                                      <div class="col-md-1"><button type="submit" class="btn btn-danger w-100"><i class="fas fa-plus"></i></button></div>
                                  </form>
                                  <table class="table table-striped" id="table-kullanicilar">
                                      <thead>
                                          <tr>
                                              <th>ID</th>
                                              <th>Ad Soyad</th>
                                              <th>Email</th>
                                              <th>Rol</th>
                                          </tr>
                                      </thead>
                                      <tbody></tbody>
                                  </table>
                              </div>
                          </div>
                      </div>

                      <div id="section-araclar" class="page-section" style="display:none;">
                          <div class="card">
                              <div class="card-header bg-info text-white">Araç Yönetimi</div>
                              <div class="card-body">

                                  <div class="card mb-4 border-info">
                                      <div class="card-header bg-light text-info">
                                          <i class="fas fa-plus-circle me-2"></i>Yeni Araç Tanımla
                                      </div>
                                      <div class="card-body">
                                          <form id="form-arac-ekle" class="row g-3">
                                              <input type="hidden" name="type" value="arac_ekle">
                                              <div class="col-md-6">
                                                  <label class="form-label">Daire Seçin</label>
                                                  <select name="daire_id" class="form-select select2-daireler" style="width: 100%;" required>
                                                  </select>
                                              </div>
                                              <div class="col-md-4">
                                                  <label class="form-label">Araç Plakası</label>
                                                  <input type="text" name="plaka" class="form-control" placeholder="34 ABC 123" required>
                                              </div>
                                              <div class="col-md-2 d-flex align-items-end">
                                                  <button type="submit" class="btn btn-info text-white w-100">Kaydet</button>
                                              </div>
                                          </form>
                                      </div>
                                  </div>

                                  <table class="table table-striped table-hover" id="table-araclar">
                                      <thead>
                                          <tr>
                                              <th>Plaka</th>
                                              <th>Daire</th>
                                              <th>Sahibi</th>
                                              <th>İşlem</th>
                                          </tr>
                                      </thead>
                                      <tbody></tbody>
                                  </table>
                              </div>
                          </div>
                      </div>
                      <div id="section-gelistirmeler" class="page-section" style="display:none;">
                          <div class="card">
                              <div class="card-header bg-secondary text-white">İş Planı Yönetimi</div>
                              <div class="card-body">
                                  <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalGelistirmeEkle">
                                      <i class="fas fa-plus"></i> Yeni İş Planı Ekle
                                  </button>
                                  <!-- İş Planı Ekle Modal -->
                                  <div class="modal fade" id="modalGelistirmeEkle" tabindex="-1">
                                      <div class="modal-dialog">
                                          <div class="modal-content">
                                              <div class="modal-header bg-secondary text-white">
                                                  <h5 class="modal-title"><i class="fas fa-circle-up me-2"></i>Yeni İş Planı Ekle</h5>
                                                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                              </div>
                                              <form id="form-gelistirme-ekle" enctype="multipart/form-data">
                                                  <div class="modal-body">
                                                      <input type="hidden" name="type" value="gelistirme_ekle">
                                                      <div class="mb-3">
                                                          <label class="form-label">Başlık</label>
                                                          <input type="text" name="baslik" class="form-control" required>
                                                      </div>
                                                      <div class="mb-3">
                                                          <label class="form-label">Açıklama</label>
                                                          <textarea name="aciklama" class="form-control" rows="3" required></textarea>
                                                      </div>
                                                      <div class="mb-3">
                                                          <label class="form-label">Durum</label>
                                                          <select name="durum" class="form-select" required>
                                                              <option value="Planlandı">Planlandı</option>
                                                              <option value="Devam Ediyor" selected>Devam Ediyor</option>
                                                              <option value="Tamamlandı">Tamamlandı</option>
                                                          </select>
                                                      </div>
                                                      <div class="mb-3">
                                                          <label class="form-label">Öncelik</label>
                                                          <select name="oncelik" class="form-select" required>
                                                              <option value="Düşük">Düşük</option>
                                                              <option value="Orta" selected>Orta</option>
                                                              <option value="Yüksek">Yüksek</option>
                                                          </select>
                                                      </div>
                                                      <div class="mb-3">
                                                          <label class="form-label">Sorumlu</label>
                                                          <input type="text" name="sorumlu" class="form-control" required>
                                                      </div>
                                                      <div class="mb-3">
                                                          <label class="form-label">Hedef Tarih</label>
                                                          <input type="date" name="hedef_tarih" class="form-control" required>
                                                      </div>
                                                      <div class="mb-3">
                                                          <label class="form-label">Dosya Ekle</label>
                                                          <input type="file" name="dosyalar[]" class="form-control" multiple>
                                                          <small class="text-muted">Birden fazla dosya seçebilirsiniz.</small>
                                                      </div>
                                                  </div>
                                                  <div class="modal-footer">
                                                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                                                      <button type="submit" class="btn btn-secondary">Kaydet</button>
                                                  </div>
                                              </form>
                                          </div>
                                      </div>
                                  </div>
                                  <hr>
                                  <div class="table-responsive">
                                      <table class="table table-striped" id="table-gelistirmeler">
                                          <thead>
                                              <tr>
                                                  <th>id</th>
                                                  <th>Başlık</th>
                                                  <th>Açıklama</th>
                                                  <th>Durum</th>
                                                  <th>Öncelik</th>
                                                  <th>Sorumlu</th>
                                                  <th>Hedef Tarihi</th>
                                                  <th>Oluşturma Tarihi</th>
                                                  <th>Dosyalar</th>
                                                  <th>İşlem</th>
                                              </tr>
                                          </thead>
                                          <tbody></tbody>
                                      </table>
                                  </div>
                              </div>
                          </div>
                      </div>

                      <div id="section-duyurular" class="page-section" style="display:none;">
                          <div class="card">
                              <div class="card-header bg-primary text-white">Duyuru Yönetimi</div>
                              <div class="card-body">
                                  <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalDuyuruEkle">
                                      <i class="fas fa-plus"></i> Yeni Duyuru Ekle
                                  </button>
                                  <hr>
                                  <div class="table-responsive">
                                      <table class="table table-striped" id="table-duyurular">
                                          <thead>
                                              <tr>
                                                  <th>ID</th>
                                                  <th>Başlık</th>
                                                  <th>Açıklama</th>
                                                  <th>Değiştirilme Tarihi</th>
                                                  <th>Değiştiren</th>
                                                  <th>Durum</th>
                                                  <th>Dosyalar</th>
                                                  <th>İşlemler</th>
                                              </tr>
                                          </thead>
                                          <tbody></tbody>
                                      </table>
                                  </div>
                              </div>
                          </div>
                      </div>



                  </main>
              </div>



              <!-- Duyuru Ekle Modal -->
              <div class="modal fade" id="modalDuyuruEkle" tabindex="-1">
                  <div class="modal-dialog">
                      <div class="modal-content">
                          <div class="modal-header bg-primary text-white">
                              <h5 class="modal-title"><i class="fas fa-bullhorn me-2"></i>Yeni Duyuru Ekle</h5>
                              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                          </div>
                          <form id="form-duyuru-ekle" enctype="multipart/form-data">
                              <div class="modal-body">
                                  <input type="hidden" name="type" value="duyuru_ekle">
                                  <div class="mb-3">
                                      <label class="form-label">Başlık</label>
                                      <input type="text" name="baslik" class="form-control" required>
                                  </div>
                                  <div class="mb-3">
                                      <label class="form-label">Açıklama</label>
                                      <textarea name="aciklama" class="form-control" rows="3" required></textarea>
                                  </div>
                                  <div class="mb-3">
                                      <label class="form-label">Durum</label>
                                      <select name="status" class="form-select">
                                          <option value="1" selected>Aktif</option>
                                          <option value="0">Pasif</option>
                                      </select>
                                  </div>
                                  <div class="mb-3">
                                      <label class="form-label">Dosya Ekle</label>
                                      <input type="file" name="files[]" class="form-control" multiple>
                                      <small class="text-muted">Birden fazla dosya seçebilirsiniz.</small>
                                  </div>
                              </div>
                              <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                                  <button type="submit" class="btn btn-primary">Kaydet</button>
                              </div>
                          </form>
                      </div>
                  </div>
              </div>
              <!-- Resim Modalı -->
              <div class="modal" id="imgModal">
                  <button class="modal-close" id="closeModal">&times;</button>
                  <div class="modal-content">
                      <img src="" alt="Büyütülmüş Resim" id="modalImage">
                  </div>
              </div>
              <!-- Diğer Modallar -->
              <div class="modal fade" id="modalHizliAracEkle" tabindex="-1">
                  <div class="modal-dialog">
                      <div class="modal-content">
                          <div class="modal-header bg-info text-white">
                              <h5 class="modal-title">Hızlı Araç Ekle</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <form id="form-modal-arac-ekle">
                              <div class="modal-body">
                                  <input type="hidden" name="type" value="arac_ekle">
                                  <input type="hidden" name="daire_id" id="modal_arac_daire_id">

                                  <div class="mb-3">
                                      <label>Seçilen Daire:</label>
                                      <input type="text" id="modal_arac_daire_bilgi" class="form-control" readonly>
                                  </div>
                                  <div class="mb-3">
                                      <label>Plaka Giriniz:</label>
                                      <input type="text" name="plaka" class="form-control" required placeholder="34 XX 000">
                                  </div>
                              </div>
                              <div class="modal-footer">
                                  <button type="submit" class="btn btn-info text-white">Kaydet</button>
                              </div>
                          </form>
                      </div>
                  </div>
              </div>

              <div class="modal fade" id="daireDuzenleModal" tabindex="-1">
                  <div class="modal-dialog">
                      <div class="modal-content">
                          <div class="modal-header bg-warning text-dark">
                              <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Daire Bilgilerini Düzenle</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <form id="form-daire-duzenle">
                              <div class="modal-body">
                                  <input type="hidden" name="type" value="daire_guncelle">
                                  <input type="hidden" name="daire_id" id="edit_daire_id">

                                  <div class="row g-3">
                                      <div class="col-md-4">
                                          <label class="form-label">Blok</label>
                                          <input type="text" name="blok" id="edit_blok" class="form-control" required>
                                      </div>
                                      <div class="col-md-4">
                                          <label class="form-label">Kat</label>
                                          <input type="number" name="kat" id="edit_kat" class="form-control" required>
                                      </div>
                                      <div class="col-md-4">
                                          <label class="form-label">No</label>
                                          <input type="number" name="numara" id="edit_numara" class="form-control" required>
                                      </div>
                                      <div class="col-12">
                                          <label class="form-label">Kullanıcı (Ev Sahibi)</label>
                                          <select name="kullanici_id" id="edit_kullanici_id" class="form-select">
                                              <option value="0">Atama Yok (Boş)</option>
                                              <?php
                                                // Kullanıcıları burada PHP ile dolduruyoruz ki modal açılınca hazır olsun
                                                $users_sql = "SELECT * FROM users WHERE rol = 'kullanici' ORDER BY ad ASC";
                                                $users_res = $conn->query($users_sql);
                                                while ($u = $users_res->fetch_assoc()) {
                                                    echo "<option value='{$u['id']}'>{$u['ad']} {$u['soyad']}</option>";
                                                }
                                                ?>
                                          </select>
                                      </div>
                                  </div>
                              </div>
                              <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                                  <button type="submit" class="btn btn-warning">Güncelle</button>
                              </div>
                          </form>
                      </div>
                  </div>
              </div>

              <div class="modal fade" id="modalTelefonYonetimi" tabindex="-1">
                  <div class="modal-dialog">
                      <div class="modal-content">
                          <div class="modal-header bg-success text-white">
                              <h5 class="modal-title"><i class="fas fa-phone-alt me-2"></i>İletişim Numaraları</h5>
                              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                          </div>
                          <div class="modal-body">
                              <input type="hidden" id="modal_telefon_daire_id">
                              <div class="alert alert-light border" id="modal_telefon_daire_bilgi">
                              </div>

                              <form id="form-telefon-ekle" class="row g-2 mb-3 align-items-end">
                                  <input type="hidden" name="type" value="telefon_ekle">
                                  <div class="col-5">
                                      <label class="small text-muted">Kime Ait?</label>
                                      <input type="text" name="ad_soyad" class="form-control form-control-sm" placeholder="Örn: Ahmet Bey" required>
                                  </div>
                                  <div class="col-5">
                                      <label class="small text-muted">Numara</label>
                                      <input type="text" name="telefon" class="form-control form-control-sm phone-mask" placeholder="05XX..." required>
                                  </div>
                                  <div class="col-2">
                                      <button type="submit" class="btn btn-sm btn-success w-100"><i class="fas fa-plus"></i></button>
                                  </div>
                              </form>

                              <hr>

                              <h6 class="small text-muted">Kayıtlı Numaralar</h6>
                              <div id="telefon-listesi-container">
                                  <div class="text-center text-muted"><small>Yükleniyor...</small></div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>

              <!-- İş Planı Düzenle Modal -->
              <div class="modal fade" id="modalGelistirmeDuzenle" tabindex="-1">
                  <div class="modal-dialog">
                      <div class="modal-content">
                          <div class="modal-header bg-warning text-dark">
                              <h5 class="modal-title"><i class="fas fa-edit me-2"></i>İş Planı Düzenle</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <form id="form-gelistirme-duzenle" enctype="multipart/form-data">
                              <div class="modal-body">
                                  <input type="hidden" name="type" value="gelistirme_guncelle">
                                  <input type="hidden" name="gelistirme_id" id="edit_gelistirme_id">
                                  <div class="mb-3">
                                      <label class="form-label">Başlık</label>
                                      <input type="text" name="baslik" id="edit_gelistirme_baslik" class="form-control" required>
                                  </div>
                                  <div class="mb-3">
                                      <label class="form-label">Açıklama</label>
                                      <textarea name="aciklama" id="edit_gelistirme_aciklama" class="form-control" rows="3" required></textarea>
                                  </div>
                                  <div class="mb-3">
                                      <label class="form-label">Durum</label>
                                      <select name="durum" id="edit_gelistirme_durum" class="form-select" required>
                                          <option value="Planlandı">Planlandı</option>
                                          <option value="Devam Ediyor">Devam Ediyor</option>
                                          <option value="Tamamlandı">Tamamlandı</option>
                                      </select>
                                  </div>
                                  <div class="mb-3">
                                      <label class="form-label">Öncelik</label>
                                      <select name="oncelik" id="edit_gelistirme_oncelik" class="form-select" required>
                                          <option value="Düşük">Düşük</option>
                                          <option value="Orta">Orta</option>
                                          <option value="Yüksek">Yüksek</option>
                                      </select>
                                  </div>
                                  <div class="mb-3">
                                      <label class="form-label">Sorumlu</label>
                                      <input type="text" name="sorumlu" id="edit_gelistirme_sorumlu" class="form-control" required>
                                  </div>
                                  <div class="mb-3">
                                      <label class="form-label">Hedef Tarih</label>
                                      <input type="date" name="hedef_tarih" id="edit_gelistirme_hedef_tarih" class="form-control" required>
                                  </div>

                                  <div class="mb-3">
                                      <label class="form-label">Ek Dosya Yükle</label>
                                      <input type="file" name="dosyalar[]" class="form-control" multiple>
                                      <small class="text-muted">Birden fazla dosya seçebilirsiniz. (Mevcut dosyalar silinmez)</small>
                                  </div>
                                  <div class="mb-3">
                                      <label class="form-label">Mevcut Dosyalar</label>
                                      <div id="edit_gelistirme_dosyalar"></div>
                                  </div>

                              </div>
                              <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                                  <button type="submit" class="btn btn-warning">Güncelle</button>
                              </div>
                          </form>
                      </div>
                  </div>
              </div>
              <!-- Duyuru Düzenle Modal -->
              <div class="modal fade" id="modalDuyuruDuzenle" tabindex="-1">
                  <div class="modal-dialog">
                      <div class="modal-content">
                          <form id="form-duyuru-duzenle" enctype="multipart/form-data">
                              <div class="modal-header">
                                  <h5 class="modal-title">Duyuru Düzenle</h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                              </div>
                              <div class="modal-body">
                                  <input type="hidden" name="id" id="edit_duyuru_id">
                                  <div class="mb-3">
                                      <label for="edit_duyuru_baslik" class="form-label">Başlık</label>
                                      <input type="text" class="form-control" name="baslik" id="edit_duyuru_baslik" required>
                                  </div>
                                  <div class="mb-3">
                                      <label for="edit_duyuru_aciklama" class="form-label">Açıklama</label>
                                      <textarea class="form-control" name="aciklama" id="edit_duyuru_aciklama" rows="3" required></textarea>
                                  </div>
                                  <div class="mb-3">
                                      <label for="edit_duyuru_status" class="form-label">Durum</label>
                                      <select class="form-select" name="status" id="edit_duyuru_status">
                                          <option value="1">Aktif</option>
                                          <option value="0">Pasif</option>
                                      </select>
                                  </div>
                                  <div class="mb-3">
                                      <label class="form-label">Mevcut Dosyalar</label>
                                      <div id="edit_duyuru_dosyalar"></div>
                                  </div>
                                  <div class="mb-3">
                                      <label for="edit_duyuru_files" class="form-label">Yeni Dosya Ekle</label>
                                      <input type="file" class="form-control" name="files[]" id="edit_duyuru_files" multiple>
                                  </div>
                              </div>
                              <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                                  <button type="submit" class="btn btn-primary">Kaydet</button>
                              </div>
                          </form>
                      </div>
                  </div>
              </div>


              <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
              <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
              <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
              <script src="assets/script.js"></script>
              <script>
                  // --- Sayfa Yüklendiğinde ---
                  $(document).ready(function() {
                      // Select2 Başlat (Bootstrap 5 teması ile)
                      $('.select2-daireler').select2({
                          theme: 'bootstrap-5',
                          placeholder: "Daireleri Seçin",
                          allowClear: true
                      });

                      // İlk verileri yükle
                      loadDashboardStats();
                      loadTableData('daireler');
                      loadTableData('duyurular');
                      loadTableData('gelistirmeler');
                      loadTableData('borclar');
                      loadTableData('kullanicilar');
                      loadTableData('araclar');
                      loadDaireSelectOptions(); // Select2'nun içini doldur
                  });

                  // --- Sekme Geçişleri ---
                  function showSection(sectionId) {
                      $('.page-section').hide();
                      $('#section-' + sectionId).fadeIn();
                      // Tüm menülerdeki active'leri kaldır
                      $('.nav-link').removeClass('active');
                      // Sadece ilgili menüye active ekle (hem mobil hem masaüstü menüde)
                      $('.nav-link').each(function() {
                          // Menüdeki href yoksa (onclick ile açılanlar) ve sectionId ile birebir eşleşiyorsa
                          var text = $(this).text().trim().toLowerCase();
                          if (
                              (sectionId === 'dashboard' && text.includes('dashboard')) ||
                              (sectionId === 'duyurular' && text.includes('duyurular')) ||
                              (sectionId === 'gelistirmeler' && text.includes('gelistirmeler')) ||
                              (sectionId === 'daireler' && text.includes('daireler')) ||
                              (sectionId === 'borclar' && text.includes('borçlar')) ||
                              (sectionId === 'kullanicilar' && text.includes('kullanıcılar')) ||
                              (sectionId === 'araclar' && text.includes('araçlar'))
                          ) {
                              $(this).addClass('active');
                          }
                      });
                  }

                  // --- AJAX Form Gönderimi (Genel Fonksiyon) ---
                  $('form').on('submit', function(e) {
                      e.preventDefault();
                      var form = $(this);
                      var formData = new FormData(this);

                      $('#loader').css('display', 'flex'); // Loader göster

                      $.ajax({
                          url: 'admin_actions2.php',
                          type: 'POST',
                          data: formData,
                          processData: false,
                          contentType: false,
                          dataType: 'json',
                          success: function(response) {
                              $('#loader').hide();
                              if (response.status === 'success') {
                                  showAlert('success', response.message);

                                  // Sadece daireler seçimini ve tutar inputunu temizle
                                  if (form.attr('id') === 'form-borc-ekle') {
                                      form.find('select[name="daire_ids[]"]').val(null).trigger('change');
                                      form.find('input[name="tutar"]').val("");
                                  } else {
                                      form.trigger("reset"); // Diğer formlar için tam reset
                                  }

                                  // İlgili tabloları güncelle
                                  if (form.attr('id') === 'form-borc-ekle') loadTableData('borclar');
                                  if (form.attr('id') === 'form-daire-ekle') {
                                      loadTableData('daireler');
                                      loadDaireSelectOptions();
                                  }
                                  if (form.attr('id') === 'form-kullanici-ekle') loadTableData('kullanicilar');
                                  loadDashboardStats(); // İstatistikleri her işlemde yenile
                              } else {
                                  showAlert('danger', response.message);
                              }
                          },
                          error: function() {
                              $('#loader').hide();
                              showAlert('danger', 'Sunucu hatası oluştu!');
                          }
                      });
                  });

                  // --- Veri Çekme Fonksiyonları (Tabloları Doldur) ---
                  function loadTableData(type) {
                      // Sadece borclar tablosu için filtreleri sakla ve uygula
                      var filterDurum = null,
                          filterAra = null;
                      if (type === 'borclar') {
                          filterDurum = $('#filter-borc-durum').val();
                          filterAra = $('#filter-borc-ara').val();
                      }
                      $.ajax({
                          url: 'get_table_data.php',
                          type: 'POST',
                          data: {
                              type: type
                          },
                          success: function(html) {
                              $('#table-' + type + ' tbody').html(html);
                              // Tablo güncellendikten sonra filtreleri tekrar uygula
                              if (type === 'borclar') {
                                  if (filterDurum !== null) $('#filter-borc-durum').val(filterDurum);
                                  if (filterAra !== null) $('#filter-borc-ara').val(filterAra);
                                  // Filtre fonksiyonunu tetikle
                                  $('#filter-borc-durum, #filter-borc-ara').trigger('change');
                              }
                          }
                      });
                  }

                  function loadDashboardStats() {
                      $.ajax({
                          url: 'get_table_data.php',
                          type: 'POST',
                          data: {
                              type: 'stats'
                          },
                          success: function(html) {
                              $('#dashboard-stats-container').html(html);
                          }
                      });
                  }

                  // Borç Ekleme Kısmındaki Selectbox'ı doldur
                  function loadDaireSelectOptions() {
                      $.ajax({
                          url: 'get_table_data.php',
                          type: 'POST',
                          data: {
                              type: 'daire_options'
                          },
                          success: function(html) {
                              $('.select2-daireler').html(html);
                          }
                      });
                  }

                  // --- İşlem Butonları (Ödeme Yap, Sil vb.) ---

                  // Borç Ödeme
                  $(document).on('click', '.btn-ode', function() {
                      var id = $(this).data('id');
                      if (!confirm('Bu borç ödendi olarak işaretlenecek ve kasaya işlenecek. Onaylıyor musunuz?')) return;

                      $('#loader').css('display', 'flex');
                      $.post('admin_actions2.php', {
                          type: 'borc_ode',
                          id: id
                      }, function(response) {
                          $('#loader').hide();
                          // Yanıt JSON değilse manuel parse edelim (güvenlik için)
                          if (typeof response === 'string') response = JSON.parse(response);

                          if (response.status === 'success') {
                              showAlert('success', response.message);
                              loadTableData('borclar');
                              loadDashboardStats();
                          } else {
                              showAlert('danger', response.message);
                          }
                      }, 'json');
                  });


                  // --- Yardımcı Fonksiyonlar ---
                  function showAlert(type, message) {
                      var alertHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
                          message +
                          '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                      $('#alert-area').html(alertHtml);
                      // 5 saniye sonra kaybolsun
                      setTimeout(function() {
                          $('.alert').alert('close');
                      }, 5000);
                  }

                  // Basit Frontend Filtreleme (Borçlar İçin)
                  $('#filter-borc-durum, #filter-borc-ara').on('change keyup', function() {
                      var durum = $('#filter-borc-durum').val();
                      var text = $('#filter-borc-ara').val().toLowerCase();

                      $('#table-borclar tbody tr').each(function() {
                          var row = $(this);
                          var rowText = row.text().toLowerCase();
                          var rowDurum = row.find('.badge').text().toLowerCase(); // Ödendi/Ödenmedi metni

                          var show = true;
                          if (durum !== '') {
                              // Durum 1 (Ödendi) ise badge class success kontrolü veya metin kontrolü
                              var isOdendi = row.find('.badge-success').length > 0;
                              if (durum === '1' && !isOdendi) show = false;
                              if (durum === '0' && isOdendi) show = false;
                          }
                          if (text !== '' && !rowText.includes(text)) show = false;

                          row.toggle(show);
                      });
                  });

                  // 1. Araç Ekleme Formu Submit (Araçlar Sekmesi)
                  $('#form-arac-ekle').on('submit', function(e) {
                      e.preventDefault();
                      submitFormViaAjax($(this), function() {
                          loadTableData('araclar'); // Tabloyu yenile
                          loadDashboardStats(); // İstatistiği yenile
                      });
                  });

                  // 2. Modal İçindeki Hızlı Araç Ekleme Formu Submit
                  $('#form-modal-arac-ekle').on('submit', function(e) {
                      e.preventDefault();
                      submitFormViaAjax($(this), function() {
                          $('#modalHizliAracEkle').modal('hide'); // Modalı kapat
                          loadTableData('araclar');
                          loadDashboardStats();
                      });
                  });

                  // 3. Daire Listesindeki "Araç Ekle" Butonuna Tıklanınca
                  $(document).on('click', '.btn-arac-ekle-modal', function() {
                      var daireId = $(this).data('id');
                      var daireBilgi = $(this).data('info');

                      $('#modal_arac_daire_id').val(daireId);
                      $('#modal_arac_daire_bilgi').val(daireBilgi);

                      // Formu temizle
                      $('#form-modal-arac-ekle').find('input[name="plaka"]').val('');

                      $('#modalHizliAracEkle').modal('show');
                  });

                  // Yardımcı Fonksiyon: AJAX Form Gönderimi (Kod tekrarını önlemek için)
                  function submitFormViaAjax(form, successCallback) {
                      $('#loader').css('display', 'flex');
                      var formData = new FormData(form[0]);

                      $.ajax({
                          url: 'admin_actions2.php',
                          type: 'POST',
                          data: formData,
                          processData: false,
                          contentType: false,
                          dataType: 'json',
                          success: function(response) {
                              $('#loader').hide();
                              if (response.status === 'success') {
                                  showAlert('success', response.message);
                                  form.trigger("reset");
                                  if (form.find('.select2-daireler').length > 0) {
                                      $('.select2-daireler').val(null).trigger('change');
                                  }
                                  if (successCallback) successCallback();
                              } else {
                                  showAlert('danger', response.message);
                              }
                          },
                          error: function() {
                              $('#loader').hide();
                              showAlert('danger', 'Sunucu hatası!');
                          }
                      });
                  }

                  // 1. Daire Düzenleme Modalını Aç ve Doldur
                  $(document).on('click', '.btn-daire-duzenle-modal', function() {
                      // Butondaki dataları al
                      var id = $(this).data('id');
                      var blok = $(this).data('blok');
                      var kat = $(this).data('kat');
                      var numara = $(this).data('numara');
                      var kullanici = $(this).data('kullanici');

                      // Modal içindeki inputlara yerleştir
                      $('#edit_daire_id').val(id);
                      $('#edit_blok').val(blok);
                      $('#edit_kat').val(kat);
                      $('#edit_numara').val(numara);
                      $('#edit_kullanici_id').val(kullanici ? kullanici : 0);

                      // Modalı göster
                      $('#daireDuzenleModal').modal('show');
                  });

                  // 2. Daire Düzenleme Formu Submit
                  $('#form-daire-duzenle').on('submit', function(e) {
                      e.preventDefault();
                      submitFormViaAjax($(this), function() {
                          $('#daireDuzenleModal').modal('hide'); // Modalı kapat
                          loadTableData('daireler'); // Tabloyu yenile
                          loadDashboardStats(); // İstatistikleri yenile (Dolu/Boş değişmiş olabilir)
                      });
                  });

                  $(document).on('click', '.btn-telefon-yonet', function() {
                      var daireId = $(this).data('id');
                      var daireBilgi = $(this).data('info');

                      $('#modal_telefon_daire_id').val(daireId);
                      $('#modal_telefon_daire_bilgi').html('<strong>' + daireBilgi + '</strong> için kayıtlı numaralar:');

                      // Modal açılınca listeyi yükle
                      loadTelefonListesi(daireId);

                      $('#modalTelefonYonetimi').modal('show');
                  });

                  // 2. Telefon Ekleme Formu Submit
                  $('#form-telefon-ekle').on('submit', function(e) {
                      e.preventDefault();
                      // Daire ID'yi form verisine manuel ekle
                      var formData = new FormData(this);
                      formData.append('daire_id', $('#modal_telefon_daire_id').val());

                      $.ajax({
                          url: 'admin_actions2.php',
                          type: 'POST',
                          data: formData,
                          processData: false,
                          contentType: false,
                          dataType: 'json',
                          success: function(response) {
                              if (response.status === 'success') {
                                  $('#form-telefon-ekle')[0].reset(); // Formu temizle
                                  loadTelefonListesi($('#modal_telefon_daire_id').val()); // Listeyi yenile
                              } else {
                                  alert(response.message);
                              }
                          }
                      });
                  });

                  // 3. Telefon Silme Butonu
                  $(document).on('click', '.btn-telefon-sil', function() {
                      var id = $(this).data('id');
                      if (!confirm('Bu numarayı silmek istiyor musunuz?')) return;

                      $.post('admin_actions2.php', {
                          type: 'telefon_sil',
                          id: id
                      }, function(response) {
                          if (typeof response === 'string') response = JSON.parse(response);
                          if (response.status === 'success') {
                              loadTelefonListesi($('#modal_telefon_daire_id').val());
                          } else {
                              alert('Hata oluştu');
                          }
                      }, 'json');
                  });

                  // Yardımcı Fonksiyon: Telefon Listesini Getir
                  function loadTelefonListesi(daireId) {
                      $.post('get_table_data.php', {
                          type: 'telefon_listesi',
                          daire_id: daireId
                      }, function(html) {
                          $('#telefon-listesi-container').html(html);
                      });
                  }

                  // Geliştirme Düzenle Modalını Aç ve Doldur
                  $(document).on('click', '.btn-gelistirme-duzenle-modal', function() {
                      var id = $(this).data('id');
                      var baslik = $(this).data('baslik');
                      var aciklama = $(this).data('aciklama');
                      var durum = $(this).data('durum');
                      var oncelik = $(this).data('oncelik');
                      var sorumlu = $(this).data('sorumlu');
                      var hedef_tarih = $(this).data('hedef_tarih');

                      $('#edit_gelistirme_id').val(id);
                      $('#edit_gelistirme_baslik').val(baslik);
                      $('#edit_gelistirme_aciklama').val(aciklama);
                      $('#edit_gelistirme_durum').val(durum);
                      $('#edit_gelistirme_oncelik').val(oncelik);
                      $('#edit_gelistirme_sorumlu').val(sorumlu);
                      $('#edit_gelistirme_hedef_tarih').val(hedef_tarih);

                      // Mevcut dosyaları AJAX ile çek
                      $('#edit_gelistirme_dosyalar').html('<div class="text-muted">Yükleniyor...</div>');
                      $.post('get_table_data.php', {
                          type: 'gelistirme_detay',
                          id: id
                      }, function(data) {
                          if (data.dosyalar && data.dosyalar.length > 0) {
                              var html = '<ul class="list-group">';
                              data.dosyalar.forEach(function(file) {
                                  html += '<li class="list-group-item d-flex justify-content-between align-items-center">';
                                  html += '<a href="' + file.file_path + '" target="_blank">' + file.file_name + '</a>';
                                  html += '<div class="form-check ms-2"><input class="form-check-input" type="checkbox" name="dosya_sil[]" value="' + file.id + '" id="gelistirme_dosya_sil_' + file.id + '"><label class="form-check-label" for="gelistirme_dosya_sil_' + file.id + '">Sil</label></div>';
                                  html += '</li>';
                              });
                              html += '</ul>';
                              $('#edit_gelistirme_dosyalar').html(html);
                          } else {
                              $('#edit_gelistirme_dosyalar').html('<div class="text-muted">Ekli dosya yok.</div>');
                          }
                      }, 'json');

                      $('#modalGelistirmeDuzenle').modal('show');
                  });

                  // Geliştirme Düzenle Formu Submit (Çift tetiklenmeyi önle)
                  $('#form-gelistirme-duzenle').off('submit').on('submit', function(e) {
                      e.preventDefault();
                      var form = $(this);
                      var formData = new FormData(this);
                      $('#loader').css('display', 'flex');
                      $.ajax({
                          url: 'admin_actions2.php',
                          type: 'POST',
                          data: formData,
                          processData: false,
                          contentType: false,
                          dataType: 'json',
                          success: function(response) {
                              $('#loader').hide();
                              if (response.status === 'success') {
                                  showAlert('success', response.message);
                                  $('#modalGelistirmeDuzenle').modal('hide');
                                  loadTableData('gelistirmeler');
                              } else {
                                  showAlert('danger', response.message);
                              }
                          },
                          error: function() {
                              $('#loader').hide();
                              showAlert('danger', 'Sunucu hatası oluştu!');
                          }
                      });
                  });

                  // Duyuru Düzenle Modalını Aç ve Doldur (dosya listesiyle)
                  $(document).on('click', '.btn-duyuru-duzenle', function() {
                      var id = $(this).data('id');
                      var baslik = $(this).data('baslik');
                      var aciklama = $(this).data('aciklama');
                      var status = $(this).data('status');
                      $('#edit_duyuru_id').val(id);
                      $('#edit_duyuru_baslik').val(baslik);
                      $('#edit_duyuru_aciklama').val(aciklama);
                      $('#edit_duyuru_status').val(status);
                      // Dosyaları AJAX ile çek
                      $('#edit_duyuru_dosyalar').html('<div class="text-muted">Yükleniyor...</div>');
                      $.post('get_table_data.php', {
                          type: 'duyuru_detay',
                          id: id
                      }, function(data) {
                          if (data.dosyalar && data.dosyalar.length > 0) {
                              var html = '<ul class="list-group">';
                              data.dosyalar.forEach(function(file) {
                                  html += '<li class="list-group-item d-flex justify-content-between align-items-center">';
                                  html += '<a href="' + file.file_path + '" target="_blank">' + file.file_name + '</a>';
                                  html += '<div class="form-check ms-2"><input class="form-check-input" type="checkbox" name="dosya_sil[]" value="' + file.id + '" id="dosya_sil_' + file.id + '"><label class="form-check-label" for="dosya_sil_' + file.id + '">Sil</label></div>';
                                  html += '</li>';
                              });
                              html += '</ul>';
                              $('#edit_duyuru_dosyalar').html(html);
                          } else {
                              $('#edit_duyuru_dosyalar').html('<div class="text-muted">Ekli dosya yok.</div>');
                          }
                      }, 'json');
                      $('#modalDuyuruDuzenle').modal('show');
                  });

                  // Duyuru Düzenle Formu Submit (dosya desteğiyle)
                  $('#form-duyuru-duzenle').on('submit', function(e) {
                      e.preventDefault();
                      var form = $(this)[0];
                      var formData = new FormData(form);
                      formData.append('type', 'duyuru_guncelle');
                      $('#loader').css('display', 'flex');
                      $.ajax({
                          url: 'admin_actions2.php',
                          type: 'POST',
                          data: formData,
                          processData: false,
                          contentType: false,
                          dataType: 'json',
                          success: function(response) {
                              $('#loader').hide();
                              if (response.status === 'success') {
                                  $('#modalDuyuruDuzenle').modal('hide');
                                  showAlert('success', response.message);
                                  loadTableData('duyurular');
                              } else {
                                  showAlert('danger', response.message);
                              }
                          }
                      });
                  });
              </script>
      </body>

      </html>