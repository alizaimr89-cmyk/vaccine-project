
<?php
require_once __DIR__ . '/../includes/functions.php';
require_login('admin');

$pdo = db();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $contactName = trim($_POST['contact_name'] ?? '');
    $hospitalName = trim($_POST['hospital_name'] ?? '');
    $email = filter_var(
        trim($_POST['email'] ?? ''),
        FILTER_VALIDATE_EMAIL
    );
    $password = $_POST['password'] ?? '';
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if (
        !$contactName || !$hospitalName || !$email ||
        strlen($password) < 8 || !$address || !$city
    ) {
        $error = 'Complete all required fields. Password must be at least 8 characters.';
    } else {
        try {
            $pdo->beginTransaction();

            // Create the hospital login account.
            $stmt = $pdo->prepare(
                "INSERT INTO users
                (name, email, password_hash, role, status)
                VALUES (?, ?, ?, 'hospital', 'active')"
            );

            $stmt->execute([
                $contactName,
                $email,
                password_hash($password, PASSWORD_DEFAULT)
            ]);

            $userId = (int) $pdo->lastInsertId();

            // Link the hospital profile to this login account.
            $stmt = $pdo->prepare(
                "INSERT INTO hospitals
                (user_id, name, address, city, phone, email, status)
                VALUES (?, ?, ?, ?, ?, ?, 'active')"
            );

            $stmt->execute([
                $userId,
                $hospitalName,
                $address,
                $city,
                $phone,
                $email
            ]);

            $pdo->commit();

            $success = 'Hospital account and profile created successfully.';
        } catch (Throwable $ex) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            if ($ex instanceof PDOException &&
                $ex->getCode() === '23000') {
                $error = 'This email may already be registered, or the hospital profile conflicts with an existing record.';
            } else {
                $error = 'Could not create the account. Check the database tables and required fields.';
            }
        }
    }
}

$pageTitle = 'Create Hospital Account';
require __DIR__ . '/../includes/header.php';
?>

<section class="auth-layout">
    <form class="form-card" method="post">
        <h2>Create Hospital Account</h2>
        <p class="muted">Admin only — create a login and linked hospital profile.</p>

        <?php if ($error): ?>
            <div class="alert error"><?= e($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert success"><?= e($success) ?></div>
            <p>
                <a href="<?= BASE_URL ?>/hospital-login.php">
                    Open Hospital Login →
                </a>
            </p>
        <?php endif; ?>

        <input type="hidden" name="csrf"
               value="<?= e(csrf_token()) ?>">

        <label>Contact person's name
            <input name="contact_name" required maxlength="160">
        </label>

        <label>Hospital name
            <input name="hospital_name" required maxlength="160">
        </label>

        <label>Hospital login email
            <input type="email" name="email" required maxlength="190">
        </label>

        <label>Temporary password (minimum 8 characters)
            <input type="password" name="password"
                   required minlength="8" autocomplete="new-password">
        </label>

        <label>Address
            <input name="address" required maxlength="255">
        </label>

        <label>City
            <input name="city" required maxlength="100">
        </label>

        <label>Phone (optional)
            <input name="phone" maxlength="30">
        </label>

        <button class="button primary full" type="submit">
            Create Hospital Account →
        </button>

        <p class="form-foot">
            <a href="<?= BASE_URL ?>/admin/manage.php">Back to Admin Management</a>
        </p>
    </form>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
