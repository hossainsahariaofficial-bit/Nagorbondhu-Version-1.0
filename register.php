<?php
require_once __DIR__ . '/includes/bootstrap.php';
$authMode = 'register';
$authTitle = 'নিবন্ধন করুন';
$authWide = true;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $area = trim($_POST['area'] ?? '');
    $password = (string) ($_POST['password'] ?? '');
    $confirm = (string) ($_POST['confirm'] ?? '');
    $terms = isset($_POST['terms']);

    if (!csrf_valid()) {
        $error = 'নিরাপত্তা যাচাই ব্যর্থ হয়েছে। পেজটি রিফ্রেশ করে আবার চেষ্টা করুন।';
    } elseif ($name === '' || $phone === '' || $email === '' || $area === '' || $password === '') {
        $error = 'তারকা চিহ্নিত সব তথ্য পূরণ করুন।';
    } elseif (mb_strlen($name) < 3) {
        $error = 'পূর্ণ নাম কমপক্ষে ৩ অক্ষরের হতে হবে।';
    } elseif (!valid_bd_phone($phone)) {
        $error = 'সঠিক ১১ সংখ্যার বাংলাদেশি মোবাইল নম্বর লিখুন (যেমন ০১৭xxxxxxxx)।';
    } elseif (!valid_email($email)) {
        $error = 'সঠিক ইমেইল ঠিকানা লিখুন।';
    } elseif (mb_strlen($password) < 6) {
        $error = 'পাসওয়ার্ড কমপক্ষে ৬ অক্ষরের হতে হবে।';
    } elseif ($password !== $confirm) {
        $error = 'পাসওয়ার্ড দুটি মিলছে না।';
    } elseif (!$terms) {
        $error = 'এগিয়ে যেতে ব্যবহারের শর্তাবলিতে সম্মতি দিন।';
    } elseif (!db_ok()) {
        $error = db_unavailable_message();
    } else {
        $phone = normalize_phone($phone);
        $email = mb_strtolower($email);

        if (db_val('SELECT COUNT(*) FROM users WHERE email = ?', [$email]) > 0) {
            $error = 'এই ইমেইল দিয়ে আগেই একটি অ্যাকাউন্ট তৈরি করা হয়েছে। প্রবেশ করুন অথবা অন্য ইমেইল ব্যবহার করুন।';
        } elseif (db_val('SELECT COUNT(*) FROM users WHERE phone = ?', [$phone]) > 0) {
            $error = 'এই মোবাইল নম্বর দিয়ে আগেই একটি অ্যাকাউন্ট তৈরি করা হয়েছে। প্রবেশ করুন অথবা অন্য নম্বর ব্যবহার করুন।';
        } else {
            $userId = db_insert(
                "INSERT INTO users (name, phone, email, password_hash, area, role)
                 VALUES (?, ?, ?, ?, ?, 'citizen')",
                [$name, $phone, $email, password_hash($password, PASSWORD_DEFAULT), $area]
            );

            if ($userId <= 0) {
                $error = 'অ্যাকাউন্ট তৈরি করা যায়নি। ' . (db_error() !== '' ? 'ডেটাবেজ ত্রুটি: ' . db_error() : db_unavailable_message());
            } else {
                $user = db_one('SELECT * FROM users WHERE id = ?', [$userId]);
                if ($user) {
                    auth_login_citizen($user);
                    flash_set('success', 'অভিনন্দন! আপনার নাগরিক অ্যাকাউন্ট তৈরি হয়েছে।');
                    header('Location: dashboard.php');
                    exit;
                }
                $error = 'অ্যাকাউন্ট তৈরি হয়েছে, কিন্তু লগইন করা যায়নি। অনুগ্রহ করে প্রবেশ করুন।';
            }
        }
    }
}
include __DIR__ . '/includes/auth-header.php';
?>
<div class="auth-head">
  <?= brand('index.php', 'আমাদের পাড়া, আমাদের দায়িত্ব', 'auth-mark') ?>
  <span class="eyebrow"><?= icon('user-plus', 'icon-xs') ?> নতুন নাগরিক</span>
  <h2>নগরবন্ধু পরিবারে যোগ দিন</h2>
  <p>ইতিমধ্যে অ্যাকাউন্ট আছে? <a href="login.php">প্রবেশ করুন</a></p>
</div>

<?php if ($error !== ''): ?>
  <div class="alert alert-danger" role="alert" style="margin-bottom:16px"><?= icon('alert-circle', 'icon-sm') ?><div><?= e($error) ?></div></div>
<?php endif; ?>

<form class="auth-form" method="post" action="register.php" data-validate novalidate>
  <?= csrf_field() ?>
  <div class="form-grid">
    <div class="field span-2">
      <label class="field-label" for="regName">পূর্ণ নাম <span class="req">*</span></label>
      <input class="input" id="regName" name="name" type="text" placeholder="যেমন: সাদিয়া আফরিন" autocomplete="name" required minlength="3" value="<?= e($_POST['name'] ?? '') ?>">
      <span class="field-hint">আপনার নাম পাবলিক প্রোফাইলে দেখা যাবে।</span>
    </div>

    <div class="field">
      <label class="field-label" for="regPhone">মোবাইল নম্বর <span class="req">*</span></label>
      <input class="input" id="regPhone" name="phone" type="tel" inputmode="tel" placeholder="০১৭xxxxxxxx" autocomplete="tel" required data-rule="phone" value="<?= e($_POST['phone'] ?? '') ?>">
      <span class="field-hint">১১ সংখ্যার বাংলাদেশি মোবাইল নম্বর।</span>
    </div>

    <div class="field">
      <label class="field-label" for="regEmail">ইমেইল ঠিকানা <span class="req">*</span></label>
      <input class="input" id="regEmail" name="email" type="email" placeholder="আপনার ইমেইল লিখুন" autocomplete="email" required value="<?= e($_POST['email'] ?? '') ?>">
      <span class="field-hint">গুরুত্বপূর্ণ হালনাগাদ এখানে পাঠানো হবে।</span>
    </div>

    <div class="field span-2">
      <label class="field-label" for="regArea">আপনার এলাকা <span class="req">*</span></label>
      <select class="select" id="regArea" name="area" required>
        <option value="">এলাকা নির্বাচন করুন</option>
        <option>মিরপুর ১০, ঢাকা</option>
        <option>শাহবাগ, ঢাকা</option>
        <option>ধানমন্ডি, ঢাকা</option>
        <option>মোহাম্মদপুর, ঢাকা</option>
        <option>কাজীপাড়া, ঢাকা</option>
        <option>উত্তরা, ঢাকা</option>
        <option>বনানী, ঢাকা</option>
        <option>রামপুরা, ঢাকা</option>
        <option>গুলশান, ঢাকা</option>
        <option>সায়েদাবাদ, ঢাকা</option>
        <option>বাড্ডা, ঢাকা</option>
      </select>
      <span class="field-hint">এলাকা অনুযায়ী আপনার আশপাশের রিপোর্ট দেখানো হবে।</span>
    </div>

    <div class="field">
      <label class="field-label" for="regPassword">পাসওয়ার্ড <span class="req">*</span></label>
      <div class="input-group">
        <input class="input has-toggle" id="regPassword" name="password" type="password" placeholder="কমপক্ষে ৬ অক্ষর" autocomplete="new-password" required minlength="6">
        <button class="password-toggle" type="button" data-password-toggle="regPassword" aria-label="পাসওয়ার্ড দেখুন" aria-pressed="false">
          <span class="eye-open"><?= icon('eye', 'icon-sm') ?></span>
          <span class="eye-off" style="display:none"><?= icon('eye-off', 'icon-sm') ?></span>
        </button>
      </div>
      <span class="field-hint">অক্ষর, সংখ্যা ও বিশেষ চিহ্ন মিশিয়ে শক্তিশালী পাসওয়ার্ড দিন।</span>
    </div>

    <div class="field">
      <label class="field-label" for="regConfirm">পাসওয়ার্ড নিশ্চিত করুন <span class="req">*</span></label>
      <input class="input" id="regConfirm" name="confirm" type="password" placeholder="পাসওয়ার্ডটি আবার লিখুন" autocomplete="new-password" required minlength="6" data-rule="match" data-match-target="#regPassword">
      <span class="field-hint">দুই জায়গায় একই পাসওয়ার্ড লিখুন।</span>
    </div>

    <div class="field span-2" data-skip-validation>
      <label class="checkbox">
        <input type="checkbox" name="terms" required>
        <span>আমি <a href="terms.php">ব্যবহারের শর্তাবলি</a> ও <a href="privacy.php">গোপনীয়তা নীতি</a> পড়েছি এবং সম্মত আছি।</span>
      </label>
    </div>
  </div>

  <button class="btn btn-primary btn-lg btn-block" type="submit"><?= icon('user-plus', 'icon-sm') ?> অ্যাকাউন্ট তৈরি করুন</button>
</form>

<div class="auth-demo">
  <strong>নোট:</strong> নিবন্ধনের পর আপনার নাম, মোবাইল, ইমেইল ও এলাকা MySQL ডেটাবেজে সংরক্ষিত হবে এবং আপনি সরাসরি ড্যাশবোর্ডে প্রবেশ করবেন।
</div>
<p class="auth-security"><?= icon('shield', 'icon-sm') ?> নাগরিকদের তথ্য সুরক্ষা আমাদের সর্বোচ্চ অগ্রাধিকার</p>
<?php include __DIR__ . '/includes/auth-footer.php'; ?>
