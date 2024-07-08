<?php
$servername = "bd_dev01.mysql.dbaas.com.br";
$username = "bd_dev01";
$password = "CP6337sf@@@";
$dbname = "bd_dev01";
$port = 3306;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
  die("Conexão falhou: " . $conn->connect_error);
}

function obterConexao() {
  global $conn;
  return $conn;
}

?>