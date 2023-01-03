-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-10-2022 a las 07:15:08
-- Versión del servidor: 10.4.25-MariaDB
-- Versión de PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `universidad`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `add_detalle` (IN `codigo` INT, IN `token_user` VARCHAR(50), IN `horario` INT)   BEGIN
    	DECLARE precio_actual DECIMAL(10,2);
        #DECLARE descripcion VARCHAR(50);
        
        SELECT precio INTO precio_actual FROM curso WHERE codigo_cur = codigo;
        #SELECT descripcion INTO descripcion FROM curso WHERE codigo_cur = codigo;
        
        INSERT INTO detalle_matricula(codigo_cur,horario,colegiatura,token_user) VALUES (codigo,horario,precio_actual,token_user);
        
        SELECT t.correlativo_temp, t.codigo_cur, c.descripcion,f.facultad,h.horario,t.colegiatura
        FROM detalle_matricula t  
        INNER JOIN curso c ON t.codigo_cur = c.codigo_cur 
        INNER JOIN horario h ON t.horario = h.idhorario
        INNER JOIN facultad f ON f.idfacultad = c.facultad
        WHERE t.token_user = token_user;
        
	END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `anular_boleta` (IN `noboleta_` INT)   BEGIN
        	DECLARE existe INT;
            DECLARE registro INT;
            DECLARE a INT;
            DECLARE codigo_cur_ INT;
            DECLARE horario_ INT;
            	SET existe = (SELECT COUNT(*) FROM boleta WHERE noboleta = noboleta_ AND estado = 1);
                
                	IF existe > 0 THEN
                    	CREATE TEMPORARY TABLE tb_temp(
                        	id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        	codigo_cur INT,
                        	horario INT);
                            
                            SET a = 1;
                            
                            SET registro = (SELECT COUNT(*) FROM detalle WHERE noboleta = noboleta_);
                            	
                                IF registro > 0 THEN
                                	INSERT INTO tb_temp(codigo_cur, horario) SELECT codigo_cur, horario FROM detalle WHERE noboleta = noboleta_; 
                                    
                                    WHILE a <= registro DO
                                    	SELECT codigo_cur, horario INTO codigo_cur_, horario_ FROM tb_temp WHERE id = a;
                                        SET a =a+1;
                                    END WHILE;
                                    	UPDATE boleta SET estado = 0 WHERE noboleta=noboleta_;
                                        DROP TABLE tb_temp;
                                        SELECT * FROM boleta WHERE noboleta = noboleta_;
                                END IF;
                    ELSE
                    	SELECT 0 boleta;
                    END IF;
                
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `dataDashboard` ()   BEGIN
    	DECLARE usuarios INT;
        DECLARE alumnos INT;
        DECLARE catedraticos INT;
        DECLARE cursos INT;
        DECLARE matriculas INT;
        
        SELECT COUNT(*) INTO usuarios FROM usuario WHERE estado != 0;
        SELECT COUNT(*) INTO alumnos FROM alumno WHERE estado != 0;
        SELECT COUNT(*) INTO catedraticos FROM catedratico WHERE estado != 0;
        SELECT COUNT(*) INTO cursos FROM curso WHERE estado != 0;
        SELECT COUNT(*) INTO matriculas FROM boleta WHERE estado != 0;
        
        SELECT usuarios, alumnos, catedraticos, cursos, matriculas;
        
    END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `eliminar_detalle` (IN `correlativo` INT, IN `token` VARCHAR(50))   BEGIN
    	DELETE FROM detalle_matricula WHERE correlativo_temp = correlativo;
        
        SELECT t.correlativo_temp, t.codigo_cur, c.descripcion,f.facultad,h.horario,t.colegiatura
        FROM detalle_matricula t  
        INNER JOIN curso c ON t.codigo_cur = c.codigo_cur 
        INNER JOIN horario h ON t.horario = h.idhorario
        INNER JOIN facultad f ON f.idfacultad = c.facultad
        WHERE t.token_user = token_user;
    END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `procesar_inscripcion` (IN `usuario` INT, IN `carnet` INT, IN `token` VARCHAR(50))   BEGIN
        	DECLARE boleta INT;
            DECLARE registros INT;
            DECLARE total DECIMAL(10,2);
            DECLARE temp_curso INT;
            DECLARE temp_horario INT;
            DECLARE a INT;
            SET a = 1;
            
            CREATE TEMPORARY TABLE tb_token(
            	id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            	cod_cur BIGINT,
            	horario INT);
            SET registros = (SELECT COUNT(*) FROM detalle_matricula WHERE token_user = token);
            
            IF registros > 0 THEN
            	INSERT INTO tb_token(cod_cur, horario) SELECT codigo_cur, horario FROM detalle_matricula WHERE token_user = token;
                
                INSERT INTO boleta(usuario, carnet) VALUES(usuario,carnet);
                SET boleta = LAST_INSERT_ID();
                
                INSERT INTO detalle(noboleta,codigo_cur,horario,colegiatura) SELECT (boleta) AS noboleta, codigo_cur,horario,colegiatura FROM detalle_matricula
                WHERE token_user = token;
                
                WHILE a <= registros DO
                	SELECT cod_cur, horario INTO temp_curso, temp_horario FROM tb_token WHERE id = a;
                    
                    SET a=a+1;
                END WHILE;
                
                SET total = (SELECT SUM(colegiatura) FROM detalle_matricula WHERE token_user = token);
                UPDATE boleta SET total = total WHERE noboleta = boleta;
                DELETE FROM detalle_matricula WHERE token_user = token;
                TRUNCATE TABLE tb_token;
                SELECT * FROM boleta WHERE noboleta = boleta;
            ELSE
            	SELECT 0;
            END IF;
           
		END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumno`
--

CREATE TABLE `alumno` (
  `carnet` int(50) NOT NULL COMMENT 'carnet unico para ID alumno',
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `direccion` varchar(100) NOT NULL,
  `dpi` bigint(20) NOT NULL,
  `telefono` int(20) NOT NULL,
  `fecha_ins` datetime NOT NULL DEFAULT current_timestamp(),
  `idusuario` int(20) NOT NULL,
  `estado` int(2) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `alumno`
--

INSERT INTO `alumno` (`carnet`, `nombre`, `apellido`, `direccion`, `dpi`, `telefono`, `fecha_ins`, `idusuario`, `estado`) VALUES
(4, 'Antonio', 'Cruz', 'Casa 5, lote 2, las cruces, tepescoelhoyo', 1234567890, 44444445, '2022-09-28 22:03:13', 1, 1),
(5, 'Cristiano Ronaldo', 'Dos Santos Aveiro', 'Alguna mansion en Europa', 4545452342, 55550000, '2022-09-28 22:05:56', 17, 1),
(6, 'Cesc', 'Fabregas', 'Barcelona', 23452345356, 43546712, '2022-09-29 21:03:59', 17, 1),
(7, 'Tibaut', 'Courtois', 'Belgica', 876856784567, 66778899, '2022-09-29 21:09:21', 17, 1),
(8, 'Wayne', 'Rooney', 'Inglaterra', 2147483647, 23233434, '2022-09-29 21:10:26', 17, 1),
(9, 'Romelu', 'Lukaku', 'En algun lugar de Paises Bajos', 23455672225656, 22334455, '2022-10-16 12:38:43', 1, 1),
(10, 'Robin', 'Van Persie', 'Paises Bajos', 234124534574567, 44221133, '2022-10-16 16:24:24', 1, 1),
(11, 'Carlos', 'Ruiz', 'Guatemala', 1234345456324567, 33445566, '2022-10-16 17:25:17', 1, 1),
(12, 'Luka', 'Modric', 'Croacia', 1234234508923475, 57960573, '2022-10-16 22:48:46', 1, 1),
(13, 'Gareth', 'Bale', 'Gales', 42743502364502, 56729439, '2022-10-16 22:49:31', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `apertura_curso`
--

CREATE TABLE `apertura_curso` (
  `correlativo` int(11) NOT NULL,
  `codigo_cur` int(20) NOT NULL,
  `codigo_cat` int(20) NOT NULL,
  `fecha_apertura` datetime NOT NULL DEFAULT current_timestamp(),
  `idusuario` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `apertura_curso`
--

INSERT INTO `apertura_curso` (`correlativo`, `codigo_cur`, `codigo_cat`, `fecha_apertura`, `idusuario`) VALUES
(1, 1, 6, '2022-10-02 00:18:10', 1),
(4, 34, 0, '2022-10-02 17:29:48', 1),
(5, 35, 0, '2022-10-02 17:30:43', 1),
(6, 37, 4, '2022-10-02 17:41:36', 1),
(7, 38, 1, '2022-10-02 17:42:58', 1),
(8, 39, 5, '2022-10-02 17:44:20', 1),
(9, 40, 3, '2022-10-02 17:44:30', 1),
(10, 41, 2, '2022-10-02 18:11:19', 1),
(11, 42, 1, '2022-10-02 19:16:20', 1),
(12, 43, 6, '2022-10-13 00:08:28', 1),
(13, 44, 4, '2022-10-13 00:11:04', 1),
(14, 45, 2, '2022-10-16 22:44:45', 1),
(15, 46, 4, '2022-10-16 22:45:18', 1),
(16, 47, 6, '2022-10-16 22:45:53', 1),
(17, 48, 5, '2022-10-16 22:46:19', 1),
(18, 49, 8, '2022-10-16 22:50:54', 1),
(19, 50, 10, '2022-10-16 22:51:14', 1),
(20, 51, 10, '2022-10-16 22:51:37', 1),
(21, 52, 7, '2022-10-16 22:52:15', 1),
(22, 53, 10, '2022-10-16 22:53:15', 1),
(23, 54, 6, '2022-10-16 22:54:25', 1),
(24, 55, 4, '2022-10-16 22:54:59', 1),
(25, 56, 1, '2022-10-16 22:55:24', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `boleta`
--

CREATE TABLE `boleta` (
  `noboleta` int(10) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario` int(10) NOT NULL,
  `carnet` int(10) NOT NULL,
  `total` float NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `boleta`
--

INSERT INTO `boleta` (`noboleta`, `fecha`, `usuario`, `carnet`, `total`, `estado`) VALUES
(2, '2022-10-14 00:28:49', 1, 4, 482, 1),
(3, '2022-10-14 00:35:31', 1, 4, 315, 1),
(4, '2022-10-14 00:41:09', 1, 5, 657, 1),
(5, '2022-10-14 23:46:55', 1, 5, 315, 1),
(6, '2022-10-14 23:49:03', 1, 7, 175, 1),
(7, '2022-10-14 23:51:54', 1, 7, 342, 1),
(8, '2022-10-14 23:56:40', 1, 8, 175, 1),
(9, '2022-10-14 23:59:08', 1, 5, 160, 0),
(10, '2022-10-15 00:03:46', 1, 5, 160, 1),
(11, '2022-10-15 00:05:32', 1, 5, 175, 1),
(12, '2022-10-15 00:06:54', 1, 6, 315, 1),
(13, '2022-10-15 00:08:00', 1, 4, 482, 1),
(14, '2022-10-15 00:08:55', 1, 6, 315, 1),
(15, '2022-10-15 00:09:20', 1, 5, 155, 1),
(16, '2022-10-15 00:24:39', 1, 5, 167, 1),
(17, '2022-10-15 00:25:50', 1, 4, 175, 1),
(18, '2022-10-15 00:27:35', 1, 5, 482, 1),
(19, '2022-10-15 00:35:11', 1, 5, 315, 1),
(20, '2022-10-15 00:36:43', 1, 5, 155, 1),
(21, '2022-10-15 00:45:02', 1, 5, 315, 1),
(22, '2022-10-15 00:46:11', 1, 5, 657, 1),
(23, '2022-10-15 00:52:04', 1, 5, 160, 1),
(24, '2022-10-15 01:02:22', 1, 5, 155, 1),
(25, '2022-10-15 01:03:20', 1, 5, 155, 0),
(26, '2022-10-15 01:06:47', 1, 4, 175, 1),
(27, '2022-10-15 01:13:39', 1, 5, 155, 1),
(28, '2022-10-15 01:27:02', 1, 5, 155, 1),
(29, '2022-10-15 01:28:49', 1, 5, 315, 1),
(30, '2022-10-15 01:29:18', 1, 6, 155, 1),
(31, '2022-10-15 01:30:18', 1, 6, 315, 1),
(32, '2022-10-15 01:31:45', 1, 5, 155, 1),
(33, '2022-10-15 01:52:34', 1, 5, 482, 1),
(34, '2022-10-15 01:57:16', 1, 5, 155, 1),
(35, '2022-10-15 01:57:47', 1, 5, 160, 1),
(36, '2022-10-15 02:02:57', 1, 5, 315, 1),
(37, '2022-10-15 02:08:57', 1, 5, 155, 1),
(38, '2022-10-15 02:10:25', 1, 4, 175, 1),
(39, '2022-10-15 02:15:32', 1, 5, 315, 0),
(40, '2022-10-15 02:16:43', 1, 5, 175, 1),
(41, '2022-10-15 02:17:32', 1, 5, 155, 1),
(42, '2022-10-15 02:19:26', 1, 4, 160, 1),
(43, '2022-10-15 02:21:40', 1, 5, 155, 1),
(44, '2022-10-15 02:25:21', 1, 5, 155, 1),
(45, '2022-10-15 02:27:19', 1, 5, 155, 0),
(46, '2022-10-15 02:28:17', 1, 5, 155, 1),
(47, '2022-10-15 02:28:56', 1, 5, 167, 1),
(48, '2022-10-15 12:54:35', 1, 5, 155, 1),
(49, '2022-10-15 12:57:51', 1, 5, 175, 1),
(50, '2022-10-16 00:56:37', 1, 5, 155, 1),
(51, '2022-10-16 01:01:02', 1, 5, 482, 1),
(52, '2022-10-16 01:02:55', 1, 6, 657, 1),
(53, '2022-10-16 01:04:11', 1, 6, 315, 1),
(54, '2022-10-16 01:06:17', 1, 7, 315, 0),
(55, '2022-10-16 01:08:23', 1, 7, 315, 1),
(56, '2022-10-16 01:09:23', 1, 8, 315, 1),
(57, '2022-10-16 01:14:41', 1, 6, 315, 1),
(58, '2022-10-16 01:15:10', 1, 5, 315, 1),
(59, '2022-10-16 01:16:20', 1, 4, 155, 1),
(60, '2022-10-16 01:16:56', 1, 4, 167, 1),
(61, '2022-10-16 01:19:25', 1, 5, 155, 0),
(62, '2022-10-16 01:31:43', 1, 5, 657, 1),
(63, '2022-10-16 01:34:34', 1, 5, 315, 1),
(64, '2022-10-16 01:49:01', 1, 5, 155, 1),
(65, '2022-10-16 01:49:35', 1, 5, 175, 1),
(66, '2022-10-16 02:06:34', 1, 8, 315, 1),
(67, '2022-10-16 02:09:26', 1, 5, 315, 1),
(68, '2022-10-16 02:14:53', 1, 5, 175, 1),
(69, '2022-10-16 02:15:48', 1, 5, 155, 1),
(70, '2022-10-16 02:17:07', 1, 5, 160, 1),
(71, '2022-10-16 02:19:35', 1, 4, 155, 1),
(72, '2022-10-16 02:21:09', 1, 5, 175, 1),
(73, '2022-10-16 02:23:55', 1, 5, 155, 1),
(74, '2022-10-16 02:25:14', 1, 4, 175, 1),
(75, '2022-10-16 02:28:39', 1, 4, 155, 1),
(76, '2022-10-16 02:29:31', 1, 5, 155, 1),
(77, '2022-10-16 02:30:30', 1, 5, 175, 1),
(78, '2022-10-16 02:32:33', 1, 5, 160, 1),
(79, '2022-10-16 02:33:27', 1, 5, 175, 1),
(80, '2022-10-16 02:34:40', 1, 4, 175, 1),
(81, '2022-10-16 02:35:37', 1, 4, 175, 1),
(82, '2022-10-16 02:36:30', 1, 5, 175, 1),
(83, '2022-10-16 02:38:05', 1, 5, 175, 1),
(84, '2022-10-16 02:42:16', 1, 5, 175, 1),
(85, '2022-10-16 02:43:35', 1, 5, 175, 1),
(86, '2022-10-16 02:44:29', 1, 5, 175, 1),
(87, '2022-10-16 02:45:27', 1, 4, 175, 1),
(88, '2022-10-16 02:47:06', 1, 5, 657, 1),
(89, '2022-10-16 12:38:54', 1, 9, 155, 1),
(90, '2022-10-16 12:55:06', 1, 9, 482, 1),
(91, '2022-10-16 12:58:59', 1, 5, 657, 1),
(92, '2022-10-16 16:24:37', 1, 10, 175, 1),
(93, '2022-10-16 16:26:25', 1, 10, 657, 1),
(94, '2022-10-16 16:27:38', 1, 10, 315, 1),
(95, '2022-10-16 16:32:25', 1, 10, 315, 1),
(96, '2022-10-16 16:36:49', 1, 10, 175, 1),
(97, '2022-10-16 16:38:46', 1, 5, 155, 1),
(98, '2022-10-16 16:40:12', 1, 9, 160, 1),
(99, '2022-10-16 16:41:51', 1, 6, 155, 1),
(100, '2022-10-16 16:44:45', 1, 9, 155, 1),
(101, '2022-10-16 16:49:29', 1, 10, 155, 1),
(102, '2022-10-16 16:53:29', 1, 5, 155, 1),
(103, '2022-10-16 16:54:38', 1, 9, 155, 1),
(104, '2022-10-16 17:02:32', 1, 10, 155, 1),
(105, '2022-10-16 17:03:04', 1, 6, 160, 1),
(106, '2022-10-16 17:05:45', 1, 8, 155, 1),
(107, '2022-10-16 17:20:53', 1, 6, 160, 1),
(108, '2022-10-16 17:21:24', 1, 9, 155, 1),
(109, '2022-10-16 17:22:09', 1, 5, 160, 1),
(110, '2022-10-16 17:27:18', 1, 10, 342, 1),
(111, '2022-10-16 17:27:44', 1, 9, 315, 1),
(112, '2022-10-16 17:31:34', 1, 5, 167, 1),
(113, '2022-10-16 17:35:37', 1, 6, 175, 1),
(114, '2022-10-16 17:38:33', 1, 5, 175, 1),
(115, '2022-10-16 17:39:42', 1, 7, 175, 1),
(116, '2022-10-16 17:46:33', 1, 5, 175, 1),
(117, '2022-10-16 17:51:00', 1, 7, 502, 0),
(118, '2022-10-16 17:53:03', 1, 6, 160, 1),
(119, '2022-10-16 17:53:50', 1, 7, 175, 1),
(120, '2022-10-16 18:01:14', 1, 5, 175, 1),
(121, '2022-10-16 18:04:03', 1, 6, 155, 1),
(122, '2022-10-16 18:04:41', 1, 6, 155, 1),
(123, '2022-10-16 18:06:55', 1, 6, 155, 0),
(124, '2022-10-16 18:12:12', 1, 10, 175, 0),
(125, '2022-10-16 18:14:06', 1, 10, 175, 0),
(126, '2022-10-16 18:15:38', 1, 5, 175, 1),
(127, '2022-10-16 18:30:43', 1, 10, 175, 1),
(128, '2022-10-16 18:37:48', 1, 10, 175, 1),
(129, '2022-10-16 18:40:00', 1, 11, 175, 0),
(130, '2022-10-16 18:41:35', 1, 11, 167, 0),
(131, '2022-10-16 18:51:07', 1, 11, 155, 0),
(132, '2022-10-18 22:25:20', 1, 9, 482, 0),
(133, '2022-10-18 22:51:50', 1, 5, 155, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `catedratico`
--

CREATE TABLE `catedratico` (
  `codigo_cat` int(10) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `direccion` varchar(100) NOT NULL,
  `dpi` bigint(20) NOT NULL,
  `telefono` int(20) NOT NULL,
  `fecha_reg` datetime NOT NULL DEFAULT current_timestamp(),
  `idusuario` int(10) NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `catedratico`
--

INSERT INTO `catedratico` (`codigo_cat`, `nombre`, `apellido`, `direccion`, `dpi`, `telefono`, `fecha_reg`, `idusuario`, `estado`) VALUES
(1, 'Raul', 'Alvarez Genes', 'Barcelona', 123456789, 44556633, '2022-10-01 00:08:54', 1, 1),
(2, 'Ronald ', 'Ayala', 'Guatemala', 23345657567, 33442211, '2022-10-01 00:14:41', 1, 1),
(3, 'William', 'DaFoe', 'Los Angeles', 6563523422344, 56455634, '2022-10-01 01:02:32', 1, 1),
(4, 'Silvester', 'Stallone', 'Hollywood', 1234123345356, 55667788, '2022-10-01 01:20:09', 1, 1),
(5, 'Jackie', 'Chan', 'Algún lugar de China', 56465674562, 22113322, '2022-10-01 01:23:45', 1, 1),
(6, 'Joseph', 'Bartomeu', 'Barcelona', 33452342412423, 45362712, '2022-10-01 01:24:59', 1, 1),
(7, 'Enma', 'Stone', 'Los Angeles, California', 1234235235634, 45875643, '2022-10-16 22:47:08', 1, 1),
(8, 'Emma', 'Watson', 'Inglaterra', 23789045612450, 56372823, '2022-10-16 22:47:39', 1, 1),
(9, 'Nicole', 'Kidman', 'Australia', 1234123451245, 56473820, '2022-10-16 22:48:02', 1, 1),
(10, 'Scarlett', 'Johansson', 'En mi corazon', 78902034529075, 50987634, '2022-10-16 22:50:16', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `razon_social` varchar(10) NOT NULL,
  `telefono` int(10) NOT NULL,
  `correo` varchar(50) NOT NULL,
  `direccion` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id`, `nombre`, `razon_social`, `telefono`, `correo`, `direccion`) VALUES
(1, 'Universidad de Charver  de Guatemala', '', 24200000, 'info@charver.edu.gt', 'Lote 6-66, 5av. Residencial Elsa Polindo, Km.32 CA9 Sur, Rio Quitacalzon, Amatitlan, Guatemala, Guatemala, C.A.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `curso`
--

CREATE TABLE `curso` (
  `codigo_cur` int(10) NOT NULL,
  `descripcion` varchar(50) NOT NULL,
  `facultad` int(10) NOT NULL,
  `catedratico` int(10) NOT NULL,
  `horario` int(10) NOT NULL,
  `idusuario` int(10) NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `precio` float NOT NULL,
  `estado` int(10) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `curso`
--

INSERT INTO `curso` (`codigo_cur`, `descripcion`, `facultad`, `catedratico`, `horario`, `idusuario`, `fecha_creacion`, `precio`, `estado`) VALUES
(41, 'Desarrollo Web I', 1, 2, 5, 1, '2022-10-02 18:11:19', 155, 1),
(42, 'Matematicas I', 1, 3, 4, 1, '2022-10-02 19:16:20', 160, 1),
(43, 'Matematicas II', 1, 6, 3, 1, '2022-10-13 00:08:28', 167, 1),
(44, 'Redes de computadoras I', 1, 1, 1, 1, '2022-10-13 00:11:04', 175, 1),
(45, 'Estadistica I', 1, 2, 1, 1, '2022-10-16 22:44:45', 105.5, 1),
(46, 'Estadistica Inferencial', 1, 4, 4, 1, '2022-10-16 22:45:18', 110.75, 1),
(47, 'Logica', 1, 6, 6, 1, '2022-10-16 22:45:53', 75, 1),
(48, 'Emprendedores de Negocios', 3, 5, 1, 1, '2022-10-16 22:46:19', 20.45, 1),
(49, 'Derecho I', 4, 8, 1, 1, '2022-10-16 22:50:54', 125, 1),
(50, 'Derecho II', 4, 10, 5, 1, '2022-10-16 22:51:14', 200.99, 1),
(51, 'Programacion I', 1, 10, 2, 1, '2022-10-16 22:51:37', 125.55, 1),
(52, 'Programacion II', 1, 7, 4, 1, '2022-10-16 22:52:15', 255.65, 1),
(53, 'Algebra Lineal', 1, 10, 4, 1, '2022-10-16 22:53:15', 105.5, 1),
(54, 'Contabilidad I', 6, 6, 1, 1, '2022-10-16 22:54:25', 75, 1),
(55, 'Analisis Quimico I', 5, 4, 1, 1, '2022-10-16 22:54:59', 125.98, 1),
(56, 'Anatomia I', 5, 1, 5, 1, '2022-10-16 22:55:24', 110.75, 1);

--
-- Disparadores `curso`
--
DELIMITER $$
CREATE TRIGGER `apertura_DI` AFTER INSERT ON `curso` FOR EACH ROW BEGIN
        	INSERT INTO apertura_curso (codigo_cur, codigo_cat,idusuario)
            VALUES (new.codigo_cur, new.catedratico, new.idusuario);
        END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle`
--

CREATE TABLE `detalle` (
  `correlativo` int(10) NOT NULL,
  `noboleta` int(10) NOT NULL,
  `codigo_cur` int(10) NOT NULL,
  `horario` int(10) NOT NULL,
  `colegiatura` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `detalle`
--

INSERT INTO `detalle` (`correlativo`, `noboleta`, `codigo_cur`, `horario`, `colegiatura`) VALUES
(1, 2, 41, 5, 155),
(2, 2, 42, 4, 160),
(3, 2, 43, 3, 167),
(4, 3, 41, 5, 155),
(5, 3, 42, 4, 160),
(7, 4, 41, 5, 155),
(8, 4, 42, 4, 160),
(9, 4, 43, 3, 167),
(10, 4, 44, 4, 175),
(11, 5, 41, 5, 155),
(12, 5, 42, 4, 160),
(14, 6, 44, 4, 175),
(15, 7, 43, 3, 167),
(16, 7, 44, 4, 175),
(18, 8, 44, 4, 175),
(19, 9, 42, 4, 160),
(20, 10, 42, 4, 160),
(21, 11, 44, 4, 175),
(22, 12, 41, 5, 155),
(23, 12, 42, 4, 160),
(25, 13, 41, 5, 155),
(26, 13, 42, 4, 160),
(27, 13, 43, 3, 167),
(28, 14, 41, 5, 155),
(29, 14, 42, 4, 160),
(31, 15, 41, 5, 155),
(32, 16, 43, 3, 167),
(33, 17, 44, 4, 175),
(34, 18, 41, 5, 155),
(35, 18, 42, 4, 160),
(36, 18, 43, 3, 167),
(37, 19, 41, 5, 155),
(38, 19, 42, 4, 160),
(40, 20, 41, 5, 155),
(41, 21, 41, 5, 155),
(42, 21, 42, 4, 160),
(44, 22, 41, 5, 155),
(45, 22, 42, 4, 160),
(46, 22, 43, 3, 167),
(47, 22, 44, 4, 175),
(51, 23, 42, 4, 160),
(52, 24, 41, 5, 155),
(53, 25, 41, 5, 155),
(54, 26, 44, 4, 175),
(55, 27, 41, 5, 155),
(56, 28, 41, 5, 155),
(57, 29, 41, 5, 155),
(58, 29, 42, 4, 160),
(60, 30, 41, 5, 155),
(61, 31, 41, 5, 155),
(62, 31, 42, 4, 160),
(64, 32, 41, 5, 155),
(65, 33, 41, 5, 155),
(66, 33, 42, 4, 160),
(67, 33, 43, 3, 167),
(68, 34, 41, 5, 155),
(69, 35, 42, 4, 160),
(70, 36, 41, 5, 155),
(71, 36, 42, 4, 160),
(73, 37, 41, 5, 155),
(74, 38, 44, 4, 175),
(75, 39, 41, 5, 155),
(76, 39, 42, 4, 160),
(78, 40, 44, 4, 175),
(79, 41, 41, 5, 155),
(80, 42, 42, 4, 160),
(81, 43, 41, 5, 155),
(82, 44, 41, 5, 155),
(83, 45, 41, 5, 155),
(84, 46, 41, 5, 155),
(85, 47, 43, 3, 167),
(86, 48, 41, 5, 155),
(87, 49, 44, 4, 175),
(88, 50, 41, 5, 155),
(89, 51, 41, 5, 155),
(90, 51, 42, 4, 160),
(91, 51, 43, 3, 167),
(92, 52, 41, 5, 155),
(93, 52, 42, 4, 160),
(94, 52, 43, 3, 167),
(95, 52, 44, 4, 175),
(99, 53, 41, 5, 155),
(100, 53, 42, 4, 160),
(102, 54, 41, 5, 155),
(103, 54, 42, 4, 160),
(105, 55, 41, 5, 155),
(106, 55, 42, 4, 160),
(108, 56, 41, 5, 155),
(109, 56, 42, 4, 160),
(111, 57, 41, 5, 155),
(112, 57, 42, 4, 160),
(114, 58, 41, 5, 155),
(115, 58, 42, 4, 160),
(117, 59, 41, 5, 155),
(118, 60, 43, 3, 167),
(119, 61, 41, 5, 155),
(120, 62, 41, 5, 155),
(121, 62, 42, 4, 160),
(122, 62, 43, 3, 167),
(123, 62, 44, 4, 175),
(127, 63, 41, 5, 155),
(128, 63, 42, 4, 160),
(130, 64, 41, 5, 155),
(131, 65, 44, 4, 175),
(132, 66, 41, 5, 155),
(133, 66, 42, 4, 160),
(135, 67, 41, 5, 155),
(136, 67, 42, 4, 160),
(138, 68, 44, 4, 175),
(139, 69, 41, 5, 155),
(140, 70, 42, 4, 160),
(141, 71, 41, 5, 155),
(142, 72, 44, 4, 175),
(143, 73, 41, 5, 155),
(144, 74, 44, 4, 175),
(145, 75, 41, 5, 155),
(146, 76, 41, 5, 155),
(147, 77, 44, 4, 175),
(148, 78, 42, 4, 160),
(149, 79, 44, 4, 175),
(150, 80, 44, 4, 175),
(151, 81, 44, 4, 175),
(152, 82, 44, 4, 175),
(153, 83, 44, 4, 175),
(154, 84, 44, 4, 175),
(155, 85, 44, 4, 175),
(156, 86, 44, 4, 175),
(157, 87, 44, 4, 175),
(158, 88, 41, 5, 155),
(159, 88, 42, 4, 160),
(160, 88, 43, 3, 167),
(161, 88, 44, 4, 175),
(162, 89, 41, 5, 155),
(163, 90, 41, 5, 155),
(164, 90, 42, 4, 160),
(165, 90, 43, 3, 167),
(166, 91, 41, 5, 155),
(167, 91, 42, 4, 160),
(168, 91, 43, 3, 167),
(169, 91, 44, 1, 175),
(173, 92, 44, 1, 175),
(174, 93, 41, 5, 155),
(175, 93, 42, 4, 160),
(176, 93, 43, 3, 167),
(177, 93, 44, 1, 175),
(181, 94, 41, 5, 155),
(182, 94, 42, 4, 160),
(184, 95, 41, 5, 155),
(185, 95, 42, 4, 160),
(187, 96, 44, 1, 175),
(188, 97, 41, 5, 155),
(189, 98, 42, 4, 160),
(190, 99, 41, 5, 155),
(191, 100, 41, 5, 155),
(192, 101, 41, 5, 155),
(193, 102, 41, 5, 155),
(194, 103, 41, 5, 155),
(195, 104, 41, 5, 155),
(196, 105, 42, 4, 160),
(197, 106, 41, 5, 155),
(198, 107, 42, 4, 160),
(199, 108, 41, 5, 155),
(200, 109, 42, 4, 160),
(201, 110, 43, 3, 167),
(202, 110, 44, 1, 175),
(204, 111, 41, 5, 155),
(205, 111, 42, 4, 160),
(207, 112, 43, 3, 167),
(208, 113, 44, 1, 175),
(209, 114, 44, 1, 175),
(210, 115, 44, 1, 175),
(211, 116, 44, 1, 175),
(212, 117, 44, 1, 175),
(213, 117, 43, 3, 167),
(214, 117, 42, 4, 160),
(215, 118, 42, 4, 160),
(216, 119, 44, 1, 175),
(217, 120, 44, 1, 175),
(218, 121, 41, 5, 155),
(219, 122, 41, 5, 155),
(220, 123, 41, 5, 155),
(221, 124, 44, 1, 175),
(222, 125, 44, 1, 175),
(223, 126, 44, 1, 175),
(224, 127, 44, 1, 175),
(225, 128, 44, 1, 175),
(226, 129, 44, 1, 175),
(227, 130, 43, 3, 167),
(228, 131, 41, 5, 155),
(229, 132, 41, 5, 155),
(230, 132, 42, 4, 160),
(231, 132, 43, 3, 167),
(232, 133, 41, 5, 155);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_matricula`
--

CREATE TABLE `detalle_matricula` (
  `correlativo_temp` int(10) NOT NULL,
  `codigo_cur` int(10) NOT NULL,
  `facultad` int(10) NOT NULL,
  `horario` int(10) NOT NULL,
  `colegiatura` float NOT NULL,
  `token_user` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facultad`
--

CREATE TABLE `facultad` (
  `idfacultad` int(10) NOT NULL,
  `facultad` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `facultad`
--

INSERT INTO `facultad` (`idfacultad`, `facultad`) VALUES
(1, 'Ingenieria'),
(3, 'Administracion'),
(4, 'Derecho'),
(5, 'Medicina'),
(6, 'Auditoria');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horario`
--

CREATE TABLE `horario` (
  `idhorario` int(10) NOT NULL,
  `horario` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `horario`
--

INSERT INTO `horario` (`idhorario`, `horario`) VALUES
(1, '7:00 - 9:00 L-V'),
(2, '9:00 - 11:00 L-V'),
(3, '11:00 - 13:00 L-V'),
(4, '14:00 - 16:00 L-V'),
(5, '16:00 - 18:00 L-V'),
(6, '18:00 - 20:00 L-V');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `idrol` int(10) NOT NULL COMMENT 'Clave de tipo de rol asignado a cada usuario',
  `rol` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`idrol`, `rol`) VALUES
(1, 'Administrador'),
(2, 'Alumno'),
(3, 'Catedratico');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `idusuario` int(10) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `clave` varchar(100) NOT NULL,
  `rol` int(10) NOT NULL,
  `estado` int(2) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='En esta tabla se contienen los diferentes usuarios del siste';

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`idusuario`, `nombre`, `apellido`, `correo`, `usuario`, `clave`, `rol`, `estado`) VALUES
(1, 'Abner', 'Barahona', 'admin@charveruniversity.com', 'admin', '21232f297a57a5a743894a0e4a801fc3', 1, 1),
(2, 'Ashley', 'Lopez', 'aslop@charver.edu.gt', 'ashlopez', '202cb962ac59075b964b07152d234b70', 2, 1),
(4, 'Jason', 'Montenegro', 'jsosa@charver.edu.gt', 'jsosar', '1234', 2, 1),
(5, 'Ronald ', 'Ayala', 'rayala@charver.edu.gt', 'rayala', '555555', 3, 1),
(7, 'Juan', 'Arriola', 'jarriola@gmail.com', 'jarriola', '1234', 3, 0),
(8, 'Marta', 'Ortiz', 'mortiz@charver.edu.gt', 'mortiz', '223233', 2, 1),
(9, 'Anny', 'Alicia', 'aalicia@charver.edu.gt', 'aalicia', '889898', 3, 1),
(10, 'Corina', 'Menendez', 'cmenendez@charver.edu.gt', 'cmenendez', '202cb962ac59075b964b07152d234b70', 3, 1),
(11, 'Edgar', 'mayonesa', 'emayonesa@charver.edu.gt', 'emayonesa', '66666', 2, 1),
(12, 'Elmer', 'Sosa', 'esosa@charver.edu.gt', 'esosa', '77777', 2, 1),
(13, 'Elvis', 'Samario', 'esamario@charver.edu.gt', 'esamario', '08908080808', 2, 1),
(14, 'Nestor', 'Ortiz', 'nortiz@charver.edu.gt', 'nortiz', '789fsw9dfs', 2, 1),
(15, 'Cristian', 'Carrera', 'ccarrera@charver.edu.gt', 'ccarrera', 'jhadshjads', 2, 1),
(16, 'Cesar', 'Lopez', 'clopez@gmail.com', 'clopez', 'assdfsf', 2, 1),
(17, 'Brandon', 'Chu', 'bchu@charver.edu.gt', 'bchu', '202cb962ac59075b964b07152d234b70', 1, 1),
(18, 'Pedro', 'Ramos', 'pramos@charver.edu.gt', 'pramos', '7834783478934', 2, 1),
(19, 'Werner', 'Canel', 'wcanel@charver.edu.gt', 'wcanel', '123', 2, 1),
(20, 'Gilberto', 'Monterroso', 'gmonterroso@charver.edu.gt', 'gmonterroso', '202cb962ac59075b964b07152d234b70', 1, 1),
(21, 'James', 'Cameron', 'jcameron@charver.edu.gt', 'jcameron', '202cb962ac59075b964b07152d234b70', 3, 1),
(22, 'prueba', 'prueba', 'prueba@charver.edu.gt', 'prueba', '202cb962ac59075b964b07152d234b70', 2, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alumno`
--
ALTER TABLE `alumno`
  ADD PRIMARY KEY (`carnet`),
  ADD KEY `idusuario` (`idusuario`);

--
-- Indices de la tabla `apertura_curso`
--
ALTER TABLE `apertura_curso`
  ADD PRIMARY KEY (`correlativo`),
  ADD KEY `codigo_cur` (`codigo_cur`),
  ADD KEY `codigo_cat` (`codigo_cat`),
  ADD KEY `idusuario` (`idusuario`);

--
-- Indices de la tabla `boleta`
--
ALTER TABLE `boleta`
  ADD PRIMARY KEY (`noboleta`),
  ADD KEY `usuario` (`usuario`),
  ADD KEY `carnet` (`carnet`);

--
-- Indices de la tabla `catedratico`
--
ALTER TABLE `catedratico`
  ADD PRIMARY KEY (`codigo_cat`),
  ADD KEY `idusuario` (`idusuario`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `curso`
--
ALTER TABLE `curso`
  ADD PRIMARY KEY (`codigo_cur`),
  ADD KEY `idusuario` (`idusuario`),
  ADD KEY `idfacultad` (`facultad`),
  ADD KEY `idfecha` (`horario`),
  ADD KEY `catedratico` (`catedratico`) USING BTREE;

--
-- Indices de la tabla `detalle`
--
ALTER TABLE `detalle`
  ADD PRIMARY KEY (`correlativo`),
  ADD KEY `noboleta` (`noboleta`),
  ADD KEY `codigo_cur` (`codigo_cur`),
  ADD KEY `horario` (`horario`);

--
-- Indices de la tabla `detalle_matricula`
--
ALTER TABLE `detalle_matricula`
  ADD PRIMARY KEY (`correlativo_temp`),
  ADD KEY `codigo_cur` (`codigo_cur`),
  ADD KEY `horario` (`horario`),
  ADD KEY `facultad` (`facultad`);

--
-- Indices de la tabla `facultad`
--
ALTER TABLE `facultad`
  ADD PRIMARY KEY (`idfacultad`);

--
-- Indices de la tabla `horario`
--
ALTER TABLE `horario`
  ADD PRIMARY KEY (`idhorario`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`idrol`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`idusuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alumno`
--
ALTER TABLE `alumno`
  MODIFY `carnet` int(50) NOT NULL AUTO_INCREMENT COMMENT 'carnet unico para ID alumno', AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `apertura_curso`
--
ALTER TABLE `apertura_curso`
  MODIFY `correlativo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `boleta`
--
ALTER TABLE `boleta`
  MODIFY `noboleta` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT de la tabla `catedratico`
--
ALTER TABLE `catedratico`
  MODIFY `codigo_cat` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `curso`
--
ALTER TABLE `curso`
  MODIFY `codigo_cur` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT de la tabla `detalle`
--
ALTER TABLE `detalle`
  MODIFY `correlativo` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=233;

--
-- AUTO_INCREMENT de la tabla `detalle_matricula`
--
ALTER TABLE `detalle_matricula`
  MODIFY `correlativo_temp` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=284;

--
-- AUTO_INCREMENT de la tabla `facultad`
--
ALTER TABLE `facultad`
  MODIFY `idfacultad` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `horario`
--
ALTER TABLE `horario`
  MODIFY `idhorario` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `idrol` int(10) NOT NULL AUTO_INCREMENT COMMENT 'Clave de tipo de rol asignado a cada usuario', AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `idusuario` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alumno`
--
ALTER TABLE `alumno`
  ADD CONSTRAINT `FK_IDUSUARIO` FOREIGN KEY (`idusuario`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `boleta`
--
ALTER TABLE `boleta`
  ADD CONSTRAINT `boleta_ibfk_1` FOREIGN KEY (`carnet`) REFERENCES `alumno` (`carnet`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `boleta_ibfk_2` FOREIGN KEY (`usuario`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `catedratico`
--
ALTER TABLE `catedratico`
  ADD CONSTRAINT `catedratico_ibfk_1` FOREIGN KEY (`idusuario`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `curso`
--
ALTER TABLE `curso`
  ADD CONSTRAINT `curso_ibfk_1` FOREIGN KEY (`idusuario`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `curso_ibfk_3` FOREIGN KEY (`facultad`) REFERENCES `facultad` (`idfacultad`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `curso_ibfk_4` FOREIGN KEY (`horario`) REFERENCES `horario` (`idhorario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `curso_ibfk_5` FOREIGN KEY (`catedratico`) REFERENCES `catedratico` (`codigo_cat`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle`
--
ALTER TABLE `detalle`
  ADD CONSTRAINT `detalle_ibfk_1` FOREIGN KEY (`noboleta`) REFERENCES `boleta` (`noboleta`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_ibfk_2` FOREIGN KEY (`codigo_cur`) REFERENCES `curso` (`codigo_cur`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_matricula`
--
ALTER TABLE `detalle_matricula`
  ADD CONSTRAINT `detalle_matricula_ibfk_1` FOREIGN KEY (`codigo_cur`) REFERENCES `curso` (`codigo_cur`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
