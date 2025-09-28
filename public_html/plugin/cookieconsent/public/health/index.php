<?php
require __DIR__ . '/../../app/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'status' => 'ok',
    'time' => (new \DateTimeImmutable('now'))->format(DATE_ATOM),
]);
