-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 11 Eyl 2026, 01:58:40
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `database`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `baslik` varchar(255) NOT NULL,
  `aciklama` text NOT NULL,
  `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` varchar(100) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `announcements`
--

INSERT INTO `announcements` (`id`, `baslik`, `aciklama`, `modified`, `modified_by`, `status`) VALUES
(1, 'Sistem Bakım Duyurusu', 'Cumartesi günü 02:00 – 04:00 saatleri arasında sistem bakımı yapılacaktır.', '2025-01-10 14:30:00', 'admin', 1),
(3, 'Geçici Kesinti', 'Sunucu taşıma işlemleri nedeniyle kısa süreli erişim kesintisi yaşanabilir.', '2025-01-18 18:45:00', 'admin', 0),
(5, 'Merhaba', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Temporibus atque unde ex eos corporis ipsa ducimus ab possimus excepturi eum, architecto, tenetur natus vel esse, nam quod nulla modi. Rerum.', '2025-12-24 14:26:44', 'İbrahim Bostancı', 1),
(6, 'Yalıtım Kontrolü', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Officiis ipsum hic harum necessitatibus rerum voluptas doloremque cupiditate repellendus alias? Sequi odio debitis corrupti ut minima in saepe fugit eveniet! Minima?\r\nBlanditiis impedit repellendus, ipsam qui quaerat laboriosam non dolore, obcaecati, reiciendis tempora debitis alias natus molestias voluptate? Earum iste quisquam ipsam, illum consequuntur quos sequi. Nulla placeat reiciendis ducimus natus?\r\nFuga non incidunt id, ipsam suscipit nulla aspernatur atque quasi inventore ipsa repudiandae tempora delectus temporibus totam natus officia accusantium quam. At earum necessitatibus facilis fuga animi nulla, vitae reiciendis.\r\nNatus amet, quis minima illo voluptates perspiciatis, in optio officiis eius ea qui maxime, dolorum quasi commodi. Repudiandae praesentium, vitae ut cumque ullam ea ipsum tempora soluta culpa repellendus illo!\r\nVeniam delectus inventore praesentium, accusantium a blanditiis! Eos suscipit temporibus sed sint iste, nihil adipisci sunt enim aut, veniam nam quibusdam reprehenderit! Odio voluptas, distinctio eos esse praesentium tenetur accusamus.', '2025-12-24 14:50:56', 'İbrahim Bostancı', 1),
(8, 'Yalıtım Kontrolü', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Officiis ipsum hic harum necessitatibus rerum voluptas doloremque cupiditate repellendus alias? Sequi odio debitis corrupti ut minima in saepe fugit eveniet! Minima?\r\nBlanditiis impedit repellendus, ipsam qui quaerat laboriosam non dolore, obcaecati, reiciendis tempora debitis alias natus molestias voluptate? Earum iste quisquam ipsam, illum consequuntur quos sequi. Nulla placeat reiciendis ducimus natus?\r\nFuga non incidunt id, ipsam suscipit nulla aspernatur atque quasi inventore ipsa repudiandae tempora delectus temporibus totam natus officia accusantium quam. At earum necessitatibus facilis fuga animi nulla, vitae reiciendis.\r\nNatus amet, quis minima illo voluptates perspiciatis, in optio officiis eius ea qui maxime, dolorum quasi commodi. Repudiandae praesentium, vitae ut cumque ullam ea ipsum tempora soluta culpa repellendus illo!\r\nVeniam delectus inventore praesentium, accusantium a blanditiis! Eos suscipit temporibus sed sint iste, nihil adipisci sunt enim aut, veniam nam quibusdam reprehenderit! Odio voluptas, distinctio eos esse praesentium tenetur accusamus.', '2025-12-29 17:10:24', 'İbrahim Bostancı', 1);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `announcement_dosyalar`
--

CREATE TABLE `announcement_dosyalar` (
  `id` int(11) NOT NULL,
  `announcement_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Tablo döküm verisi `announcement_dosyalar`
--

INSERT INTO `announcement_dosyalar` (`id`, `announcement_id`, `file_path`) VALUES
(2, 8, 'uploads/announcements/69528b53420db_turizon-3d-logo.png');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `araclar`
--

CREATE TABLE `araclar` (
  `id` int(11) NOT NULL,
  `daire_id` int(11) DEFAULT NULL,
  `plaka` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `araclar`
--

INSERT INTO `araclar` (`id`, `daire_id`, `plaka`) VALUES
(1, 8, '34 GS 1905'),
(2, 9, '34 GS 1901'),
(3, 10, '34 FB 1907'),
(4, 12, '06 GS 1905'),
(5, 13, '35 GS 1905');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `borclar`
--

CREATE TABLE `borclar` (
  `id` int(11) NOT NULL,
  `daire_id` int(11) DEFAULT NULL,
  `yil` int(11) DEFAULT NULL,
  `ay` int(11) DEFAULT NULL,
  `kalem` enum('su','elektrik','aidat','internet','demirbas','Garaj Kumanda','Yuvarlama Farkı') DEFAULT NULL,
  `tutat` decimal(10,2) DEFAULT NULL,
  `durum` tinyint(4) DEFAULT 0,
  `odeme_tarihi` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `borclar`
--

INSERT INTO `borclar` (`id`, `daire_id`, `yil`, `ay`, `kalem`, `tutat`, `durum`, `odeme_tarihi`) VALUES
(1, 8, 2026, 1, 'elektrik', 2500.00, 1, '2026-01-15'),
(2, 9, 2026, 1, 'elektrik', 2500.00, 1, '2026-01-15'),
(3, 10, 2026, 1, 'elektrik', 2500.00, 1, '2026-01-15'),
(4, 12, 2026, 1, 'elektrik', 2500.00, 1, '2026-01-15'),
(5, 13, 2026, 1, 'elektrik', 2500.00, 1, '2026-01-15'),
(6, 8, 2026, 1, 'su', 1450.00, 1, '2026-01-15'),
(7, 9, 2026, 1, 'su', 1450.00, 1, '2026-01-15'),
(8, 10, 2026, 1, 'su', 1450.00, 1, '2026-01-15'),
(9, 12, 2026, 1, 'su', 1450.00, 1, '2026-01-15'),
(10, 13, 2026, 1, 'su', 1450.00, 1, '2026-01-15'),
(11, 8, 2026, 1, 'aidat', 750.00, 1, '2026-01-15'),
(12, 9, 2026, 1, 'aidat', 750.00, 1, '2026-01-15'),
(13, 10, 2026, 1, 'aidat', 750.00, 1, '2026-01-15'),
(14, 12, 2026, 1, 'aidat', 750.00, 1, '2026-01-15'),
(15, 13, 2026, 1, 'aidat', 750.00, 1, '2026-01-15'),
(16, 8, 2026, 2, 'aidat', 1000.00, 1, '2026-01-15'),
(17, 9, 2026, 2, 'aidat', 1000.00, 1, '2026-01-15'),
(18, 10, 2026, 2, 'aidat', 1000.00, 1, '2026-01-15'),
(19, 12, 2026, 2, 'aidat', 1000.00, 1, '2026-01-15'),
(20, 13, 2026, 2, 'aidat', 1000.00, 1, '2026-01-15'),
(21, 8, 2026, 2, 'su', 2000.00, 1, '2026-01-15'),
(22, 9, 2026, 2, 'su', 2000.00, 1, '2026-01-15'),
(23, 10, 2026, 2, 'su', 2000.00, 1, '2026-01-15'),
(24, 12, 2026, 2, 'su', 2000.00, 1, '2026-01-15'),
(25, 13, 2026, 2, 'su', 2000.00, 1, '2026-01-15'),
(26, 8, 2026, 2, 'elektrik', 3200.00, 1, '2026-01-15'),
(27, 9, 2026, 2, 'elektrik', 3200.00, 1, '2026-01-15'),
(28, 10, 2026, 2, 'elektrik', 3200.00, 1, '2026-01-15'),
(29, 12, 2026, 2, 'elektrik', 3200.00, 1, '2026-01-15'),
(30, 13, 2026, 2, 'elektrik', 3200.00, 1, '2026-01-15'),
(31, 8, 2026, 2, 'demirbas', 600.00, 1, '2026-01-15'),
(32, 9, 2026, 2, 'demirbas', 600.00, 1, '2026-01-15'),
(33, 10, 2026, 2, 'demirbas', 600.00, 1, '2026-01-15'),
(34, 12, 2026, 2, 'demirbas', 600.00, 0, NULL),
(35, 13, 2026, 2, 'demirbas', 600.00, 0, NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `daireler`
--

CREATE TABLE `daireler` (
  `id` int(11) NOT NULL,
  `blok` varchar(10) DEFAULT NULL,
  `kat` int(11) DEFAULT NULL,
  `numara` int(11) DEFAULT NULL,
  `kullanici_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `daireler`
--

INSERT INTO `daireler` (`id`, `blok`, `kat`, `numara`, `kullanici_id`) VALUES
(6, 'A', -1, 15, NULL),
(7, 'A', -1, 16, NULL),
(8, 'A', 0, 1, 2),
(9, 'A', 0, 2, 3),
(10, 'A', 1, 3, 5),
(12, 'A', 1, 4, 4),
(13, 'A', 2, 5, 6),
(14, 'A', 2, 6, 7),
(15, 'A', 3, 7, NULL),
(16, 'A', 3, 8, NULL),
(17, 'A', 4, 9, NULL),
(18, 'A', 4, 10, NULL),
(19, 'A', 5, 11, NULL),
(20, 'A', 5, 12, NULL),
(21, 'A', 6, 13, NULL),
(22, 'A', 6, 14, NULL),
(23, 'B', -1, 11, NULL),
(24, 'B', -1, 12, NULL),
(25, 'B', 0, 1, NULL),
(26, 'B', 0, 2, NULL),
(27, 'B', 1, 3, NULL),
(28, 'B', 1, 4, NULL),
(29, 'B', 2, 5, NULL),
(30, 'B', 2, 6, NULL),
(31, 'B', 3, 7, NULL),
(32, 'B', 3, 8, NULL),
(33, 'B', 4, 9, NULL),
(34, 'B', 4, 10, NULL),
(36, 'B', -2, 0, NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `daire_telefonlar`
--

CREATE TABLE `daire_telefonlar` (
  `id` int(11) NOT NULL,
  `daire_id` int(11) NOT NULL,
  `ad_soyad` varchar(100) DEFAULT NULL COMMENT 'Numaranın sahibi (Örn: Baba, Kiracı vb.)',
  `telefon` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `daire_telefonlar`
--

INSERT INTO `daire_telefonlar` (`id`, `daire_id`, `ad_soyad`, `telefon`) VALUES
(1, 8, 'Ahmet Bey', '05555555555'),
(2, 9, 'Furkan Bey', '05555555555'),
(3, 10, 'Hatip Bey', '05555555555'),
(4, 12, 'Kemal Bey', '05555555555'),
(5, 13, 'Sanem Hanım', '05555555555');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `gelirler`
--

CREATE TABLE `gelirler` (
  `id` int(11) NOT NULL,
  `daire_id` int(11) DEFAULT NULL,
  `borc_id` int(11) DEFAULT NULL,
  `miktar` decimal(10,2) NOT NULL,
  `odeme_tarihi` date NOT NULL,
  `odeme_yontemi` enum('nakit','banka','kredi_karti') DEFAULT 'nakit',
  `aciklama` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `gelistirmeler`
--

CREATE TABLE `gelistirmeler` (
  `id` int(11) NOT NULL,
  `baslik` varchar(255) DEFAULT NULL,
  `aciklama` text DEFAULT NULL,
  `durum` enum('Planlandı','Devam Ediyor','Tamamlandı') DEFAULT NULL,
  `oncelik` enum('Düşük','Orta','Yüksek') DEFAULT NULL,
  `sorumlu` varchar(100) DEFAULT NULL,
  `hedef_tarih` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `gelistirmeler`
--

INSERT INTO `gelistirmeler` (`id`, `baslik`, `aciklama`, `durum`, `oncelik`, `sorumlu`, `hedef_tarih`, `created_at`) VALUES
(6, 'Ahmet Veli', 'Merhaba Uranüs', 'Devam Ediyor', 'Yüksek', 'KURUMSAL KAYNAK PLANLAMA', '2028-12-08', '2025-12-19 11:04:43'),
(7, 'Aasdasda', 'Lorem ipsum dolor comet, some date', 'Planlandı', 'Düşük', 'KURUMSAL KAYNAK PLANLAMA', '2026-01-01', '2025-12-24 05:56:55'),
(8, 'asdadsa', 'dasdadadad', 'Devam Ediyor', 'Orta', 'Efe Geçkin', '2026-01-01', '2025-12-24 07:28:19'),
(9, 'Yalıtım Kontrolü', 'duvarlardaki yalıtımlar kontorl edilecek', 'Planlandı', 'Yüksek', 'İbrahim', '2026-01-05', '2025-12-24 08:01:13'),
(10, 'Yalıtım Kontrolü', 'yalıtımlar kontrol edilecek ', 'Planlandı', 'Yüksek', 'Efe Geçkin', '2026-01-02', '2025-12-24 08:03:37'),
(11, 'asdadad', 'sddadadssdsa', 'Tamamlandı', 'Düşük', 'Efe Geçkin', '2026-01-01', '2025-12-24 08:08:02'),
(12, 'Merhaba', 'asdsadadadsa', 'Devam Ediyor', 'Orta', 'Efe Geçkin', '2025-12-26', '2025-12-24 10:19:57'),
(13, 'MERHABA DÜNYA', 'KENDİM EKLEDİM', 'Devam Ediyor', 'Orta', 'Efe Geçkin', '2026-01-22', '2026-01-09 06:19:23');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `gelistirme_dosyalar`
--

CREATE TABLE `gelistirme_dosyalar` (
  `id` int(11) NOT NULL,
  `gelistirme_id` int(11) DEFAULT NULL,
  `dosya` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `gelistirme_dosyalar`
--

INSERT INTO `gelistirme_dosyalar` (`id`, `gelistirme_id`, `dosya`) VALUES
(7, 7, '1766555815_694b80a782424.jpg'),
(8, 8, '694b9613e96cc_i17_siyah.jpg'),
(9, 9, '694b9dc981038_indirim.jpg'),
(10, 10, '694b9e59624d5_img_692ec19d4f0069.68261741.jpg'),
(12, 12, '694bbe4d5cc8d_person.jpg'),
(13, 11, 'uploads/gelistirmeler/694bbf18be11f_i17pro_beyaz.jpg'),
(14, 11, 'uploads/gelistirmeler/694bbf18c9c72_i17pro_beyaz.jpg'),
(29, 13, 'uploads/gelistirmeler/69609dfebfe43_site_yonetim.png'),
(32, 13, 'uploads/gelistirmeler/69609f384fcec_site_yonetim.png');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `harcamalar`
--

CREATE TABLE `harcamalar` (
  `id` int(11) NOT NULL,
  `tarih` date NOT NULL,
  `miktar` decimal(10,2) NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `aciklama` text DEFAULT NULL,
  `belge_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `harcamalar`
--

INSERT INTO `harcamalar` (`id`, `tarih`, `miktar`, `kategori`, `aciklama`, `belge_path`, `created_at`) VALUES
(13, '2026-01-15', 12000.00, 'Elektrik', '', '', '2026-01-15 11:11:54'),
(14, '2026-01-15', 8000.00, 'Su', '', '', '2026-01-15 11:12:04'),
(16, '2026-01-15', 2000.00, 'Temizlik', '', '', '2026-01-15 11:12:39'),
(17, '2026-01-15', 1500.00, 'Bakım', '', '', '2026-01-15 11:12:47'),
(18, '2026-02-02', 12685.00, 'Elektrik', '', '', '2026-01-15 11:18:30'),
(19, '2026-02-02', 5942.00, 'Su', '', '', '2026-01-15 11:18:46'),
(20, '2026-02-02', 2000.00, 'Temizlik', '', '', '2026-01-15 11:19:07'),
(21, '2026-02-02', 2500.00, 'Bakım', '', '', '2026-01-15 11:19:17');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kasa`
--

CREATE TABLE `kasa` (
  `id` int(11) NOT NULL,
  `islem_tipi` enum('gelir','gider') NOT NULL,
  `miktar` decimal(10,2) NOT NULL,
  `aciklama` text DEFAULT NULL,
  `tarih` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `kasa`
--

INSERT INTO `kasa` (`id`, `islem_tipi`, `miktar`, `aciklama`, `tarih`, `created_at`) VALUES
(1, 'gider', 12000.00, 'Elektrik - ', '2026-01-15', '2026-01-15 11:11:54'),
(2, 'gider', 8000.00, 'Su - ', '2026-01-15', '2026-01-15 11:12:04'),
(4, 'gider', 2000.00, 'Temizlik - ', '2026-01-15', '2026-01-15 11:12:39'),
(5, 'gider', 1500.00, 'Bakım - ', '2026-01-15', '2026-01-15 11:12:47'),
(6, 'gelir', 750.00, 'Aidat Tahsilatı: Blok A Daire 5 (1/2026 - aidat)', '2026-01-15', '2026-01-15 11:14:56'),
(7, 'gelir', 750.00, 'Aidat Tahsilatı: Blok A Daire 4 (1/2026 - aidat)', '2026-01-15', '2026-01-15 11:14:58'),
(8, 'gelir', 750.00, 'Aidat Tahsilatı: Blok A Daire 3 (1/2026 - aidat)', '2026-01-15', '2026-01-15 11:14:59'),
(9, 'gelir', 750.00, 'Aidat Tahsilatı: Blok A Daire 2 (1/2026 - aidat)', '2026-01-15', '2026-01-15 11:14:59'),
(10, 'gelir', 750.00, 'Aidat Tahsilatı: Blok A Daire 1 (1/2026 - aidat)', '2026-01-15', '2026-01-15 11:15:01'),
(11, 'gelir', 1450.00, 'Aidat Tahsilatı: Blok A Daire 5 (1/2026 - su)', '2026-01-15', '2026-01-15 11:15:02'),
(12, 'gelir', 1450.00, 'Aidat Tahsilatı: Blok A Daire 4 (1/2026 - su)', '2026-01-15', '2026-01-15 11:15:02'),
(13, 'gelir', 1450.00, 'Aidat Tahsilatı: Blok A Daire 3 (1/2026 - su)', '2026-01-15', '2026-01-15 11:15:02'),
(14, 'gelir', 1450.00, 'Aidat Tahsilatı: Blok A Daire 2 (1/2026 - su)', '2026-01-15', '2026-01-15 11:15:03'),
(15, 'gelir', 1450.00, 'Aidat Tahsilatı: Blok A Daire 1 (1/2026 - su)', '2026-01-15', '2026-01-15 11:15:03'),
(16, 'gelir', 2500.00, 'Aidat Tahsilatı: Blok A Daire 5 (1/2026 - elektrik)', '2026-01-15', '2026-01-15 11:15:03'),
(17, 'gelir', 2500.00, 'Aidat Tahsilatı: Blok A Daire 4 (1/2026 - elektrik)', '2026-01-15', '2026-01-15 11:15:05'),
(18, 'gelir', 2500.00, 'Aidat Tahsilatı: Blok A Daire 3 (1/2026 - elektrik)', '2026-01-15', '2026-01-15 11:15:05'),
(19, 'gelir', 2500.00, 'Aidat Tahsilatı: Blok A Daire 2 (1/2026 - elektrik)', '2026-01-15', '2026-01-15 11:15:05'),
(20, 'gelir', 2500.00, 'Aidat Tahsilatı: Blok A Daire 1 (1/2026 - elektrik)', '2026-01-15', '2026-01-15 11:15:42'),
(21, 'gider', 12685.00, 'Elektrik - ', '2026-02-02', '2026-01-15 11:18:30'),
(22, 'gider', 5942.00, 'Su - ', '2026-02-02', '2026-01-15 11:18:46'),
(23, 'gider', 2000.00, 'Temizlik - ', '2026-02-02', '2026-01-15 11:19:07'),
(24, 'gider', 2500.00, 'Bakım - ', '2026-02-02', '2026-01-15 11:19:17'),
(25, 'gelir', 1000.00, 'Aidat Tahsilatı: Blok A Daire 1 (2/2026 - aidat)', '2026-01-15', '2026-01-15 11:20:39'),
(26, 'gelir', 1000.00, 'Aidat Tahsilatı: Blok A Daire 2 (2/2026 - aidat)', '2026-01-15', '2026-01-15 11:20:41'),
(27, 'gelir', 1000.00, 'Aidat Tahsilatı: Blok A Daire 3 (2/2026 - aidat)', '2026-01-15', '2026-01-15 11:20:41'),
(28, 'gelir', 1000.00, 'Aidat Tahsilatı: Blok A Daire 4 (2/2026 - aidat)', '2026-01-15', '2026-01-15 11:20:42'),
(29, 'gelir', 1000.00, 'Aidat Tahsilatı: Blok A Daire 5 (2/2026 - aidat)', '2026-01-15', '2026-01-15 11:20:42'),
(30, 'gelir', 2000.00, 'Aidat Tahsilatı: Blok A Daire 1 (2/2026 - su)', '2026-01-15', '2026-01-15 11:20:43'),
(31, 'gelir', 2000.00, 'Aidat Tahsilatı: Blok A Daire 2 (2/2026 - su)', '2026-01-15', '2026-01-15 11:20:44'),
(32, 'gelir', 2000.00, 'Aidat Tahsilatı: Blok A Daire 3 (2/2026 - su)', '2026-01-15', '2026-01-15 11:20:44'),
(33, 'gelir', 2000.00, 'Aidat Tahsilatı: Blok A Daire 4 (2/2026 - su)', '2026-01-15', '2026-01-15 11:20:45'),
(34, 'gelir', 2000.00, 'Aidat Tahsilatı: Blok A Daire 5 (2/2026 - su)', '2026-01-15', '2026-01-15 11:20:45'),
(35, 'gelir', 3200.00, 'Aidat Tahsilatı: Blok A Daire 1 (2/2026 - elektrik)', '2026-01-15', '2026-01-15 11:20:46'),
(36, 'gelir', 3200.00, 'Aidat Tahsilatı: Blok A Daire 2 (2/2026 - elektrik)', '2026-01-15', '2026-01-15 11:20:47'),
(37, 'gelir', 3200.00, 'Aidat Tahsilatı: Blok A Daire 3 (2/2026 - elektrik)', '2026-01-15', '2026-01-15 11:20:49'),
(38, 'gelir', 3200.00, 'Aidat Tahsilatı: Blok A Daire 4 (2/2026 - elektrik)', '2026-01-15', '2026-01-15 11:20:49'),
(39, 'gelir', 3200.00, 'Aidat Tahsilatı: Blok A Daire 5 (2/2026 - elektrik)', '2026-01-15', '2026-01-15 11:20:49'),
(40, 'gelir', 600.00, 'Aidat Tahsilatı: Blok A Daire 1 (2/2026 - demirbas)', '2026-01-15', '2026-01-15 11:20:50'),
(41, 'gelir', 600.00, 'Aidat Tahsilatı: Blok A Daire 2 (2/2026 - demirbas)', '2026-01-15', '2026-01-15 11:20:50'),
(42, 'gelir', 600.00, 'Aidat Tahsilatı: Blok A Daire 3 (2/2026 - demirbas)', '2026-01-15', '2026-01-15 11:20:51');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kvkk`
--

CREATE TABLE `kvkk` (
  `id` int(11) NOT NULL,
  `metin` longtext NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'KVKK metninin aktif olup olmadığı. 1: Aktif, 0: Pasif.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Tablo döküm verisi `kvkk`
--

INSERT INTO `kvkk` (`id`, `metin`, `status`) VALUES
(1, 'Bu aydınlatma metni, 6698 sayılı Kişisel Verilerin Korunması Kanununun 10 uncu maddesi ile Aydınlatma Yükümlülüğünün Yerine Getirilmesinde Uyulacak Usul ve Esaslar Hakkında Tebliğ kapsamında veri sorumlusu sıfatıyla Kişisel Verileri Koruma Kurumu (Kurum) tarafından hazırlanmıştır.\r\n\r\n	\r\nKurum tarafından, insan kaynakları süreçlerinin yönetilmesi, çalışanlar için iş akdi ve mevzuattan kaynaklı yükümlülüklerin yerine getirilmesi, çalışanlar için yan haklar ve menfaatleri süreçlerinin yürütülmesi, eğitim faaliyetlerinin yürütülmesi, iş sağlığı ve güvenliği faaliyetlerinin yürütülmesi ile sözleşme süreçlerinin yürütülmesi amacıyla özlük dosyaları kapsamında çalışanlara ait kişisel veriler (ad soyad, TC kimlik no, iletişim, diploma, adli sicil kaydı, eğitim, sağlık, mesleğe ilişkin veriler, mal beyanı, askerlik durumu, fotoğraf, sosyal güvenlik bilgileri, güvenlik soruşturması, izin bilgisi, disiplin bilgisi, bakmakla yükümlü olduğu kişilerin çalışma durumu, kimlik verileri, okul ve sağlık verileri, çocukların öz üvey olma durum bilgisi, çocukların cinsiyeti verileri) işlenmektedir.\r\n', 1);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `ad` varchar(50) NOT NULL,
  `soyad` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefon` varchar(15) DEFAULT NULL,
  `rol` enum('admin','kullanici') DEFAULT 'kullanici',
  `sifre_hash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `ad`, `soyad`, `email`, `telefon`, `rol`, `sifre_hash`) VALUES
(1, 'Admin', 'Kullanıcı', 'admin@mail.com', '5417673471', 'admin', '$2y$10$4UcO1xPAAPZjmL2xTRDVZuWbnLVk6KdHMlDCD8zWJq.gSRV3tbbfi'),
(2, 'Kullanici', 'Bir', 'deneme1@mail.com', NULL, 'kullanici', '$2y$10$iDF/ZFeaHygb6jSz1rsxbeJ.ZVLLXU.P.pbucS4wqHewJtIco4veG'),
(3, 'Kullanıcı', 'İki', 'deneme2@mail.com', NULL, 'kullanici', '$2y$10$b18zvFBPtZp/RPsm2osfEubNKAbrUjQodXr2Da3SS0TtpzwzhB.qW'),
(4, 'Kullanıcı', 'Üç', 'deneme3@mail.com', NULL, 'kullanici', '$2y$10$g5eqssfzkso74fkXid8Riu1lvE7.CGae.QBRBRhBU9f53Y1tMipzC'),
(5, 'Kullanıcı', 'Dört', 'deneme4@mail.com', NULL, 'kullanici', '$2y$10$k/In6O6LoxVvbpU3sN3rSuByE3gADugBzDJYhJJPChhyGMlaVMfdu'),
(6, 'Kullanıcı', 'Beş', 'deneme5@mail.com', NULL, 'kullanici', '$2y$10$gTr7zRNd9PcLSLp0WKx4aOcJ23EqKcYnYj3LC7rK36EDXywUKHv/S'),
(7, 'Samet', 'Yılmaz', 'samet@mail.com', NULL, 'kullanici', '$2y$10$nbKpwxnW803lS7Q8XS3zU.wakwDEPt.Jy/HhySfH1/gZM4o1yQpdu');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `user_kvkk_onay`
--

CREATE TABLE `user_kvkk_onay` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `kvkk_id` int(11) NOT NULL,
  `onay_tarihi` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Kullanıcıların hangi KVKK metnini ne zaman onayladığını tutar.';

--
-- Tablo döküm verisi `user_kvkk_onay`
--

INSERT INTO `user_kvkk_onay` (`id`, `user_id`, `kvkk_id`, `onay_tarihi`) VALUES
(24, 3, 1, '2026-01-15 14:21:21'),
(25, 7, 1, '2026-09-11 02:09:09'),
(26, 2, 1, '2026-09-11 02:15:37');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `announcement_dosyalar`
--
ALTER TABLE `announcement_dosyalar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `announcement_id` (`announcement_id`);

--
-- Tablo için indeksler `araclar`
--
ALTER TABLE `araclar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `daire_id` (`daire_id`);

--
-- Tablo için indeksler `borclar`
--
ALTER TABLE `borclar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `daire_id` (`daire_id`);

--
-- Tablo için indeksler `daireler`
--
ALTER TABLE `daireler`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kullanici_id` (`kullanici_id`);

--
-- Tablo için indeksler `daire_telefonlar`
--
ALTER TABLE `daire_telefonlar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `daire_id` (`daire_id`);

--
-- Tablo için indeksler `gelirler`
--
ALTER TABLE `gelirler`
  ADD PRIMARY KEY (`id`),
  ADD KEY `daire_id` (`daire_id`),
  ADD KEY `borc_id` (`borc_id`);

--
-- Tablo için indeksler `gelistirmeler`
--
ALTER TABLE `gelistirmeler`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `gelistirme_dosyalar`
--
ALTER TABLE `gelistirme_dosyalar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gelistirme_id` (`gelistirme_id`);

--
-- Tablo için indeksler `harcamalar`
--
ALTER TABLE `harcamalar`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `kasa`
--
ALTER TABLE `kasa`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `kvkk`
--
ALTER TABLE `kvkk`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Tablo için indeksler `user_kvkk_onay`
--
ALTER TABLE `user_kvkk_onay`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_kvkk_unique` (`user_id`,`kvkk_id`),
  ADD KEY `fk_user_kvkk_kvkk` (`kvkk_id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Tablo için AUTO_INCREMENT değeri `announcement_dosyalar`
--
ALTER TABLE `announcement_dosyalar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `araclar`
--
ALTER TABLE `araclar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Tablo için AUTO_INCREMENT değeri `borclar`
--
ALTER TABLE `borclar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Tablo için AUTO_INCREMENT değeri `daireler`
--
ALTER TABLE `daireler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- Tablo için AUTO_INCREMENT değeri `daire_telefonlar`
--
ALTER TABLE `daire_telefonlar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Tablo için AUTO_INCREMENT değeri `gelirler`
--
ALTER TABLE `gelirler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `gelistirmeler`
--
ALTER TABLE `gelistirmeler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Tablo için AUTO_INCREMENT değeri `gelistirme_dosyalar`
--
ALTER TABLE `gelistirme_dosyalar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Tablo için AUTO_INCREMENT değeri `harcamalar`
--
ALTER TABLE `harcamalar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Tablo için AUTO_INCREMENT değeri `kasa`
--
ALTER TABLE `kasa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- Tablo için AUTO_INCREMENT değeri `kvkk`
--
ALTER TABLE `kvkk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Tablo için AUTO_INCREMENT değeri `user_kvkk_onay`
--
ALTER TABLE `user_kvkk_onay`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `announcement_dosyalar`
--
ALTER TABLE `announcement_dosyalar`
  ADD CONSTRAINT `announcement_dosyalar_ibfk_1` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `araclar`
--
ALTER TABLE `araclar`
  ADD CONSTRAINT `araclar_ibfk_1` FOREIGN KEY (`daire_id`) REFERENCES `daireler` (`id`);

--
-- Tablo kısıtlamaları `borclar`
--
ALTER TABLE `borclar`
  ADD CONSTRAINT `borclar_ibfk_1` FOREIGN KEY (`daire_id`) REFERENCES `daireler` (`id`);

--
-- Tablo kısıtlamaları `daireler`
--
ALTER TABLE `daireler`
  ADD CONSTRAINT `daireler_ibfk_1` FOREIGN KEY (`kullanici_id`) REFERENCES `users` (`id`);

--
-- Tablo kısıtlamaları `daire_telefonlar`
--
ALTER TABLE `daire_telefonlar`
  ADD CONSTRAINT `daire_telefonlar_ibfk_1` FOREIGN KEY (`daire_id`) REFERENCES `daireler` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `gelirler`
--
ALTER TABLE `gelirler`
  ADD CONSTRAINT `gelirler_ibfk_1` FOREIGN KEY (`daire_id`) REFERENCES `daireler` (`id`),
  ADD CONSTRAINT `gelirler_ibfk_2` FOREIGN KEY (`borc_id`) REFERENCES `borclar` (`id`);

--
-- Tablo kısıtlamaları `gelistirme_dosyalar`
--
ALTER TABLE `gelistirme_dosyalar`
  ADD CONSTRAINT `gelistirme_dosyalar_ibfk_1` FOREIGN KEY (`gelistirme_id`) REFERENCES `gelistirmeler` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `user_kvkk_onay`
--
ALTER TABLE `user_kvkk_onay`
  ADD CONSTRAINT `fk_user_kvkk_kvkk` FOREIGN KEY (`kvkk_id`) REFERENCES `kvkk` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user_kvkk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
