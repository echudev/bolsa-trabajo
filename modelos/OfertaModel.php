<?php

require_once 'conexion.php';

/**
 * Modelo de acceso a datos para la entidad `ofertas`.
 * Incluye operaciones de listado, conteo y acciones CRUD con soporte
 * de filtros dinámicos y paginación.
 */
class OfertaModel
{

    private PDO $pdo;

    // Listas blancas para ordenar de forma segura
    public const ALLOWED_SORT = ['o.puesto', 'o.empresa', 'u.apellido, u.nombre', 'o.estado_aprobacion', 'o.fecha_creacion', 'o.fecha_modificacion', 'IFNULL(o.fecha_modificacion, o.fecha_creacion)'];
    public const ALLOWED_ORDER = ['ASC', 'DESC'];

    /**
     * Constructor del modelo OfertaModel.
     *
     * @param PDO|null $pdo Conexión PDO opcional. Si no se pasa, se conecta automáticamente.
     */
    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? conectarDB();
    }


    /**
     * Obtiene una lista de ofertas con filtros dinámicos, paginación y ordenamiento.
     *
     * @param array $filtros Puede incluir:
     *   - 'busqueda': string (puesto o descripción),
     *   - 'estado_aprobacion': string|array,
     *   - 'activa': int (1 o 0),
     *   - 'ver_inactivas': bool,
     *   - 'modalidad', 'jornada', 'experiencia', 'usuario', 'solo_propias',
     *   - 'sort': string (columna ya validada),
     *   - 'order': 'ASC'|'DESC',
     *   - 'limite': int,
     *   - 'offset': int.
     *
     * @return array Lista de ofertas (array asociativo).
     */
    public function obtenerOfertas(array $filtros = []): array
    {
        try {
            $sql = "SELECT o.*, u.nombre AS nombre, u.apellido, u.rol
                    FROM ofertas o
                    LEFT JOIN usuarios u ON o.publicada_por = u.id_usuario
                    WHERE o.fecha_eliminacion IS NULL";
            $params = [];

            // ————— Si viene un filtro “activa” explícito, lo usamos:
            if (isset($filtros['activa'])) {
                $sql .= " AND o.activa = :activa";
                $params['activa'] = (int)$filtros['activa'];
            }
            // ————— Si NO viene “activa” explícito, y ver_inactivas está vacío, 
            //         forzamos mostrar solo abiertas no vencidas:
            elseif (empty($filtros['ver_inactivas'])) {
                $sql .= " AND o.activa = 1 
                      AND (o.fecha_fin IS NULL OR o.fecha_fin >= CURDATE())";
            }


            if (!empty($filtros['busqueda'])) {
                $sql .= " AND (o.puesto LIKE :busqueda OR o.descripcion LIKE :busqueda)";
                $params['busqueda'] = '%' . $filtros['busqueda'] . '%';
            }

            if (!empty($filtros['usuario'])) {
                $sql .= " AND (
                    u.nombre LIKE :usuario OR 
                    u.apellido LIKE :usuario OR 
                    u.email LIKE :usuario
                )";
                $params['usuario'] = '%' . $filtros['usuario'] . '%';
            }

            if (!empty($filtros['modalidad'])) {
                $sql .= " AND o.modalidad = :modalidad";
                $params['modalidad'] = $filtros['modalidad'];
            }

            if (!empty($filtros['jornada'])) {
                $sql .= " AND o.jornada = :jornada";
                $params['jornada'] = $filtros['jornada'];
            }

            if (isset($filtros['experiencia']) && $filtros['experiencia'] !== '') {
                if ($filtros['experiencia'] === '3-5') {
                    $sql .= " AND o.experiencia_requerida BETWEEN 3 AND 5";
                } elseif ($filtros['experiencia'] === '+5') {
                    $sql .= " AND o.experiencia_requerida > 5";
                } else {
                    $sql .= " AND o.experiencia_requerida = :experiencia";
                    $params['experiencia'] = (int) $filtros['experiencia'];
                }
            }

            if (!empty($filtros['solo_propias']) && isset($_SESSION['usuario_id'])) {
                $sql .= " AND o.publicada_por = :usuario_id";
                $params['usuario_id'] = $_SESSION['usuario_id'];
            }

            if (!empty($filtros['estado_aprobacion'])) {
                if (is_array($filtros['estado_aprobacion'])) {
                    $keys = [];
                    foreach ($filtros['estado_aprobacion'] as $i => $valor) {
                        $key = ":estado_aprobacion_$i";
                        $keys[] = $key;
                        $params[$key] = $valor;
                    }
                    $sql .= " AND o.estado_aprobacion IN (" . implode(',', $keys) . ")";
                } else {
                    $sql .= " AND o.estado_aprobacion = :estado_aprobacion";
                    $params['estado_aprobacion'] = $filtros['estado_aprobacion'];
                }
            }

            // ——— Orden seguro ———
            $columna = 'o.fecha_creacion';
            $direccion = 'DESC';

            if (!empty($filtros['sort']) && in_array($filtros['sort'], self::ALLOWED_SORT, true)) {
                $columna = $filtros['sort'];
            }

            if (!empty($filtros['order']) && in_array(strtoupper($filtros['order']), self::ALLOWED_ORDER, true)) {
                $direccion = strtoupper($filtros['order']);
            }

            $sql .= " ORDER BY $columna $direccion";

            // LIMIT y OFFSET (validados como enteros no negativos)
            if (isset($filtros['limite']) && isset($filtros['offset'])) {
                $limite = max(1, (int)$filtros['limite']);
                $offset = max(0, (int)$filtros['offset']);
                $sql .= " LIMIT $limite OFFSET $offset";
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }


    /**
     * Cuenta la cantidad total de ofertas que cumplen los filtros.
     *
     * @param array $filtros Mismos filtros que en `obtenerOfertas`.
     * @return int Cantidad de ofertas encontradas.
     */
    public function contarOfertas($filtros = [])
    {
        try {
            $sql = "SELECT COUNT(*) 
                    FROM ofertas o
                    LEFT JOIN usuarios u ON o.publicada_por = u.id_usuario
                    WHERE o.fecha_eliminacion IS NULL";
            $params = [];

            // ————— Si viene un filtro “activa” explícito, lo usamos:
            if (isset($filtros['activa'])) {
                $sql .= " AND o.activa = :activa";
                $params['activa'] = (int)$filtros['activa'];
            }
            // ————— Si NO viene “activa” explícito, y ver_inactivas está vacío, 
            //         forzamos mostrar solo abiertas no vencidas:
            elseif (empty($filtros['ver_inactivas'])) {
                $sql .= " AND o.activa = 1 
                      AND (o.fecha_fin IS NULL OR o.fecha_fin >= CURDATE())";
            }

            // Búsqueda textual
            if (!empty($filtros['busqueda'])) {
                $sql .= " AND (o.puesto LIKE :busqueda OR o.descripcion LIKE :busqueda)";
                $params['busqueda'] = '%' . $filtros['busqueda'] . '%';
            }

            if (!empty($filtros['usuario'])) {
                $sql .= " AND (
                    u.nombre LIKE :usuario OR 
                    u.apellido LIKE :usuario OR 
                    u.email LIKE :usuario
                )";
                $params['usuario'] = '%' . $filtros['usuario'] . '%';
            }

            // Modalidad
            if (!empty($filtros['modalidad'])) {
                $sql .= " AND o.modalidad = :modalidad";
                $params['modalidad'] = $filtros['modalidad'];
            }

            // Jornada
            if (!empty($filtros['jornada'])) {
                $sql .= " AND o.jornada = :jornada";
                $params['jornada'] = $filtros['jornada'];
            }

            if (isset($filtros['experiencia']) && $filtros['experiencia'] !== '') {
                if ($filtros['experiencia'] === '3-5') {
                    $sql .= " AND o.experiencia_requerida BETWEEN 3 AND 5";
                } else {
                    $sql .= " AND o.experiencia_requerida = :experiencia";
                    $params['experiencia'] = (int) $filtros['experiencia'];
                }
            }

            // Solo propias
            if (!empty($filtros['solo_propias']) && isset($_SESSION['usuario_id'])) {
                $sql .= " AND o.publicada_por = :usuario_id";
                $params['usuario_id'] = $_SESSION['usuario_id'];
            }

            if (!empty($filtros['estado_aprobacion'])) {
                if (is_array($filtros['estado_aprobacion'])) {
                    $keys = [];
                    foreach ($filtros['estado_aprobacion'] as $i => $valor) {
                        $key = ":estado_aprobacion_$i";
                        $keys[] = $key;
                        $params[$key] = $valor;
                    }
                    $sql .= " AND o.estado_aprobacion IN (" . implode(',', $keys) . ")";
                } else {
                    $sql .= " AND o.estado_aprobacion = :estado_aprobacion";
                    $params['estado_aprobacion'] = $filtros['estado_aprobacion'];
                }
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }


    /**
     * Crea una nueva oferta.
     *
     * @param array $datos Datos del formulario (coinciden con columnas de la tabla).
     * @return int|false ID de la nueva oferta si se creó correctamente, false en caso de error.
     */
    public function crear($datos)
    {
        try {
            $sql = "INSERT INTO ofertas (
                    puesto, descripcion, empresa, ubicacion, modalidad,
                    jornada, horario, experiencia_requerida, enlace,
                    email_contacto, telefono_contacto, fecha_fin,
                    activa, publicada_por, estado_aprobacion
                )
                VALUES (
                    :puesto, :descripcion, :empresa, :ubicacion, :modalidad,
                    :jornada, :horario, :experiencia_requerida, :enlace,
                    :email_contacto, :telefono_contacto, :fecha_fin,
                    :activa, :publicada_por, :estado_aprobacion
                )";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'puesto' => $datos['puesto'],
                'descripcion' => $datos['descripcion'],
                'empresa' => $datos['empresa'],
                'ubicacion' => $datos['ubicacion'],
                'modalidad' => $datos['modalidad'],
                'jornada' => $datos['jornada'] === '' ? null : $datos['jornada'],
                'horario' => $datos['horario'],
                'experiencia_requerida' => $datos['experiencia_requerida'],
                'enlace' => $datos['enlace'],
                'email_contacto' => $datos['email_contacto'],
                'telefono_contacto' => $datos['telefono_contacto'],
                'fecha_fin' => $datos['fecha_fin'] ?: null,
                'activa' => !empty($datos['activa']) ? 1 : 0,
                'publicada_por' => $datos['publicada_por'] ?? 1,
                'estado_aprobacion' => $datos['estado_aprobacion'] ?? 'aprobado'
            ]);

            return $this->pdo->lastInsertId(); // id de la nueva oferta
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Actualiza los datos de una oferta existente.
     *
     * @param int $id_oferta ID de la oferta a modificar.
     * @param array $datos Datos nuevos a actualizar.
     * @return bool True si se actualizó correctamente, false en caso de error.
     */
    public function actualizar($id_oferta, $datos)
    {
        try {
            $sql = "UPDATE ofertas SET
            puesto = :puesto,
            descripcion = :descripcion,
            empresa = :empresa,
            ubicacion = :ubicacion,
            modalidad = :modalidad,
            jornada = :jornada,
            horario = :horario,
            experiencia_requerida = :experiencia_requerida,
            enlace = :enlace,
            email_contacto = :email_contacto,
            telefono_contacto = :telefono_contacto,
            fecha_fin = :fecha_fin,
            activa = :activa,
            fecha_modificacion = CURRENT_TIMESTAMP";

            if (isset($datos['estado_aprobacion'])) {
                $sql .= ", estado_aprobacion = :estado_aprobacion";
            }

            $sql .= " WHERE id_oferta = :id_oferta";
            $params = [
                'puesto' => $datos['puesto'],
                'descripcion' => $datos['descripcion'],
                'empresa' => $datos['empresa'],
                'ubicacion' => $datos['ubicacion'],
                'modalidad' => $datos['modalidad'],
                'jornada' => $datos['jornada'] === '' ? null : $datos['jornada'],
                'horario' => $datos['horario'],
                'experiencia_requerida' => $datos['experiencia_requerida'],
                'enlace' => $datos['enlace'],
                'email_contacto' => $datos['email_contacto'],
                'telefono_contacto' => $datos['telefono_contacto'],
                'fecha_fin' => $datos['fecha_fin'] ?: null,
                'activa' => !empty($datos['activa']) ? 1 : 0,
                'id_oferta' => $id_oferta
            ];

            if (isset($datos['estado_aprobacion'])) {
                $params['estado_aprobacion'] = $datos['estado_aprobacion'];
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Elimina lógicamente una oferta (marca fecha de eliminación).
     *
     * @param int $id_oferta ID de la oferta.
     * @return bool True si se eliminó correctamente, false en caso de error.
     */
    public function eliminar($id_oferta)
    {
        try {
            $sql = "UPDATE ofertas SET fecha_eliminacion = CURRENT_TIMESTAMP WHERE id_oferta = :id_oferta";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id_oferta' => $id_oferta]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Obtiene una oferta por su ID.
     *
     * @param int $id_oferta ID de la oferta.
     * @return array|null Datos de la oferta o null si no se encuentra.
     */
    public function obtenerPorId($id_oferta)
    {
        try {
            $stmt = $this->pdo->prepare("
                                    SELECT o.*, u.nombre, u.apellido, u.rol
                                    FROM ofertas o
                                    LEFT JOIN usuarios u ON o.publicada_por = u.id_usuario
                                    WHERE o.id_oferta = :id_oferta AND o.fecha_eliminacion IS NULL
                                ");
            $stmt->execute(['id_oferta' => $id_oferta]);

            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Actualiza el estado de aprobación de una oferta.
     *
     * @param int $id_oferta ID de la oferta.
     * @param string $estado Nuevo estado de aprobación ('pendiente', 'aprobado', 'rechazado').
     * @return bool True si se actualizó correctamente, false en caso de error.
     */
    public function actualizarEstadoAprobacion($id_oferta, $estado)
    {
        try {
            $sql = "UPDATE ofertas 
                SET estado_aprobacion = :estado, fecha_modificacion = CURRENT_TIMESTAMP 
                WHERE id_oferta = :id_oferta";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'estado' => $estado,
                'id_oferta' => $id_oferta
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Finaliza una oferta (activa = 0).
     *
     * @param int $id_oferta ID de la oferta.
     * @return bool True si se finalizó correctamente, false en caso de error.
     */
    public function desactivar($id_oferta)
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE ofertas SET activa = 0 WHERE id_oferta = :id_oferta");
            $stmt->execute(['id_oferta' => $id_oferta]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Reabre una oferta (activa = 1).
     * Si la fecha de fin ya pasó, se borra para que quede abierta.
     *
     * @param int $id_oferta ID de la oferta.
     * @return bool True si se reactivó correctamente, false en caso de error.
     */
    public function reactivar($id_oferta)
    {
        try {
            $sql = "UPDATE ofertas 
                    SET activa = 1,
                        fecha_fin = IF(fecha_fin < CURDATE(), NULL, fecha_fin)
                    WHERE id_oferta = :id_oferta";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id_oferta' => $id_oferta]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Marca como inactivas todas las ofertas vencidas (fecha_fin < hoy).
     *
     * @return bool True si se ejecutó correctamente, false en caso de error.
     */
    public function finalizarOfertasVencidas(): bool
    {
        try {
            $sql = "UPDATE ofertas 
                SET activa = 0 
                WHERE activa = 1 
                  AND fecha_fin IS NOT NULL 
                  AND fecha_fin < CURDATE()";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            return true;
        } catch (PDOException $e) {

            return false;
        }
    }
}
