<?php
require_once 'conexion.php';

/**
 * Modelo de acceso a datos para la tabla `usuarios`.
 * Encapsula operaciones CRUD, búsquedas con filtros dinámicos,
 * recuperación de cuenta y actualización de perfil.
 */
class UsuarioModel
{
    private PDO $pdo;

    /**
     * Constructor del modelo UsuarioModel.
     *
     * @param PDO|null $pdo Conexión PDO opcional. Si no se pasa, se conecta automáticamente.
     */
    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? conectarDB();
    }

    /**
     * Obtiene un usuario activo por su email (uso principal en login).
     *
     * @param string $email Correo del usuario.
     * @return array|null Datos del usuario o null si no existe o no está activo.
     */
    public function obtenerPorEmail($email)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE email = :email AND activo = 1");
            $stmt->execute(['email' => $email]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Crea un nuevo usuario.
     *
     * @param array $datos Datos del usuario: nombre, apellido, email, password, 
     *                     y opcionalmente rol y estado_aprobacion.
     * @return bool True si se creó correctamente, false en caso de error.
     */
    public function crear($datos)
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO usuarios (nombre, apellido, email, password, rol, activo, estado_aprobacion)
                                         VALUES (:nombre, :apellido, :email, :password, :rol, 1, :estado_aprobacion)");
            $stmt->execute([
                'nombre' => $datos['nombre'],
                'apellido' => $datos['apellido'],
                'email' => $datos['email'],
                'password' => $datos['password'],
                'rol' => $datos['rol'] ?? 'estudiante',
                'estado_aprobacion' => $datos['estado_aprobacion'] ?? 'aprobado'
            ]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Verifica si un email ya existe en la base de datos.
     *
     * @param string $email Email a verificar.
     * @return bool True si ya existe, false si no.
     */
    public function existeEmail($email)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $total = $stmt->fetchColumn();

            return $total > 0;
        } catch (PDOException $e) {
            return false;
        }
    }


    /**
     * Obtiene todos los usuarios aplicando filtros opcionales.
     *
     * @param array $filtros Puede incluir:
     *   - 'nombre', 'email', 'rol[]', 'estado', 'estado_aprobacion[]', 'excluir_id',
     *   - 'sort' (columna), 'order' ('ASC'|'DESC'),
     *   - 'limite', 'offset' para paginación.
     *
     * @return array Lista de usuarios.
     */
    public function obtenerTodos($filtros = [])
    {
        try {
            $sql = "SELECT * FROM usuarios WHERE 1=1";
            $params = [];

            if (!empty($filtros['nombre'])) {
                $sql .= " AND (nombre LIKE :nombre OR apellido LIKE :nombre)";
                $params['nombre'] = '%' . $filtros['nombre'] . '%';
            }

            if (!empty($filtros['email'])) {
                $sql .= " AND email LIKE :email";
                $params['email'] = '%' . $filtros['email'] . '%';
            }

            if (!empty($filtros['rol'])) {
                $roles = $filtros['rol'];
                if (is_array($roles)) {
                    $placeholders = [];
                    foreach ($roles as $i => $rol) {
                        $key = ":rol_$i";
                        $placeholders[] = $key;
                        $params["rol_$i"] = $rol;
                    }
                    $sql .= " AND rol IN (" . implode(',', $placeholders) . ")";
                } else {
                    $sql .= " AND rol = :rol";
                    $params['rol'] = $roles;
                }
            }

            if (isset($filtros['estado']) && $filtros['estado'] !== '') {
                $sql .= " AND activo = :estado";
                $params['estado'] = $filtros['estado'];
            }

            if (!empty($filtros['estado_aprobacion'])) {
                $estados = $filtros['estado_aprobacion'];
                if (is_array($estados)) {
                    $placeholders = [];
                    foreach ($estados as $i => $estado) {
                        $key = ":estado_aprobacion_$i";
                        $placeholders[] = $key;
                        $params["estado_aprobacion_$i"] = $estado;
                    }
                    $sql .= " AND estado_aprobacion IN (" . implode(',', $placeholders) . ")";
                } else {
                    $sql .= " AND estado_aprobacion = :estado_aprobacion";
                    $params['estado_aprobacion'] = $estados;
                }
            }

            if (!empty($filtros['excluir_id'])) {
                $sql .= " AND id_usuario != :excluir_id";
                $params['excluir_id'] = $filtros['excluir_id'];
            }

            // ——— Orden Dinámico ———
            if (!empty($filtros['sort']) && !empty($filtros['order'])) {

                $sql .= " ORDER BY {$filtros['sort']} {$filtros['order']}";
            } else {
                // Orden por defecto:
                $sql .= " ORDER BY apellido, nombre";
            }

            // ——— Paginación ———
            if (isset($filtros['limite']) && isset($filtros['offset'])) {
                $limite = (int)$filtros['limite'];
                $offset = (int)$filtros['offset'];
                $sql .= " LIMIT $limite OFFSET $offset";
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Cuenta la cantidad de usuarios que cumplen con los filtros.
     *
     * @param array $filtros Mismos filtros que en `obtenerTodos`.
     * @return int Cantidad total de usuarios encontrados.
     */
    public function contarUsuarios($filtros = [])
    {
        try {
            $sql = "SELECT COUNT(*) FROM usuarios WHERE 1=1";
            $params = [];

            if (!empty($filtros['nombre'])) {
                $sql .= " AND (nombre LIKE :nombre OR apellido LIKE :nombre)";
                $params['nombre'] = '%' . $filtros['nombre'] . '%';
            }

            if (!empty($filtros['email'])) {
                $sql .= " AND email LIKE :email";
                $params['email'] = '%' . $filtros['email'] . '%';
            }

            if (!empty($filtros['rol'])) {
                $roles = $filtros['rol'];
                if (is_array($roles)) {
                    $placeholders = [];
                    foreach ($roles as $i => $rol) {
                        $key = ":rol_$i";
                        $placeholders[] = $key;
                        $params["rol_$i"] = $rol;
                    }
                    $sql .= " AND rol IN (" . implode(',', $placeholders) . ")";
                } else {
                    $sql .= " AND rol = :rol";
                    $params['rol'] = $roles;
                }
            }

            if (isset($filtros['estado']) && $filtros['estado'] !== '') {
                $sql .= " AND activo = :estado";
                $params['estado'] = $filtros['estado'];
            }

            if (!empty($filtros['estado_aprobacion'])) {
                $estados = $filtros['estado_aprobacion'];
                if (is_array($estados)) {
                    $placeholders = [];
                    foreach ($estados as $i => $estado) {
                        $key = ":estado_aprobacion_$i";
                        $placeholders[] = $key;
                        $params["estado_aprobacion_$i"] = $estado;
                    }
                    $sql .= " AND estado_aprobacion IN (" . implode(',', $placeholders) . ")";
                } else {
                    $sql .= " AND estado_aprobacion = :estado_aprobacion";
                    $params['estado_aprobacion'] = $estados;
                }
            }
            if (!empty($filtros['excluir_id'])) {
                $sql .= " AND id_usuario != :excluir_id";
                $params['excluir_id'] = $filtros['excluir_id'];
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Cambia el rol de un usuario.
     *
     * @param int $id_usuario ID del usuario.
     * @param string $nuevoRol Nuevo rol a asignar.
     * @return bool True si se actualizó correctamente, false en caso de error.
     */
    public function cambiarRol($id_usuario, $nuevoRol)
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE usuarios SET rol = :rol WHERE id_usuario = :id_usuario");
            $stmt->execute(['rol' => $nuevoRol, 'id_usuario' => $id_usuario]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Desactiva un usuario (activo = 0).
     *
     * @param int $id_usuario ID del usuario.
     * @return bool True si se desactivó correctamente, false en caso de error.
     */
    public function desactivar($id_usuario)
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE usuarios SET activo = 0 WHERE id_usuario = :id_usuario");
            $stmt->execute(['id_usuario' => $id_usuario]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Reactiva un usuario (activo = 1).
     *
     * @param int $id_usuario ID del usuario.
     * @return bool True si se reactivó correctamente, false en caso de error.
     */
    public function reactivar($id_usuario)
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE usuarios SET activo = 1 WHERE id_usuario = :id_usuario");
            $stmt->execute(['id_usuario' => $id_usuario]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Obtiene un usuario por su ID.
     *
     * @param int $id_usuario ID del usuario.
     * @return array|null Datos del usuario o null si no existe.
     */
    public function obtenerPorId($id_usuario)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = :id_usuario");
            $stmt->execute(['id_usuario' => $id_usuario]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }


    /**
     * Actualiza el estado de aprobación de un usuario.
     *
     * @param int $id_usuario ID del usuario.
     * @param string $nuevoEstado Nuevo estado ('pendiente', 'aprobado', etc.).
     * @return bool True si se actualizó correctamente, false en caso de error.
     */
    public function actualizarEstadoAprobacion($id_usuario, $nuevoEstado)
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE usuarios SET estado_aprobacion = :estado WHERE id_usuario = :id");
            $stmt->execute([
                'estado' => $nuevoEstado,
                'id' => $id_usuario
            ]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Actualiza los datos de perfil de un usuario.
     *
     * @param int $id_usuario ID del usuario.
     * @param string $nombre Nuevo nombre.
     * @param string $apellido Nuevo apellido.
     * @param string $email Nuevo email.
     * @param string|null $passwordHash Nuevo hash de contraseña (si es null, no se actualiza).
     * @return bool True si se actualizó correctamente, false en caso de error.
     */
    public function actualizarDatosPerfil(
        int $id_usuario,
        string $nombre,
        string $apellido,
        string $email,
        ?string $passwordHash = null
    ): bool {
        try {
            $sql = "UPDATE usuarios
                    SET nombre   = :nombre,
                        apellido = :apellido,
                        email    = :email";
            $params = [
                'nombre'     => $nombre,
                'apellido'   => $apellido,
                'email'      => $email,
                'id_usuario' => $id_usuario,
            ];

            if ($passwordHash !== null) {
                $sql .= ", password = :password";
                $params['password'] = $passwordHash;
            }

            $sql .= " WHERE id_usuario = :id_usuario";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Busca un usuario por su token de recuperación de contraseña.
     *
     * @param string $token Token de recuperación.
     * @return array|null Datos del usuario o null si no se encuentra.
     */
    public function obtenerPorToken($token)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE reset_token = :token");
            $stmt->execute(['token' => $token]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Actualiza la contraseña de un usuario y elimina el token de recuperación.
     *
     * @param int $id_usuario ID del usuario.
     * @param string $nuevaPassword Hash de la nueva contraseña.
     * @return bool True si se actualizó correctamente, false en caso de error.
     */
    public function actualizarPasswordPorToken($id_usuario, $nuevaPassword)
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE usuarios SET password = :password, reset_token = null, reset_expira = null WHERE id_usuario = :id_usuario");
            $stmt->execute([
                'id_usuario' => $id_usuario,
                'password' => $nuevaPassword
            ]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Guarda un token de recuperación y su fecha de expiración para un usuario.
     *
     * @param int $id_usuario ID del usuario.
     * @param string $token Token generado.
     * @param string $tokenExpiracion Fecha y hora de expiración del token.
     * @return bool True si se guardó correctamente, false en caso de error.
     */
    public function guardarToken($id_usuario, $token, $tokenExpiracion)
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE usuarios SET reset_token = :reset_token, reset_expira = :reset_expira WHERE id_usuario = :id_usuario");
            $stmt->execute([
                'reset_token' => $token,
                'reset_expira' => $tokenExpiracion,
                'id_usuario' => $id_usuario
            ]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
