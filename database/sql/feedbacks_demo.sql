-- Script SQL para configurar datos de prueba para feedbacks
-- Ejecutar en phpMyAdmin para ver la sección de comentarios funcionando

-- 1. Asignar empleado a las reservas existentes
UPDATE reservas 
SET id_empleado = 1  -- Asignar al empleado con ID 1 (Pedro Empleado)
WHERE id_empleado IS NULL;

-- 2. Insertar datos de prueba en la tabla feedbacks (CON FECHAS RECIENTES)
INSERT INTO feedbacks (id_reserva, calificacion, comentarios, usuario_creacion, fecha_creacion, usuario_actualizacion, fecha_actualizacion) VALUES
-- Comentarios de 5 estrellas (aparecerán en la pasarela)
(15, 5, 'Es la mejor estética canina de la ciudad, mi perro quedó hermoso', 'renzomd68@gmail.com', '2025-11-30 15:30:00', 'renzomd68@gmail.com', '2025-11-30 15:30:00'),
(14, 5, 'Recibí muy buena atención, el personal es súper cariñoso', 'renzomd68@gmail.com', '2025-11-30 14:15:30', 'renzomd68@gmail.com', '2025-11-30 14:15:30'),
(13, 5, 'Excelente servicio, mi mascota quedó hermosa y relajada', 'renzomd68@gmail.com', '2025-11-30 13:45:20', 'renzomd68@gmail.com', '2025-11-30 13:45:20'),
(12, 5, 'Muy profesional el trato y cuidado, volveré seguro', 'renzomd68@gmail.com', '2025-11-30 12:20:15', 'renzomd68@gmail.com', '2025-11-30 12:20:15'),
(10, 5, 'Quedé encantado con el resultado, superó mis expectativas', 'renzomd68@gmail.com', '2025-11-30 11:10:45', 'renzomd68@gmail.com', '2025-11-30 11:10:45'),
(9, 5, 'Personal muy amable y cariñoso con las mascotas', 'renzomd68@gmail.com', '2025-11-30 10:30:30', 'renzomd68@gmail.com', '2025-11-30 10:30:30'),
(7, 5, 'Increíble trabajo, mi perro salió feliz y bien cuidado', 'renzomd68@gmail.com', '2025-11-30 09:45:25', 'renzomd68@gmail.com', '2025-11-30 09:45:25'),
(6, 5, 'Mi perro salió feliz y relajado, servicio de calidad', 'renzomd68@gmail.com', '2025-11-30 08:15:35', 'renzomd68@gmail.com', '2025-11-30 08:15:35'),
-- Algunos comentarios de otras calificaciones
(11, 4, 'Buen servicio pero podría mejorar los tiempos', 'renzomd68@gmail.com', '2025-11-30 16:20:45', 'renzomd68@gmail.com', '2025-11-30 16:20:45'),
(8, 3, 'Regular, esperaba más por el precio', 'renzomd68@gmail.com', '2025-11-30 07:30:50', 'renzomd68@gmail.com', '2025-11-30 07:30:50');

-- 3. Crear reservas para hoy (30 de noviembre de 2025) para probar el panel
INSERT INTO reservas (id_mascota, id_cliente, id_usuario, id_empleado, fecha, hora, enfermedad, vacuna, alergia, estado, usuario_creacion, fecha_creacion) VALUES
-- Reservas para hoy
(1, 1, 4, 1, '2025-11-30', '09:00:00', 1, 1, 0, 'P', 'renzomd68@gmail.com', NOW()),
(2, 1, 4, 1, '2025-11-30', '10:30:00', 0, 1, 0, 'P', 'renzomd68@gmail.com', NOW()),
(3, 1, 4, 1, '2025-11-30', '14:00:00', 0, 0, 0, 'A', 'renzomd68@gmail.com', NOW()),
(1, 1, 4, 1, '2025-11-30', '15:30:00', 1, 1, 1, 'P', 'renzomd68@gmail.com', NOW()),
(2, 1, 4, 1, '2025-11-30', '16:45:00', 0, 0, 0, 'P', 'renzomd68@gmail.com', NOW());

-- 4. Insertar detalles para las nuevas reservas
INSERT INTO detalles_reservas (id_reserva, precio_unitario, igv, total, id_servicio, estado, usuario_creacion, fecha_creacion)
SELECT 
    id_reserva,
    50.00 as precio_unitario,
    9.00 as igv,
    59.00 as total,
    1 as id_servicio,
    'A' as estado,
    'sistema' as usuario_creacion,
    NOW() as fecha_creacion
FROM reservas 
WHERE fecha = '2025-11-30' 
AND id_reserva NOT IN (SELECT id_reserva FROM detalles_reservas);

-- 5. Verificar los datos insertados
SELECT 
    'FEEDBACKS DE 5 ESTRELLAS:' as tipo,
    f.id_feedback,
    f.calificacion,
    f.comentarios,
    r.id_reserva,
    m.nombre as mascota_nombre,
    p.nombres as cliente_nombre,
    r.fecha as fecha_servicio
FROM feedbacks f
JOIN reservas r ON f.id_reserva = r.id_reserva  
JOIN mascotas m ON r.id_mascota = m.id_mascota
JOIN clientes c ON r.id_cliente = c.id_cliente
JOIN personas p ON c.id_persona = p.id_persona
WHERE f.calificacion = 5
ORDER BY f.fecha_creacion DESC

UNION ALL

SELECT 
    'RESERVAS DE HOY:' as tipo,
    NULL as id_feedback,
    NULL as calificacion,
    CONCAT('Reserva ', r.id_reserva, ' - ', TIME(r.hora), ' - Estado: ', r.estado) as comentarios,
    r.id_reserva,
    m.nombre as mascota_nombre,
    p.nombres as cliente_nombre,
    r.fecha as fecha_servicio
FROM reservas r
JOIN mascotas m ON r.id_mascota = m.id_mascota
JOIN clientes c ON r.id_cliente = c.id_cliente  
JOIN personas p ON c.id_persona = p.id_persona
WHERE r.fecha = '2025-11-30' AND r.id_empleado = 1
ORDER BY fecha_servicio, comentarios;