<?php
require_once __DIR__ . '/includes/bootstrap.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (!csrf_valid()) {
        $error = 'নিরাপত্তা যাচাই ব্যর্থ হয়েছে। পেজটি রিফ্রেশ করে আবার চেষ্টা করুন।';
    } elseif ($email === '' || $password === '') {
        $error = 'প্রশাসনিক ইমেইল ও পাসওয়ার্ড দুটোই পূরণ করুন।';
    } elseif (!valid_email($email)) {
        $error = 'সঠিক প্রশাসনিক ইমেইল ঠিকানা লিখুন।';
    } elseif (!db_ok()) {
        $error = db_unavailable_message();
    } else {
        $user = find_user_by_identifier($email);
        if (!$user || !password_verify($password, (string) $user['password_hash'])) {
            $error = 'প্রশাসনিক ইমেইল অথবা পাসওয়ার্ড সঠিক নয়।';
        } elseif (($user['role'] ?? '') !== 'admin') {
            $error = 'এই অ্যাকাউন্টটি প্রশাসনিক নয়। নাগরিক লগইন পেজ ব্যবহার করুন।';
        } elseif (($user['status'] ?? 'active') === 'suspended') {
            $error = 'এই প্রশাসনিক অ্যাকাউন্টটি স্থগিত আছে। সিস্টেম প্রশাসকের সঙ্গে যোগাযোগ করুন।';
        } else {
            auth_login_admin($user);
            flash_set('success', 'প্রশাসনিক প্যানেলে স্বাগতম, ' . first_name_of((string) $user['name']) . '!');
            header('Location: admin.php');
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="bn">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#0B5138">
<title>প্রশাসনিক প্রবেশ — নগরবন্ধু</title>
<meta name="robots" content="noindex, nofollow">
<link rel="icon" href="assets/img/favicon.svg" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&family=Noto+Sans+Bengali:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="admin-login-body">
<a class="skip-link" href="#adminLoginMain">মূল কনটেন্টে যান</a>

<main class="admin-login-shell" id="adminLoginMain">
  <aside class="admin-login-aside">
    <?= brand('index.php', 'প্রশাসনিক নিয়ন্ত্রণ কেন্দ্র', 'brand-light') ?>

    <div>
      <span class="eyebrow" style="background:rgba(255,255,255,.12);color:#C8EBD9"><?= icon('shield', 'icon-xs') ?> সুরক্ষিত প্রশাসনিক অঞ্চল</span>
      <h1>নাগরিকের আশা, প্রশাসনের দায়িত্ব—একই প্ল্যাটফর্মে।</h1>
      <p>রিপোর্ট যাচাই, কর্তৃপক্ষকে কাজ বরাদ্দ, অনুদান নিষ্পত্তি এবং নাগরিক সেবার প্রতিটি ধাপ স্বচ্ছভাবে পরিচালনা করুন।</p>

      <div class="admin-login-features">
        <div class="admin-login-feature">
          <?= icon('check-square', 'icon-sm') ?>
          <div><strong>দ্রুত যাচাই ও অনুমোদন</strong><br>নতুন রিপোর্ট এক জায়গা থেকেই যাচাই করুন।</div>
        </div>
        <div class="admin-login-feature">
          <?= icon('bar-chart', 'icon-sm') ?>
          <div><strong>তাৎক্ষণিক বিশ্লেষণ</strong><br>এলাকা, ধরন ও কর্তৃপক্ষভিত্তিক অগ্রগতি দেখুন।</div>
        </div>
        <div class="admin-login-feature">
          <?= icon('shield', 'icon-sm') ?>
          <div><strong>নিরাপদ প্রবেশ</strong><br>দুই-ধাপ যাচাই ও সেশন পর্যবেক্ষণ সক্রিয়।</div>
        </div>
      </div>
    </div>

    <div>
      <div class="auth-aside-visual" style="border-color:rgba(255,255,255,.12)">
        <div class="auth-stat"><strong><?= bn('২,৪০০+') ?></strong><span>সক্রিয় নাগরিক</span></div>
        <div class="auth-stat"><strong><?= bn('৮৬৭') ?></strong><span>সমাধান সম্পন্ন</span></div>
        <div class="auth-stat"><strong><?= bn('১২') ?></strong><span>সংযুক্ত সংস্থা</span></div>
      </div>
      <p class="admin-login-foot" style="margin-top:16px">© <?= bn(date('Y')) ?> নগরবন্ধু — ঢাকা মহানগর প্রশাসনিক প্যানেল</p>
    </div>
  </aside>

  <section class="admin-login-main">
    <a class="auth-back" href="index.php"><?= icon('arrow-left', 'icon-sm') ?> ওয়েবসাইটে ফিরে যান</a>

    <span class="admin-login-badge"><?= icon('lock', 'icon-xs') ?> অনুমোদিত কর্মকর্তাদের জন্য</span>
    <h2>প্রশাসনিক প্রবেশ</h2>
    <p>আপনার প্রশাসনিক অ্যাকাউন্ট দিয়ে প্রবেশ করুন।</p>

    <?php if ($error !== ''): ?>
      <div class="alert alert-danger" role="alert" style="margin-bottom:16px"><?= icon('alert-circle', 'icon-sm') ?><div><?= e($error) ?></div></div>
    <?php endif; ?>

    <form class="auth-form" method="post" action="admin-login.php" data-validate novalidate>
      <?= csrf_field() ?>
      <div class="field">
        <label class="field-label" for="adminEmail">প্রশাসনিক ইমেইল <span class="req">*</span></label>
        <div class="input-group">
          <span class="input-icon"><?= icon('mail', 'icon-sm') ?></span>
          <input class="input has-icon" id="adminEmail" name="email" type="email" placeholder="admin@nagorbondhu.gov.bd" autocomplete="username" required value="<?= e($_POST['email'] ?? '') ?>">
        </div>
        <span class="field-hint">সরকারি ডোমেইনের অফিসিয়াল ইমেইল ব্যবহার করুন।</span>
        <span class="field-error"><?= icon('alert-circle', 'icon-xs') ?><span>সঠিক ইমেইল ঠিকানা লিখুন।</span></span>
      </div>

      <div class="field">
        <label class="field-label" for="adminPassword">পাসওয়ার্ড <span class="req">*</span></label>
        <div class="input-group">
          <span class="input-icon"><?= icon('lock', 'icon-sm') ?></span>
          <input class="input has-icon has-toggle" id="adminPassword" name="password" type="password" placeholder="আপনার পাসওয়ার্ড লিখুন" autocomplete="current-password" required minlength="6">
          <button class="password-toggle" type="button" data-password-toggle="adminPassword" aria-label="পাসওয়ার্ড দেখুন" aria-pressed="false">
            <span class="eye-open"><?= icon('eye', 'icon-sm') ?></span>
            <span class="eye-off" style="display:none"><?= icon('eye-off', 'icon-sm') ?></span>
          </button>
        </div>
      </div>

      <div class="row-between">
        <label class="checkbox" style="font-size:.82rem"><input type="checkbox" name="remember" checked> আমাকে মনে রাখুন</label>
        <a class="text-link" href="contact.php" style="font-size:.8rem">পাসওয়ার্ড ভুলে গেছেন?</a>
      </div>

      <button class="btn btn-primary btn-lg btn-block" type="submit"><?= icon('log-in', 'icon-sm') ?> প্রশাসনিক প্যানেলে প্রবেশ</button>
    </form>

    <div class="auth-demo" style="margin-top:16px">
      <strong>ডেমো প্রশাসনিক অ্যাকাউন্ট:</strong> <code>admin@nagorbondhu.gov.bd</code> / <code>admin123</code>। পাসওয়ার্ড ডেটাবেজে সুরক্ষিত হ্যাশ হিসেবে সংরক্ষিত থাকে।
    </div>

    <p class="admin-login-trust"><?= icon('shield', 'icon-sm') ?> আপনার প্রবেশ তথ্য এনক্রিপ্টেড সংযোগে সুরক্ষিত থাকে</p>
  </section>
</main>

<script src="assets/js/app.js"></script>
</body>
</html>
