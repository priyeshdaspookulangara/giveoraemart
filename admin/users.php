<?php
// admin/users.php - User Management & RBAC Control

$activeNav = 'users';
require_once __DIR__ . '/includes/admin_header.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../controllers/AdminController.php';

// Require strictly Admin role for role editing
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    AdminController::handleUserRoleUpdate();
    exit;
}

$users = UserModel::getAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0">User & Role Manager</h2>
        <p class="text-muted small mb-0">Role-based access control (RBAC) for Admins, Editors, Donors, and Beneficiaries.</p>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 bg-white">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email Address</th>
                        <th>Current Role</th>
                        <th>Registered Date</th>
                        <th class="text-end">Update Role Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">No registered users found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td class="fw-bold text-muted">#<?= $u['id'] ?></td>
                            <td class="fw-bold text-dark"><?= htmlspecialchars($u['name']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td>
                                <span class="badge bg-<?= $u['role'] === 'admin' ? 'danger' : ($u['role'] === 'editor' ? 'primary' : ($u['role'] === 'donor' ? 'success' : 'info')) ?> text-uppercase px-3 py-2">
                                    <?= $u['role'] ?>
                                </span>
                            </td>
                            <td class="small text-muted"><?= date('M j, Y - g:i A', strtotime($u['created_at'])) ?></td>
                            <td class="text-end">
                                <form action="users.php" method="POST" class="d-inline-flex align-items-center gap-2">
                                    <?= csrf_input() ?>
                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                    <select name="role" class="form-select form-select-sm w-auto">
                                        <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                        <option value="editor" <?= $u['role'] === 'editor' ? 'selected' : '' ?>>Editor</option>
                                        <option value="donor" <?= $u['role'] === 'donor' ? 'selected' : '' ?>>Donor</option>
                                        <option value="beneficiary" <?= $u['role'] === 'beneficiary' ? 'selected' : '' ?>>Beneficiary</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-outline-primary" title="Update Role">
                                        <i class="fa-solid fa-save"></i> Save
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
