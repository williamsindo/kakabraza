<?php
function orders_handler($method, $segments, $body, $pdo) {
    if ($method === 'GET' && empty($segments)) {
        $stmt = $pdo->query("SELECT o.*, c.name as customer_name, c.email as customer_email FROM orders o LEFT JOIN customers c ON o.customer_id = c.id ORDER BY o.created_at DESC");
        echo json_encode($stmt->fetchAll()); return;
    }

    if ($method === 'GET' && isset($segments[0])) {
        $id = intval($segments[0]);
        $stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ?'); $stmt->execute([$id]); $order = $stmt->fetch();
        if (!$order) { http_response_code(404); echo json_encode(['error'=>'Order not found']); return; }
        $stmt2 = $pdo->prepare('SELECT oi.*, p.name as product_name FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?'); $stmt2->execute([$id]); $items = $stmt2->fetchAll();
        echo json_encode(array_merge($order, ['items'=>$items])); return;
    }

    if ($method === 'POST') {
        // create order: expect customer_id, items [{product_id, quantity}], shipping_address, notes
        $customer_id = $body['customer_id'] ?? null; $items = $body['items'] ?? null;
        if (!$customer_id || !$items || !is_array($items) || count($items)===0) { http_response_code(400); echo json_encode(['error'=>'Customer and items required']); return; }

        $pdo->beginTransaction();
        try {
            // calculate total
            $total = 0.0;
            foreach ($items as $it) {
                $stmtp = $pdo->prepare('SELECT price, stock FROM products WHERE id = ?'); $stmtp->execute([$it['product_id']]); $prod = $stmtp->fetch();
                if (!$prod) { throw new Exception('Product not found: ' . $it['product_id']); }
                if ($prod['stock'] < $it['quantity']) { throw new Exception('Not enough stock for product ' . $it['product_id']); }
                $total += $prod['price'] * $it['quantity'];
            }
            $order_number = 'ORD' . time();
            $stmt = $pdo->prepare('INSERT INTO orders (order_number, customer_id, total_amount, shipping_address, notes) VALUES (?,?,?,?,?)');
            $stmt->execute([$order_number, $customer_id, $total, $body['shipping_address'] ?? '', $body['notes'] ?? '']);
            $order_id = $pdo->lastInsertId();
            foreach ($items as $it) {
                $stmtp = $pdo->prepare('SELECT price FROM products WHERE id = ?'); $stmtp->execute([$it['product_id']]); $prod = $stmtp->fetch();
                $unit = $prod['price']; $subtotal = $unit * $it['quantity'];
                $stmti = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, unit_price, subtotal) VALUES (?,?,?,?,?)');
                $stmti->execute([$order_id, $it['product_id'], $it['quantity'], $unit, $subtotal]);
                $pdo->prepare('UPDATE products SET stock = stock - ? WHERE id = ?')->execute([$it['quantity'], $it['product_id']]);
            }
            $pdo->commit();
            http_response_code(201); echo json_encode(['message'=>'Order created','orderId'=>$order_id,'order_number'=>$order_number,'total'=>$total]); return;
        } catch (Exception $e) {
            $pdo->rollBack(); http_response_code(400); echo json_encode(['error'=>$e->getMessage()]); return;
        }
    }

    if ($method === 'PATCH' && isset($segments[0]) && isset($segments[1]) && $segments[1] === 'status') {
        $id = intval($segments[0]); $status = $body['status'] ?? null;
        if (!$status) { http_response_code(400); echo json_encode(['error'=>'Status required']); return; }
        $stmt = $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?'); $stmt->execute([$status,$id]); echo json_encode(['message'=>'Order status updated']); return;
    }

    if ($method === 'PATCH' && isset($segments[0]) && isset($segments[1]) && $segments[1] === 'payment') {
        $id = intval($segments[0]); $pstatus = $body['payment_status'] ?? null; if (!$pstatus) { http_response_code(400); echo json_encode(['error'=>'Payment status required']); return; }
        $stmt = $pdo->prepare('UPDATE orders SET payment_status = ? WHERE id = ?'); $stmt->execute([$pstatus,$id]); echo json_encode(['message'=>'Payment status updated']); return;
    }

    if ($method === 'DELETE' && isset($segments[0])) {
        $id = intval($segments[0]); $pdo->prepare('DELETE FROM order_items WHERE order_id = ?')->execute([$id]); $pdo->prepare('DELETE FROM orders WHERE id = ?')->execute([$id]); echo json_encode(['message'=>'Order deleted']); return;
    }

    http_response_code(405); echo json_encode(['error'=>'Method not allowed']);
}
?>