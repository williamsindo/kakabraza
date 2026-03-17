<?php
function users_handler($method, $segments, $body, $pdo) {
    if ($method === 'POST' && isset($segments[0]) && $segments[0] === 'register') {
        // Register
        $username = $body['username'] ?? null;
        $email = $body['email'] ?? null;
        $password = $body['password'] ?? null; // plain text per request

        if (!$username || !$email || !$password) {
            http_response_code(400);
            echo json_encode(['error' => 'All fields are required']);
            return;
        }

        // Check existing
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            http_response_code(400);
            echo json_encode(['error' => 'User already exists']);
            return;
        }

        $stmt = $pdo->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
        $stmt->execute([$username, $email, $password]);

        http_response_code(201);
        echo json_encode(['message' => 'User registered', 'userId' => $pdo->lastInsertId()]);
        return;
    }

    if ($method === 'POST' && isset($segments[0]) && $segments[0] === 'login') {
        $username = $body['username'] ?? null;
        $password = $body['password'] ?? null;
        if (!$username || !$password) {
            http_response_code(400);
            echo json_encode(['error' => 'Username and password required']);
            return;
        }
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if (!$user || $user['password'] !== $password) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);
            return;
        }
        echo json_encode(['message' => 'Login successful', 'user' => ['id'=>$user['id'],'username'=>$user['username'],'email'=>$user['email']]]);
        return;
    }

    if ($method === 'GET' && empty($segments)) {
        // List users
        $stmt = $pdo->query('SELECT id, username, email, created_at FROM users');
        $users = $stmt->fetchAll();
        echo json_encode($users);
        return;
    }

    if ($method === 'GET' && isset($segments[0])) {
        $id = intval($segments[0]);
        $stmt = $pdo->prepare('SELECT id, username, email, created_at FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        if (!$user) {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
            return;
        }
        echo json_encode($user);
        return;
    }

    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>