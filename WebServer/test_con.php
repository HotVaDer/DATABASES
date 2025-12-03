<?php
$server = "127.0.0.1";

$connectionOptions = [
    "Database" => "master",
    "Uid" => "sa",
    "PWD" => "MyStrongPass123!",
    "Encrypt" => "no",
    "TrustServerCertificate" => "yes"
];

$conn = sqlsrv_connect($server, $connectionOptions);

if (!$conn) {
    echo "<pre>";
    print_r(sqlsrv_errors());
    echo "</pre>";
} else {
    echo "OK CONNECTED TO SQL SERVER";
}
?>
