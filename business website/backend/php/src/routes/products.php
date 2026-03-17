<?php
function products_handler($method, $segments, $body, $pdo) {
    if ($method === 'GET' && empty($segments)) {
        $stmt = $pdo->query('SELECT * FROM products');
        $rows = $stmt->fetchAll();
        echo json_encode($rows);
        return;
    }

    if ($method === 'GET' && isset($segments[0])) {
        $id = intval($segments[0]);
        $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) { http_response_code(404); echo json_encode(['error'=>'Product not found']); return; }
        echo json_encode($row);
        return;
    }

    if ($method === 'POST') {
        $name = $body['name'] ?? null;
        $price = $body['price'] ?? null;
        if (!$name || !$price) { http_response_code(400); echo json_encode(['error'=>'Name and price required']); return; }
        $stmt = $pdo->prepare('INSERT INTO products (name, description, price, stock, image_url) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$name, $body['description'] ?? '', $price, $body['stock'] ?? 0, $body['image_url'] ?? '']);
        http_response_code(201); echo json_encode(['message'=>'Product created','id'=>$pdo->lastInsertId()]); return;
    }

    if ($method === 'PUT' && isset($segments[0])) {
        $id = intval($segments[0]);
        $stmt = $pdo->prepare('UPDATE products SET name=?, description=?, price=?, stock=?, image_url=? WHERE id=?');
        $stmt->execute([$body['name'] ?? '', $body['description'] ?? '', $body['price'] ?? 0, $body['stock'] ?? 0, $body['image_url'] ?? '', $id]);
        echo json_encode(['message'=>'Product updated']); return;
    }

    if ($method === 'DELETE' && isset($segments[0])) {
        $id = intval($segments[0]);
        $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
        $stmt->execute([$id]);
        echo json_encode(['message'=>'Product deleted']); return;
    }

    if ($method === 'PATCH' && isset($segments[0]) && isset($segments[1]) && $segments[1] === 'stock') {
        $id = intval($segments[0]);
        $qty = intval($body['quantity'] ?? 0);
        $stmt = $pdo->prepare('UPDATE products SET stock = stock + ? WHERE id = ?');
        $stmt->execute([$qty, $id]);
        echo json_encode(['message'=>'Stock updated']); return;
    }

    http_response_code(405);
    echo json_encode(['error'=>'Method not allowed']);
}
?>