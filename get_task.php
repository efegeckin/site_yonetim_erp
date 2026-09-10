<?php
require "db.php";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $task = $conn->query("SELECT * FROM gelistirmeler WHERE id = $id")->fetch_assoc();

    if ($task) {
        $files = [];
        $res = $conn->query("SELECT id, dosya FROM gelistirme_dosyalar WHERE gelistirme_id = $id");
        while ($f = $res->fetch_assoc()) {
            $files[] = $f;
        }

        echo json_encode(['success' => true, 'task' => $task, 'files' => $files]);
    } else {
        echo json_encode(['success' => false]);
    }
}
