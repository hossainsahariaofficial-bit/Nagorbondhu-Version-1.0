<?php
require_once __DIR__ . '/includes/bootstrap.php';
$authMode = 'login';
$authTitle = 'প্রবেশ করুন';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password = (string) ($_POST['password'] ?? '');
    if (!csrf_valid()) {
        $error = 'নিরাপত্তা যাচাই ব্যর্থ হয়েছে। পেজটি রিফ্রেশ করে আবার চেষ্টা করুন।';
    } elseif ($identifier === '' || $password === '') {
        $error = 'মোবাইল নম্বর বা ইমেইল এবং পাসওয়ার্ড দুটোই পূরণ করুন।';
    } elseif (!db_ok()) {
        $error = db_unavailable_message();
    } else {
        $user = find_user_by_identifier($identifier);
        if (!$user || !password_verify($password, (string) $user['password_hash'])) {
            $error = 'মোবাইল নম্বর/ইমেইল অথবা পাসওয়ার্ড সঠিক নয়। আবার চেষ্টা করুন।';
        } elseif (($user['status'] ?? 'active') === 'suspended') {
            $error = 'আপনার অ্যাকাউন্টটি সাময়িকভাবে স্থগিত আছে। সহায়তা দলের সঙ্গে যোগাযোগ করুন।';
        } elseif (($user['role'] ?? 'citizen') !== 'citizen') {
            $error = 'এই লগইন নাগরিকদের জন্য। প্রশাসনিক প্যানেল দিয়ে প্রবেশ করুন।';
        } else {
            auth_login_citizen($user);
            flash_set('success', 'স্বাগতম, ' . first_name_of((string) $user['name']) . '! আপনার ড্যাশবোর্ডে প্রবেশ করা হয়েছে।');
            header('Location: dashboard.php');
            exit;
        }
    }
}
include __DIR__ . '/includes/auth-header.php';
?>
<div class="auth-head">
  <?= brand('index.php', 'আমাদের পাড়া, আমাদের দায়িত্ব', 'auth-mark') ?>
  <span class="eyebrow"><?= icon('log-in', 'icon-xs') ?> নাগরিক প্রবেশ</span>
  <h2>আপনার অ্যাকাউন্টে প্রবেশ করুন</h2>
  <p>নতুন ব্যবহারকারী? <a href="register.php">বিনামূল্যে নিবন্ধন করুন</a></p>
</div>

<?php if ($error !== ''): ?>
  <div class="alert alert-danger" role="alert" style="margin-bottom:16px"><?= icon('alert-circle', 'icon-sm') ?><div><?= e($error) ?></div></div>
<?php endif; ?>

<form class="auth-form" method="post" action="login.php" data-validate novalidate>
  <?= csrf_field() ?>
  <div class="field">
    <label class="field-label" for="identifier">মোবাইল নম্বর বা ইমেইল <span class="req">*</span></label>
    <div class="input-group">
      <span class="input-icon"><?= icon('user', 'icon-sm') ?></span>
      <input class="input has-icon" id="identifier" name="identifier" type="text" placeholder="০১৭xx-xxxxxx অথবা ইমেইল" autocomplete="username" required value="<?= e($_POST['identifier'] ?? '') ?>">
    </div>
    <span class="field-hint">যে নম্বর বা ইমেইল দিয়ে অ্যাকাউন্ট খুলেছেন সেটি দিন।</span>
  </div>

  <div class="field">
    <label class="field-label" for="loginPassword">পাসওয়ার্ড <span class="req">*</span></label>
    <div class="input-group">
      <span class="input-icon"><?= icon('lock', 'icon-sm') ?></span>
      <input class="input has-icon has-toggle" id="loginPassword" name="password" type="password" placeholder="আপনার পাসওয়ার্ড লিখুন" autocomplete="current-password" required minlength="4">
      <button class="password-toggle" type="button" data-password-toggle="loginPassword" aria-label="পাসওয়ার্ড দেখুন" aria-pressed="false">
        <span class="eye-open"><?= icon('eye', 'icon-sm') ?></span>
        <span class="eye-off" style="display:none"><?= icon('eye-off', 'icon-sm') ?></span>
      </button>
    </div>
  </div>

  <div class="row-between">
    <label class="checkbox" style="font-size:.82rem"><input type="checkbox" name="remember" checked> আমাকে মনে রাখুন</label>
    <a class="text-link" href="contact.php" style="font-size:.8rem">পাসওয়ার্ড ভুলে গেছেন?</a>
  </div>

  <button class="btn btn-primary btn-lg btn-block" type="submit"><?= icon('log-in', 'icon-sm') ?> প্রবেশ করুন</button>

  <div class="auth-divider">অথবা</div>
  <a class="btn btn-outline btn-block" href="register.php"><?= icon('user-plus', 'icon-sm') ?> নতুন অ্যাকাউন্ট তৈরি করুন</a>
</form>

<div class="auth-demo">
  <strong>ডেমো নাগরিক অ্যাকাউন্ট:</strong> <code>sadia.afrin@example.com</code> অথবা <code>01711234567</code> — পাসওয়ার্ড <code>123456</code>। প্রশাসনিক অ্যাকাউন্টের জন্য <a href="admin-login.php">প্রশাসনিক প্যানেল</a> ব্যবহার করুন।
</div>
<p class="auth-security"><?= icon('shield', 'icon-sm') ?> আপনার তথ্য এনক্রিপ্টেড সংযোগে সুরক্ষিত থাকে</p>
<?php include __DIR__ . '/includes/auth-footer.php'; ?>
