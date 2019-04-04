<?php

$database = [
    'host' => getenv('DATABASE_HOSTNAME') ?? 'localhost',
    'user' => getenv('DATABASE_USER') ?? 'root',
    'pass' => getenv('DATABASE_PASSWORD') ?? ''
];

try {
    $connection = new PDO('mysql:host=' . $database['host'] . ';', $database['user'], $database['pass']);
} catch (Exception $err) {
    echo $err->getMessage() . PHP_EOL;
    die;
}

// Get arguments
$sourceDatabase = $argv[1] ?? null;
$finalDatabase = $argv[2] ?? null;

// Now do the magic
$stmt = $connection->prepare('SHOW DATABASES;');
$stmt->execute();

$databases = getResultsFromDb($stmt);

if (!in_array($sourceDatabase, $databases) ||
    !in_array($finalDatabase, $databases)) {
    throw new Exception('Source database or final database doesn`t exist');
}

echo 'Moving tables from ' . $sourceDatabase . ' to ' . $finalDatabase . PHP_EOL;
$connection->query('USE ' . $sourceDatabase);
$stmt = $connection->prepare('SHOW TABLES;');
$stmt->execute();


$tables = getResultsFromDb($stmt);

try {
    $connection->beginTransaction();
    $connection->query('DROP DATABASE IF EXISTS ' . $finalDatabase . '; CREATE DATABASE ' . $finalDatabase);
    foreach ($tables as $table) {
        $sql = 'RENAME TABLE ' . $sourceDatabase . '.' . $table . ' TO ' . $finalDatabase . '.' . $table;
        echo $sql . PHP_EOL;
        $connection->query($sql);
    }
} catch (Exception $err) {
    echo $err->getMessage();
    die;
}

echo 'DONE' . PHP_EOL;

function getResultsFromDb($stmt): array
{
    return array_map(function ($item) {
        return $item[0];
    }, $stmt->fetchAll(PDO::FETCH_NUM));
}