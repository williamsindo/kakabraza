<?php
function admin_handler($method, $segments, $body, $pdo) {
    if ($method === 'GET' && isset($segments[0]) && $segments[0] === 'stats' && isset($segments[1]) && $segments[1] === 'dashboard') {
        $totalProducts = $pdo->query('SELECT COUNT(*) as c FROM products')->fetch()['c'];
        $totalOrders = $pdo->query('SELECT COUNT(*) as c FROM orders')->fetch()['c'];
        $totalCustomers = $pdo->query('SELECT COUNT(*) as c FROM customers')->fetch()['c'];
        $totalRevenue = $pdo->query('SELECT IFNULL(SUM(total_amount),0) as s FROM orders WHERE status != "Cancelled"')->fetch()['s'];
        $status = $pdo->query('SELECT status, COUNT(*) as count FROM orders GROUP BY status')->fetchAll();
        echo json_encode(['totalProducts'=>$totalProducts,'totalOrders'=>$totalOrders,'totalCustomers'=>$totalCustomers,'totalRevenue'=>$totalRevenue,'ordersByStatus'=>$status]); return;
    }

    if ($method === 'GET' && isset($segments[0]) && $segments[0] === 'reports' && isset($segments[1]) && $segments[1] === 'sales') {
        $rows = $pdo->query("SELECT o.id,o.order_number,c.name as customer_name,o.total_amount,o.status,o.payment_status,o.created_at,COUNT(oi.id) as item_count FROM orders o LEFT JOIN customers c ON o.customer_id = c.id LEFT JOIN order_items oi ON o.id = oi.order_id WHERE o.status != 'Cancelled' GROUP BY o.id ORDER BY o.created_at DESC")->fetchAll();
        $totalRevenue = array_sum(array_map(function($r){return floatval($r['total_amount']);}, $rows));
        $avg = count($rows)>0? $totalRevenue/count($rows):0;
        echo json_encode(['totalRevenue'=>$totalRevenue,'totalOrders'=>count($rows),'averageOrderValue'=>$avg,'orders'=>$rows]); return;
    }

    if ($method === 'GET' && isset($segments[0]) && $segments[0] === 'export' && isset($segments[1]) && $segments[1] === 'sales-csv') {
        $rows = $pdo->query('SELECT o.order_number,c.name as customer_name,o.total_amount,o.status,o.payment_status,o.created_at FROM orders o LEFT JOIN customers c ON o.customer_id = c.id ORDER BY o.created_at DESC')->fetchAll();
        header('Content-Type: text/csv'); header('Content-Disposition: attachment; filename="sales_report.csv"');
        $out = fopen('php://output','w'); fputcsv($out, ['Order Number','Customer','Amount','Status','Payment','Date']);
        foreach($rows as $r) { fputcsv($out, [$r['order_number'],$r['customer_name'],$r['total_amount'],$r['status'],$r['payment_status'],$r['created_at']]); }
        fclose($out); return;
    }

    http_response_code(404); echo json_encode(['error'=>'Not found']);
}
?>