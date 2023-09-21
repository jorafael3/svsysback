-- MySQL dump 10.13  Distrib 5.5.62, for Win64 (AMD64)
--
-- Host: localhost    Database: svsys
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.24-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `guias`
--

DROP TABLE IF EXISTS `guias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `guias` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `FECHA_DE_EMISION` datetime DEFAULT NULL,
  `FACTURA` varchar(50) DEFAULT NULL,
  `TELEFONO` varchar(50) DEFAULT NULL,
  `FECHA_VALIDEZ` datetime DEFAULT NULL,
  `CLIENTE` varchar(20) DEFAULT NULL,
  `CLIENTE_RUC` varchar(20) DEFAULT NULL,
  `SOLICITANTE` varchar(100) DEFAULT NULL,
  `DIRECCION_1` varchar(20) DEFAULT NULL,
  `PTO_DE_PARTIDA` varchar(200) DEFAULT NULL,
  `PTO_DE_LLEGADA` varchar(200) DEFAULT NULL,
  `DIRECCION_2` varchar(200) DEFAULT NULL,
  `TIPO_DE_ENTREGA` varchar(200) DEFAULT NULL,
  `PEDIDO_INTERNO` varchar(50) DEFAULT NULL,
  `PED_COMPRA` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guias`
--

LOCK TABLES `guias` WRITE;
/*!40000 ALTER TABLE `guias` DISABLE KEYS */;
INSERT INTO `guias` VALUES (20,'2023-09-14 00:00:00','019005-001149898','4-2465649','2023-10-04 00:00:00','6133582 - SALVACERO ','0992681845001','SALVACERO CIA. LTDA.','VENEZUELA #3804 ENTR','','GUAYAS - GUAYAQUIL','VENEZUELA #3804 ENTRE LA 12 Y  CALLE','ENTREGADO','505420824-010','ANDEC'),(21,'2023-09-14 00:00:00','019-005-001149856','4-2465649','2023-10-04 00:00:00','6133582 - SALVACERO ','0992681845001','SALVACERO CIA. LTDA.','VENEZUELA #3804 ENTR','SUMINISTRADOR ANDEC','GUAYAS - GUAYAQUIL','VENEZUELA #3804 ENTRE LA 12 Y  CALLE','ENTREGADO','505420405','4583093878');
/*!40000 ALTER TABLE `guias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `guias_detalle`
--

DROP TABLE IF EXISTS `guias_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `guias_detalle` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `PEDIDO_INTERNO` varchar(20) DEFAULT NULL,
  `ORD` varchar(20) DEFAULT NULL,
  `CODIGO` varchar(50) DEFAULT NULL,
  `DESCRIPCION` varchar(500) DEFAULT NULL,
  `UNIDAD` varchar(20) DEFAULT NULL,
  `POR_DESPACHAR` varchar(20) DEFAULT NULL,
  `DESPACHADA` varchar(100) DEFAULT NULL,
  `ENTREGADA` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guias_detalle`
--

LOCK TABLES `guias_detalle` WRITE;
/*!40000 ALTER TABLE `guias_detalle` DISABLE KEYS */;
INSERT INTO `guias_detalle` VALUES (3,'505420824-010','010','91001871','Varilla Corrugada 10mm x 12m ANDEC','UN','990.00','',''),(4,'505420405','01','91001868','Varilla Corrugada 08mm x 12m ANDEC','UN','1,950.00','','');
/*!40000 ALTER TABLE `guias_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sis_menu`
--

DROP TABLE IF EXISTS `sis_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sis_menu` (
  `menu_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(50) DEFAULT NULL,
  `ruta` varchar(500) DEFAULT NULL,
  `icono` varchar(50) DEFAULT NULL,
  `vista` varchar(1000) DEFAULT NULL,
  `variable` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`menu_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sis_menu`
--

LOCK TABLES `sis_menu` WRITE;
/*!40000 ALTER TABLE `sis_menu` DISABLE KEYS */;
INSERT INTO `sis_menu` VALUES (1,'Dashboard','dashboard','cilSpeedometer','dashboard/Dashboard','dashboard'),(2,'Despacho','despacho','cilNotes',NULL,NULL),(3,'Mantenimiento','mantenimiento','cilPuzzle',NULL,NULL),(4,'Facturas','Facuras','cilPuzzle','facturas/Facturas','facturas'),(5,'Desarrollo','desarrollo','cilPuzzle',NULL,'');
/*!40000 ALTER TABLE `sis_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sis_submenu`
--

DROP TABLE IF EXISTS `sis_submenu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sis_submenu` (
  `submenu_ID` int(11) NOT NULL AUTO_INCREMENT,
  `padre_id` int(11) DEFAULT NULL,
  `sub_nombre` varchar(200) DEFAULT NULL,
  `ruta` varchar(500) DEFAULT NULL,
  `icono` varchar(50) DEFAULT NULL,
  `vista` varchar(1000) DEFAULT NULL,
  `variable` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`submenu_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sis_submenu`
--

LOCK TABLES `sis_submenu` WRITE;
/*!40000 ALTER TABLE `sis_submenu` DISABLE KEYS */;
INSERT INTO `sis_submenu` VALUES (1,2,'Guias','despacho/guias',NULL,'despacho/guias/Guias','guias'),(2,2,'Administracion','despacho/administrar',NULL,'despacho/administrar/Administrar','administracion'),(3,3,'Usuarios','mantenimiento/usuarios',NULL,'mantenimiento/usuarios/Usuarios','usuarios'),(4,3,'Clientes','mantenimiento/clientes',NULL,NULL,'clientes'),(5,5,'Scrapys','desarrollo/scrapy',NULL,'desarrollo/scrapy/Scrapy','scrapy');
/*!40000 ALTER TABLE `sis_submenu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sis_usuario_accesos`
--

DROP TABLE IF EXISTS `sis_usuario_accesos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sis_usuario_accesos` (
  `usuario_ID` int(11) DEFAULT NULL,
  `menu_ID` int(11) DEFAULT NULL,
  `submenu_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sis_usuario_accesos`
--

LOCK TABLES `sis_usuario_accesos` WRITE;
/*!40000 ALTER TABLE `sis_usuario_accesos` DISABLE KEYS */;
INSERT INTO `sis_usuario_accesos` VALUES (1,1,NULL),(1,2,NULL),(1,2,1),(1,2,2),(1,3,NULL),(1,3,3),(1,5,NULL),(1,5,5);
/*!40000 ALTER TABLE `sis_usuario_accesos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `Usuario_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Usuario` varchar(20) DEFAULT NULL,
  `Nombre` varchar(100) DEFAULT NULL,
  `password` varchar(500) DEFAULT NULL,
  `fecha_creado` datetime NOT NULL DEFAULT current_timestamp(),
  `Estado` int(11) DEFAULT 1,
  `email` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`Usuario_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'JALVARADO','Jorge','12345','2023-09-14 16:38:17',1,NULL);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'svsys'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-09-21 17:59:59
