<?php
// includes/matrix_engine.php - Breadth-First Search (BFS) Auto-Spillover & Multi-Level Incentives

require_once __DIR__ . '/functions.php';

/**
 * Places a newly registered user into the 3-matrix tree using Breadth-First Search (BFS) auto-spillover.
 * Max 3 children per node.
 *
 * @param int $newUserId
 * @param int|null $sponsorId
 */
function place_user_in_3_matrix($newUserId, $sponsorId = null) {
    $pdo = get_db_connection();

    // Check if already placed
    $stmt = $pdo->prepare("SELECT matrix_parent_id FROM users WHERE id = ?");
    $stmt->execute([$newUserId]);
    $user = $stmt->fetch();
    if ($user && $user['matrix_parent_id'] !== null) {
        return; // Already placed
    }

    // Determine starting root for BFS
    $startRootId = null;
    if ($sponsorId) {
        $startRootId = $sponsorId;
    } else {
        // Fallback to top admin user or first created user
        $topUser = $pdo->query("SELECT id FROM users ORDER BY id ASC LIMIT 1")->fetch();
        $startRootId = $topUser ? $topUser['id'] : null;
    }

    if (!$startRootId || $startRootId == $newUserId) {
        return;
    }

    // Queue for Breadth-First Search (BFS)
    $queue = [$startRootId];
    $visited = [$startRootId => true];

    $assignedParentId = null;
    $assignedPosition = null;

    while (!empty($queue)) {
        $currentParentId = array_shift($queue);

        // Fetch current children of this node
        $cStmt = $pdo->prepare("SELECT id, matrix_position FROM users WHERE matrix_parent_id = ? ORDER BY matrix_position ASC");
        $cStmt->execute([$currentParentId]);
        $existingChildren = $cStmt->fetchAll();

        // If node has less than 3 children, place new user here
        if (count($existingChildren) < 3) {
            $assignedParentId = $currentParentId;
            $takenPositions = array_column($existingChildren, 'matrix_position');
            for ($pos = 1; $pos <= 3; $pos++) {
                if (!in_array($pos, $takenPositions)) {
                    $assignedPosition = $pos;
                    break 2;
                }
            }
        } else {
            // Push children to BFS queue
            foreach ($existingChildren as $child) {
                if (!isset($visited[$child['id']])) {
                    $visited[$child['id']] = true;
                    $queue[] = $child['id'];
                }
            }
        }
    }

    if ($assignedParentId && $assignedPosition) {
        $upStmt = $pdo->prepare("UPDATE users SET matrix_parent_id = ?, matrix_position = ? WHERE id = ?");
        $upStmt->execute([$assignedParentId, $assignedPosition, $newUserId]);
    }
}

/**
 * Distributes $50 per head across 10 levels up the 3-matrix tree.
 */
function distribute_matrix_commissions($fromUserId, $orderId = null) {
    $pdo = get_db_connection();

    $currUserStmt = $pdo->prepare("SELECT matrix_parent_id FROM users WHERE id = ?");
    $insStmt = $pdo->prepare("INSERT INTO commissions (user_id, from_user_id, order_id, amount, income_type, level) VALUES (?, ?, ?, ?, 'MATRIX', ?)");

    $currentChildId = $fromUserId;

    for ($level = 1; $level <= 10; $level++) {
        $currUserStmt->execute([$currentChildId]);
        $row = $currUserStmt->fetch();

        if (!$row || !$row['matrix_parent_id']) {
            break; // Reached root
        }

        $parentId = $row['matrix_parent_id'];
        $matrixAmount = 50.00; // $50 per head in all 10 matrix levels

        $insStmt->execute([$parentId, $fromUserId, $orderId, $matrixAmount, $level]);
        $currentChildId = $parentId;
    }
}

/**
 * Distributes level referral income across 7 sponsor levels:
 * Level 1: $500
 * Level 2: $250
 * Level 3: $200
 * Level 4: $150
 * Level 5: $100
 * Level 6: $50
 * Level 7: $50
 */
function distribute_level_commissions($fromUserId, $orderId = null) {
    $pdo = get_db_connection();

    $levelAmounts = [
        1 => 500.00,
        2 => 250.00,
        3 => 200.00,
        4 => 150.00,
        5 => 100.00,
        6 => 50.00,
        7 => 50.00
    ];

    $sponsorStmt = $pdo->prepare("SELECT sponsor_id FROM users WHERE id = ?");
    $insStmt = $pdo->prepare("INSERT INTO commissions (user_id, from_user_id, order_id, amount, income_type, level) VALUES (?, ?, ?, ?, 'LEVEL', ?)");

    $currentUserId = $fromUserId;

    for ($level = 1; $level <= 7; $level++) {
        $sponsorStmt->execute([$currentUserId]);
        $row = $sponsorStmt->fetch();

        if (!$row || !$row['sponsor_id']) {
            break; // No further sponsor
        }

        $sponsorId = $row['sponsor_id'];
        $amount = $levelAmounts[$level];

        $insStmt->execute([$sponsorId, $fromUserId, $orderId, $amount, $level]);
        $currentUserId = $sponsorId;
    }
}

/**
 * Calculates total earnings breakdown for a user.
 */
function get_user_earnings($userId) {
    $pdo = get_db_connection();

    $stmt = $pdo->prepare("
        SELECT
            SUM(CASE WHEN income_type = 'MATRIX' THEN amount ELSE 0 END) as matrix_total,
            SUM(CASE WHEN income_type = 'LEVEL' THEN amount ELSE 0 END) as level_total,
            SUM(amount) as grand_total
        FROM commissions
        WHERE user_id = ?
    ");
    $stmt->execute([$userId]);
    $totals = $stmt->fetch();

    return [
        'matrix_total' => (float)($totals['matrix_total'] ?? 0.00),
        'level_total' => (float)($totals['level_total'] ?? 0.00),
        'grand_total' => (float)($totals['grand_total'] ?? 0.00)
    ];
}
