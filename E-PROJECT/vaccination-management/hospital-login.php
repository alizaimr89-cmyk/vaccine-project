
<?php
require_once __DIR__ . '/includes/functions.php';

if (current_user() && current_user()['role'] === 'hospital') {
    redirect('/hospital/index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $email = filter_var(
        trim($_POST['email'] ?? ''),
        FILTER_VALIDATE_EMAIL
    );
    $password = $_POST['password'] ?? '';

    $stmt = db()->prepare(
        "SELECT id, name, email, password_hash, role, status
         FROM users
         WHERE email = ? AND role = 'hospital'
         LIMIT 1"
    );
    $stmt->execute([$email ?: '']);
    $user = $stmt->fetch();

    if (
        $user &&
        password_verify($password, $user['password_hash']) &&
        $user['status'] === 'active'
    ) {
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => 'hospital'
        ];

        redirect('/hospital/index.php');
    }

    $error = 'Hospital email or password is incorrect, or the account is inactive.';
}

$pageTitle = 'Hospital Login';
require __DIR__ . '/includes/header.php';
?>

<section class="auth-layout">
    <form class="form-card" method="post">
        <h2>Hospital Login</h2>
        <p class="muted">Sign in to manage vaccinations.</p>

        <?php if ($error): ?>
            <div class="alert error"><?= e($error) ?></div>
        <?php endif; ?>

        <input type="hidden" name="csrf"
               value="<?= e(csrf_token()) ?>">

        <label>Hospital email
            <input type="email" name="email" required>
        </label>

        <label>Password
            <input type="password" name="password" required>
        </label>

        <button class="button primary full" type="submit">
            Sign in →
        </button>
    </form>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
