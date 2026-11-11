<?php
if(!defined('DB_SERVER')){
    require_once(__DIR__ . "/../initialize.php");
}

class DBConnection{

    private $host;
    private $username;
    private $password;
    private $database;
    private $port;
    
    public $conn;
    
    public function __construct(){
        $this->host = DB_SERVER;
        $this->username = DB_USERNAME;
        $this->password = DB_PASSWORD;
        $this->database = DB_NAME;
        $this->port = defined('DB_PORT') ? (int)DB_PORT : 3306;

        if (!isset($this->conn)) {
            mysqli_report(MYSQLI_REPORT_OFF);

            $this->conn = mysqli_init();
            if (!$this->conn) {
                die("mysqli_init failed");
            }

            $this->conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 15);

            $ssl_enabled = defined('DB_SSL') && DB_SSL;
            if ($ssl_enabled) {
                $this->conn->ssl_set(NULL, NULL, NULL, NULL, NULL);
                if (defined('MYSQLI_OPT_SSL_VERIFY_SERVER_CERT')) {
                    $this->conn->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);
                }
            }

            $flags = ($ssl_enabled && defined('MYSQLI_CLIENT_SSL')) ? MYSQLI_CLIENT_SSL : 0;
            $connected = @$this->conn->real_connect(
                $this->host,
                $this->username,
                $this->password,
                $this->database,
                $this->port,
                NULL,
                $flags
            );

            // If SSL connection failed, fallback to regular connection
            if (!$connected && $ssl_enabled) {
                $connected = @$this->conn->real_connect(
                    $this->host,
                    $this->username,
                    $this->password,
                    $this->database,
                    $this->port
                );
            }

            if (!$connected) {
                echo "<div style='font-family:system-ui,-apple-system,sans-serif;max-width:600px;margin:50px auto;padding:24px;border:1px solid #f5c6cb;background:#f8d7da;color:#721c24;border-radius:8px;line-height:1.6;'>";
                echo "<h3 style='margin-top:0;'>⚠️ Database Connection Failed</h3>";
                echo "<p><strong>Error:</strong> " . htmlspecialchars($this->conn->connect_error) . "</p>";
                echo "<p>Please ensure you have configured your database environment variables on Render:</p>";
                echo "<ul>";
                echo "<li><code>DB_HOST</code>: " . htmlspecialchars($this->host) . "</li>";
                echo "<li><code>DB_PORT</code>: " . htmlspecialchars((string)$this->port) . "</li>";
                echo "<li><code>DB_USER</code>: " . htmlspecialchars($this->username) . "</li>";
                echo "<li><code>DB_NAME</code>: " . htmlspecialchars($this->database) . "</li>";
                echo "</ul>";
                echo "<p>Or provide <code>DATABASE_URL</code> (e.g. <code>mysql://user:pass@host:port/dbname</code>).</p>";
                echo "</div>";
                exit;
            }

            $this->conn->set_charset("utf8mb4");

            // Auto seed database if tables are missing
            $this->auto_seed_if_empty();
        }    
    }

    private function auto_seed_if_empty(){
        try {
            $check = $this->conn->query("SHOW TABLES LIKE 'system_info'");
            if ($check && $check->num_rows == 0) {
                $sql_file = defined('base_app') ? base_app . 'database/mvogms_db.sql' : __DIR__ . '/../database/mvogms_db.sql';
                if (file_exists($sql_file)) {
                    $sql_content = file_get_contents($sql_file);
                    if ($this->conn->multi_query($sql_content)) {
                        do {
                            if ($res = $this->conn->store_result()) {
                                $res->free();
                            }
                        } while ($this->conn->more_results() && $this->conn->next_result());
                    }
                }
            }
        } catch (Throwable $e) {
            error_log("Auto-seed notice: " . $e->getMessage());
        }
    }

    public function __destruct(){
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>
