<?php
//=======================================================================\\
//  ## ####### #######                                                   \\
//  ## ##      ##   ##                                                   \\
//  ## ##      ## ####  |\  | |¯¯¯ ¯¯|¯¯ \      / |¯¯¯| |¯¯¯| | / |¯¯¯|  \\
//  ## ##      ##       | \ | |--    |    \    /  | | | | |_| |<   ¯\_   \\
//  ## ####### ##       |  \| |___   |     \/\/   |___| | |\  | \ |___|  \\
// --------------------------------------------------------------------- \\
//       Brazillian Developer / WebSite: http://www.icpfree.com.br       \\
//                 Email & Skype: ivan1507@gmail.com.br                  \\
//=======================================================================\\
try {
    // Determina a string de conexão com base no banco de dados (MySQL ou SQL Server)
    if (strtolower($db_data) === "l2j") {
        // Conexão MySQL
        $dsn = "mysql:host=$db_ip;dbname=$db_name;charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT            => 10, // Timeout em segundos
        ];
        } else {
            $dsn = "sqlsrv:Server=$db_ip;Database=$db_name;ConnectionPooling=0;LoginTimeout=10";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ];
        }

    // Cria a conexão com o banco de dados
    $conn = new PDO($dsn, $db_user, $db_pass, $options);
    
} catch (PDOException $e) {
    // Exibe o erro detalhado, útil para diagnóstico
    echo 'ERROR: ' . $e->getMessage();
}
?>