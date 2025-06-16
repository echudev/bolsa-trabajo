<?php
require_once 'conexion.php';

/**
 * Modelo para acceder a la tabla `configuracion_sistema`.
 * Permite obtener, actualizar y listar valores de configuración del sistema.
 */
class ConfiguracionSistemaModel
{
    private $pdo;

    /**
     * Constructor del modelo ConfiguracionSistemaModel.
     *
     * @param PDO|null $pdo Conexión PDO opcional. Si no se pasa, se conecta automáticamente.
     */
    public function __construct($pdo = null)
    {
        $this->pdo = $pdo ?? conectarDB();
    }

    /**
     * Obtiene el valor asociado a una clave de configuración.
     *
     * @param string $clave Clave de configuración.
     * @return string|null Valor asociado o null si no existe o hay error.
     */
    public function obtenerValor($clave)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT valor FROM configuracion_sistema WHERE clave = :clave");
            $stmt->execute(['clave' => $clave]);
            $fila = $stmt->fetch();
            return $fila ? $fila['valor'] : null;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Devuelve todas las configuraciones como array asociativo.
     *
     * @return array Array asociativo con pares clave => valor.
     */
    public function obtenerTodas()
    {
        try {
            $stmt = $this->pdo->query("SELECT clave, valor FROM configuracion_sistema");
            $resultados = $stmt->fetchAll();
            $config = [];

            foreach ($resultados as $fila) {
                $config[$fila['clave']] = $fila['valor'];
            }

            return $config;
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Actualiza el valor de una clave de configuración existente.
     * Si la clave no existe, no realiza ninguna acción.
     *
     * @param string $clave Clave a actualizar.
     * @param string $valor Nuevo valor.
     * @return bool True si se actualizó, false en caso de error o si la clave no existe.
     */
    public function actualizarValor($clave, $valor)
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE configuracion_sistema SET valor = :valor WHERE clave = :clave");
            $stmt->execute(['clave' => $clave, 'valor' => $valor]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
}
