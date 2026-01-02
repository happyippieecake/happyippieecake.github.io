<?php
require_once 'db_connect.php';

// 1. Add column if not exists
try {
    $conn->query("ALTER TABLE pesanan ADD COLUMN order_id VARCHAR(50) NULL AFTER id");
    echo "Column order_id added.\n";
    $conn->query("ALTER TABLE pesanan ADD INDEX (order_id)");
    echo "Index added.\n";
} catch (Exception $e) {
    echo "Column likely exists or error: " . $e->getMessage() . "\n";
}

// 2. Backfill Logic
// Fetch all pending pesanan without order_id
$result = $conn->query("SELECT * FROM pesanan WHERE (order_id IS NULL OR order_id='') ORDER BY id ASC");
$rows = [];
while($r = $result->fetch_assoc()) $rows[] = $r;

echo "Found " . count($rows) . " rows to backfill.\n";

$grouped = [];
foreach($rows as $r) {
    // Basic grouping by Name + Alamat + Date
    // Improved: Check if this row has a payment link?
    // In current schema, payments.pesanan_id points to one row.
    
    // Check if this row is a "Head" (has payment)
    $pay = $conn->query("SELECT order_id FROM payments WHERE pesanan_id = " . $r['id'])->fetch_assoc();
    
    if ($pay && $pay['order_id']) {
        // This row is definitely part of order X
        $key = $pay['order_id'];
    } else {
        // No direct link. Use heuristic.
        // If the previous row has same User+Addr+Date and is "close" in ID, assume same group.
        $signature = md5($r['nama_pemesan'] . $r['alamat'] . $r['tanggal_pesan']);
        
        // Find existing group for this signature?
        // But wait, if duplicates exist (Double Submission), we want to SEPARATE them.
        // Heuristic: If ID gap is > 10, start new group even if signature matches?
        // Or if we already have a group for this signature, append?
        
        // To fix the "Double Submission" issue:
        // If we see 6 items: A1, B1, C1 (Paid), A2, B2, C2 (Unpaid).
        // 1, 2, 3 -> Paid
        // 4, 5, 6 -> Unpaid
        // If we iterate in order:
        // ID 1: Has payment -> Key = HPC-PAID
        // ID 2: No payment. Signature matches ID 1. ID diff is 1. -> Assign HPC-PAID.
        // ID 3: No payment. Signature matches ID 2. ID diff is 1. -> Assign HPC-PAID.
        // ID 4: No payment. Signature matches ID 3. ID diff is 1. -> Assign HPC-PAID ???
        
        // Here is the risk. If user submitted TWICE in rapid succession, IDs are continuous.
        // Ideally, we want to split.
        // But if I split, I might split a valid large order.
        
        // However, the user SPECIFICALLY complained about duplicates showing up as ONE.
        // So they WANT them separated.
        // If the user made 2 identical orders, they are 2 orders.
        // So we should try to assign a NEW order_id for the second batch if we can detect it.
        // How to detect "Second Batch"?
        // Distinct sets of Menu Items?
        // If I see "Cupcake" again in the same group, it's likely a new order?
        // Yes! A user rarely orders the EXACT SAME menu item twice as separate rows in ONE cart.
        // (Usually quantity increases, not new row).
        // So if we encounter a Menu ID that is ALREADY in the current group, we FORCE a split!
        
        $key = 'HPC-GEN-' . $signature . '-1'; // Default
    }
    
    $grouped[] = ['row' => $r, 'pay_order_id' => ($pay['order_id']??null)];
}

// Global pass to assign IDs
$current_group_id = null;
$current_signature = '';
$seen_menu_ids = [];

foreach($rows as $r) {
    $signature = md5($r['nama_pemesan'] . $r['alamat'] . $r['tanggal_pesan']);
    
    // Check if this row is a Head (has payment)
    $pay = $conn->query("SELECT order_id FROM payments WHERE pesanan_id = " . $r['id'])->fetch_assoc();
    
    $should_start_new = false;
    
    // 1. If Signature differs from current -> New Group
    if ($signature !== $current_signature) {
        $should_start_new = true;
    }
    // 2. If this row has an explicit Payment Order ID -> New Group (and use that ID)
    elseif ($pay && $pay['order_id']) {
        $should_start_new = true;
    }
    // 3. If this row's menu_id is already seen in current group -> Likely duplicate submission -> Splinter off!
    elseif (in_array($r['menu_id'], $seen_menu_ids)) {
        $should_start_new = true;
    }
    
    if ($should_start_new) {
        // Start new group
        if ($pay && $pay['order_id']) {
            $current_group_id = $pay['order_id'];
        } else {
            // Generate new
            $current_group_id = 'HPC-' . date('YmdHis', strtotime($r['tanggal_pesan'] . ' 12:00:00')) . '-' . strtoupper(substr(uniqid(), -4));
            // Add slight random suffix collision avoidance
            if(rand(0,1)) $current_group_id .= 'B';
        }
        $current_signature = $signature;
        $seen_menu_ids = [];
    }
    
    // Add to seen
    $seen_menu_ids[] = $r['menu_id'];
    
    // Update DB
    $conn->query("UPDATE pesanan SET order_id = '$current_group_id' WHERE id = " . $r['id']);
    echo "Updated ID " . $r['id'] . " to Group " . $current_group_id . "\n";
}

echo "Migration done.";
