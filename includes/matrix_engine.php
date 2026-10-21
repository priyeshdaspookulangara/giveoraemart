<?php
// includes/matrix_engine.php - Breadth-First Search (BFS) Auto-Spillover & Multi-Level Incentives

require_once __DIR__ . '/functions.php';

/**
 * Places a newly registered user into the 3-matrix tree using Breadth-First Search (BFS) auto-spillover.
 * Max 3 children per node. Top apex root is "givoratraders" (ID: 1).
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

    // Apex root ID defaults to 1 ("givoratraders")
    $startRootId = 1;
    if ($sponsorId) {
        $startRootId = $sponsorId;
    }

    if ($startRootId == $newUserId) {
        $startRootId = 1;
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
 * Checks if a user is qualified for Matrix Income (Needs at least 3 direct referrals).
 */
function is_matrix_qualified($userId) {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE sponsor_id = ?");
    $stmt->execute([$userId]);
    $referralCount = (int)$stmt->fetchColumn();
    return $referralCount >= 3;
}

/**
 * Gets direct referral count for a user.
 */
function get_direct_referral_count($userId) {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE sponsor_id = ?");
    $stmt->execute([$userId]);
    return (int)$stmt->fetchColumn();
}

/**
 * Distributes $50 per head across 11 levels up the 3-matrix tree.
 * QUALIFICATION REQUIREMENT: User must have referred at least 3 direct members to earn matrix income.
 */
function distribute_matrix_commissions($fromUserId, $orderId = null) {
    $pdo = get_db_connection();

    $currUserStmt = $pdo->prepare("SELECT matrix_parent_id FROM users WHERE id = ?");
    $insStmt = $pdo->prepare("INSERT INTO commissions (user_id, from_user_id, order_id, amount, income_type, level) VALUES (?, ?, ?, ?, 'MATRIX', ?)");

    $currentChildId = $fromUserId;

    for ($level = 1; $level <= 11; $level++) {
        $currUserStmt->execute([$currentChildId]);
        $row = $currUserStmt->fetch();

        if (!$row || !$row['matrix_parent_id']) {
            break; // Reached root
        }

        $parentId = $row['matrix_parent_id'];

        // Qualification Check: Parent MUST have referred at least 3 direct members
        if (is_matrix_qualified($parentId)) {
            $matrixAmount = 50.00; // $50 per head in all 11 matrix levels
            $insStmt->execute([$parentId, $fromUserId, $orderId, $matrixAmount, $level]);
        }

        $currentChildId = $parentId;
    }
}

/**
 * Distributes level referral income across 7 sponsor levels:
 * Level 1: $500, Level 2: $250, Level 3: $200, Level 4: $150, Level 5: $100, Level 6: $50, Level 7: $50
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

/**
 * Recursively builds array tree structure for 3-Matrix downline visualization.
 *
 * @param int $rootUserId
 * @param int $depth Current depth limit for rendering
 * @return array
 */
function get_matrix_tree_data($rootUserId, $maxDepth = 3, $currentDepth = 1) {
    $pdo = get_db_connection();

    $stmt = $pdo->prepare("SELECT id, name, email, matrix_parent_id, matrix_position, created_at FROM users WHERE id = ?");
    $stmt->execute([$rootUserId]);
    $node = $stmt->fetch();

    if (!$node) {
        return null;
    }

    $node['is_qualified'] = is_matrix_qualified($node['id']);
    $node['direct_ref_count'] = get_direct_referral_count($node['id']);
    $node['children'] = [];

    if ($currentDepth <= $maxDepth) {
        $cStmt = $pdo->prepare("SELECT id, matrix_position FROM users WHERE matrix_parent_id = ? ORDER BY matrix_position ASC");
        $cStmt->execute([$rootUserId]);
        $children = $cStmt->fetchAll();

        // Organize children by positions 1, 2, 3
        $childrenByPos = [];
        foreach ($children as $child) {
            $childrenByPos[$child['matrix_position']] = $child['id'];
        }

        for ($pos = 1; $pos <= 3; $pos++) {
            if (isset($childrenByPos[$pos])) {
                $node['children'][$pos] = get_matrix_tree_data($childrenByPos[$pos], $maxDepth, $currentDepth + 1);
            } else {
                $node['children'][$pos] = null; // Empty slot
            }
        }
    }

    return $node;
}

/**
 * HTML Helper to render a visual 3-matrix tree node component.
 */
function render_matrix_tree_html($treeNode, $depth = 1, $maxDepth = 3) {
    if (!$treeNode) return '';

    $isRoot = ($treeNode['id'] == 1);
    $badgeColor = $isRoot ? 'bg-amber-500/20 text-amber-300 border-amber-500/30' : ($treeNode['is_qualified'] ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' : 'bg-rose-500/20 text-rose-300 border-rose-500/30');

    $html = '<div class="flex flex-col items-center my-2 text-center relative">';

    // Node Card
    $html .= '<div class="bg-slate-900 border border-slate-700/80 rounded-2xl p-3 shadow-xl w-48 transition hover:border-emerald-500/50 z-10 hover:scale-105">';
    $html .= '  <div class="flex items-center justify-between mb-1.5">';
    $html .= '    <span class="text-[10px] font-mono text-slate-500">#' . $treeNode['id'] . '</span>';
    $html .= '    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold border ' . $badgeColor . '">' . ($isRoot ? 'TOP APEX' : ($treeNode['is_qualified'] ? 'QUALIFIED' : 'UNQUALIFIED')) . '</span>';
    $html .= '  </div>';
    $html .= '  <h4 class="font-bold text-white text-xs truncate">' . htmlspecialchars($treeNode['name']) . '</h4>';
    $html .= '  <p class="text-[10px] text-slate-400 truncate mt-0.5">' . htmlspecialchars($treeNode['email']) . '</p>';
    $html .= '  <div class="mt-2 pt-1.5 border-t border-slate-800 text-[10px] text-slate-400 flex justify-between">';
    $html .= '    <span>Direct Refs: <b class="text-emerald-400">' . $treeNode['direct_ref_count'] . '</b></span>';
    $html .= '  </div>';
    $html .= '</div>';

    // Children Container
    if (!empty($treeNode['children']) && $depth < $maxDepth) {
        $html .= '<div class="w-px h-6 bg-slate-700 my-1"></div>';
        $html .= '<div class="flex items-start justify-center gap-4 relative pt-2 border-t border-slate-700/80 w-full overflow-x-auto">';

        foreach ([1, 2, 3] as $pos) {
            $childNode = $treeNode['children'][$pos] ?? null;
            if ($childNode) {
                $html .= render_matrix_tree_html($childNode, $depth + 1, $maxDepth);
            } else {
                // Render Empty Slot Box
                $html .= '<div class="flex flex-col items-center my-2 text-center">';
                $html .= '  <div class="bg-slate-950/60 border border-dashed border-slate-800 rounded-2xl p-3 w-36 text-center">';
                $html .= '    <span class="text-[9px] font-bold uppercase text-slate-600 block mb-1">Position ' . $pos . '</span>';
                $html .= '    <div class="w-6 h-6 rounded-full bg-slate-900 text-slate-700 flex items-center justify-center mx-auto text-xs"><i class="fa-solid fa-plus"></i></div>';
                $html .= '    <span class="text-[10px] text-slate-600 block mt-1">Available Slot</span>';
                $html .= '  </div>';
                $html .= '</div>';
            }
        }
        $html .= '</div>';
    }

    $html .= '</div>';
    return $html;
}
