<?php
namespace Liki\Routing;
/**
 * Clase LoginProtector
 * Protección contra ataques de fuerza bruta en formularios de login
 * 
 * Características:
 * - Límite de intentos fallidos por IP y/o usuario
 * - Ventana de tiempo configurable
 * - Bloqueo temporal con tiempo exponencial (opcional)
 * - Almacenamiento en archivos JSON (sin dependencias externas)
 * - Limpieza automática de registros antiguos
 * - Uso de bloqueo con flock() para evitar condiciones de carrera
 */
class LoginProtector
{
    /** @var string Directorio donde se guardan los registros */
    private string $storageDir;

    /** @var int Número máximo de intentos permitidos antes de bloquear */
    private int $maxAttempts;

    /** @var int Ventana de tiempo en segundos para contar intentos */
    private int $timeWindow;

    /** @var int Tiempo de bloqueo en segundos (base) */
    private int $lockoutTime;

    /** @var bool Aplicar bloqueo exponencial (duplica el tiempo en cada reincidencia) */
    private bool $exponentialLockout;

    /**
     * Constructor
     *
     * @param array $config Configuración opcional
     */
    public function __construct(array $config = [])
    {
        $this->storageDir          = $config['storage_dir']          ?? sys_get_temp_dir() . '/logs';
        $this->maxAttempts         = $config['max_attempts']         ?? 5;
        $this->timeWindow          = $config['time_window']          ?? 900;   // 15 min
        $this->lockoutTime         = $config['lockout_time']         ?? 900;   // 15 min
        $this->exponentialLockout  = $config['exponential_lockout']  ?? true;

        // Crear directorio si no existe
        if (!is_dir($this->storageDir)) {
            if (!mkdir($this->storageDir, 0750, true) && !is_dir($this->storageDir)) {
                throw new RuntimeException("No se pudo crear el directorio: {$this->storageDir}");
            }
        }

        if (!is_writable($this->storageDir)) {
            throw new RuntimeException("El directorio no es escribible: {$this->storageDir}");
        }
    }

    /**
     * Verifica si se ha superado el límite de intentos
     * (método principal solicitado)
     *
     * @param string      $identifier IP o nombre de usuario
     * @param string|null $username   Opcional: para combinarlo con la IP
     * @return bool TRUE si está bloqueado, FALSE si puede intentar
     */
    public function isLocked(string $identifier, ?string $username = null): bool
    {
        $data = $this->load($identifier);
        if ($data === null) {
            return false;
        }

        $now = time();

        // Si hay un bloqueo activo, verificar si ya expiró
        if (!empty($data['locked_until']) && $data['locked_until'] > $now) {
            return true;
        }

        // Si el bloqueo expiró, limpiar
        if (!empty($data['locked_until']) && $data['locked_until'] <= $now) {
            $this->reset($identifier);
            return false;
        }

        // Filtrar intentos dentro de la ventana de tiempo
        $attempts = array_filter(
            $data['attempts'] ?? [],
            fn($timestamp) => ($now - $timestamp) <= $this->timeWindow
        );

        return count($attempts) >= $this->maxAttempts;
    }

    /**
     * Método alias más descriptivo: devuelve true si el login está permitido
     *
     * @param string      $identifier
     * @param string|null $username
     * @return bool
     */
    public function canAttempt(string $identifier, ?string $username = null): bool
    {
        return !$this->isLocked($identifier, $username);
    }

    /**
     * Registra un intento fallido
     *
     * @param string      $identifier IP o identificador principal
     * @param string|null $username   Nombre de usuario (opcional)
     * @return void
     */
    public function recordFailure(string $identifier, ?string $username = null): void
    {
        $data = $this->load($identifier) ?? [
            'attempts'     => [],
            'lock_count'   => 0,
            'locked_until' => 0,
        ];

        $now = time();

        // Limpiar intentos fuera de ventana
        $data['attempts'] = array_values(array_filter(
            $data['attempts'],
            fn($ts) => ($now - $ts) <= $this->timeWindow
        ));

        $data['attempts'][] = $now;

        // Si se alcanzó el máximo, bloquear
        if (count($data['attempts']) >= $this->maxAttempts) {
            $data['lock_count']   = ($data['lock_count'] ?? 0) + 1;
            $multiplier           = $this->exponentialLockout
                ? min(2 ** ($data['lock_count'] - 1), 16) // tope x16
                : 1;
            $data['locked_until'] = $now + ($this->lockoutTime * $multiplier);
        }

        $this->save($identifier, $data);
    }

    /**
     * Registra un intento exitoso (limpia los contadores)
     *
     * @param string      $identifier
     * @param string|null $username
     * @return void
     */
    public function recordSuccess(string $identifier, ?string $username = null): void
    {
        $this->reset($identifier);
    }

    /**
     * Reinicia los contadores para un identificador
     *
     * @param string $identifier
     * @return void
     */
    public function reset(string $identifier): void
    {
        $file = $this->getFilePath($identifier);
        if (is_file($file)) {
            @unlink($file);
        }
    }

    /**
     * Devuelve el tiempo restante de bloqueo (en segundos)
     *
     * @param string $identifier
     * @return int
     */
    public function getRemainingLockout(string $identifier): int
    {
        $data = $this->load($identifier);
        if (empty($data['locked_until'])) {
            return 0;
        }
        return max(0, $data['locked_until'] - time());
    }

    /**
     * Devuelve los intentos restantes antes del bloqueo
     *
     * @param string $identifier
     * @return int
     */
    public function getRemainingAttempts(string $identifier): int
    {
        $data = $this->load($identifier);
        $now  = time();

        $attempts = array_filter(
            $data['attempts'] ?? [],
            fn($ts) => ($now - $ts) <= $this->timeWindow
        );

        return max(0, $this->maxAttempts - count($attempts));
    }

    // ------------------------------------------------------------------
    // Métodos internos
    // ------------------------------------------------------------------

    /**
     * Genera la ruta del archivo para un identificador
     */
    private function getFilePath(string $identifier): string
    {
        $hash = hash('sha256', strtolower(trim($identifier)));
        return $this->storageDir . DIRECTORY_SEPARATOR . $hash . '.json';
    }

    /**
     * Carga los datos de un identificador con bloqueo de lectura
     */
    private function load(string $identifier): ?array
    {
        $file = $this->getFilePath($identifier);
        if (!is_file($file)) {
            return null;
        }

        $fp = @fopen($file, 'rb');
        if (!$fp) {
            return null;
        }

        flock($fp, LOCK_SH);
        $content = stream_get_contents($fp);
        flock($fp, LOCK_UN);
        fclose($fp);

        $data = json_decode($content, true);
        return is_array($data) ? $data : null;
    }

    /**
     * Guarda los datos con bloqueo exclusivo
     */
    private function save(string $identifier, array $data): void
    {
        $file = $this->getFilePath($identifier);

        $fp = @fopen($file, 'cb');
        if (!$fp) {
            throw new RuntimeException("No se pudo abrir para escritura: {$file}");
        }

        if (!flock($fp, LOCK_EX)) {
            fclose($fp);
            throw new RuntimeException("No se pudo bloquear el archivo: {$file}");
        }

        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, json_encode($data, JSON_PRETTY_PRINT));

        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);

        @chmod($file, 0640);
    }
}