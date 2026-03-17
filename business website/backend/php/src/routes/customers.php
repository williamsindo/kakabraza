<?php
function customers_handler($method, $segments, $body, $pdo) {
    if ($method === 'GET' && empty($segments)) {
        $stmt = $pdo->query('SELECT * FROM customers ORDER BY created_at DESC');
        echo json_encode($stmt->fetchAll()); return;
    }

    if ($method === 'GET' && isset($segments[0])) {
        $id = intval($segments[0]);
        $stmt = $pdo->prepare('SELECT * FROM customers WHERE id = ?');
        $stmt->execute([$id]);
        $customer = $stmt->fetch();
        if (!$customer) { http_response_code(404); echo json_encode(['error'=>'Customer not found']); return; }

        $stmt2 = $pdo->prepare('SELECT * FROM orders WHERE customer_id = ? ORDER BY created_at DESC');
        $stmt2->execute([$id]);
        $orders = $stmt2->fetchAll();

        echo json_encode(array_merge($customer, ['orders'=>$orders])); return;
    }

    if ($method === 'POST') {
        $name = $body['name'] ?? null; $email = $body['email'] ?? null;
        if (!$name || !$email) { http_response_code(400); echo json_encode(['error'=>'Name and email required']); return; }
        $stmt = $pdo->prepare('SELECT id FROM customers WHERE email = ?'); $stmt->execute([$email]); if ($stmt->fetch()) { http_response_code(400); echo json_encode(['error'=>'Customer exists']); return; }
        $stmt = $pdo->prepare('INSERT INTO customers (name,email,phone,address,city,country) VALUES (?,?,?,?,?,?)');
        $stmt->execute([$name,$email,$body['phone'] ?? '', $body['address'] ?? '', $body['city'] ?? '', $body['country'] ?? '']);
        http_response_code(201); echo json_encode(['message'=>'Customer created','id'=>$pdo->lastInsertId()]); return;
    }

    if ($method === 'PUT' && isset($segments[0])) {
        $id = intval($segments[0]);
        $stmt = $pdo->prepare('UPDATE customers SET name=?,email=?,phone=?,address=?,city=?,country=? WHERE id=?');
        $stmt->execute([$body['name'] ?? '', $body['email'] ?? '', $body['phone'] ?? '', $body['address'] ?? '', $body['city'] ?? '', $body['country'] ?? '', $id]);
        echo json_encode(['message'=>'Customer updated']); return;
    }

    if ($method === 'DELETE' && isset($segments[0])) {
        $id = intval($segments[0]);
        $stmt = $pdo->prepare('DELETE FROM customers WHERE id = ?'); $stmt->execute([$id]); echo json_encode(['message'=>'Customer deleted']); return;
    }

    http_response_code(405); echo json_encode(['error'=>'Method not allowed']);
}
?>