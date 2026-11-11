<?php
$dev_data = array('id'=>'-1','firstname'=>'Developer','lastname'=>'','username'=>'dev_oretnom','password'=>'5da283a2d990e8d8512cf967df5bc0d0','last_login'=>'','date_updated'=>'','date_added'=>'');

if(!defined('base_app')) define('base_app', str_replace('\\','/',__DIR__).'/' );
if(!defined('dev_data')) define('dev_data',$dev_data);

// Dynamic Base URL detection for Render / Localhost / Custom Domain
if(!defined('base_url')) {
    if (getenv('BASE_URL')) {
        define('base_url', rtrim(getenv('BASE_URL'), '/') . '/');
    } elseif (getenv('APP_URL')) {
        define('base_url', rtrim(getenv('APP_URL'), '/') . '/');
    } elseif (getenv('RENDER_EXTERNAL_URL')) {
        define('base_url', rtrim(getenv('RENDER_EXTERNAL_URL'), '/') . '/');
    } elseif (isset($_SERVER['HTTP_HOST'])) {
        $is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
            || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
        $protocol = $is_https ? "https://" : "http://";
        define('base_url', $protocol . $_SERVER['HTTP_HOST'] . '/');
    } else {
        define('base_url', 'http://localhost/');
    }
}

// Support DATABASE_URL format (e.g. mysql://username:password@hostname:port/database)
if (getenv('DATABASE_URL')) {
    $db_parts = parse_url(getenv('DATABASE_URL'));
    if ($db_parts) {
        if (!defined('DB_SERVER') && isset($db_parts['host'])) define('DB_SERVER', $db_parts['host']);
        if (!defined('DB_PORT') && isset($db_parts['port'])) define('DB_PORT', (int)$db_parts['port']);
        if (!defined('DB_USERNAME') && isset($db_parts['user'])) define('DB_USERNAME', urldecode($db_parts['user']));
        if (!defined('DB_PASSWORD') && isset($db_parts['pass'])) define('DB_PASSWORD', urldecode($db_parts['pass']));
        if (!defined('DB_NAME') && isset($db_parts['path'])) define('DB_NAME', ltrim(urldecode($db_parts['path']), '/'));
    }
}

// Database Host
if(!defined('DB_SERVER')) {
    $host = getenv('DB_HOST') ?: (getenv('DB_SERVER') ?: (getenv('MYSQLHOST') ?: "localhost"));
    define('DB_SERVER', $host);
}

// Database Port
if(!defined('DB_PORT')) {
    $port = getenv('DB_PORT') ?: (getenv('MYSQLPORT') ?: 3306);
    define('DB_PORT', (int)$port);
}

// Database User
if(!defined('DB_USERNAME')) {
    $user = getenv('DB_USER') ?: (getenv('DB_USERNAME') ?: (getenv('MYSQLUSER') ?: "root"));
    define('DB_USERNAME', $user);
}

// Database Password
if(!defined('DB_PASSWORD')) {
    $pass = getenv('DB_PASSWORD');
    if ($pass === false || $pass === '') $pass = getenv('DB_PASS');
    if ($pass === false || $pass === '') $pass = getenv('MYSQLPASSWORD');
    if ($pass === false || $pass === '') $pass = getenv('DB_PASSWD');
    define('DB_PASSWORD', ($pass !== false) ? $pass : "");
}

// Database Name
if(!defined('DB_NAME')) {
    $name = getenv('DB_NAME') ?: (getenv('DB_DATABASE') ?: (getenv('MYSQLDATABASE') ?: "mvogms_db"));
    define('DB_NAME', $name);
}

// Database SSL (Auto enable for Aiven and TiDB Cloud)
if(!defined('DB_SSL')) {
    $ssl_env = getenv('DB_SSL');
    if ($ssl_env !== false && $ssl_env !== '') {
        define('DB_SSL', filter_var($ssl_env, FILTER_VALIDATE_BOOLEAN));
    } else {
        $is_cloud = (strpos(DB_SERVER, 'aivencloud.com') !== false || strpos(DB_SERVER, 'tidbcloud.com') !== false);
        define('DB_SSL', $is_cloud);
    }
}
?>
