// Modal kapandıktan sonra odak body'e aktar
$("#duyuruModal").on("hidden.bs.modal", function () {
  $("body").focus();
  // Bootstrap bazen overlay ve scroll'u bırakabiliyor, bunları temizle
  $("body").removeClass("modal-open");
  if ($(".modal-backdrop").length) $(".modal-backdrop").remove();
  $("body").css("padding-right", "");
});

// Sadece AJAX ile duyuru detayını çekip modalı dolduran kod kalsın
$(document).on("click", ".duyuru-item[data-id]", function () {
  var duyuruId = $(this).data("id");
  $.post(
    "get_table_data.php",
    {
      type: "duyuru_detay",
      id: duyuruId,
    },
    function (data) {
      $("#duyuruModalBaslik").text(data.baslik);
      $("#duyuruModalAciklama").text(data.aciklama);
      $("#duyuruModalmodified").text(
        data.modified ? "Güncelleme: " + data.modified : ""
      );
      $("#duyuruModalmodifiedBy").text(
        data.modified_by ? "Güncelleyen: " + data.modified_by : ""
      );
      if (data.image_path) {
        $("#duyuruModalImage").attr("src", data.image_path).show();
      } else {
        $("#duyuruModalImage").hide();
      }
      $("#duyuruModalFiles").html(renderDuyuruFiles(data.dosyalar));
      var modal = new bootstrap.Modal(document.getElementById("duyuruModal"));
      modal.show();
    },
    "json"
  );
});

function renderDuyuruFiles(dosyalar) {
  let filesHtml = "";
  if (dosyalar && dosyalar.length > 0) {
    filesHtml = '<b>Ek Dosyalar:</b><ul class="list-unstyled">';
    dosyalar.forEach(function (file) {
      let fileExt = file.file_name.split(".").pop().toLowerCase();
      let isImage = ["jpg", "jpeg", "png", "gif", "webp", "bmp"].includes(
        fileExt
      );

      if (isImage) {
        filesHtml +=
          '<li><img src="' +
          file.file_path +
          '" alt="' +
          file.file_name +
          '" class="thumb" data-full="' +
          file.file_path +
          '" style="max-width:80px; max-height:80px; border-radius:4px; margin-right:8px; cursor:pointer;"></li>';
      } else {
        filesHtml +=
          '<li><a href="' +
          file.file_path +
          '" target="_blank"><i class="fas fa-paperclip"></i> ' +
          file.file_name +
          "</a></li>";
      }
    });
    filesHtml += "</ul>";
  }
  return filesHtml;
}

$(document).on("click", ".thumb", function () {
  let imgSrc = $(this).data("full") || $(this).attr("src");
  $("#modalImage").attr("src", imgSrc);
  $("#imgModal").css("display", "flex");
});

$(document).ready(function () {
  // Site Harcama Listesi filtreleme (Tarih)
  function filterHarcamaTable() {
    let tarihFilter = $('input[name="harcamaTarih"]:checked').attr("id");
    let now = new Date();
    let rows = $(".card-header.bg-warning:contains('Site Harcama Listesi')")
      .parent()
      .find("table tbody tr");
    let shown = 0;
    rows.each(function () {
      let show = true;
      let tds = $(this).find("td");
      if (tds.length < 5) {
        $(this).show();
        return;
      } // skip empty/info rows
      // Tarih (ilk sütun)
      let tarihText = tds.eq(0).text().trim();
      let [gun, ay, yil] = tarihText.split(".");
      let rowDate = new Date(
        parseInt(yil, 10),
        parseInt(ay, 10) - 1,
        parseInt(gun, 10)
      );

      if (tarihFilter === "harcamaSon3ay") {
        let threeMonthsAgo = new Date(now.getFullYear(), now.getMonth() - 2, 1);
        if (rowDate < threeMonthsAgo) show = false;
      } else if (tarihFilter === "harcamaSon1yil") {
        let oneYearAgo = new Date(now.getFullYear() - 1, now.getMonth(), 1);
        if (rowDate < oneYearAgo) show = false;
      }
      // Son 10: sadece ilk 10 satırı göster
      if (show) {
        if (tarihFilter === "harcamaSon10" && shown >= 10) {
          $(this).hide();
        } else {
          $(this).show();
          shown++;
        }
      } else {
        $(this).hide();
      }
    });
  }

  $(document).on("change", 'input[name="harcamaTarih"]', filterHarcamaTable);
  filterHarcamaTable();
  // Borç Listesi filtreleme (Tarih ve Durum)
  function filterBorcTable() {
    // Tarih filtresi
    let tarihFilter = $('input[name="borcTarih"]:checked').attr("id");
    // Durum filtresi
    let durumFilter = $('input[name="borcDurum"]:checked').attr("id");

    let now = new Date();
    let rows = $("#borcTable tbody tr");
    let shown = 0;
    rows.each(function () {
      let show = true;
      let tds = $(this).find("td");
      if (tds.length < 4) {
        $(this).show();
        return;
      } // skip empty/info rows
      // Yıl/Ay
      let yilAy = tds.eq(0).text().trim();
      let [yil, ay] = yilAy.split("-");
      yil = parseInt(yil, 10);
      ay = parseInt(ay, 10);
      let rowDate = new Date(yil, ay - 1, 1);

      // Tarih filtresi
      if (tarihFilter === "son3ay") {
        let threeMonthsAgo = new Date(now.getFullYear(), now.getMonth() - 2, 1);
        if (rowDate < threeMonthsAgo) show = false;
      } else if (tarihFilter === "son1yil") {
        let oneYearAgo = new Date(now.getFullYear() - 1, now.getMonth(), 1);
        if (rowDate < oneYearAgo) show = false;
      }
      // Son 10: sadece ilk 10 satırı göster
      // Son 10, shown sayısı ile kontrol edilir

      // Durum filtresi
      let durumText = tds.eq(3).text().toLowerCase();
      if (durumFilter === "odendi" && !durumText.includes("ödendi"))
        show = false;
      if (durumFilter === "odenmedi" && !durumText.includes("ödenmedi"))
        show = false;

      if (show) {
        if (tarihFilter === "son10" && shown >= 10) {
          $(this).hide();
        } else {
          $(this).show();
          shown++;
        }
      } else {
        $(this).hide();
      }
    });
  }

  // Filtre değişimlerinde çalıştır
  $(document).on(
    "change",
    'input[name="borcTarih"], input[name="borcDurum"]',
    filterBorcTable
  );
  // Sayfa yüklenince de uygula
  filterBorcTable();

  // Otomatik bildirim kapatma
  setTimeout(function () {
    $(".notification").fadeOut(function () {
      // fadeOut sonrası DOM'dan kaldır (varsa)
      if (this && this.remove) this.remove();
    });
  }, 5000);

  // Resim modalı
  $(".thumb").on("click", function () {
    let imgSrc = $(this).data("full") || $(this).attr("src");
    $("#modalImage").attr("src", imgSrc);
    $("#imgModal").css("display", "flex");
  });

  // Modal kapatma
  $("#closeModal, #imgModal").on("click", function (e) {
    if (e.target === this || $(e.target).hasClass("modal-close")) {
      $("#imgModal").fadeOut(200);
    }
  });

  // ESC tuşu ile modal kapatma
  $(document).on("keydown", function (e) {
    if (e.key === "Escape") {
      $("#imgModal").fadeOut(200);
      if (typeof closeModal === "function") closeModal();
      if (typeof closeDeleteModal === "function") closeDeleteModal();
    }
  });

  // Dosya yükleme önizleme
  $("#dosyalar").on("change", function () {
    let files = $(this)[0].files;
    let preview = $("#filePreview");
    preview.empty();

    for (let i = 0; i < files.length; i++) {
      let file = files[i];
      let reader = new FileReader();

      reader.onload = function (e) {
        let fileType = file.type.split("/")[0];
        let icon = "fa-file";

        if (fileType === "image") icon = "fa-file-image";
        else if (fileType === "application") {
          if (file.type.includes("pdf")) icon = "fa-file-pdf";
          else if (file.type.includes("word")) icon = "fa-file-word";
        }

        preview.append(`
            <div class="file-preview-item">
                <i class="fas ${icon}"></i>
                <span>${file.name}</span>
            </div>
        `);
      };

      reader.readAsDataURL(file);
    }
  });

  // Form modalı açıldığında tarihi bugün yap
  $("#hedef_tarih").val(new Date().toISOString().split("T")[0]);
});

$(document).ready(function () {
  // ...existing code...

  // Yıl filtreleme (AJAX sonrası da çalışsın diye)
  $(document).on("input", "#gelistirmeYilFilter", function () {
    let yil = $(this).val().trim();
    $(".kanban").each(function (idx) {
      let hedefYil = "";
      let bulundu = false;
      $(this)
        .find("small.text-muted")
        .each(function () {
          let tarihText = $(this).text().trim();
          let match = tarihText.match(/(\d{4})/);
          if (match) {
            hedefYil = match[1];
            bulundu = true;
          }
        });
      if (yil === "" || hedefYil === yil) {
        $(this).show();
      } else {
        $(this).hide();
      }
    });
  });

  // ...existing code...
});

// Resme tıklayınca büyüt
$(document).on("click", ".thumb", function () {
  let imgSrc = $(this).data("full") || $(this).attr("src");
  $("#modalImage").attr("src", imgSrc);
  $("#imgModal").css("display", "flex");
});
$(document).on("click", "#closeModal, #imgModal", function (e) {
  if (e.target.id === "closeModal" || e.target.id === "imgModal") {
    $("#imgModal").css("display", "none");
    $("#modalImage").attr("src", "");
  }
});

$(document).ready(function () {
  function addBorcIndirListener() {
    const borcIndirBtn = document.getElementById("borc-indir");
    if (borcIndirBtn) {
      borcIndirBtn.onclick = function () {
        let dosyaAdi = ad ? `borc_listesi_${ad}.xlsx` : "borc_listesi.xlsx";
        exportTableWithSheetJS("borcTable", dosyaAdi);
      };
    }
  }

  // Eğer tek daire varsa otomatik yükle
  var tekDaireId = document.getElementById("tekDaireId");
  if (tekDaireId) {
    var daire_id = tekDaireId.value;
    $.post(
      "get_user_daire_data.php",
      {
        daire_id: daire_id,
      },
      function (html) {
        $("#kullanici-daire-bilgileri").html(html).removeClass("d-none");
        addBorcIndirListener();
      }
    );
  } else {
    // Daire seçimi değiştiğinde verileri güncelle
    $("#daireSec").on("change", function () {
      var daire_id = $(this).val();
      if (!daire_id) return;
      $.post(
        "get_user_daire_data.php",
        {
          daire_id: daire_id,
        },
        function (html) {
          $("#kullanici-daire-bilgileri").html(html).removeClass("d-none");
          addBorcIndirListener();
        }
      );
    });
  }
});

// Türkçe karakterleri ASCII'ye çeviren fonksiyon
function turkceToAscii(str) {
  return str
    .replace(/ç/g, "c")
    .replace(/Ç/g, "c")
    .replace(/ğ/g, "g")
    .replace(/Ğ/g, "g")
    .replace(/ı/g, "i")
    .replace(/İ/g, "i")
    .replace(/ö/g, "o")
    .replace(/Ö/g, "o")
    .replace(/ş/g, "s")
    .replace(/Ş/g, "s")
    .replace(/ü/g, "u")
    .replace(/Ü/g, "u");
}

let ad = "";
if (
  typeof window.kullaniciAdSoyad !== "undefined" &&
  window.kullaniciAdSoyad.trim() !== ""
) {
  ad = window.kullaniciAdSoyad.trim();
} else {
  const kullanici = document.getElementById("kullanici");
  if (kullanici && kullanici.textContent.trim() !== "") {
    ad = kullanici.textContent.trim();
  } else {
    ad = "kullanici";
  }
}
// Dosya adı için Türkçe karakterleri ASCII'ye çevir ve boşlukları kaldır
ad = turkceToAscii(ad.replace(/\s+/g, ""));

// KVKK işlemleri (elementler varsa ekle)

const kvkkRadio = document.getElementById("kvkk_onay");
const kvkkOnayHidden = document.getElementById("kvkk_onay_hidden");
const kvkkBtn = document.getElementById("kvkkBtn");
const kvkkSifreBtn = document.getElementById("kvkkSifreBtn");
const kvkkSbmt = document.getElementById("kvkkSbmt");
const kvkkBtnText = document.getElementById("kvkkBtnText");
const kvkkSifreBtnText = document.getElementById("kvkkSifreBtnText");
const sifreDegis = document.getElementById("sifreDegis");
const kvkk = document.getElementById("kvkk");
const yeniSifre = document.getElementById("yeni_sifre");
const yeniSifreTekrar = document.getElementById("yeni_sifre_tekrar");

if (kvkkBtn) {
  kvkkBtn.type = "button";
}

if (kvkkRadio && kvkkOnayHidden && kvkkBtn && kvkkBtnText) {
  let degis = false;
  kvkkBtn.addEventListener("click", function (e) {
    if (kvkkRadio.checked) {
      kvkkOnayHidden.value = "1";
      //sayfayı yenile
      location.reload();
    } else {
      e.preventDefault();
      if (!degis) {
        kvkkBtnText.innerHTML =
          " KVKK metnini kabul etmesseniz çıkış yapılacaktır!";
        degis = true;
      } else {
        this.closest(".modal").style.display = "none";
        location.href = "logout.php";
      }
    }
  });

  kvkkRadio.addEventListener("change", function () {
    if (this.checked) {
      sifreDegis.style.transition = "all 0.5s";
      sifreDegis.style.display = "flex";
      sifreDegis.style.opacity = "1";

      kvkk.style.transition = "opacity 0.5s";
      kvkk.style.opacity = "0.0";
      kvkk.style.display = "none";

      kvkkSifreBtnText.innerHTML = " Lütfen yeni şifrenizi belirleyiniz!";
      kvkkSifreBtn.type = "button";
    }
  });

  kvkkSifreBtn.addEventListener("click", function (e) {
    if (yeniSifre.value === "" || yeniSifreTekrar.value === "") {
      e.preventDefault();
      kvkkSifreBtnText.innerHTML = " Lütfen şifre alanlarını doldurunuz!";
      return;
    } else if (yeniSifre.value !== yeniSifreTekrar.value) {
      e.preventDefault();
      kvkkSifreBtnText.innerHTML =
        " Şifreler uyuşmuyor, lütfen kontrol ediniz!";
      return;
    }
    // Şifre uzunluk kontrolü
    else if (yeniSifre.value.length < 6) {
      e.preventDefault();
      kvkkSifreBtnText.innerHTML = " Şifre en az 8 karakter olmalıdır!";
      return;
    } else {
      kvkkSifreBtnText.innerHTML = "";

      // Her şey tamam, formu gönder
      kvkkSbmt.submit();
    }
  });
}

//Tabloyu excele aktarma işlemi
// SheetJS ile tabloyu Excel olarak indir
function exportTableWithSheetJS(tableId, filename) {
  const table = document.getElementById(tableId);
  if (!table) {
    alert("Tablo bulunamadı!");
    return;
  }
  // Tabloyu SheetJS ile JSON'a çevir
  const wb = XLSX.utils.table_to_book(table, { sheet: "Borç Listesi" });
  const ws = wb.Sheets["Borç Listesi"];
  // Sütun genişlikleri
  ws["!cols"] = [
    { wch: 12 }, // Yıl/Ay
    { wch: 16 }, // Kalem
    { wch: 14 }, // Tutar
    { wch: 12 }, // Durum
  ];
  // Başlık satırını kalın ve mavi arka planlı yap
  for (let C = 0; C < 4; ++C) {
    const cell = XLSX.utils.encode_cell({ r: 0, c: C });
    if (ws[cell])
      ws[cell].s = {
        font: { bold: true, color: { rgb: "FFFFFF" } },
        fill: { fgColor: { rgb: "0070C0" } }, // Daha canlı mavi
        alignment: { horizontal: "center" },
      };
  }
  // Satırları stillendir
  const range = XLSX.utils.decode_range(ws["!ref"]);
  for (let R = 1; R <= range.e.r; ++R) {
    // Para sütunu (2. sütun)
    const paraCell = XLSX.utils.encode_cell({ r: R, c: 2 });
    if (ws[paraCell]) {
      ws[paraCell].s = {
        numFmt: "#,##0.00 TL",
        font: { color: { rgb: "228B22" } }, // Daha canlı yeşil
        alignment: { horizontal: "right" },
      };
    }
    // Durum sütunu (3. sütun)
    const durumCell = XLSX.utils.encode_cell({ r: R, c: 3 });
    if (ws[durumCell]) {
      let val = ws[durumCell].v ? ws[durumCell].v.toString().toLowerCase() : "";
      let fillColor = val.includes("ödendi")
        ? "A9D08E"
        : val.includes("ödenmedi")
        ? "F4B084"
        : "FFFFFF";
      let fontColor = val.includes("ödendi")
        ? "006100"
        : val.includes("ödenmedi")
        ? "9C0006"
        : "000000";
      ws[durumCell].s = {
        fill: { fgColor: { rgb: fillColor } },
        font: { bold: true, color: { rgb: fontColor } },
        alignment: { horizontal: "center" },
      };
    }
  }
  XLSX.writeFile(
    wb,
    filename.endsWith(".xlsx") ? filename : filename.replace(/\.csv$/, ".xlsx")
  );
}

const borcIndirBtn = document.getElementById("borc-indir");
if (borcIndirBtn) {
  borcIndirBtn.onclick = function () {
    let dosyaAdi = ad ? `borc_listesi_${ad}.xlsx` : "borc_listesi.xlsx";
    exportTableWithSheetJS("borcTable", dosyaAdi);
  };
}

// Silme işlemi (geliştirme ve duyuru)
$(document).on("click", ".btn-sil", function () {
  var id = $(this).data("id");
  var type = $(this).data("type");
  if (type === "gelistirme") {
    if (!confirm("Bu geliştirme kaydını silmek istediğinize emin misiniz?"))
      return;
    $.post(
      "admin_actions2.php",
      {
        type: "gelistirme_sil",
        id: id,
      },
      function (response) {
        console.log("Silme response:", response);
        if (response.status === "success") {
          alert("Geliştirme başarıyla silindi!");
          location.reload();
        } else {
          alert("Silme başarısız: " + response.message);
        }
      },
      "json"
    );
  } else if (type === "duyuru") {
    if (!confirm("Bu duyuruyu silmek istediğinize emin misiniz?")) return;
    $.post(
      "admin_actions2.php",
      {
        type: "duyuru_sil",
        id: id,
      },
      function (response) {
        console.log("Silme response:", response);
        if (response.status === "success") {
          alert("Duyuru başarıyla silindi!");
          location.reload();
        } else {
          alert("Silme başarısız: " + response.message);
        }
      },
      "json"
    );
  } else if (type === "borc") {
    if (!confirm("Bu borcu silmek istediğinize emin misiniz?")) return;
    $.post(
      "admin_actions2.php",
      {
        type: "borc_sil",
        id: id,
      },
      function (response) {
        console.log("Silme response:", response);
        if (response.status === "success") {
          alert("Borç başarıyla silindi!");
          location.reload();
        } else {
          alert("Silme başarısız: " + response.message);
        }
      },
      "json"
    );
  }
});
