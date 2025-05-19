-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: May 20, 2025 at 01:39 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `swift_invoice_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `business_types`
--

CREATE TABLE `business_types` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `business_types`
--

INSERT INTO `business_types` (`id`, `name`) VALUES
(1, 'S.A. de C.V.'),
(2, 'S. de R.L.'),
(3, 'S. de R.L. de C.V.'),
(4, 'S.C.'),
(5, 'A.C.'),
(6, 'S.N.C.'),
(7, 'S.A.P.I. de C.V.'),
(8, 'S.A.'),
(9, 'S.R.L.'),
(10, 'Cooperativa');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `mother_last_name` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `rfc` varchar(13) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `first_name`, `last_name`, `mother_last_name`, `phone`, `email`, `rfc`, `address`, `created_at`) VALUES
(2, 'PANFILOMENO', 'MOMICHIS', 'INSANO', '6624560923', 'momichiscorjjp@gmail.com', 'MOM123124215', 'AV DEL PARQUE S/N MESA DE OTAY, 22430, TIJUANA, BC, MEXICO.', '2025-05-10 10:54:57'),
(3, 'JUAN', 'TORRES', 'VASQUEZ', '6641234567', 'jtorres@gmail.com', 'JAM123124213', 'COL LAS TORRES, 22476. TIJUANA, BAJA CALIFORNIA.', '2025-05-11 12:20:52'),
(4, 'ABRAHAM', 'ESTRADA', 'SOLANO', '6631236878', 'aleluyo@gmail.com', 'ESSA04102911', 'BLVD ALBERTO LIMON PADILLA S/N MESA DE OTAY', '2025-05-13 21:11:12');

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` int(11) NOT NULL,
  `business_name` varchar(100) NOT NULL,
  `rfc` varchar(13) NOT NULL,
  `fiscal_address` text NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `legal_representative` varchar(100) DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `business_type_id` int(11) DEFAULT NULL,
  `tax_regime_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `business_name`, `rfc`, `fiscal_address`, `phone`, `email`, `legal_representative`, `logo_path`, `created_at`, `business_type_id`, `tax_regime_id`) VALUES
(2, 'EVASION DE IMPUESTOS', 'DIDD12345677', 'AV DEL PARQUE S/N MESA DE OTAY', '6631236878', 'insanos@gmail.com', 'OMAR DIDDY', NULL, '2025-05-18 19:32:48', 1, 9),
(3, 'EVASION DE IMPUESTOS', 'DIDD12345677', 'AV DEL PARQUE MESA DE OTAY', '6631236878', 'aleluyo@gmail.com', 'OMAR DIDDY', NULL, '2025-05-18 19:34:42', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `invoice_number` varchar(20) NOT NULL,
  `invoice_date` date NOT NULL,
  `pdf_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `type` enum('Producto','Servicio') NOT NULL DEFAULT 'Producto',
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `type`, `price`, `created_at`) VALUES
(1, 'Servicios médicos (consultas)', 'Servicios médicos (consultas)', 'Producto', 500.00, '2025-05-10 11:15:06'),
(2, 'Exámenes médicos laborales', 'Exámenes médicos laborales', 'Producto', 600.00, '2025-05-10 11:15:06'),
(3, 'Servicios de diseño gráfico', 'Servicios de diseño gráfico', 'Producto', 1200.00, '2025-05-10 11:15:06'),
(4, 'Servicios de fotografía comercial', 'Servicios de fotografía comercial', 'Producto', 1100.00, '2025-05-10 11:15:06'),
(5, 'Servicios de instalación eléctrica', 'Servicios de instalación eléctrica', 'Producto', 1500.00, '2025-05-10 11:15:06'),
(6, 'Servicios de mantenimiento de edificios', 'Servicios de mantenimiento de edificios', 'Producto', 1300.00, '2025-05-10 11:15:06'),
(7, 'Servicios educativos y capacitación', 'Servicios educativos y capacitación', 'Producto', 1000.00, '2025-05-10 11:15:06'),
(8, 'Servicios de imprenta comercial', 'Servicios de imprenta comercial', 'Producto', 950.00, '2025-05-10 11:15:06'),
(9, 'Servicios de reclutamiento de personal', 'Servicios de reclutamiento de personal', 'Producto', 1400.00, '2025-05-10 11:15:06'),
(10, 'Servicios de plomería', 'Servicios de plomería', 'Producto', 800.00, '2025-05-10 11:15:06'),
(11, 'Servicios médicos (consultas)', 'Servicios médicos (consultas)', 'Producto', 500.00, '2025-05-10 11:15:07'),
(12, 'Exámenes médicos laborales', 'Exámenes médicos laborales', 'Producto', 600.00, '2025-05-10 11:15:07'),
(13, 'Servicios de diseño gráfico', 'Servicios de diseño gráfico', 'Producto', 1200.00, '2025-05-10 11:15:07'),
(14, 'Servicios de fotografía comercial', 'Servicios de fotografía comercial', 'Producto', 1100.00, '2025-05-10 11:15:07'),
(15, 'Servicios de instalación eléctrica', 'Servicios de instalación eléctrica', 'Producto', 1500.00, '2025-05-10 11:15:07'),
(16, 'Servicios de mantenimiento de edificios', 'Servicios de mantenimiento de edificios', 'Producto', 1300.00, '2025-05-10 11:15:07'),
(17, 'Servicios educativos y capacitación', 'Servicios educativos y capacitación', 'Producto', 1000.00, '2025-05-10 11:15:07'),
(18, 'Servicios de imprenta comercial', 'Servicios de imprenta comercial', 'Producto', 950.00, '2025-05-10 11:15:07'),
(19, 'Servicios de reclutamiento de personal', 'Servicios de reclutamiento de personal', 'Producto', 1400.00, '2025-05-10 11:15:07'),
(20, 'Servicios de plomería', 'Servicios de plomería', 'Producto', 800.00, '2025-05-10 11:15:07'),
(21, 'Servicios de carpintería', 'Servicios de carpintería', 'Producto', 900.00, '2025-05-10 11:15:07'),
(22, 'Servicios de auditoría contable', 'Servicios de auditoría contable', 'Producto', 2100.00, '2025-05-10 11:15:07'),
(23, 'Servicios de odontología', 'Servicios de odontología', 'Producto', 700.00, '2025-05-10 11:15:07'),
(24, 'Consultas de medicina general', 'Consultas de medicina general', 'Producto', 500.00, '2025-05-10 11:15:07'),
(25, 'Servicios de veterinaria', 'Servicios de veterinaria', 'Producto', 600.00, '2025-05-10 11:15:07'),
(26, 'Gasolina', 'Gasolina', 'Producto', 23.50, '2025-05-10 11:15:07'),
(27, 'Diesel', 'Diesel', 'Producto', 24.00, '2025-05-10 11:15:07'),
(28, 'Accesorios de computadoras', 'Accesorios de computadoras (cables, adaptadores)', 'Producto', 350.00, '2025-05-10 11:15:07'),
(29, 'Computadoras portátiles', 'Computadoras portátiles', 'Producto', 15000.00, '2025-05-10 11:15:07'),
(30, 'Tablets', 'Tablets', 'Producto', 8000.00, '2025-05-10 11:15:07'),
(31, 'Impresoras', 'Impresoras', 'Producto', 2500.00, '2025-05-10 11:15:07'),
(32, 'Baterías', 'Baterías', 'Producto', 300.00, '2025-05-10 11:15:07'),
(33, 'Papel para impresión', 'Papel para impresión', 'Producto', 120.00, '2025-05-10 11:15:07'),
(34, 'Tóner para impresora', 'Tóner para impresora', 'Producto', 800.00, '2025-05-10 11:15:07'),
(35, 'Carpetas y archivadores', 'Carpetas y archivadores', 'Producto', 90.00, '2025-05-10 11:15:07'),
(36, 'Plumas', 'Plumas', 'Producto', 15.00, '2025-05-10 11:15:07'),
(37, 'Cuadernos', 'Cuadernos', 'Producto', 40.00, '2025-05-10 11:15:07'),
(38, 'Productos de limpieza multiusos', 'Productos de limpieza multiusos', 'Producto', 100.00, '2025-05-10 11:15:07'),
(39, 'Jabón líquido', 'Jabón líquido', 'Producto', 70.00, '2025-05-10 11:15:07'),
(40, 'Muebles de oficina', 'Muebles de oficina (escritorios, sillas)', 'Producto', 3500.00, '2025-05-10 11:15:07');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `sale_date` date NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `tax_percentage` decimal(5,2) NOT NULL,
  `tax_amount` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `client_type` enum('person','company') NOT NULL DEFAULT 'person'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `client_id`, `sale_date`, `subtotal`, `tax_percentage`, `tax_amount`, `total`, `created_at`, `client_type`) VALUES
(4, 2, '2025-05-11', 430.00, 16.00, 68.80, 498.80, '2025-05-12 04:46:22', 'person'),
(30, 2, '2025-05-11', 120.00, 16.00, 19.20, 139.20, '2025-05-12 11:46:51', 'person'),
(35, 3, '2025-05-13', 3100.00, 16.00, 496.00, 3596.00, '2025-05-13 21:09:06', 'person'),
(36, 2, '2025-05-19', 24.00, 16.00, 0.24, 24.24, '2025-05-19 21:39:10', 'person');

-- --------------------------------------------------------

--
-- Table structure for table `sale_details`
--

CREATE TABLE `sale_details` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `tax_rate` decimal(4,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sale_details`
--

INSERT INTO `sale_details` (`id`, `sale_id`, `product_id`, `quantity`, `unit_price`, `tax_rate`, `subtotal`) VALUES
(62, 4, 39, 1, 70.00, 0.00, 70.00),
(63, 4, 33, 1, 120.00, 0.00, 120.00),
(64, 4, 33, 2, 120.00, 0.00, 240.00),
(65, 30, 33, 1, 120.00, 0.00, 120.00),
(73, 35, 32, 2, 300.00, 0.00, 600.00),
(74, 35, 31, 1, 2500.00, 0.00, 2500.00),
(75, 36, 27, 1, 24.00, 1.00, 24.00);

-- --------------------------------------------------------

--
-- Table structure for table `tax_regimes`
--

CREATE TABLE `tax_regimes` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tax_regimes`
--

INSERT INTO `tax_regimes` (`id`, `name`) VALUES
(1, 'General de Ley Personas Morales'),
(2, 'Régimen Simplificado de Confianza'),
(3, 'Régimen de Incorporación Fiscal'),
(4, 'Régimen de Actividades Empresariales y Profesionales'),
(5, 'Régimen de Sueldos y Salarios'),
(6, 'Régimen de Arrendamiento'),
(7, 'Régimen de Personas Morales con Fines no Lucrativos'),
(8, 'Régimen de Enajenación de Bienes'),
(9, 'Régimen de Dividendos'),
(10, 'Régimen de Plataformas Digitales');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `first_name`, `last_name`, `phone`, `email`, `created_at`) VALUES
(3, 'admin', '$2y$10$s1Xs.yQ5SXvgnBh0oX7w3e/M4pvRt9ufsCXTQ2mMoeIeoVu6Tg.pW', 'Admin', 'Invoice', '6631234567', 'l22211924@tectijuana.edu.mx', '2025-05-10 10:46:59'),
(4, 'jesus', '$2y$10$nH3qFu286z1OreJ5z9SgkOWR6D5qPwpK9Lyt5SqjEAH7H9UasE4Te', 'jesus', 'triana', '6641234567', 'jesus@gmail.com', '2025-05-11 11:49:53'),
(5, 'ladoblep', '$2y$10$gfRkLreaITxY9zI56O08JOg5YrLZZ6XgabcyYTOHhUGIRWvCErjLW', 'Peso', 'Pluma', '6641234567', 'pesopluma@gmail.com', '2025-05-11 12:26:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `business_types`
--
ALTER TABLE `business_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_email` (`email`),
  ADD UNIQUE KEY `unique_rfc` (`rfc`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_business_type` (`business_type_id`),
  ADD KEY `fk_tax_regime` (`tax_regime_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`),
  ADD KEY `sale_id` (`sale_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sale_details`
--
ALTER TABLE `sale_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `tax_regimes`
--
ALTER TABLE `tax_regimes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `business_types`
--
ALTER TABLE `business_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `sale_details`
--
ALTER TABLE `sale_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `tax_regimes`
--
ALTER TABLE `tax_regimes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `companies`
--
ALTER TABLE `companies`
  ADD CONSTRAINT `fk_business_type` FOREIGN KEY (`business_type_id`) REFERENCES `business_types` (`id`),
  ADD CONSTRAINT `fk_tax_regime` FOREIGN KEY (`tax_regime_id`) REFERENCES `tax_regimes` (`id`);

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`);

--
-- Constraints for table `sale_details`
--
ALTER TABLE `sale_details`
  ADD CONSTRAINT `sale_details_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`),
  ADD CONSTRAINT `sale_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
