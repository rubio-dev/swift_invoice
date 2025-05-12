CREATE DATABASE IF NOT EXISTS `swift_invoice_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `swift_invoice_db`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `business_types`
--

CREATE TABLE `business_types` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `business_types`
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
-- Estructura de tabla para la tabla `clients`
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
-- Volcado de datos para la tabla `clients`
--

INSERT INTO `clients` (`id`, `first_name`, `last_name`, `mother_last_name`, `phone`, `email`, `rfc`, `address`, `created_at`) VALUES
(2, 'PANFILOMENO', 'MOMICHIS', 'INSANO', '6624560923', 'momichiscorjjp@gmail.com', 'MOM123124215', 'AV DEL PARQUE S/N MESA DE OTAY, 22430, TIJUANA, BC, MEXICO.', '2025-05-10 03:54:57'),
(3, 'JUAN', 'TORRES', 'VASQUEZ', '6641234567', 'jtorres@gmail.com', 'JAM123124213', 'COL LAS TORRES, 22476. TIJUANA, BAJA CALIFORNIA.', '2025-05-11 05:20:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `companies`
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
-- Volcado de datos para la tabla `companies`
--

INSERT INTO `companies` (`id`, `business_name`, `rfc`, `fiscal_address`, `phone`, `email`, `legal_representative`, `logo_path`, `created_at`, `business_type_id`, `tax_regime_id`) VALUES
(1, 'Los Pollos Hermanos', 'JFDKJKSJF3322', 'DSJFKDSJFD F DKFKDS FKJDS JKFS', '6641234567', 'jesus@gmail.com', 'hjhjhj', NULL, '2025-05-11 04:51:58', 10, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `invoices`
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
-- Estructura de tabla para la tabla `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `created_at`) VALUES
(1, 'Servicios médicos (consultas)', 'Servicios médicos (consultas)', 500.00, '2025-05-10 04:15:06'),
(2, 'Exámenes médicos laborales', 'Exámenes médicos laborales', 600.00, '2025-05-10 04:15:06'),
(3, 'Servicios de diseño gráfico', 'Servicios de diseño gráfico', 1200.00, '2025-05-10 04:15:06'),
(4, 'Servicios de fotografía comercial', 'Servicios de fotografía comercial', 1100.00, '2025-05-10 04:15:06'),
(5, 'Servicios de instalación eléctrica', 'Servicios de instalación eléctrica', 1500.00, '2025-05-10 04:15:06'),
(6, 'Servicios de mantenimiento de edificios', 'Servicios de mantenimiento de edificios', 1300.00, '2025-05-10 04:15:06'),
(7, 'Servicios educativos y capacitación', 'Servicios educativos y capacitación', 1000.00, '2025-05-10 04:15:06'),
(8, 'Servicios de imprenta comercial', 'Servicios de imprenta comercial', 950.00, '2025-05-10 04:15:06'),
(9, 'Servicios de reclutamiento de personal', 'Servicios de reclutamiento de personal', 1400.00, '2025-05-10 04:15:06'),
(10, 'Servicios de plomería', 'Servicios de plomería', 800.00, '2025-05-10 04:15:06'),
(11, 'Servicios médicos (consultas)', 'Servicios médicos (consultas)', 500.00, '2025-05-10 04:15:07'),
(12, 'Exámenes médicos laborales', 'Exámenes médicos laborales', 600.00, '2025-05-10 04:15:07'),
(13, 'Servicios de diseño gráfico', 'Servicios de diseño gráfico', 1200.00, '2025-05-10 04:15:07'),
(14, 'Servicios de fotografía comercial', 'Servicios de fotografía comercial', 1100.00, '2025-05-10 04:15:07'),
(15, 'Servicios de instalación eléctrica', 'Servicios de instalación eléctrica', 1500.00, '2025-05-10 04:15:07'),
(16, 'Servicios de mantenimiento de edificios', 'Servicios de mantenimiento de edificios', 1300.00, '2025-05-10 04:15:07'),
(17, 'Servicios educativos y capacitación', 'Servicios educativos y capacitación', 1000.00, '2025-05-10 04:15:07'),
(18, 'Servicios de imprenta comercial', 'Servicios de imprenta comercial', 950.00, '2025-05-10 04:15:07'),
(19, 'Servicios de reclutamiento de personal', 'Servicios de reclutamiento de personal', 1400.00, '2025-05-10 04:15:07'),
(20, 'Servicios de plomería', 'Servicios de plomería', 800.00, '2025-05-10 04:15:07'),
(21, 'Servicios de carpintería', 'Servicios de carpintería', 900.00, '2025-05-10 04:15:07'),
(22, 'Servicios de auditoría contable', 'Servicios de auditoría contable', 2100.00, '2025-05-10 04:15:07'),
(23, 'Servicios de odontología', 'Servicios de odontología', 700.00, '2025-05-10 04:15:07'),
(24, 'Consultas de medicina general', 'Consultas de medicina general', 500.00, '2025-05-10 04:15:07'),
(25, 'Servicios de veterinaria', 'Servicios de veterinaria', 600.00, '2025-05-10 04:15:07'),
(26, 'Gasolina', 'Gasolina', 23.50, '2025-05-10 04:15:07'),
(27, 'Diesel', 'Diesel', 24.00, '2025-05-10 04:15:07'),
(28, 'Accesorios de computadoras', 'Accesorios de computadoras (cables, adaptadores)', 350.00, '2025-05-10 04:15:07'),
(29, 'Computadoras portátiles', 'Computadoras portátiles', 15000.00, '2025-05-10 04:15:07'),
(30, 'Tablets', 'Tablets', 8000.00, '2025-05-10 04:15:07'),
(31, 'Impresoras', 'Impresoras', 2500.00, '2025-05-10 04:15:07'),
(32, 'Baterías', 'Baterías', 300.00, '2025-05-10 04:15:07'),
(33, 'Papel para impresión', 'Papel para impresión', 120.00, '2025-05-10 04:15:07'),
(34, 'Tóner para impresora', 'Tóner para impresora', 800.00, '2025-05-10 04:15:07'),
(35, 'Carpetas y archivadores', 'Carpetas y archivadores', 90.00, '2025-05-10 04:15:07'),
(36, 'Plumas', 'Plumas', 15.00, '2025-05-10 04:15:07'),
(37, 'Cuadernos', 'Cuadernos', 40.00, '2025-05-10 04:15:07'),
(38, 'Productos de limpieza multiusos', 'Productos de limpieza multiusos', 100.00, '2025-05-10 04:15:07'),
(39, 'Jabón líquido', 'Jabón líquido', 70.00, '2025-05-10 04:15:07'),
(40, 'Muebles de oficina', 'Muebles de oficina (escritorios, sillas)', 3500.00, '2025-05-10 04:15:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sales`
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
-- Volcado de datos para la tabla `sales`
--

INSERT INTO `sales` (`id`, `client_id`, `sale_date`, `subtotal`, `tax_percentage`, `tax_amount`, `total`, `created_at`, `client_type`) VALUES
(4, 2, '2025-05-11', 430.00, 16.00, 68.80, 498.80, '2025-05-11 21:46:22', 'person'),
(30, 2, '2025-05-11', 120.00, 16.00, 19.20, 139.20, '2025-05-12 04:46:51', 'person'),
(32, 1, '2025-05-12', 53.50, 16.00, 8.56, 62.06, '2025-05-12 04:52:47', 'company'),
(33, 1, '2025-05-12', 15.00, 16.00, 2.40, 17.40, '2025-05-12 04:55:13', 'company'),
(34, 1, '2025-05-13', 70.00, 16.00, 11.20, 81.20, '2025-05-12 22:16:56', 'company');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sale_details`
--

CREATE TABLE `sale_details` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sale_details`
--

INSERT INTO `sale_details` (`id`, `sale_id`, `product_id`, `quantity`, `unit_price`, `subtotal`) VALUES
(62, 4, 39, 1, 70.00, 70.00),
(63, 4, 33, 1, 120.00, 120.00),
(64, 4, 33, 2, 120.00, 240.00),
(65, 30, 33, 1, 120.00, 120.00),
(67, 32, 36, 2, 15.00, 30.00),
(68, 32, 26, 1, 23.50, 23.50),
(69, 33, 36, 1, 15.00, 15.00),
(72, 34, 39, 1, 70.00, 70.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tax_regimes`
--

CREATE TABLE `tax_regimes` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tax_regimes`
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
-- Estructura de tabla para la tabla `users`
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
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `first_name`, `last_name`, `phone`, `email`, `created_at`) VALUES
(3, 'admin', '$2y$10$s1Xs.yQ5SXvgnBh0oX7w3e/M4pvRt9ufsCXTQ2mMoeIeoVu6Tg.pW', 'Admin', 'Invoice', '6631234567', 'l22211924@tectijuana.edu.mx', '2025-05-10 03:46:59'),
(4, 'jesus', '$2y$10$nH3qFu286z1OreJ5z9SgkOWR6D5qPwpK9Lyt5SqjEAH7H9UasE4Te', 'jesus', 'triana', '6641234567', 'jesus@gmail.com', '2025-05-11 04:49:53'),
(5, 'ladoblep', '$2y$10$gfRkLreaITxY9zI56O08JOg5YrLZZ6XgabcyYTOHhUGIRWvCErjLW', 'Peso', 'Pluma', '6641234567', 'pesopluma@gmail.com', '2025-05-11 05:26:08');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `business_types`
--
ALTER TABLE `business_types`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_email` (`email`),
  ADD UNIQUE KEY `unique_rfc` (`rfc`);

--
-- Indices de la tabla `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_business_type` (`business_type_id`),
  ADD KEY `fk_tax_regime` (`tax_regime_id`);

--
-- Indices de la tabla `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`),
  ADD KEY `sale_id` (`sale_id`);

--
-- Indices de la tabla `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sale_details`
--
ALTER TABLE `sale_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indices de la tabla `tax_regimes`
--
ALTER TABLE `tax_regimes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `business_types`
--
ALTER TABLE `business_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT de la tabla `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `sale_details`
--
ALTER TABLE `sale_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT de la tabla `tax_regimes`
--
ALTER TABLE `tax_regimes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `companies`
--
ALTER TABLE `companies`
  ADD CONSTRAINT `fk_business_type` FOREIGN KEY (`business_type_id`) REFERENCES `business_types` (`id`),
  ADD CONSTRAINT `fk_tax_regime` FOREIGN KEY (`tax_regime_id`) REFERENCES `tax_regimes` (`id`);

--
-- Filtros para la tabla `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`);

--
-- Filtros para la tabla `sale_details`
--
ALTER TABLE `sale_details`
  ADD CONSTRAINT `sale_details_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`),
  ADD CONSTRAINT `sale_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

