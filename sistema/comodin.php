DELIMITER $$
	CREATE PROCEDURE add_detalle(codigo int, token_user varchar(50),horario int)
    BEGIN
    	DECLARE precio_actual decimal(10,2);
        DECLARE facultad_des varchar(50);
        SELECT precio INTO precio_actual FROM curso WHERE codigo_cur = codigo;
        
        INSERT INTO detalle_matricula(token_user,codigo_cur,facultad,horario,colegiatura) VALUES (token_user, codigo,facultad_des,horario,precio_actual);
        
        SELECT t.correlativo_temp, t.codigo_cur, c.descripcion,f.facultad,h.horario,t.colegiatura FROM detalle_matricula t 
        INNER JOIN curso c ON t.codigo_cur = c.codigo_cur
        INNER JOIN horario h  ON t.horario = h.idhorario
        INNER JOIN facultad f ON t.facultad = f.idfacultad
        WHERE t.token_user = token_user;
     END;$$
DELIMITER ;



//version anterior consulta

BEGIN
    	DECLARE precio_actual decimal(10,2);
        SELECT precio INTO precio_actual FROM curso WHERE codigo_cur = codigo;
        
        INSERT INTO detalle_matricula(token_user,codigo_cur,horario,colegiatura) VALUES (token_user, codigo,horario,precio_actual);
        
        SELECT t.correlativo_temp, t.codigo_cur, c.descripcion,f.facultad,h.horario,t.colegiatura FROM detalle_matricula t 
        INNER JOIN curso c 
        INNER JOIN facultad f 
        INNER JOIN horario h 
        ON t.codigo_cur = c.codigo_cur
        WHERE t.token_user = token_user;
     END


     SELECT b.noboleta, DATE_FORMAT(b.fecha, '%d/%m/%Y') as fecha, DATE_FORMAT(b.fecha,'%H:%i:%s') AS hora, 
     b.carnet, b.estado, u.nombre as vendedor, al.carnet, al.nombre, al.apellido, al.telefono,al.direccion 
     FROM boleta b 
     INNER JOIN usuario u ON b.usuario = u.idusuario 
     INNER JOIN alumno al ON b.carnet = al.carnet 
     WHERE b.noboleta = 20 AND b.carnet = 5 AND b.estado != 1;