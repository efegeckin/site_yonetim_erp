<!-- Mobil Menü -->
<nav style="background-color: #2c3e50;" class="navbar navbar-dark d-md-none">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <span class="navbar-brand mx-auto"><i class="fas fa-building me-2"></i>Site ERP</span>
        </div>
    </nav>

    <!-- Butona tıklandığında açılan kısım -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="mobileSidebarLabel"><i class="fas fa-building me-2"></i>Site ERP</h5>
            <button type="button" class="btn text-bg-danger" data-bs-dismiss="offcanvas" aria-label="Close"><i class="fa-solid fa-xmark fs-5"></i></button>
        </div>
        <div class="offcanvas-body">
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link active" onclick="showSection('dashboard');" data-bs-dismiss="offcanvas"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" onclick="showSection('daireler');" data-bs-dismiss="offcanvas"><i class="fas fa-home me-2"></i>Daireler</a></li>
                <li class="nav-item"><a class="nav-link" onclick="showSection('borclar');" data-bs-dismiss="offcanvas"><i class="fas fa-money-bill-wave me-2"></i>Borçlar</a></li>
                <li class="nav-item"><a class="nav-link" onclick="showSection('kullanicilar');" data-bs-dismiss="offcanvas"><i class="fas fa-users me-2"></i>Kullanıcılar</a></li>
                <li class="nav-item"><a class="nav-link" onclick="showSection('araclar');" data-bs-dismiss="offcanvas"><i class="fas fa-car me-2"></i>Araçlar</a></li>
                <li class="nav-item"><a class="nav-link" onclick="window.location.href='kasa.php'" href="kasa.php" data-bs-dismiss="offcanvas"><i class="fas fa-car me-2"></i>Kasa</a></li>
            </ul>
        </div>
    </div>