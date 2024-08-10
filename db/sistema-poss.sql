-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-04-2024 a las 19:02:19
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sistema-poss`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `prc_ActualizarDetalleVenta` (IN `p_codigo_producto` VARCHAR(20), IN `p_cantidad` FLOAT, IN `p_id` INT)   BEGIN

 declare v_nro_boleta varchar(20);
 declare v_total_venta float;

/*
ACTUALIZAR EL STOCK DEL PRODUCTO QUE SEA MODIFICADO
......
.....
.......
*/

/*
ACTULIZAR CODIGO, CANTIDAD Y TOTAL DEL ITEM MODIFICADO
*/

 UPDATE venta_detalle 
 SET codigo_producto = p_codigo_producto, 
 cantidad = p_cantidad, 
 total_venta = (p_cantidad * (select precio_venta_producto from productos where codigo_producto = p_codigo_producto))
 WHERE id = p_id;
 
 set v_nro_boleta = (select nro_boleta from venta_detalle where id = p_id);
 set v_total_venta = (select sum(total_venta) from venta_detalle where nro_boleta = v_nro_boleta);
 
 update venta_cabecera
   set total_venta = v_total_venta
 where nro_boleta = v_nro_boleta;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `prc_eliminar_venta` (IN `p_nro_boleta` VARCHAR(8))   BEGIN

DECLARE v_codigo VARCHAR(20);
DECLARE v_cantidad FLOAT;
DECLARE done INT DEFAULT FALSE;

DECLARE cursor_i CURSOR FOR 
SELECT codigo_producto,cantidad 
FROM venta_detalle 
where CAST(nro_boleta AS CHAR CHARACTER SET utf8)  = CAST(p_nro_boleta AS CHAR CHARACTER SET utf8) ;

DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

OPEN cursor_i;
read_loop: LOOP
FETCH cursor_i INTO v_codigo, v_cantidad;

	IF done THEN
	  LEAVE read_loop;
	END IF;
    
    UPDATE PRODUCTOS 
       SET stock_producto = stock_producto + v_cantidad
    WHERE CAST(codigo_producto AS CHAR CHARACTER SET utf8) = CAST(v_codigo AS CHAR CHARACTER SET utf8);
    
END LOOP;
CLOSE cursor_i;

DELETE FROM VENTA_DETALLE WHERE CAST(nro_boleta AS CHAR CHARACTER SET utf8) = CAST(p_nro_boleta AS CHAR CHARACTER SET utf8) ;
DELETE FROM VENTA_CABECERA WHERE CAST(nro_boleta AS CHAR CHARACTER SET utf8)  = CAST(p_nro_boleta AS CHAR CHARACTER SET utf8) ;

SELECT 'Se eliminó correctamente la venta';
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `prc_ListarCategorias` ()   BEGIN
select * from categorias;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `prc_ListarProductos` ()   SELECT  '' as detalles,
		'' as acciones,
		codigo_producto,
		p.id_categoria,
        imagen,
		upper(c.descripcion) as nombre_categoria,
		upper(p.descripcion) as producto,
        p.id_tipo_afectacion_igv,
        upper(tai.descripcion) as tipo_afectacion_igv,
        p.id_unidad_medida,
        upper(cum.descripcion) as unidad_medida,
		ROUND(costo_unitario,2) as costo_unitario,
		ROUND(precio_unitario_con_igv,2) as precio_unitario_con_igv,
        ROUND(precio_unitario_sin_igv,2) as precio_unitario_sin_igv,
        ROUND(precio_unitario_mayor_con_igv,2) as precio_unitario_mayor_con_igv,
        ROUND(precio_unitario_mayor_sin_igv,2) as precio_unitario_mayor_sin_igv,
        ROUND(precio_unitario_oferta_con_igv,2) as precio_unitario_oferta_con_igv,
        ROUND(precio_unitario_oferta_sin_igv,2) as precio_unitario_oferta_sin_igv,
		stock,
		minimo_stock,
		ventas,
		ROUND(costo_total,2) as costo_total,
		p.fecha_creacion,
		p.fecha_actualizacion,
        case when p.estado = 1 then 'ACTIVO' else 'INACTIVO' end estado
	FROM productos p INNER JOIN categorias c on p.id_categoria = c.id
					 inner join tipo_afectacion_igv tai on tai.codigo = p.id_tipo_afectacion_igv
					inner join codigo_unidad_medida cum on cum.id = p.id_unidad_medida
    WHERE p.estado in (0,1)
	order by p.codigo_producto desc$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `prc_ListarProductosMasVendidos` ()  NO SQL BEGIN

select  p.codigo_producto,
		p.descripcion,
        sum(vd.cantidad) as cantidad,
        sum(Round(vd.importe_total,2)) as total_venta
from detalle_venta vd inner join productos p on vd.codigo_producto = p.codigo_producto
group by p.codigo_producto,
		p.descripcion
order by  sum(Round(vd.importe_total,2)) DESC
limit 10;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `prc_ListarProductosPocoStock` ()  NO SQL BEGIN
select p.codigo_producto,
		p.descripcion,
        p.stock,
        p.minimo_stock
from productos p
where p.stock <= p.minimo_stock
order by p.stock asc;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `prc_movimentos_arqueo_caja_por_usuario` (`p_id_usuario` INT)   BEGIN

select 
ac.monto_apertura as y,
'MONTO APERTURA' as label,
"#6c757d" as color
from arqueo_caja ac inner join usuarios usu on ac.id_usuario = usu.id_usuario
where ac.id_usuario = p_id_usuario
and date(ac.fecha_apertura) = curdate()
union  
select 
ac.ingresos as y,
'INGRESOS' as label,
"#28a745" as color
from arqueo_caja ac inner join usuarios usu on ac.id_usuario = usu.id_usuario
where ac.id_usuario = p_id_usuario
and date(ac.fecha_apertura) = curdate()
union
select 
ac.devoluciones as y,
'DEVOLUCIONES' as label,
"#ffc107" as color
from arqueo_caja ac inner join usuarios usu on ac.id_usuario = usu.id_usuario
where ac.id_usuario = p_id_usuario
and date(ac.fecha_apertura) = curdate()
union
select 
ac.gastos as y,
'GASTOS' as label,
"#17a2b8" as color
from arqueo_caja ac inner join usuarios usu on ac.id_usuario = usu.id_usuario
where ac.id_usuario = p_id_usuario
and date(ac.fecha_apertura) = curdate();
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `prc_ObtenerDatosDashboard` ()  NO SQL BEGIN
  DECLARE totalProductos int;
  DECLARE totalCompras float;
  DECLARE totalVentas float;
  DECLARE ganancias float;
  DECLARE productosPocoStock int;
  DECLARE ventasHoy float;

  SET totalProductos = (SELECT
      COUNT(*)
    FROM productos p);
    
  SET totalCompras = (SELECT
      SUM(p.costo_total)
    FROM productos p);  

	SET totalVentas = 0;
  SET totalVentas = (SELECT
      SUM(v.importe_total)
    FROM venta v);

  SET ganancias = 0;
  SET ganancias = (SELECT
      SUM(dv.importe_total) - SUM(dv.cantidad * dv.costo_unitario)
    FROM detalle_venta dv);
    
  SET productosPocoStock = (SELECT
      COUNT(1)
    FROM productos p
    WHERE p.stock <= p.minimo_stock);
    
    SET ventasHoy = 0;
  SET ventasHoy = (SELECT
      SUM(v.importe_total)
    FROM venta v
    WHERE DATE(v.fecha_emision) = CURDATE());

  SELECT
    IFNULL(totalProductos, 0) AS totalProductos,
    IFNULL(CONCAT('S./ ', FORMAT(totalCompras, 2)), 0) AS totalCompras,
    IFNULL(CONCAT('S./ ', FORMAT(totalVentas, 2)), 0) AS totalVentas,
    IFNULL(CONCAT('S./ ', FORMAT(ganancias, 2), ' - ','  % ', FORMAT((ganancias / totalVentas) *100,2)), 0) AS ganancias,
    IFNULL(productosPocoStock, 0) AS productosPocoStock,
    IFNULL(CONCAT('S./ ', FORMAT(ventasHoy, 2)), 0) AS ventasHoy;



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `prc_obtenerNroBoleta` ()  NO SQL select serie_boleta,
		IFNULL(LPAD(max(c.nro_correlativo_venta)+1,8,'0'),'00000001') nro_venta 
from empresa c$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `prc_ObtenerVentasMesActual` ()  NO SQL BEGIN
SELECT date(vc.fecha_emision) as fecha_venta,
		sum(round(vc.importe_total,2)) as total_venta,
        ifnull((SELECT sum(round(vc1.importe_total,2))
			FROM venta vc1
		where date(vc1.fecha_emision) >= date(last_day(now() - INTERVAL 2 month) + INTERVAL 1 day)
		and date(vc1.fecha_emision) <= last_day(last_day(now() - INTERVAL 2 month) + INTERVAL 1 day)
        and date(vc1.fecha_emision) = DATE_ADD(date(vc.fecha_emision), INTERVAL -1 MONTH)
		group by date(vc1.fecha_emision)),0) as total_venta_ant
FROM venta vc
where date(vc.fecha_emision) >= date(last_day(now() - INTERVAL 1 month) + INTERVAL 1 day)
and date(vc.fecha_emision) <= last_day(date(CURRENT_DATE))
group by date(vc.fecha_emision);


END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `prc_ObtenerVentasMesAnterior` ()  NO SQL BEGIN
SELECT date(vc.fecha_venta) as fecha_venta,
		sum(round(vc.total_venta,2)) as total_venta,
        sum(round(vc.total_venta,2)) as total_venta_ant
FROM venta_cabecera vc
where date(vc.fecha_venta) >= date(last_day(now() - INTERVAL 2 month) + INTERVAL 1 day)
and date(vc.fecha_venta) <= last_day(last_day(now() - INTERVAL 2 month) + INTERVAL 1 day)
group by date(vc.fecha_venta);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `prc_registrar_kardex_bono` (IN `p_codigo_producto` VARCHAR(20), IN `p_concepto` VARCHAR(100), IN `p_nuevo_stock` FLOAT)   BEGIN

	/*VARIABLES PARA EXISTENCIAS ACTUALES*/
	declare v_unidades_ex float;
	declare v_costo_unitario_ex float;    
	declare v_costo_total_ex float;
    
    declare v_unidades_in float;
	declare v_costo_unitario_in float;    
	declare v_costo_total_in float;
    
	/*OBTENEMOS LAS ULTIMAS EXISTENCIAS DEL PRODUCTO*/    
    SELECT k.ex_costo_unitario , k.ex_unidades, k.ex_costo_total
    into v_costo_unitario_ex, v_unidades_ex, v_costo_total_ex
    FROM KARDEX K
    WHERE K.CODIGO_PRODUCTO = p_codigo_producto
    ORDER BY ID DESC
    LIMIT 1;
    
    /*SETEAMOS LOS VALORES PARA EL REGISTRO DE INGRESO*/
    SET v_unidades_in = p_nuevo_stock - v_unidades_ex;
    SET v_costo_unitario_in = v_costo_unitario_ex;
    SET v_costo_total_in = v_unidades_in * v_costo_unitario_in;
    
    /*SETEAMOS LAS EXISTENCIAS ACTUALES*/
    SET v_unidades_ex = ROUND(p_nuevo_stock,2);    
    SET v_costo_total_ex = ROUND(v_costo_total_ex + v_costo_total_in,2);
    
    IF(v_costo_total_ex > 0) THEN
		SET v_costo_unitario_ex = ROUND(v_costo_total_ex/v_unidades_ex,2);
	else
		SET v_costo_unitario_ex = ROUND(0,2);
    END IF;
    
        
	INSERT INTO KARDEX(codigo_producto,
						fecha,
                        concepto,
                        comprobante,
                        in_unidades,
                        in_costo_unitario,
                        in_costo_total,
                        ex_unidades,
                        ex_costo_unitario,
                        ex_costo_total)
				VALUES(p_codigo_producto,
						curdate(),
                        p_concepto,
                        '',
                        v_unidades_in,
                        v_costo_unitario_in,
                        v_costo_total_in,
                        v_unidades_ex,
                        v_costo_unitario_ex,
                        v_costo_total_ex);

	/*ACTUALIZAMOS EL STOCK, EL NRO DE VENTAS DEL PRODUCTO*/
	UPDATE PRODUCTOS 
	SET stock = v_unidades_ex, 
         costo_unitario = v_costo_unitario_ex,
         costo_total= v_costo_total_ex
	WHERE codigo_producto = p_codigo_producto ;                      

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `prc_registrar_kardex_compra` (IN `p_id_compra` INT, IN `p_comprobante` VARCHAR(20), IN `p_codigo_producto` VARCHAR(20), IN `p_concepto` VARCHAR(100), IN `p_cantidad_compra` FLOAT, IN `p_costo_compra` FLOAT)   BEGIN

	/*VARIABLES PARA EXISTENCIAS ACTUALES*/
	declare v_unidades_ex float;
	declare v_costo_unitario_ex float;    
	declare v_costo_total_ex float;
    
    declare v_unidades_in float;
	declare v_costo_unitario_in float;    
	declare v_costo_total_in float;
    
	/*OBTENEMOS LAS ULTIMAS EXISTENCIAS DEL PRODUCTO*/    
    SELECT k.ex_costo_unitario , k.ex_unidades, k.ex_costo_total
    into v_costo_unitario_ex, v_unidades_ex, v_costo_total_ex
    FROM KARDEX K
    WHERE K.CODIGO_PRODUCTO = p_codigo_producto
    ORDER BY ID DESC
    LIMIT 1;
    
    /*SETEAMOS LOS VALORES PARA EL REGISTRO DE INGRESO*/
    SET v_unidades_in = p_cantidad_compra;
    SET v_costo_unitario_in = p_costo_compra;
    SET v_costo_total_in = v_unidades_in * v_costo_unitario_in;
    
    /*SETEAMOS LAS EXISTENCIAS ACTUALES*/
    SET v_unidades_ex = v_unidades_ex + ROUND(p_cantidad_compra,2);    
    SET v_costo_total_ex = ROUND(v_costo_total_ex + v_costo_total_in,2);
    SET v_costo_unitario_ex = ROUND(v_costo_total_ex/v_unidades_ex,2);

	INSERT INTO KARDEX(codigo_producto,
						fecha,
                        concepto,
                        comprobante,
                        in_unidades,
                        in_costo_unitario,
                        in_costo_total,
                        ex_unidades,
                        ex_costo_unitario,
                        ex_costo_total)
				VALUES(p_codigo_producto,
						curdate(),
                        p_concepto,
                        p_comprobante,
                        v_unidades_in,
                        v_costo_unitario_in,
                        v_costo_total_in,
                        v_unidades_ex,
                        v_costo_unitario_ex,
                        v_costo_total_ex);

	/*ACTUALIZAMOS EL STOCK, EL NRO DE VENTAS DEL PRODUCTO*/
	UPDATE PRODUCTOS 
	SET stock = v_unidades_ex, 
         costo_unitario = v_costo_unitario_ex,
         costo_total= v_costo_total_ex
	WHERE codigo_producto = p_codigo_producto ;  
  

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `prc_registrar_kardex_existencias` (IN `p_codigo_producto` VARCHAR(25), IN `p_concepto` VARCHAR(100), IN `p_comprobante` VARCHAR(100), IN `p_unidades` FLOAT, IN `p_costo_unitario` FLOAT, IN `p_costo_total` FLOAT)   BEGIN
  INSERT INTO KARDEX (codigo_producto, fecha, concepto, comprobante, ex_unidades, ex_costo_unitario, ex_costo_total)
    VALUES (p_codigo_producto, CURDATE(), p_concepto, p_comprobante, p_unidades, p_costo_unitario, p_costo_total);

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `prc_registrar_kardex_vencido` (IN `p_codigo_producto` VARCHAR(20), IN `p_concepto` VARCHAR(100), IN `p_nuevo_stock` FLOAT)   BEGIN

	declare v_unidades_ex float;
	declare v_costo_unitario_ex float;    
	declare v_costo_total_ex float;
    
    declare v_unidades_out float;
	declare v_costo_unitario_out float;    
	declare v_costo_total_out float;
    
	/*OBTENEMOS LAS ULTIMAS EXISTENCIAS DEL PRODUCTO*/    
    SELECT k.ex_costo_unitario , k.ex_unidades, k.ex_costo_total
    into v_costo_unitario_ex, v_unidades_ex, v_costo_total_ex
    FROM KARDEX K
    WHERE K.CODIGO_PRODUCTO = p_codigo_producto
    ORDER BY ID DESC
    LIMIT 1;
    
    /*SETEAMOS LOS VALORES PARA EL REGISTRO DE SALIDA*/
    SET v_unidades_out = v_unidades_ex - p_nuevo_stock;
    SET v_costo_unitario_out = v_costo_unitario_ex;
    SET v_costo_total_out = v_unidades_out * v_costo_unitario_out;
    
    /*SETEAMOS LAS EXISTENCIAS ACTUALES*/
    SET v_unidades_ex = ROUND(p_nuevo_stock,2);    
    SET v_costo_total_ex = ROUND(v_costo_total_ex - v_costo_total_out,2);
    
    IF(v_costo_total_ex > 0) THEN
		SET v_costo_unitario_ex = ROUND(v_costo_total_ex/v_unidades_ex,2);
	else
		SET v_costo_unitario_ex = ROUND(0,2);
    END IF;
    
        
	INSERT INTO KARDEX(codigo_producto,
						fecha,
                        concepto,
                        comprobante,
                        out_unidades,
                        out_costo_unitario,
                        out_costo_total,
                        ex_unidades,
                        ex_costo_unitario,
                        ex_costo_total)
				VALUES(p_codigo_producto,
						curdate(),
                        p_concepto,
                        '',
                        v_unidades_out,
                        v_costo_unitario_out,
                        v_costo_total_out,
                        v_unidades_ex,
                        v_costo_unitario_ex,
                        v_costo_total_ex);

	/*ACTUALIZAMOS EL STOCK, EL NRO DE VENTAS DEL PRODUCTO*/
	UPDATE PRODUCTOS 
	SET stock = v_unidades_ex, 
         costo_unitario = v_costo_unitario_ex,
        costo_total = v_costo_total_ex
	WHERE codigo_producto = p_codigo_producto ;                      

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `prc_registrar_kardex_venta` (IN `p_codigo_producto` VARCHAR(20), IN `p_fecha` DATE, IN `p_concepto` VARCHAR(100), IN `p_comprobante` VARCHAR(100), IN `p_unidades` FLOAT)   BEGIN

	declare v_unidades_ex float;
	declare v_costo_unitario_ex float;    
	declare v_costo_total_ex float;
    
    declare v_unidades_out float;
	declare v_costo_unitario_out float;    
	declare v_costo_total_out float;
    

	/*OBTENEMOS LAS ULTIMAS EXISTENCIAS DEL PRODUCTO*/
    
    SELECT k.ex_costo_unitario , k.ex_unidades, k.ex_costo_total
    into v_costo_unitario_ex, v_unidades_ex, v_costo_total_ex
    FROM KARDEX K
    WHERE K.CODIGO_PRODUCTO = p_codigo_producto
    ORDER BY ID DESC
    LIMIT 1;
    
    /*SETEAMOS LOS VALORES PARA EL REGISTRO DE SALIDA*/
    SET v_unidades_out = p_unidades;
    SET v_costo_unitario_out = v_costo_unitario_ex;
    SET v_costo_total_out = p_unidades * v_costo_unitario_ex;
    
    /*SETEAMOS LAS EXISTENCIAS ACTUALES*/
    SET v_unidades_ex = ROUND(v_unidades_ex - v_unidades_out,2);    
    SET v_costo_total_ex = ROUND(v_costo_total_ex -  v_costo_total_out,2);
    
    IF(v_costo_total_ex > 0) THEN
		SET v_costo_unitario_ex = ROUND(v_costo_total_ex/v_unidades_ex,2);
	else
		SET v_costo_unitario_ex = ROUND(0,2);
    END IF;
    
        
	INSERT INTO KARDEX(codigo_producto,
						fecha,
                        concepto,
                        comprobante,
                        out_unidades,
                        out_costo_unitario,
                        out_costo_total,
                        ex_unidades,
                        ex_costo_unitario,
                        ex_costo_total)
				VALUES(p_codigo_producto,
						p_fecha,
                        p_concepto,
                        p_comprobante,
                        v_unidades_out,
                        v_costo_unitario_out,
                        v_costo_total_out,
                        v_unidades_ex,
                        v_costo_unitario_ex,
                        v_costo_total_ex);

	/*ACTUALIZAMOS EL STOCK, EL NRO DE VENTAS DEL PRODUCTO*/
	UPDATE PRODUCTOS 
	SET stock = v_unidades_ex, 
		ventas = ventas + v_unidades_out,
        costo_unitario = v_costo_unitario_ex,
        costo_total = v_costo_total_ex
	WHERE codigo_producto = p_codigo_producto ;                      

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `prc_registrar_venta_detalle` (IN `p_nro_boleta` VARCHAR(8), IN `p_codigo_producto` VARCHAR(20), IN `p_cantidad` FLOAT, IN `p_total_venta` FLOAT)   BEGIN
declare v_precio_compra float;
declare v_precio_venta float;

SELECT p.precio_compra_producto,p.precio_venta_producto
into v_precio_compra, v_precio_venta
FROM productos p
WHERE p.codigo_producto  = p_codigo_producto;
    
INSERT INTO venta_detalle(nro_boleta,codigo_producto, cantidad, costo_unitario_venta,precio_unitario_venta,total_venta, fecha_venta) 
VALUES(p_nro_boleta,p_codigo_producto,p_cantidad, v_precio_compra, v_precio_venta,p_total_venta,curdate());
                                                        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `prc_top_ventas_categorias` ()   BEGIN

select cast(sum(vd.importe_total)  AS DECIMAL(8,2)) as y, c.descripcion as label
    from detalle_venta vd inner join productos p on vd.codigo_producto = p.codigo_producto
                        inner join categorias c on c.id = p.id_categoria
    group by c.descripcion
    LIMIT 10;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `prc_truncate_all_tables` ()   BEGIN

SET FOREIGN_KEY_CHECKS = 0;

truncate table venta;
truncate table detalle_venta;
truncate table compras;
truncate table detalle_compra;
truncate table kardex;
truncate table categorias;
truncate table tipo_afectacion_igv;
truncate table codigo_unidad_medida;
truncate table productos;

SET FOREIGN_KEY_CHECKS = 1;

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `arqueo_caja`
--

CREATE TABLE `arqueo_caja` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_apertura` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_cierre` datetime DEFAULT NULL,
  `monto_apertura` float NOT NULL,
  `ingresos` float DEFAULT NULL,
  `devoluciones` float DEFAULT NULL,
  `gastos` float DEFAULT NULL,
  `monto_final` float DEFAULT NULL,
  `estado` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `arqueo_caja`
--

INSERT INTO `arqueo_caja` (`id`, `id_usuario`, `fecha_apertura`, `fecha_cierre`, `monto_apertura`, `ingresos`, `devoluciones`, `gastos`, `monto_final`, `estado`) VALUES
(1, 2, '2023-09-17 20:28:00', '2023-09-17 22:07:39', 80, 475.8, NULL, 30, 525.8, 0),
(2, 2, '2023-09-16 22:36:06', '2023-09-16 22:36:06', 80, NULL, NULL, NULL, NULL, 0),
(3, 2, '2023-09-15 22:59:27', '2023-09-15 22:59:32', 80, 0, 0, 0, 80, 0),
(4, 2, '2023-09-18 23:36:20', '2023-09-18 23:37:02', 80, 0, 0, 0, 80, 0),
(5, 2, '2023-09-19 11:25:27', '2023-09-19 11:25:19', 240, 0, 36, 0, 160, 1),
(6, 1, '2023-09-19 20:01:22', '2023-09-19 20:01:16', 240, 0, 9, 0, 231, 1),
(7, 1, '2023-09-20 12:49:33', NULL, 80, NULL, NULL, NULL, 80, 1),
(8, 1, '2023-09-21 21:50:05', '2023-09-21 21:50:00', 160, 0, 0, 0, 80, 1),
(9, 1, '2023-09-24 23:09:59', NULL, 80, NULL, 15, NULL, 65, 1),
(10, 1, '2023-09-26 23:42:17', '2023-09-26 23:42:27', 80, 0, 0, 0, 80, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(150) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT NULL,
  `estado` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `descripcion`, `fecha_creacion`, `fecha_actualizacion`, `estado`) VALUES
(1, 'Materia Prima', '2024-03-20 20:58:02', NULL, 1),
(2, 'Materiales', '2024-03-20 20:58:02', NULL, 1),
(3, 'accesorios', '2024-03-20 20:58:02', NULL, 1),
(4, 'Otros', '2024-03-20 20:58:02', NULL, 1),
(5, '', '2024-03-20 20:58:02', NULL, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `rtn` varchar(50) DEFAULT NULL,
  `nombres_apellidos_razon_social` varchar(255) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `estado` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `rtn`, `nombres_apellidos_razon_social`, `direccion`, `telefono`, `estado`) VALUES
(26, NULL, 'Joseluis', 'Valle', '7845120', 1),
(27, '', '', '', '', 1),
(28, '', '', '', '', 1),
(29, '8956431285', 'Julian Betanco', 'Casa 15', '87371035', 1),
(30, 'Colinas', 'Antonela', 'Tegucigalpa', '87371035', 1),
(31, 'Colinas', 'Antonela', 'Tegucigalpa', '87371035', 1),
(32, '8956431285', 'miranda montes', 'Casa 15', '87371035', 1),
(33, 'montes', 'carminda ', 'Casa 15', '87371035', 1),
(34, '', 'Julian Betanco', 'Casa 15', '87371035', 1),
(35, '8956431285', 'Julian Betanco', 'Casa 15', '87371035', 1),
(36, 'Galas', 'Mateo', 'Casa 15', '87371035', 1),
(37, '895623', 'benites', 'Casa 15', '87371035', 1),
(38, '895623', 'benites', 'Casa 15', '87371035', 1),
(39, '895623', 'Marleni', 'Casa 15', '87371035', 1),
(40, '895623', 'Marleni', 'Casa 15', '87371035', 1),
(41, '895623', 'Marleni', 'Casa 15', '87371035', 1),
(42, '895623', 'Marleni', 'Casa 15', '87371035', 1),
(43, '8956431285', 'miranda montes', 'Casa 15', '87371035', 1),
(44, 'olmedo', 'blanca', 'Casa 15', '87371035', 1),
(45, 'nole', 'Melendez', 'Casa 15', '87371035', 1),
(46, 'volunad', 'Permel', 'Col. Altos de Loarque', '87371035', 1),
(47, 'Matiaz', 'Julian Betanco', 'Col. Altos de Loarque', '87371035', 1),
(48, '', 'Julian Betanco', 'Casa 15', '87371035', 1),
(49, NULL, NULL, NULL, NULL, 1),
(50, '123456789', 'Juan Perez', 'Calle Principal', '987654321', 1),
(51, '123456789', 'Juan Perez', 'Calle Principal', '987654321', 1),
(52, '123456789', 'Juan Perez', 'Calle Principal', '987654321', 1),
(53, '123456789', 'Juan Perez', 'Calle Principal', '987654321', 1),
(54, '123456789', 'Juan Perez', 'Calle Principal', '987654321', 1),
(55, '784512', 'Julieta', 'Roatan', '895623', 1),
(56, '895623', 'Julian Betanco', 'Casa 15', '87371035', 1),
(57, '05243185', 'Helenola', 'Cantarrana', '54123698', 1),
(58, '895623', 'Julian Betanco', 'Casa 15', '87371035', 1),
(59, 'solorsano', 'Guadalupe', 'Casa 15', '87371035', 1),
(60, '8956431285', 'Julian Betanco', 'Casa 15', '87371035', 1),
(61, '89564', 'Julian Betanco', 'Col. Altos de Loarque', '87371035', 1),
(62, '89564', 'Julian Betanco', 'Col. Altos de Loarque', '87371035', 1),
(63, '8956431285', 'miranda montes', 'Casa 15', '87371035', 1),
(64, '8956431285', 'Julian Betanco', 'Col. Altos de Loarque', '87371035', 1),
(65, '05478963', 'Marleni', 'Col. Altos de Loarque', '87371035', 1),
(66, '895623', 'miranda montes', 'Casa 15', '87371035', 1),
(67, 'Matiaz', 'miranda montes', 'Col. Altos de Loarque', '87371035', 1),
(68, 'flores', 'pamela', 'Ceiba', '895623', 1),
(69, '895623', 'Joel', 'Casa 15', '87371035', 1),
(70, 'solucia', 'Valentin', 'cantarrana', '87371035', 1),
(71, '8956431285', 'Josue', 'miramontes', '5645676878798', 1),
(72, '8956431285', 'mariadelos', 'Casa 15', '87371035', 1),
(73, '', 'miranda montes', 'Col. Altos de Loarque', '87371035', 1),
(74, '8956431285', 'Hector', 'Comayagua', '87371035', 1),
(75, '8956431285', 'Daniel', 'Col. Altos de Loarque', '87371035', 1),
(76, '', 'Jose Feliciano', 'Lempira', '96532747', 1),
(77, '759645665664466666', '', '', '', 1),
(78, '8956431285', 'Ismael', 'Lempira', '87845689', 1),
(79, '89564', 'Patricio', 'Choluteca', '895623', 1),
(80, '', 'Gustavo', 'Colon', '84516231', 1),
(81, '8956234566456', 'Luis ', 'Torocagua', '87542108', 1),
(82, '895623', 'Marco cerrano', 'San Miguel', '98654712', 1),
(83, '895623147', 'Bustamante', 'Miramontes', '84512369', 1),
(84, '0801199820578', 'Ramiro', 'Col. Bella vista', '22012300', 1),
(85, '0801199002567', 'Alex Cruz', 'Col. Lomas', '88754523', 1),
(86, '0801200078656', 'Henry', 'Loarque', '98786756', 1),
(87, '0824199856789', 'Rocio Lopez', '3 de mayo', '87654321', 1),
(88, '123344343434232322', 'pam', 'sj', '', 1),
(89, '3454533232', 'ware', 'sr', '', 1),
(90, '0801-1993-00567', 'maria de los angeles valladares', 'col. Los Laureles', '9770-5645', 1),
(91, '8956-4852-521', 'miranda montes', 'Casa 15', '8737-1035', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `codigo_unidad_medida`
--

CREATE TABLE `codigo_unidad_medida` (
  `id` varchar(3) NOT NULL,
  `descripcion` varchar(150) NOT NULL,
  `estado` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `codigo_unidad_medida`
--

INSERT INTO `codigo_unidad_medida` (`id`, `descripcion`, `estado`) VALUES
('NIU', 'UNIDAD', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `id` int(11) NOT NULL,
  `id_proveedor` int(11) DEFAULT NULL,
  `fecha_compra` datetime DEFAULT NULL,
  `id_tipo_comprobante` varchar(3) DEFAULT NULL,
  `serie` varchar(10) DEFAULT NULL,
  `correlativo` varchar(20) DEFAULT NULL,
  `id_moneda` varchar(3) DEFAULT NULL,
  `ope_exonerada` float DEFAULT NULL,
  `ope_inafecta` float DEFAULT NULL,
  `ope_gravada` float DEFAULT NULL,
  `total_igv` float DEFAULT NULL,
  `descuento` float DEFAULT NULL,
  `total_compra` float DEFAULT NULL,
  `estado` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `compras`
--

INSERT INTO `compras` (`id`, `id_proveedor`, `fecha_compra`, `id_tipo_comprobante`, `serie`, `correlativo`, `id_moneda`, `ope_exonerada`, `ope_inafecta`, `ope_gravada`, `total_igv`, `descuento`, `total_compra`, `estado`) VALUES
(1, 4, '2024-04-23 00:00:00', '01', 'F001', NULL, 'LPS', 1706.95, 0, 0, 0, 0, 1706.95, 2),
(2, 5, '2024-04-23 00:00:00', '01', 'F001', NULL, 'LPS', 2604.34, 0, 0, 0, 0, 2604.34, 2),
(3, 6, '2024-04-23 00:00:00', '01', 'F001', NULL, 'LPS', 31026.1, 0, 0, 0, 0, 31026.1, 2),
(4, 7, '2024-04-23 00:00:00', '01', 'F001', NULL, 'LPS', 428, 0, 0, 0, 0, 428, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_compra`
--

CREATE TABLE `detalle_compra` (
  `id` int(11) NOT NULL,
  `id_compra` int(11) DEFAULT NULL,
  `codigo_producto` varchar(20) DEFAULT NULL,
  `cantidad` float DEFAULT NULL,
  `costo_unitario` float DEFAULT NULL,
  `descuento` float DEFAULT NULL,
  `subtotal` float DEFAULT NULL,
  `impuesto` float DEFAULT NULL,
  `total` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `detalle_compra`
--

INSERT INTO `detalle_compra` (`id`, `id_compra`, `codigo_producto`, `cantidad`, `costo_unitario`, `descuento`, `subtotal`, `impuesto`, `total`) VALUES
(5, 2, '20201', 1, 350, 0, 304.35, 45.65, 350),
(6, 2, '20101', 1, 190, 0, 165.22, 24.78, 190),
(7, 3, '40108', 1, 50, 0, 50, 0, 50),
(10, 5, '40107', 5, 100, 0, 500, 0, 500),
(11, 6, '40108', 15, 100, 0, 1500, 0, 1500),
(12, 4, '40104', 5, 400, 0, 2000, 0, 2000),
(13, 4, '40103', 8, 500, 0, 4000, 0, 4000),
(14, 7, '40106', 5, 340, 0, 1700, 0, 1700),
(15, 8, '40108', 10, NULL, 0, 0, 0, 0),
(16, 9, '40105', 10, 20, 0, 200, 0, 200),
(17, 10, '40102', 10, 50, 0, 500, 0, 500),
(18, 11, '40109', 10, 150, 0, 1500, 0, 1500),
(20, 2, '40108', 2, 99, 0, 198, 0, 198),
(21, 3, '40109', 5, 150, 0, 750, 0, 750),
(22, 3, '40107', 6, 22.61, 0, 135.66, 0, 135.66),
(23, 4, '40109', 1, 150, 0, 150, 0, 150),
(24, 4, '40108', 2, 99, 0, 198, 0, 198),
(37, 1, '31301', 2, 239.13, 0, 478.26, 0, 478.26),
(38, 1, '31302', 2, 120, 0, 240, 0, 240),
(39, 1, '31303', 2, 173.915, 0, 347.83, 0, 347.83),
(40, 1, '31304', 1, 108.7, 0, 108.7, 0, 108.7),
(41, 1, '31305', 2, 86.95, 0, 173.9, 0, 173.9),
(42, 1, '31306', 2, 179.13, 0, 358.26, 0, 358.26),
(43, 2, '30101', 20, 39.13, 0, 782.6, 0, 782.6),
(44, 2, '20401', 10, 170, 0, 1700, 0, 1700),
(45, 2, '40101', 1, 121.74, 0, 121.74, 0, 121.74),
(46, 3, '40104', 1, 10342, 0, 10342, 0, 10342),
(47, 3, '40103', 1, 10342, 0, 10342, 0, 10342),
(48, 3, '40102', 1, 10342, 0, 10342, 0, 10342),
(49, 4, '10401', 2, 214, 0, 428, 0, 428);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_venta`
--

CREATE TABLE `detalle_venta` (
  `id` int(11) NOT NULL,
  `id_venta` int(11) DEFAULT NULL,
  `item` int(11) DEFAULT NULL,
  `codigo_producto` varchar(20) DEFAULT NULL,
  `descripcion` varchar(150) DEFAULT NULL,
  `porcentaje_igv` float DEFAULT NULL,
  `cantidad` float DEFAULT NULL,
  `costo_unitario` float DEFAULT NULL,
  `valor_unitario` float DEFAULT NULL,
  `precio_unitario` float DEFAULT NULL,
  `valor_total` float DEFAULT NULL,
  `igv` float DEFAULT NULL,
  `importe_total` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresas`
--

CREATE TABLE `empresas` (
  `id_empresa` int(11) NOT NULL,
  `razon_social` text NOT NULL,
  `nombre_comercial` varchar(255) DEFAULT NULL,
  `id_tipo_documento` varchar(20) DEFAULT NULL,
  `ruc` bigint(20) NOT NULL,
  `direccion` text NOT NULL,
  `simbolo_moneda` varchar(5) DEFAULT NULL,
  `email` text NOT NULL,
  `telefono` varchar(100) DEFAULT NULL,
  `provincia` varchar(100) DEFAULT NULL,
  `departamento` varchar(100) DEFAULT NULL,
  `distrito` varchar(100) DEFAULT NULL,
  `ubigeo` varchar(6) DEFAULT NULL,
  `usuario_sol` varchar(45) DEFAULT NULL,
  `clave_sol` varchar(45) DEFAULT NULL,
  `estado` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `empresas`
--

INSERT INTO `empresas` (`id_empresa`, `razon_social`, `nombre_comercial`, `id_tipo_documento`, `ruc`, `direccion`, `simbolo_moneda`, `email`, `telefono`, `provincia`, `departamento`, `distrito`, `ubigeo`, `usuario_sol`, `clave_sol`, `estado`) VALUES
(1, '3D INVERSIONES Y SERVICIOS GENERALES E.I.R.L.	', '3D INVERSIONES Y SERVICIOS GENERALES E.I.R.L.	', '6', 10467291241, 'CALLE BUENAVENTURA AGUIRRE 302 ', 'S/ ', 'cfredes@innred.cl', '+56983851526 - +56999688639', 'LIMA', 'LIMA', 'BARRANCO', '150104', 'MODDATOS', 'MODDATOS', 1),
(2, 'NEGOCIOS WAIMAKU \" E.I.R.L', 'NEGOCIOS WAIMAKU \" E.I.R.L', '6', 20480674414, 'AV GRAU 123', 'S/', 'audio@gmail.com', '987654321', 'LIMA', 'LIMA', 'BARRANCO', '787878', 'moddatos', 'moddatos', 1),
(3, 'IMPORTACIONES FVC EIRL', 'IMPORTACIONES FVC EIRL', '6', 20494099153, 'CALLE LIMA 123', 'S/', 'empresa@gmail.com', '987654321', 'LIMA', 'LIMA', 'JESUS MARIA', '124545', 'moddatos', 'moddatos', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `forma_pago`
--

CREATE TABLE `forma_pago` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  `estado` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `forma_pago`
--

INSERT INTO `forma_pago` (`id`, `descripcion`, `estado`) VALUES
(1, 'Contado', 1),
(2, 'Crédito', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `impuestos`
--

CREATE TABLE `impuestos` (
  `id_tipo_operacion` int(11) NOT NULL,
  `impuesto` float DEFAULT NULL,
  `estado` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `impuestos`
--

INSERT INTO `impuestos` (`id_tipo_operacion`, `impuesto`, `estado`) VALUES
(10, 15, 1),
(20, 0, 1),
(30, 0, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `kardex`
--

CREATE TABLE `kardex` (
  `id` int(11) NOT NULL,
  `codigo_producto` varchar(20) DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `concepto` varchar(100) DEFAULT NULL,
  `comprobante` varchar(50) DEFAULT NULL,
  `in_unidades` float DEFAULT NULL,
  `in_costo_unitario` float DEFAULT NULL,
  `in_costo_total` float DEFAULT NULL,
  `out_unidades` float DEFAULT NULL,
  `out_costo_unitario` float DEFAULT NULL,
  `out_costo_total` float DEFAULT NULL,
  `ex_unidades` float DEFAULT NULL,
  `ex_costo_unitario` float DEFAULT NULL,
  `ex_costo_total` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `kardex`
--

INSERT INTO `kardex` (`id`, `codigo_producto`, `fecha`, `concepto`, `comprobante`, `in_unidades`, `in_costo_unitario`, `in_costo_total`, `out_unidades`, `out_costo_unitario`, `out_costo_total`, `ex_unidades`, `ex_costo_unitario`, `ex_costo_total`) VALUES
(1, '10101', '2024-03-20 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 24, 5.9, 141.6),
(2, '20101', '2024-03-20 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 23, 12.1, 278.3),
(3, '20201', '2024-03-20 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 29, 12.4, 359.6),
(4, '30101', '2024-03-20 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 26, 3.25, 84.5),
(5, '30102', '2024-03-20 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 23, 5.15, 118.45),
(6, '20301', '2024-03-20 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 29, 9.8, 284.2),
(7, '30301', '2024-03-20 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 27, 7.49, 202.23),
(8, '40101', '2024-03-20 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 26, 8, 208),
(9, '10201', '2024-03-20 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 26, 10, 260),
(10, '10301', '2024-03-20 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 21, 3.79, 79.59),
(11, '40101', '2024-03-20 00:00:00', 'COMPRA', 'F011-22344', 10, 8, 80, NULL, NULL, NULL, 36, 8, 288),
(12, '30301', '2024-03-20 00:00:00', 'COMPRA', 'F011-22344', 1, 7.49, 7.49, NULL, NULL, NULL, 28, 7.49, 209.72),
(13, '40108', '2024-04-22 00:00:00', 'COMPRA', 'F001-NULL', 1, 50, 50, NULL, NULL, NULL, NULL, NULL, NULL),
(14, '40107', '2024-04-22 00:00:00', 'COMPRA', 'F005-NULL', 5, 100, 500, NULL, NULL, NULL, NULL, NULL, NULL),
(15, '40108', '2024-04-22 00:00:00', 'COMPRA', 'F009-NULL', 15, 100, 1500, NULL, NULL, NULL, NULL, NULL, NULL),
(16, '40104', '2024-04-22 00:00:00', 'COMPRA', 'F002-NULL', 5, 400, 2000, NULL, NULL, NULL, NULL, NULL, NULL),
(17, '40103', '2024-04-22 00:00:00', 'COMPRA', 'F002-NULL', 8, 500, 4000, NULL, NULL, NULL, NULL, NULL, NULL),
(18, '40106', '2024-04-22 00:00:00', 'COMPRA', 'F007-NULL', 5, 340, 1700, NULL, NULL, NULL, NULL, NULL, NULL),
(19, '40108', '2024-04-22 00:00:00', 'COMPRA', 'F011-NULL', 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, '40105', '2024-04-22 00:00:00', 'COMPRA', 'F012-NULL', 10, 20, 200, NULL, NULL, NULL, NULL, NULL, NULL),
(21, '40102', '2024-04-22 00:00:00', 'COMPRA', 'F012-NULL', 10, 50, 500, NULL, NULL, NULL, NULL, NULL, NULL),
(22, '40109', '2024-04-22 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0),
(23, '40109', '2024-04-22 00:00:00', 'COMPRA', 'F013-NULL', 10, 150, 1500, NULL, NULL, NULL, 10, 150, 1500),
(24, '40101', '2024-04-23 00:00:00', 'COMPRA', 'F001-NULL', 10, 8, 80, NULL, NULL, NULL, 46, 8, 368),
(25, '40109', '2024-04-23 00:00:00', 'COMPRA', 'F001-NULL', 10, 150, 1500, NULL, NULL, NULL, 20, 150, 3000),
(26, '40108', '2024-04-23 00:00:00', 'COMPRA', 'F002-NULL', 2, 99, 198, NULL, NULL, NULL, NULL, NULL, NULL),
(27, '40108', '2024-04-23 00:00:00', 'COMPRA', 'F001-NULL', 1, 50, 50, NULL, NULL, NULL, NULL, NULL, NULL),
(28, '40109', '2024-04-23 00:00:00', 'COMPRA', 'F001-NULL', 5, 150, 750, NULL, NULL, NULL, 25, 150, 3750),
(29, '40107', '2024-04-23 00:00:00', 'COMPRA', 'F001-NULL', 6, 22.61, 135.66, NULL, NULL, NULL, NULL, NULL, NULL),
(30, '31301', '2024-04-23 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0),
(31, '31302', '2024-04-23 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0),
(32, '31303', '2024-04-23 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0),
(33, '31304', '2024-04-23 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0),
(34, '31305', '2024-04-23 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0),
(35, '31306', '2024-04-23 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0),
(36, '30101', '2024-04-23 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0),
(37, '20401', '2024-04-23 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0),
(38, '40101', '2024-04-23 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0),
(39, '40102', '2024-04-23 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0),
(40, '40103', '2024-04-23 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0),
(41, '40104', '2024-04-23 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0),
(42, '40101', '2024-04-23 00:00:00', 'COMPRA', 'F001-NULL', 10, 8, 80, NULL, NULL, NULL, 10, 8, 80),
(43, '31306', '2024-04-23 00:00:00', 'COMPRA', 'F001-NULL', 2, 179.13, 358.26, NULL, NULL, NULL, 2, 179.13, 358.26),
(44, '31305', '2024-04-23 00:00:00', 'COMPRA', 'F001-NULL', 2, 86.95, 173.9, NULL, NULL, NULL, 2, 86.95, 173.9),
(45, '31304', '2024-04-23 00:00:00', 'COMPRA', 'F001-NULL', 1, 108.7, 108.7, NULL, NULL, NULL, 1, 108.7, 108.7),
(46, '31303', '2024-04-23 00:00:00', 'COMPRA', 'F001-NULL', 2, 173.915, 347.83, NULL, NULL, NULL, 2, 173.91, 347.83),
(47, '31302', '2024-04-23 00:00:00', 'COMPRA', 'F001-NULL', 2, 120, 240, NULL, NULL, NULL, 2, 120, 240),
(48, '31301', '2024-04-23 00:00:00', 'COMPRA', 'F001-NULL', 2, 239.13, 478.26, NULL, NULL, NULL, 2, 239.13, 478.26),
(49, '31301', '2024-04-23 00:00:00', 'COMPRA', 'F001-NULL', 2, 239.13, 478.26, NULL, NULL, NULL, 4, 239.13, 956.52),
(50, '31302', '2024-04-23 00:00:00', 'COMPRA', 'F001-NULL', 2, 120, 240, NULL, NULL, NULL, 4, 120, 480),
(51, '31303', '2024-04-23 00:00:00', 'COMPRA', 'F001-NULL', 2, 173.915, 347.83, NULL, NULL, NULL, 4, 173.91, 695.66),
(52, '31304', '2024-04-23 00:00:00', 'COMPRA', 'F001-NULL', 1, 108.7, 108.7, NULL, NULL, NULL, 2, 108.7, 217.4),
(53, '31305', '2024-04-23 00:00:00', 'COMPRA', 'F001-NULL', 2, 86.95, 173.9, NULL, NULL, NULL, 4, 86.95, 347.8),
(54, '31306', '2024-04-23 00:00:00', 'COMPRA', 'F001-NULL', 2, 179.13, 358.26, NULL, NULL, NULL, 4, 179.13, 716.52),
(55, '30101', '2024-04-23 00:00:00', 'COMPRA', 'F001-NULL', 20, 39.13, 782.6, NULL, NULL, NULL, 20, 39.13, 782.6),
(56, '20401', '2024-04-23 00:00:00', 'COMPRA', 'F001-NULL', 10, 170, 1700, NULL, NULL, NULL, 10, 170, 1700),
(57, '40101', '2024-04-23 00:00:00', 'COMPRA', 'F001-NULL', 1, 121.74, 121.74, NULL, NULL, NULL, 11, 18.34, 201.74),
(58, '40104', '2024-04-23 00:00:00', 'COMPRA', 'F001-NULL', 1, 10342, 10342, NULL, NULL, NULL, 1, 10342, 10342),
(59, '40103', '2024-04-23 00:00:00', 'COMPRA', 'F001-NULL', 1, 10342, 10342, NULL, NULL, NULL, 1, 10342, 10342),
(60, '40102', '2024-04-23 00:00:00', 'COMPRA', 'F001-NULL', 1, 10342, 10342, NULL, NULL, NULL, 1, 10342, 10342),
(61, '10401', '2024-04-23 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0),
(62, '40104', '2024-04-23 00:00:00', 'COMPRA', 'F001-NULL', 5, 400, 2000, NULL, NULL, NULL, 6, 2057, 12342),
(63, '40103', '2024-04-23 00:00:00', 'COMPRA', 'F001-NULL', 8, 500, 4000, NULL, NULL, NULL, 9, 1593.56, 14342),
(64, '10401', '2024-04-23 00:00:00', 'COMPRA', 'F001-NULL', 2, 214, 428, NULL, NULL, NULL, 2, 214, 428);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materiales`
--

CREATE TABLE `materiales` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `cantidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materiales`
--

INSERT INTO `materiales` (`id`, `pedido_id`, `nombre`, `cantidad`) VALUES
(212, 131, 'MADERA 2X4X10', 58),
(213, 132, 'VIDRIO 3/16 CLARO ANCHO 0.75 ALTO 0.20', 25),
(214, 132, 'VIDRIO 3/16 CLARO PULIDO UN LADO', 26),
(215, 132, 'VIDRIO 3/16 CLARO SLP PULIDO UN LADO ANCHO 22 ALTO 2', 27),
(216, 133, 'VIDRIO 3/16 CLARO PULIDO UN LADO ANCHO 0.565 ALTO 0.27', 25);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materialesb`
--

CREATE TABLE `materialesb` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `cantidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materialesb`
--

INSERT INTO `materialesb` (`id`, `pedido_id`, `nombre`, `cantidad`) VALUES
(1, 1, 'VIDRIO 3/16 CLARO ANCHO 0.75 ALTO 0.252', 54);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulos`
--

CREATE TABLE `modulos` (
  `id` int(11) NOT NULL,
  `modulo` varchar(45) DEFAULT NULL,
  `padre_id` int(11) DEFAULT NULL,
  `vista` varchar(45) DEFAULT NULL,
  `icon_menu` varchar(45) DEFAULT NULL,
  `orden` int(11) DEFAULT NULL,
  `fecha_creacion` timestamp NULL DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `modulos`
--

INSERT INTO `modulos` (`id`, `modulo`, `padre_id`, `vista`, `icon_menu`, `orden`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Kardex-Ordenes', 0, 'kardex_ordenes.php', 'fas fa-tachometer-alt', 0, NULL, NULL),
(2, 'Ordenes', 0, '', 'fas fa-store-alt', 1, NULL, NULL),
(3, 'Orden', 2, 'ventas.php', 'far fa-circle', 5, NULL, NULL),
(4, 'Administrar Ordenes', 2, 'administrar_ventas.php', 'far fa-circle', 6, NULL, NULL),
(5, 'Materia Prima', 0, NULL, 'fas fa-cart-plus', 7, NULL, NULL),
(6, 'Inventario', 5, 'productos.php', 'far fa-circle', 8, NULL, NULL),
(7, 'Carga Masiva', 5, 'carga_masiva_productos.php', 'far fa-circle', 9, NULL, NULL),
(8, 'Categorías', 5, 'categorias.php', 'far fa-circle', 10, NULL, NULL),
(9, 'Compras / Reportes', 0, '', 'fas fa-dolly', 12, NULL, NULL),
(10, 'Compras', 9, 'compras.php', 'far fa-circle', 15, NULL, NULL),
(11, 'Administracion', 0, NULL, 'fas fa-cogs', 13, NULL, NULL),
(13, 'Módulos / Perfiles', 31, 'seguridad_modulos_perfiles.php', 'far fa-circle', 25, NULL, NULL),
(22, 'Reporte de compras', 9, 'Reporte_compras.php', 'far fa-circle', 20, '2023-09-22 05:46:29', NULL),
(25, 'Clientes', 11, 'administrar_clientes.php', 'far fa-circle', 14, '2023-09-22 06:19:20', NULL),
(26, 'Proveedores', 11, 'administrar_proveedores.php', 'far fa-circle', 18, '2023-09-22 06:19:31', NULL),
(28, 'Reporte de Materia', 5, 'Reporte_materia_prima.php', 'far fa-circle', 2, '2023-09-26 15:46:51', NULL),
(30, 'Ordenes finalizadas', 2, 'obtener_ordenes.php', 'far fa-circle', 4, '2023-09-26 15:47:39', NULL),
(31, 'Seguridad', 0, '', 'fas fa-user-shield', 22, '2023-09-26 21:03:11', NULL),
(33, 'Perfiles', 31, 'seguridad_perfiles.php', 'far fa-circle', 23, '2023-09-26 21:04:53', NULL),
(34, 'Usuarios', 31, 'seguridad_usuarios.php', 'far fa-circle', 24, '2023-09-26 21:05:08', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `moneda`
--

CREATE TABLE `moneda` (
  `id` char(3) NOT NULL,
  `descripcion` varchar(45) NOT NULL,
  `simbolo` char(5) DEFAULT NULL,
  `estado` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `moneda`
--

INSERT INTO `moneda` (`id`, `descripcion`, `simbolo`, `estado`) VALUES
('LPS', 'LEMPIRAS', 'L/', 1),
('USD', 'DOLARES', '$', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_arqueo_caja`
--

CREATE TABLE `movimientos_arqueo_caja` (
  `id` int(11) NOT NULL,
  `id_arqueo_caja` int(11) DEFAULT NULL,
  `id_tipo_movimiento` int(11) DEFAULT NULL,
  `descripcion` varchar(250) DEFAULT NULL,
  `monto` float DEFAULT NULL,
  `estado` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `movimientos_arqueo_caja`
--

INSERT INTO `movimientos_arqueo_caja` (`id`, `id_arqueo_caja`, `id_tipo_movimiento`, `descripcion`, `monto`, `estado`) VALUES
(1, 5, 1, 'Producto ', 6, 1),
(11, 5, 1, 'Almuerzo', 15, 1),
(12, 5, 1, 'Producto Malogrado', 15, 1),
(13, 6, 1, 'Prueba Devolcuion', 5, 1),
(14, 6, 1, 'Prueba Devolucion 2', 3, 1),
(15, 6, 1, 'Prueba Devolucion 3', 1, 1),
(16, 9, 1, 'Dev. Prueba', 15, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id` int(11) NOT NULL,
  `mensaje` text DEFAULT NULL,
  `leido` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notificaciones`
--

INSERT INTO `notificaciones` (`id`, `mensaje`, `leido`) VALUES
(20, 'Nuevo pedido recibido para la orden: 9245', 0),
(21, 'Nuevo pedido recibido para la orden: 6471', 0),
(22, 'Nuevo pedido recibido para la orden: 6471', 0),
(23, 'Nuevo pedido recibido para la orden: 6471', 0),
(24, 'Nuevo pedido recibido para la orden: 8202\nCategoría: comedor\nDescripción: de madera\nCantidad de materiales: 1\nMateriales insertados:\nOTRO PRODUCTO (3)', 0),
(25, 'Nuevo pedido recibido para la orden: 8202\nCategoría: comedor\nDescripción: de madera\nCantidad de materiales: 2\nMateriales insertados:\nVIDRIO 3/16 CLARO PULIDO UN LADO (22)\nVIDRIO 3/16 CLARO ANCHO 0.75 ALTO 0.359 (25)', 0),
(26, 'Nuevo pedido recibido para la orden: 2003\nCategoría: comedor\nDescripción: Caoba\nCantidad de materiales: 1\nMateriales insertados:\nVIDRIO 3/16 CLARO ANCHO 0.75 ALTO 0.252 (89)', 0),
(27, 'Nuevo pedido recibido para la orden: 8202\nCategoría: comedor\nDescripción: de madera\nCantidad de materiales: 1\nMateriales insertados:\nMADERA 2X4X10 (28)', 0),
(28, 'Nuevo pedido recibido para la orden: 2003\nCategoría: comedor\nDescripción: Caoba\nCantidad de materiales: 1\nMateriales insertados:\nMADERA 2X4X10 (58)', 0),
(29, 'Nuevo pedido recibido para la orden: 2003\nCategoría: comedor\nDescripción: Caoba\nCantidad de materiales: 3\nMateriales insertados:\nVIDRIO 3/16 CLARO ANCHO 0.75 ALTO 0.20 (25)\nVIDRIO 3/16 CLARO PULIDO UN LADO (26)\nVIDRIO 3/16 CLARO SLP PULIDO UN LADO ANCHO 22 ALTO 2 (27)', 0),
(30, 'Nuevo pedido recibido para la orden: 2003\nCategoría: comedor\nDescripción: Caoba\nCantidad de materiales: 2\nMateriales insertados:\nVIDRIO 3/16 CLARO PULIDO UN LADO ANCHO 0.565 ALTO 0.27 (25)\n ()', 0),
(31, 'Nuevo pedido recibido para la orden: 2003\nCategoría: comedor\nDescripción: Caoba\nCantidad de materiales: 1\nMateriales insertados:\nMADERA 2X4X10 (54)', 0),
(32, 'Nuevo pedido recibido para la orden: 2003\nCategoría: comedor\nDescripción: Caoba\nCantidad de materiales: 1\nMateriales insertados:\nVIDRIO 3/16 CLARO ANCHO 0.75 ALTO 0.252 (54)', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ordenes`
--

CREATE TABLE `ordenes` (
  `id` int(11) NOT NULL,
  `codigo` varchar(5) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `categoria` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_pedido` date NOT NULL,
  `fecha_finalizacion` date DEFAULT NULL,
  `costo_total` decimal(10,2) NOT NULL,
  `tipo_pago` varchar(50) NOT NULL,
  `porcentaje_pago` decimal(5,2) NOT NULL,
  `monto_pago` decimal(10,2) NOT NULL,
  `estado` varchar(20) NOT NULL DEFAULT 'En proceso',
  `pago` enum('Parcial','Total') DEFAULT 'Parcial',
  `pedido` varchar(255) DEFAULT '<botón>'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ordenes`
--

INSERT INTO `ordenes` (`id`, `codigo`, `cliente_id`, `categoria`, `descripcion`, `fecha_pedido`, `fecha_finalizacion`, `costo_total`, `tipo_pago`, `porcentaje_pago`, `monto_pago`, `estado`, `pago`, `pedido`) VALUES
(11, '8202', 90, 'comedor', 'de madera', '2024-04-23', '2024-04-30', 4000.00, 'efectivo', 0.60, 2400.00, 'En proceso', 'Parcial', '<botón>'),
(12, '2003', 91, 'comedor', 'Caoba', '2024-04-23', '2024-05-11', 50000.00, 'transferencia', 0.60, 30000.00, 'Finalizada', 'Total', '<botón>');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orden_producto`
--

CREATE TABLE `orden_producto` (
  `id` int(11) NOT NULL,
  `orden_id` int(11) DEFAULT NULL,
  `producto` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `orden_producto`
--

INSERT INTO `orden_producto` (`id`, `orden_id`, `producto`) VALUES
(27, 11, 'Bar Gaveta'),
(28, 12, 'Bar Gaveta');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `codigo_orden` varchar(50) NOT NULL,
  `categoria` varchar(50) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `cantidad_materiales` int(11) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `codigo_orden`, `categoria`, `descripcion`, `cantidad_materiales`, `fecha_creacion`) VALUES
(121, '4873', 'comedor', 'redondo', 2, '2024-04-10 15:26:49'),
(122, '3108', 'comedor', 'Color blanco', 1, '2024-04-10 20:47:46'),
(123, '4252', 'oficina', 'Color negro', 1, '2024-04-10 21:38:17'),
(124, '1155', 'dormitorio', 'de niña ', 1, '2024-04-10 21:42:08'),
(125, '4764', 'oficina', 'ghghf', 1, '2024-04-11 04:04:45'),
(126, '2342', 'oficina', 'madera', 1, '2024-04-11 04:06:49'),
(127, '8202', 'comedor', 'de madera', 1, '2024-04-23 13:29:14'),
(128, '8202', 'comedor', 'de madera', 2, '2024-04-23 15:25:17'),
(129, '2003', 'comedor', 'Caoba', 1, '2024-04-23 15:27:53'),
(130, '8202', 'comedor', 'de madera', 1, '2024-04-23 15:37:14'),
(131, '2003', 'comedor', 'Caoba', 1, '2024-04-23 15:45:40'),
(132, '2003', 'comedor', 'Caoba', 3, '2024-04-23 15:47:13'),
(133, '2003', 'comedor', 'Caoba', 2, '2024-04-23 16:43:34'),
(134, '2003', 'comedor', 'Caoba', 1, '2024-04-23 16:48:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidosb`
--

CREATE TABLE `pedidosb` (
  `id` int(11) NOT NULL,
  `codigo_orden` varchar(50) NOT NULL,
  `categoria` varchar(50) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `cantidad_materiales` int(11) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidosb`
--

INSERT INTO `pedidosb` (`id`, `codigo_orden`, `categoria`, `descripcion`, `cantidad_materiales`, `fecha_creacion`) VALUES
(1, '2003', 'comedor', 'Caoba', 1, '2024-04-23 16:57:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perfiles`
--

CREATE TABLE `perfiles` (
  `id_perfil` int(11) NOT NULL,
  `descripcion` varchar(45) DEFAULT NULL,
  `estado` tinyint(4) DEFAULT NULL,
  `fecha_creacion` timestamp NULL DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `perfiles`
--

INSERT INTO `perfiles` (`id_perfil`, `descripcion`, `estado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'ADMINISTRADOR', 1, NULL, NULL),
(2, 'BODEGUERO', 1, NULL, NULL),
(3, 'PRODUCCION', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perfil_modulo`
--

CREATE TABLE `perfil_modulo` (
  `idperfil_modulo` int(11) NOT NULL,
  `id_perfil` int(11) DEFAULT NULL,
  `id_modulo` int(11) DEFAULT NULL,
  `vista_inicio` tinyint(4) DEFAULT NULL,
  `estado` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `perfil_modulo`
--

INSERT INTO `perfil_modulo` (`idperfil_modulo`, `id_perfil`, `id_modulo`, `vista_inicio`, `estado`) VALUES
(13, 1, 13, 0, 1),
(655, 4, 5, 0, 1),
(656, 4, 6, 1, 1),
(657, 4, 7, 0, 1),
(658, 4, 8, 0, 1),
(659, 2, 1, 1, 1),
(660, 1, 1, 1, 1),
(661, 1, 2, 0, 1),
(662, 1, 28, 0, 1),
(663, 1, 29, 0, 1),
(664, 1, 30, 0, 1),
(665, 1, 3, 0, 1),
(666, 1, 4, 0, 1),
(667, 1, 5, 0, 1),
(668, 1, 6, 0, 1),
(669, 1, 7, 0, 1),
(670, 1, 8, 0, 1),
(671, 1, 15, 0, 1),
(672, 1, 9, 0, 1),
(673, 1, 11, 0, 1),
(674, 1, 25, 0, 1),
(675, 1, 10, 0, 1),
(676, 1, 37, 0, 1),
(677, 1, 27, 0, 1),
(678, 1, 26, 0, 1),
(679, 1, 23, 0, 1),
(680, 1, 22, 0, 1),
(681, 1, 24, 0, 1),
(682, 1, 31, 0, 1),
(683, 1, 33, 0, 1),
(684, 1, 34, 0, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `codigo_producto` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `id_categoria` int(11) DEFAULT NULL,
  `descripcion` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `id_tipo_afectacion_igv` int(11) NOT NULL,
  `id_unidad_medida` varchar(3) NOT NULL,
  `costo_unitario` float DEFAULT 0,
  `precio_unitario_con_igv` float DEFAULT 0,
  `precio_unitario_sin_igv` float DEFAULT 0,
  `precio_unitario_mayor_con_igv` float DEFAULT 0,
  `precio_unitario_mayor_sin_igv` float DEFAULT 0,
  `precio_unitario_oferta_con_igv` float DEFAULT 0,
  `precio_unitario_oferta_sin_igv` float DEFAULT NULL,
  `stock` float DEFAULT 0,
  `minimo_stock` float DEFAULT 0,
  `ventas` float DEFAULT 0,
  `costo_total` float DEFAULT 0,
  `imagen` varchar(255) DEFAULT 'no_image.jpg',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fecha_actualizacion` date DEFAULT NULL,
  `estado` int(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`codigo_producto`, `id_categoria`, `descripcion`, `id_tipo_afectacion_igv`, `id_unidad_medida`, `costo_unitario`, `precio_unitario_con_igv`, `precio_unitario_sin_igv`, `precio_unitario_mayor_con_igv`, `precio_unitario_mayor_sin_igv`, `precio_unitario_oferta_con_igv`, `precio_unitario_oferta_sin_igv`, `stock`, `minimo_stock`, `ventas`, `costo_total`, `imagen`, `fecha_creacion`, `fecha_actualizacion`, `estado`) VALUES
('10401', 1, 'MADERA  2X4X10', 20, 'NIU', 214, 214, 214, NULL, NULL, 0, 0, 2, 0, 0, 428, 'no_image.jpg', '2024-04-23 14:52:19', '2024-04-23', 1),
('20401', 2, 'GALON THINNER 100', 20, 'NIU', 170, 170, 170, NULL, NULL, 0, 0, 10, 1, 0, 1700, 'no_image.jpg', '2024-04-23 14:22:10', '2024-04-23', 1),
('30101', 3, 'BISAGRA CIERRE LENTO RECTA', 20, 'NIU', 39.13, 39.13, 39.13, NULL, NULL, 0, 0, 20, 1, 0, 782.6, 'no_image.jpg', '2024-04-23 14:22:10', '2024-04-23', 1),
('31301', 3, 'VIDRIO 3/16 CLARO PULIDO UN LADO', 20, 'NIU', 239.13, 358.26, 358.26, NULL, NULL, 0, 0, 4, 2, 0, 956.52, 'no_image.jpg', '2024-04-23 14:12:12', '2024-04-23', 1),
('31302', 3, 'VIDRIO 3/16 CLARO PULIDO UN LADO ANCHO 0.565 ALTO 0.27', 20, 'NIU', 120, 240, 240, NULL, NULL, 0, 0, 4, 2, 0, 480, 'no_image.jpg', '2024-04-23 14:12:12', '2024-04-23', 1),
('31303', 3, 'VIDRIO 3/16 CLARO ANCHO 0.75 ALTO 0.359', 20, 'NIU', 173.91, 342.83, 342.83, NULL, NULL, 0, 0, 4, 2, 0, 695.66, 'no_image.jpg', '2024-04-23 14:12:12', '2024-04-23', 1),
('31304', 3, 'VIDRIO 3/16 CLARO ANCHO 0.75 ALTO 0.252', 20, 'NIU', 108.7, 108.7, 108.7, NULL, NULL, 0, 0, 2, 1, 0, 217.4, 'no_image.jpg', '2024-04-23 14:12:12', '2024-04-23', 1),
('31305', 3, 'VIDRIO 3/16 CLARO ANCHO 0.75 ALTO 0.20', 20, 'NIU', 86.95, 173.91, 173.91, NULL, NULL, 0, 0, 4, 1, 0, 347.8, 'no_image.jpg', '2024-04-23 14:12:12', '2024-04-23', 1),
('31306', 3, 'VIDRIO 3/16 CLARO SLP PULIDO UN LADO ANCHO 22 ALTO 2', 20, 'NIU', 179.13, 478.26, 478.26, NULL, NULL, 0, 0, 4, 1, 0, 716.52, 'no_image.jpg', '2024-04-23 14:12:12', '2024-04-23', 1),
('40101', 4, 'FLEJE DE PLASTICO', 20, 'NIU', 18.34, 121.74, 121.74, NULL, NULL, 0, 0, 11, 1, 0, 201.74, 'no_image.jpg', '2024-04-23 14:22:10', '2024-04-23', 1),
('40102', 4, 'PLANCHA DE CUARZO NEGRO ESTELAR', 20, 'NIU', 10342, 10342, 10342, NULL, NULL, 0, 0, 1, 1, 0, 10342, 'no_image.jpg', '2024-04-23 14:28:14', '2024-04-23', 1),
('40103', 4, 'PLANCHA DE CUARZO GRIS SABOIDE', 20, 'NIU', 1593.56, 10342, 10342, NULL, NULL, 0, 0, 9, 1, 0, 14342, 'no_image.jpg', '2024-04-23 14:52:19', '2024-04-23', 1),
('40104', 4, 'LANCE DE GRIS SABOIDE 1.25X1.60', 20, 'NIU', 2057, 10342, 10342, NULL, NULL, 0, 0, 6, 1, 0, 12342, 'no_image.jpg', '2024-04-23 14:52:19', '2024-04-23', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id` int(11) NOT NULL,
  `id_tipo_documento` varchar(45) NOT NULL,
  `ruc` varchar(45) NOT NULL,
  `razon_social` varchar(150) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `estado` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`id`, `id_tipo_documento`, `ruc`, `razon_social`, `direccion`, `telefono`, `estado`) VALUES
(1, '1', '20604915351', 'La mundial', 'Blv. Suyapa.', '22012355', 1),
(3, '0', '888888888', 'La ferreteria', 'Col. Tepeyac', '2246850', 1),
(4, '1', '08019002262040', 'VIDRIERIA VIALTEC S. DE R.L', 'BO. Lempira 13 calle, entre 7 y 8 avenida, 2 cuadras abajo del centro comercial lempira, Comayagüela M.D.C., F.M.', '22220662', 1),
(5, '1', '08019005472404', 'IMPROIN', 'KM5 carretera a puerto cortes boulevard del norte, San Pedro Sula, Honduras. C.A.', '22512429', 1),
(6, '1', '08019019181236', 'PIEDRAS Y DETALLES, S. DE R.L.', 'Aldea de villa nueva, calle la culebra, a 100 metros de la gasolinera AMERICAN, Tegucigalpa, M.D.C., Honduras, C.A.', '2282767', 1),
(7, '1', '08011947011846', 'DEPOSITO DE MADERA \"SAN MIGUEL\"', 'Boulevard fuerzas armadas, col. la soledad, 1ra entrada Col. San Francisco, contiguo a iglesia Mormona.', NULL, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `serie`
--

CREATE TABLE `serie` (
  `id` int(11) NOT NULL,
  `id_tipo_comprobante` varchar(3) NOT NULL,
  `serie` varchar(4) NOT NULL,
  `correlativo` int(11) DEFAULT NULL,
  `estado` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `serie`
--

INSERT INTO `serie` (`id`, `id_tipo_comprobante`, `serie`, `correlativo`, `estado`) VALUES
(1, '03', 'B001', 175, 1),
(2, '01', 'F001', 28, 1),
(3, '03', 'B002', 1, 1),
(4, '03', 'B003', 15, 1),
(5, '03 ', 'B002', 15, 1),
(6, '01', 'FL01', 2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_otp_check`
--

CREATE TABLE `tbl_otp_check` (
  `id` int(11) NOT NULL,
  `otp` int(11) NOT NULL,
  `is_expired` tinyint(4) NOT NULL,
  `create_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tbl_otp_check`
--

INSERT INTO `tbl_otp_check` (`id`, `otp`, `is_expired`, `create_at`, `id_usuario`) VALUES
(1, 79086, 1, '2024-03-08 07:39:04', 0),
(2, 70318, 1, '2024-03-08 07:39:56', 0),
(3, 49825, 1, '2024-03-08 07:51:16', 0),
(4, 72732, 1, '2024-03-08 07:59:44', 0),
(5, 29772, 1, '2024-03-08 08:10:04', 0),
(6, 39305, 1, '2024-03-08 08:24:31', 0),
(7, 43300, 0, '2024-03-14 23:40:23', 0),
(8, 13690, 0, '2024-03-14 23:40:39', 0),
(9, 60228, 0, '2024-03-14 23:53:18', 0),
(10, 83042, 0, '2024-03-14 23:55:30', 0),
(11, 60398, 1, '2024-03-16 03:54:33', 1),
(12, 62493, 0, '2024-03-16 04:15:09', 1),
(13, 43707, 0, '2024-03-16 04:18:51', 1),
(14, 10579, 0, '2024-03-16 04:27:10', 1),
(15, 70567, 0, '2024-03-16 04:31:25', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_afectacion_igv`
--

CREATE TABLE `tipo_afectacion_igv` (
  `id` int(11) NOT NULL,
  `codigo` char(3) NOT NULL,
  `descripcion` varchar(150) DEFAULT NULL,
  `letra_tributo` varchar(45) DEFAULT NULL,
  `codigo_tributo` varchar(45) DEFAULT NULL,
  `nombre_tributo` varchar(45) DEFAULT NULL,
  `tipo_tributo` varchar(45) DEFAULT NULL,
  `estado` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_afectacion_igv`
--

INSERT INTO `tipo_afectacion_igv` (`id`, `codigo`, `descripcion`, `letra_tributo`, `codigo_tributo`, `nombre_tributo`, `tipo_tributo`, `estado`) VALUES
(1, '10', 'Con ISV', 'S', '1000', 'C/ISV', 'VAT', 1),
(2, '20', 'Sin ISV', 'E', '9997', 'S/ISV', 'VAT', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_comprobante`
--

CREATE TABLE `tipo_comprobante` (
  `id` int(11) NOT NULL,
  `codigo` varchar(3) NOT NULL,
  `descripcion` varchar(50) NOT NULL,
  `estado` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `tipo_comprobante`
--

INSERT INTO `tipo_comprobante` (`id`, `codigo`, `descripcion`, `estado`) VALUES
(1, '01', 'FACTURA', 1),
(2, '02', 'COTIZACION', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_documento`
--

CREATE TABLE `tipo_documento` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(45) NOT NULL,
  `estado` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_documento`
--

INSERT INTO `tipo_documento` (`id`, `descripcion`, `estado`) VALUES
(0, 'CONSUMIDOR FINAL', 1),
(1, 'RTN', 1),
(2, 'CARNET DE EXTRANJERIA', 1),
(3, 'DNI', 1),
(4, 'PASAPORTE', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_movimiento_caja`
--

CREATE TABLE `tipo_movimiento_caja` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(150) DEFAULT NULL,
  `estado` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_movimiento_caja`
--

INSERT INTO `tipo_movimiento_caja` (`id`, `descripcion`, `estado`) VALUES
(1, 'DEVOLUCIÓN', 1),
(2, 'GASTO', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_operacion`
--

CREATE TABLE `tipo_operacion` (
  `codigo` varchar(4) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `estado` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_operacion`
--

INSERT INTO `tipo_operacion` (`codigo`, `descripcion`, `estado`) VALUES
('0101', 'Venta interna', 1),
('0102', 'Venta Interna – Anticipos', 1),
('0103', 'Venta interna - Itinerante', 1),
('0110', 'Venta Interna - Sustenta Traslado de Mercadería - Remitente', 1),
('0111', 'Venta Interna - Sustenta Traslado de Mercadería - Transportista', 1),
('0112', 'Venta Interna - Sustenta Gastos Deducibles Persona Natural', 1),
('0120', 'Venta Interna - Sujeta al IVAP', 1),
('0200', 'Exportación de Bienes ', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_precio_venta_unitario`
--

CREATE TABLE `tipo_precio_venta_unitario` (
  `codigo` varchar(2) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `estado` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_precio_venta_unitario`
--

INSERT INTO `tipo_precio_venta_unitario` (`codigo`, `descripcion`, `estado`) VALUES
('01', 'Precio unitario (incluye el ISV)', 1),
('02', 'Valor referencial unitario en operaciones sin ISV', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre_usuario` varchar(100) DEFAULT NULL,
  `apellido_usuario` varchar(100) DEFAULT NULL,
  `usuario` varchar(100) DEFAULT NULL,
  `clave` text DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `id_perfil_usuario` int(11) DEFAULT NULL,
  `estado` tinyint(4) DEFAULT NULL,
  `bloqueado` int(11) NOT NULL DEFAULT 0,
  `intentos_fallidos` int(11) DEFAULT 0,
  `primer_inicio_sesion` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre_usuario`, `apellido_usuario`, `usuario`, `clave`, `correo`, `id_perfil_usuario`, `estado`, `bloqueado`, `intentos_fallidos`, `primer_inicio_sesion`) VALUES
(1, 'Josue', 'Lopez', 'josu', '$2a$07$azybxcags23425sdg23sdeanQZqjaf6Birm2NvcYTNtJw24CsO5uq', 'josuelopezz1221@gmail.com', 1, 1, 0, 0, 1),
(2, 'Carlos', 'Reyes', 'carlos12', '$2a$07$azybxcags23425sdg23sdeanQZqjaf6Birm2NvcYTNtJw24CsO5uq', 'pguerrero@gmail.com', 2, 1, 0, 0, 1),
(3, 'Luis', 'Membreño', 'luis12', '$2a$07$azybxcags23425sdg23sdeanQZqjaf6Birm2NvcYTNtJw24CsO5uq', 'jm473049@gmail.com', 3, 1, 0, 0, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta`
--

CREATE TABLE `venta` (
  `id` int(11) NOT NULL,
  `id_empresa_emisora` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_serie` int(11) NOT NULL,
  `serie` varchar(4) NOT NULL,
  `correlativo` int(11) NOT NULL,
  `fecha_emision` date NOT NULL,
  `hora_emision` varchar(10) DEFAULT NULL,
  `fecha_vencimiento` date NOT NULL,
  `id_moneda` varchar(3) NOT NULL,
  `forma_pago` varchar(45) NOT NULL,
  `total_operaciones_gravadas` float DEFAULT 0,
  `total_operaciones_exoneradas` float DEFAULT 0,
  `total_operaciones_inafectas` float DEFAULT 0,
  `total_igv` float DEFAULT 0,
  `importe_total` float DEFAULT 0,
  `nombre_xml` varchar(255) DEFAULT NULL,
  `xml_base64` text DEFAULT NULL,
  `xml_cdr_sunat_base64` text DEFAULT NULL,
  `codigo_error_sunat` int(11) DEFAULT NULL,
  `mensaje_respuesta_sunat` text DEFAULT NULL,
  `hash_signature` varchar(45) DEFAULT NULL,
  `estado_respuesta_sunat` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `arqueo_caja`
--
ALTER TABLE `arqueo_caja`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `codigo_unidad_medida`
--
ALTER TABLE `codigo_unidad_medida`
  ADD UNIQUE KEY `id_UNIQUE` (`id`);

--
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cod_producto_idx` (`codigo_producto`),
  ADD KEY `fk_id_compra_idx` (`id_compra`);

--
-- Indices de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `empresas`
--
ALTER TABLE `empresas`
  ADD PRIMARY KEY (`id_empresa`);

--
-- Indices de la tabla `forma_pago`
--
ALTER TABLE `forma_pago`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `impuestos`
--
ALTER TABLE `impuestos`
  ADD PRIMARY KEY (`id_tipo_operacion`);

--
-- Indices de la tabla `kardex`
--
ALTER TABLE `kardex`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_id_producto_idx` (`codigo_producto`);

--
-- Indices de la tabla `materiales`
--
ALTER TABLE `materiales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`);

--
-- Indices de la tabla `materialesb`
--
ALTER TABLE `materialesb`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pedido_id` (`pedido_id`);

--
-- Indices de la tabla `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `moneda`
--
ALTER TABLE `moneda`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `movimientos_arqueo_caja`
--
ALTER TABLE `movimientos_arqueo_caja`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ordenes`
--
ALTER TABLE `ordenes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`);

--
-- Indices de la tabla `orden_producto`
--
ALTER TABLE `orden_producto`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pedidosb`
--
ALTER TABLE `pedidosb`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `perfiles`
--
ALTER TABLE `perfiles`
  ADD PRIMARY KEY (`id_perfil`);

--
-- Indices de la tabla `perfil_modulo`
--
ALTER TABLE `perfil_modulo`
  ADD PRIMARY KEY (`idperfil_modulo`),
  ADD KEY `id_perfil` (`id_perfil`),
  ADD KEY `id_modulo` (`id_modulo`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`codigo_producto`),
  ADD UNIQUE KEY `codigo_producto_UNIQUE` (`codigo_producto`),
  ADD KEY `fk_id_categoria_idx` (`id_categoria`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `serie`
--
ALTER TABLE `serie`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tbl_otp_check`
--
ALTER TABLE `tbl_otp_check`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tipo_afectacion_igv`
--
ALTER TABLE `tipo_afectacion_igv`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tipo_comprobante`
--
ALTER TABLE `tipo_comprobante`
  ADD PRIMARY KEY (`id`,`codigo`);

--
-- Indices de la tabla `tipo_documento`
--
ALTER TABLE `tipo_documento`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tipo_movimiento_caja`
--
ALTER TABLE `tipo_movimiento_caja`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tipo_operacion`
--
ALTER TABLE `tipo_operacion`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `tipo_precio_venta_unitario`
--
ALTER TABLE `tipo_precio_venta_unitario`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `id_perfil_usuario` (`id_perfil_usuario`);

--
-- Indices de la tabla `venta`
--
ALTER TABLE `venta`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `arqueo_caja`
--
ALTER TABLE `arqueo_caja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `empresas`
--
ALTER TABLE `empresas`
  MODIFY `id_empresa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `forma_pago`
--
ALTER TABLE `forma_pago`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `kardex`
--
ALTER TABLE `kardex`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT de la tabla `materiales`
--
ALTER TABLE `materiales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=219;

--
-- AUTO_INCREMENT de la tabla `materialesb`
--
ALTER TABLE `materialesb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `movimientos_arqueo_caja`
--
ALTER TABLE `movimientos_arqueo_caja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `ordenes`
--
ALTER TABLE `ordenes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `orden_producto`
--
ALTER TABLE `orden_producto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;

--
-- AUTO_INCREMENT de la tabla `pedidosb`
--
ALTER TABLE `pedidosb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `perfiles`
--
ALTER TABLE `perfiles`
  MODIFY `id_perfil` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `perfil_modulo`
--
ALTER TABLE `perfil_modulo`
  MODIFY `idperfil_modulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=685;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `serie`
--
ALTER TABLE `serie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `tbl_otp_check`
--
ALTER TABLE `tbl_otp_check`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `tipo_afectacion_igv`
--
ALTER TABLE `tipo_afectacion_igv`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tipo_comprobante`
--
ALTER TABLE `tipo_comprobante`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `tipo_movimiento_caja`
--
ALTER TABLE `tipo_movimiento_caja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `venta`
--
ALTER TABLE `venta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `materiales`
--
ALTER TABLE `materiales`
  ADD CONSTRAINT `materiales_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `materialesb`
--
ALTER TABLE `materialesb`
  ADD CONSTRAINT `fk_pedido_id` FOREIGN KEY (`pedido_id`) REFERENCES `pedidosb` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `ordenes`
--
ALTER TABLE `ordenes`
  ADD CONSTRAINT `ordenes_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
