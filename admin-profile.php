<?php
require_once __DIR__ . '/includes/bootstrap.php';

$adminPage = 'admin-profile.php';
$adminTitle = 'অ্যাডমিন প্রোফাইল';
$formError = '';

$admin = current_admin();
$activeTab = ($_POST['action'] ?? '') === 'password' ? 'security' : 'info';
$profile = array_merge([
    'phone' => '০১৭১১-২২৩৩৪৪',
    'office' => 'নগর ভবন, শাহবাগ, ঢাকা',
    'area' => 'ঢাকা মহানগর — অঞ্চল ৩',
    'bio' => 'নাগরিক রিপোর্ট যাচাই, কর্তৃপক্ষের সঙ্গে সমন্বয় এবং সমাধান পর্যবেক্ষণের দায়িত্বে নিয়োজিত।',
    'joined' => '১ মার্চ ২০২২',
    'last_login' => 'আজ, সকাল ৯:১৪',
    'timezone' => 'এশিয়া/ঢাকা (UTC+৬)',
], $_SESSION['admin_profile'] ?? []);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'profile') {
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $role = trim((string) ($_POST['role'] ?? ''));
        $office = trim((string) ($_POST['office'] ?? ''));
        $area = trim((string) ($_POST['area'] ?? ''));
        $bio = trim((string) ($_POST['bio'] ?? ''));

        if ($name === '' || $email === '' || $phone === '') {
            $formError = 'পূর্ণ নাম, ইমেইল ও মোবাইল নম্বর অবশ্যই পূরণ করতে হবে।';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $formError = 'সঠিক ইমেইল ঠিকানা লিখুন।';
        } else {
            $parts = preg_split('/\s+/u', $name, -1, PREG_SPLIT_NO_EMPTY) ?: [$name];
            $first = count($parts) >= 3 ? $parts[count($parts) - 2] : $parts[0];

            $_SESSION['admin'] = array_merge($admin, [
                'name' => $name,
                'first' => $first,
                'initials' => mb_substr($first, 0, 1),
                'role' => $role !== '' ? $role : $admin['role'],
                'email' => $email,
            ]);
            $_SESSION['admin_profile'] = array_merge($profile, [
                'phone' => $phone,
                'office' => $office,
                'area' => $area,
                'bio' => $bio,
            ]);

            flash_set('success', 'আপনার প্রোফাইল তথ্য সফলভাবে হালনাগাদ করা হয়েছে।');
            header('Location: admin-profile.php');
            exit;
        }
    } elseif ($action === 'password') {
        $currentPassword = (string) ($_POST['current_password'] ?? '');
        $newPassword = (string) ($_POST['new_password'] ?? '');
        $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

        if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
            $formError = 'পাসওয়ার্ড পরিবর্তনের জন্য সব ঘর পূরণ করুন।';
        } elseif (mb_strlen($newPassword) < 8) {
            $formError = 'নতুন পাসওয়ার্ড কমপক্ষে ৮ অক্ষরের হতে হবে।';
        } elseif ($newPassword !== $confirmPassword) {
            $formError = 'নতুন পাসওয়ার্ড ও নিশ্চিতকরণ পাসওয়ার্ড মিলছে না।';
        } elseif ($newPassword === $currentPassword) {
            $formError = 'নতুন পাসওয়ার্ড বর্তমান পাসওয়ার্ডের চেয়ে ভিন্ন হতে হবে।';
        } else {
            flash_set('success', 'পাসওয়ার্ড সফলভাবে পরিবর্তন করা হয়েছে। এটি একটি ডেমো—বাস্তব পাসওয়ার্ড সংরক্ষিত হয় না।');
            header('Location: admin-profile.php');
            exit;
        }
    }
}

include __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-page-head">
  <div>
    <div class="admin-breadcrumb-lite"><a href="admin.php">প্রশাসনিক প্যানেল</a><?= icon('chevron-right') ?><span>অ্যাডমিন প্রোফাইল</span></div>
    <h2>আমার প্রশাসনিক প্রোফাইল</h2>
    <p>ব্যক্তিগত তথ্য, যোগাযোগের বিবরণ, নিরাপত্তা এবং অ্যাকাউন্টের পছন্দসমূহ পরিচালনা করুন।</p>
  </div>
  <div class="admin-page-head-actions">
    <a class="btn btn-outline btn-sm" href="admin-settings.php"><?= icon('settings', 'icon-sm') ?> সিস্টেম সেটিংস</a>
    <a class="btn btn-outline btn-sm" href="admin.php"><?= icon('grid', 'icon-sm') ?> ড্যাশবোর্ড</a>
  </div>
</div>

<?php if ($formError !== ''): ?>
  <div class="alert alert-danger" role="alert" style="margin-bottom:16px"><?= icon('alert-circle', 'icon-sm') ?><div><?= e($formError) ?></div><button class="alert-close" type="button" aria-label="বার্তা বন্ধ করুন"><?= icon('x', 'icon-xs') ?></button></div>
<?php endif; ?>

<div class="profile-layout">
  <aside class="profile-side">
    <div class="profile-avatar-wrap">
      <?= avatar($admin['initials'], 'green', 'lg') ?>
      <button class="avatar-edit" type="button" data-avatar-edit aria-label="প্রোফাইল ছবি পরিবর্তন করুন" title="প্রোফাইল ছবি পরিবর্তন করুন"><?= icon('camera') ?></button>
    </div>
    <h2><?= e($admin['name']) ?></h2>
    <p><?= e($admin['role']) ?></p>
    <div class="profile-badges">
      <span class="badge badge-success"><?= icon('shield', 'icon-xs') ?> সুপার অ্যাডমিন</span>
      <span class="badge badge-soft">দুই-ধাপ সুরক্ষা</span>
    </div>

    <div class="profile-mini-stats">
      <div><strong><?= bn('১,২৪৮') ?></strong><span>যাচাইকৃত রিপোর্ট</span></div>
      <div><strong><?= bn('৯৬') ?>%</strong><span>সমাধান হার</span></div>
      <div><strong><?= bn('৩') ?> বছর</strong><span>সেবাকাল</span></div>
      <div><strong><?= bn('৪.৮') ?></strong><span>সন্তুষ্টি রেটিং</span></div>
    </div>

    <div class="admin-kv" style="margin-bottom:14px">
      <div><span>অ্যাডমিন আইডি</span><strong>NB-ADM-001</strong></div>
      <div><span>সর্বশেষ প্রবেশ</span><strong><?= e($profile['last_login']) ?></strong></div>
      <div><span>অফিস</span><strong><?= e($profile['office']) ?></strong></div>
      <div><span>এলাকা</span><strong><?= e($profile['area']) ?></strong></div>
    </div>

    <div class="profile-menu">
      <a href="admin-profile.php" class="active"><?= icon('user') ?> প্রোফাইল তথ্য</a>
      <a href="admin-settings.php"><?= icon('settings') ?> সিস্টেম সেটিংস</a>
      <a href="admin-notifications.php"><?= icon('bell') ?> নোটিফিকেশন</a>
      <a href="admin-issues.php"><?= icon('file-text') ?> আমার রিপোর্ট</a>
      <a href="logout.php"><?= icon('log-out') ?> প্রবেশ থেকে বের হন</a>
    </div>
  </aside>

  <div class="profile-content">
    <div class="profile-head-card">
      <div>
        <h1><?= e($admin['name']) ?></h1>
        <p><?= icon('mail', 'icon-xs') ?> <?= e($admin['email']) ?> &nbsp;·&nbsp; <?= icon('phone', 'icon-xs') ?> <?= e($profile['phone']) ?></p>
      </div>
      <span class="badge badge-success"><?= icon('check-circle', 'icon-xs') ?> যাচাইকৃত প্রশাসক</span>
    </div>

    <div class="panel" data-tabs>
      <div class="settings-nav" role="tablist" aria-label="প্রোফাইল বিভাগ">
        <button type="button" class="<?= $activeTab === 'info' ? 'active' : '' ?>" data-tab="info" role="tab" aria-selected="<?= $activeTab === 'info' ? 'true' : 'false' ?>"><?= icon('user', 'icon-sm') ?> ব্যক্তিগত তথ্য</button>
        <button type="button" data-tab="contact" role="tab" aria-selected="false"><?= icon('map-pin', 'icon-sm') ?> যোগাযোগ</button>
        <button type="button" class="<?= $activeTab === 'security' ? 'active' : '' ?>" data-tab="security" role="tab" aria-selected="<?= $activeTab === 'security' ? 'true' : 'false' ?>"><?= icon('lock', 'icon-sm') ?> নিরাপত্তা</button>
        <button type="button" data-tab="prefs" role="tab" aria-selected="false"><?= icon('bell', 'icon-sm') ?> পছন্দসমূহ</button>
      </div>

      <div class="panel-body" data-tab-panel="info" <?= $activeTab === 'info' ? '' : 'hidden' ?>>
        <form class="profile-form admin-form" method="post" action="admin-profile.php" data-validate novalidate>
          <input type="hidden" name="action" value="profile">
          <div class="form-grid">
            <div class="field">
              <label class="field-label" for="adminName">পূর্ণ নাম <span class="req">*</span></label>
              <input class="input" id="adminName" name="name" type="text" value="<?= e($admin['name']) ?>" required minlength="3">
              <span class="field-error"><?= icon('alert-circle', 'icon-xs') ?><span>পূর্ণ নাম লিখুন।</span></span>
            </div>
            <div class="field">
              <label class="field-label" for="adminRole">পদবি / ভূমিকা</label>
              <input class="input" id="adminRole" name="role" type="text" value="<?= e($admin['role']) ?>">
              <span class="field-hint">এই পদবিটি প্রশাসনিক প্যানেলে প্রদর্শিত হবে।</span>
            </div>
            <div class="field">
              <label class="field-label" for="adminEmail">ইমেইল ঠিকানা <span class="req">*</span></label>
              <input class="input" id="adminEmail" name="email" type="email" value="<?= e($admin['email']) ?>" required>
              <span class="field-error"><?= icon('alert-circle', 'icon-xs') ?><span>সঠিক ইমেইল ঠিকানা লিখুন।</span></span>
            </div>
            <div class="field">
              <label class="field-label" for="adminPhone">মোবাইল নম্বর <span class="req">*</span></label>
              <input class="input" id="adminPhone" name="phone" type="tel" value="<?= e($profile['phone']) ?>" required>
              <span class="field-hint">জরুরি যোগাযোগ ও দুই-ধাপ যাচাইয়ের জন্য ব্যবহৃত হবে।</span>
            </div>
            <div class="field">
              <label class="field-label" for="adminOffice">অফিস / শাখা</label>
              <input class="input" id="adminOffice" name="office" type="text" value="<?= e($profile['office']) ?>">
            </div>
            <div class="field">
              <label class="field-label" for="adminArea">দায়িত্বের এলাকা</label>
              <input class="input" id="adminArea" name="area" type="text" value="<?= e($profile['area']) ?>">
            </div>
            <div class="field span-2">
              <label class="field-label" for="adminBio">সংক্ষিপ্ত পরিচিতি</label>
              <textarea class="textarea" id="adminBio" name="bio" rows="3" maxlength="240"><?= e($profile['bio']) ?></textarea>
              <span class="field-hint">সর্বোচ্চ ২৪০ অক্ষর। এটি প্রশাসনিক অ্যাক্সেস তালিকায় দেখা যাবে।</span>
            </div>
          </div>
          <div class="form-actions-bar">
            <span class="left muted-note"><?= icon('calendar', 'icon-xs') ?> যোগদান: <?= e($profile['joined']) ?></span>
            <button class="btn btn-outline" type="reset">পরিবর্তন বাতিল</button>
            <button class="btn btn-primary" type="submit"><?= icon('save', 'icon-sm') ?> সংরক্ষণ করুন</button>
          </div>
        </form>
      </div>

      <div class="panel-body" data-tab-panel="contact" hidden>
        <form class="profile-form admin-form" data-demo-submit data-success-message="যোগাযোগের তথ্য সফলভাবে সংরক্ষণ করা হয়েছে।">
          <div class="info-strip"><?= icon('info', 'icon-sm') ?><span>এই তথ্যগুলো কর্তৃপক্ষ ও নাগরিকদের সঙ্গে যোগাযোগের সময় ব্যবহার করা হবে। ব্যক্তিগত মোবাইল নম্বর প্রকাশ্যে দেখানো হবে না।</span></div>
          <div class="form-grid">
            <div class="field span-2">
              <label class="field-label" for="adminAddress">কার্যালয়ের ঠিকানা</label>
              <textarea class="textarea" id="adminAddress" rows="2">নগর ভবন, ৩৫ নম্বর সড়ক, শাহবাগ, ঢাকা ১০০০</textarea>
            </div>
            <div class="field">
              <label class="field-label" for="adminOfficePhone">অফিস ফোন</label>
              <input class="input" id="adminOfficePhone" type="tel" value="০২-৫৫৬৬৭৭৮৮">
            </div>
            <div class="field">
              <label class="field-label" for="adminHours">অফিস সময়</label>
              <input class="input" id="adminHours" type="text" value="শনি–বৃহস্পতি, সকাল ৯টা–সন্ধ্যা ৬টা">
            </div>
            <div class="field">
              <label class="field-label" for="adminWebsite">অফিসিয়াল ওয়েবসাইট</label>
              <input class="input" id="adminWebsite" type="url" value="https://nagorbondhu.gov.bd">
            </div>
            <div class="field">
              <label class="field-label" for="adminTimezone">টাইমজোন</label>
              <select class="select" id="adminTimezone">
                <option selected>এশিয়া/ঢাকা (UTC+৬)</option>
                <option>এশিয়া/কলকাতা (UTC+৫:৩০)</option>
                <option>এশিয়া/দুবাই (UTC+৪)</option>
              </select>
            </div>
          </div>
          <div class="toggle-row"><div><strong>পাবলিক প্রোফাইলে দেখান</strong><p>নাম, পদবি ও অফিসের ঠিকানা নাগরিকদের জন্য প্রকাশ্য থাকবে।</p></div><label class="switch"><input type="checkbox" checked><span class="switch-track"></span></label></div>
          <div class="toggle-row"><div><strong>ফোন নম্বর প্রকাশ</strong><p>শুধুমাত্র অফিস ফোন নম্বর প্রকাশ্যে দেখানো হবে।</p></div><label class="switch"><input type="checkbox" checked><span class="switch-track"></span></label></div>
          <div class="form-actions-bar">
            <button class="btn btn-primary" type="submit"><?= icon('save', 'icon-sm') ?> যোগাযোগ সংরক্ষণ</button>
          </div>
        </form>
      </div>

      <div class="panel-body" data-tab-panel="security" <?= $activeTab === 'security' ? '' : 'hidden' ?>>
        <form class="profile-form admin-form" method="post" action="admin-profile.php" data-validate novalidate>
          <input type="hidden" name="action" value="password">
          <div class="info-strip"><?= icon('shield', 'icon-sm') ?><span>শক্তিশালী পাসওয়ার্ডে অন্তত ৮ অক্ষর, বড়-ছোট হাতের অক্ষর, সংখ্যা ও বিশেষ চিহ্ন রাখুন। নিয়মিত পাসওয়ার্ড পরিবর্তন করুন।</span></div>
          <div class="form-grid">
            <div class="field span-2">
              <label class="field-label" for="currentPassword">বর্তমান পাসওয়ার্ড <span class="req">*</span></label>
              <div class="input-group">
                <span class="input-icon"><?= icon('lock', 'icon-sm') ?></span>
                <input class="input has-icon has-toggle" id="currentPassword" name="current_password" type="password" placeholder="বর্তমান পাসওয়ার্ড লিখুন" autocomplete="current-password" required>
                <button class="password-toggle" type="button" data-password-toggle="currentPassword" aria-label="পাসওয়ার্ড দেখুন" aria-pressed="false">
                  <span class="eye-open"><?= icon('eye', 'icon-sm') ?></span>
                  <span class="eye-off" style="display:none"><?= icon('eye-off', 'icon-sm') ?></span>
                </button>
              </div>
            </div>
            <div class="field">
              <label class="field-label" for="newPassword">নতুন পাসওয়ার্ড <span class="req">*</span></label>
              <div class="input-group">
                <span class="input-icon"><?= icon('lock', 'icon-sm') ?></span>
                <input class="input has-icon has-toggle" id="newPassword" name="new_password" type="password" placeholder="নতুন পাসওয়ার্ড" autocomplete="new-password" minlength="8" required>
                <button class="password-toggle" type="button" data-password-toggle="newPassword" aria-label="পাসওয়ার্ড দেখুন" aria-pressed="false">
                  <span class="eye-open"><?= icon('eye', 'icon-sm') ?></span>
                  <span class="eye-off" style="display:none"><?= icon('eye-off', 'icon-sm') ?></span>
                </button>
              </div>
              <span class="field-hint">কমপক্ষে ৮ অক্ষর।</span>
            </div>
            <div class="field">
              <label class="field-label" for="confirmPassword">নতুন পাসওয়ার্ড নিশ্চিত করুন <span class="req">*</span></label>
              <div class="input-group">
                <span class="input-icon"><?= icon('lock', 'icon-sm') ?></span>
                <input class="input has-icon has-toggle" id="confirmPassword" name="confirm_password" type="password" placeholder="আবার লিখুন" autocomplete="new-password" minlength="8" required>
                <button class="password-toggle" type="button" data-password-toggle="confirmPassword" aria-label="পাসওয়ার্ড দেখুন" aria-pressed="false">
                  <span class="eye-open"><?= icon('eye', 'icon-sm') ?></span>
                  <span class="eye-off" style="display:none"><?= icon('eye-off', 'icon-sm') ?></span>
                </button>
              </div>
            </div>
          </div>
          <div class="form-actions-bar">
            <button class="btn btn-primary" type="submit"><?= icon('lock', 'icon-sm') ?> পাসওয়ার্ড পরিবর্তন</button>
          </div>
        </form>

        <div class="admin-form-section" style="margin-top:16px">
          <h3><?= icon('shield', 'icon-sm') ?> দুই-ধাপ যাচাই</h3>
          <div class="toggle-row"><div><strong>ওটিপি ভিত্তিক যাচাই</strong><p>প্রতিবার প্রবেশে মোবাইল নম্বরে পাঠানো ৬ সংখ্যার কোড যাচাই করুন।</p></div><label class="switch"><input type="checkbox" checked><span class="switch-track"></span></label></div>
          <div class="toggle-row"><div><strong>অজানা ডিভাইস থেকে প্রবেশে সতর্কতা</strong><p>নতুন ডিভাইস শনাক্ত হলে ইমেইলে সতর্কবার্তা পাঠান।</p></div><label class="switch"><input type="checkbox" checked><span class="switch-track"></span></label></div>
        </div>

        <div class="admin-panel" style="margin-top:16px">
          <div class="admin-panel-head"><div><h3>সক্রিয় সেশন</h3><p>বর্তমানে প্রবেশকৃত ডিভাইসের তালিকা</p></div><button class="btn btn-outline btn-sm" type="button" data-admin-action="return"><?= icon('refresh', 'icon-sm') ?> সব সেশন বন্ধ</button></div>
          <div class="table-scroll">
            <table class="data-table admin-table">
              <thead><tr><th>ডিভাইস</th><th>অবস্থান</th><th>সর্বশেষ সক্রিয়</th><th>অবস্থা</th><th class="cell-actions">কার্যক্রম</th></tr></thead>
              <tbody>
                <tr><td><div class="cell-person"><?= avatar('ও', 'blue', 'sm') ?><div><strong>Windows · Chrome</strong><span>১৯২.১৬৮.১.১২</span></div></div></td><td>ঢাকা, বাংলাদেশ</td><td class="table-sub">এখন সক্রিয়</td><td><span class="badge badge-success"><span class="status-dot"></span>বর্তমান</span></td><td class="cell-actions"><span class="table-sub">এই সেশন</span></td></tr>
                <tr><td><div class="cell-person"><?= avatar('অ', 'green', 'sm') ?><div><strong>Android · নগরবন্ধু অ্যাপ</strong><span>১০৩.২২.৪৫.৯০</span></div></div></td><td>ঢাকা, বাংলাদেশ</td><td class="table-sub">২ ঘণ্টা আগে</td><td><span class="badge badge-soft"><span class="status-dot"></span>সক্রিয়</span></td><td class="cell-actions"><button class="btn btn-sm btn-outline" type="button" data-admin-action="return">বন্ধ করুন</button></td></tr>
                <tr><td><div class="cell-person"><?= avatar('মা', 'violet', 'sm') ?><div><strong>macOS · Safari</strong><span>২০৩.৮৮.১২৭.৪</span></div></div></td><td>চট্টগ্রাম, বাংলাদেশ</td><td class="table-sub">গতকাল, বিকাল ৪:১০</td><td><span class="badge badge-soft"><span class="status-dot"></span>সক্রিয়</span></td><td class="cell-actions"><button class="btn btn-sm btn-outline" type="button" data-admin-action="return">বন্ধ করুন</button></td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="panel-body" data-tab-panel="prefs" hidden>
        <form class="profile-form admin-form" data-demo-submit data-success-message="আপনার পছন্দসমূহ সংরক্ষণ করা হয়েছে।">
          <div class="toggle-row"><div><strong>নতুন রিপোর্টের নোটিফিকেশন</strong><p>আপনার এলাকায় নতুন রিপোর্ট জমা হলে তাৎক্ষণিক বার্তা পান।</p></div><label class="switch"><input type="checkbox" checked><span class="switch-track"></span></label></div>
          <div class="toggle-row"><div><strong>মন্তব্য পর্যালোচনার সতর্কতা</strong><p>কোনো মন্তব্য পর্যালোচনার জন্য চিহ্নিত হলে জানানো হবে।</p></div><label class="switch"><input type="checkbox" checked><span class="switch-track"></span></label></div>
          <div class="toggle-row"><div><strong>অনুদান যাচাইয়ের অনুরোধ</strong><p>নতুন অনুদান যাচাইয়ের অপেক্ষায় থাকলে ইমেইল পান।</p></div><label class="switch"><input type="checkbox" checked><span class="switch-track"></span></label></div>
          <div class="toggle-row"><div><strong>দৈনিক সারসংক্ষেপ</strong><p>প্রতিদিন সকাল ৮টায় রিপোর্ট ও কার্যক্রমের সারসংক্ষেপ পান।</p></div><label class="switch"><input type="checkbox"><span class="switch-track"></span></label></div>
          <div class="form-grid">
            <div class="field">
              <label class="field-label" for="adminLanguage">প্যানেলের ভাষা</label>
              <select class="select" id="adminLanguage"><option selected>বাংলা</option><option>বাংলা ও ইংরেজি</option><option>English</option></select>
            </div>
            <div class="field">
              <label class="field-label" for="adminRows">প্রতি পেজে সারির সংখ্যা</label>
              <select class="select" id="adminRows"><option>১০</option><option selected>২৫</option><option>৫০</option><option>১০০</option></select>
            </div>
            <div class="field">
              <label class="field-label" for="adminLanding">প্রবেশের পর ডিফল্ট পাতা</label>
              <select class="select" id="adminLanding"><option selected>ড্যাশবোর্ড</option><option>সমস্যা ব্যবস্থাপনা</option><option>রিপোর্ট যাচাই</option></select>
            </div>
            <div class="field">
              <label class="field-label" for="adminDensity">তালিকার ঘনত্ব</label>
              <select class="select" id="adminDensity"><option selected>স্বাভাবিক</option><option>সংক্ষিপ্ত</option><option>প্রশস্ত</option></select>
            </div>
          </div>
          <div class="form-actions-bar">
            <button class="btn btn-primary" type="submit"><?= icon('save', 'icon-sm') ?> পছন্দ সংরক্ষণ</button>
          </div>
        </form>
      </div>
    </div>

    <div class="admin-grid cols-2">
      <section class="admin-panel">
        <div class="admin-panel-head"><div><h3>অ্যাক্সেস ও অনুমতি</h3><p>আপনার ভূমিকা অনুযায়ী অনুমোদিত কার্যক্রম</p></div><span class="badge badge-soft">সুপার অ্যাডমিন</span></div>
        <div class="table-scroll">
          <table class="data-table admin-table" style="min-width:0">
            <thead><tr><th>কার্যক্রম</th><th>অনুমতি</th></tr></thead>
            <tbody>
              <tr><td>রিপোর্ট যাচাই ও অনুমোদন</td><td><span class="badge badge-success"><?= icon('check', 'icon-xs') ?> পূর্ণ</span></td></tr>
              <tr><td>কর্তৃপক্ষকে কাজ বরাদ্দ</td><td><span class="badge badge-success"><?= icon('check', 'icon-xs') ?> পূর্ণ</span></td></tr>
              <tr><td>অনুদান যাচাই ও নিষ্পত্তি</td><td><span class="badge badge-success"><?= icon('check', 'icon-xs') ?> পূর্ণ</span></td></tr>
              <tr><td>নাগরিক অ্যাকাউন্ট স্থগিত</td><td><span class="badge badge-success"><?= icon('check', 'icon-xs') ?> পূর্ণ</span></td></tr>
              <tr><td>সিস্টেম সেটিংস পরিবর্তন</td><td><span class="badge badge-warning">সীমিত</span></td></tr>
              <tr><td>নতুন অ্যাডমিন নিয়োগ</td><td><span class="badge badge-soft">শুধু অনুমোদনসহ</span></td></tr>
            </tbody>
          </table>
        </div>
      </section>

      <section class="admin-panel">
        <div class="admin-panel-head"><div><h3>সাম্প্রতিক কার্যক্রম</h3><p>আপনার সর্বশেষ প্রশাসনিক পদক্ষেপ</p></div><a href="admin-reports.php" class="text-link">বিস্তারিত</a></div>
        <div class="activity-list">
          <div class="activity-item">
            <span class="activity-icon green"><?= icon('check-square', 'icon-sm') ?></span>
            <div class="activity-text"><p><strong>রিপোর্ট যাচাই</strong> — NB-২০২৬-০০৭৮ অনুমোদিত হয়েছে</p><span>ধানমন্ডি ২৭, ঢাকা</span></div>
            <span class="activity-time">১০ মিনিট আগে</span>
          </div>
          <div class="activity-item">
            <span class="activity-icon blue"><?= icon('landmark', 'icon-sm') ?></span>
            <div class="activity-text"><p><strong>কাজ বরাদ্দ</strong> — সড়ক মেরামতের দায়িত্ব সড়ক বিভাগকে দেওয়া হয়েছে</p><span>মিরপুর ১০, ঢাকা</span></div>
            <span class="activity-time">১ ঘণ্টা আগে</span>
          </div>
          <div class="activity-item">
            <span class="activity-icon amber"><?= icon('wallet', 'icon-sm') ?></span>
            <div class="activity-text"><p><strong>অনুদান যাচাই</strong> — ৳ ১০,০০০ অনুদান নিশ্চিত করা হয়েছে</p><span>বিকাশ · TXN-৮৪২৯১</span></div>
            <span class="activity-time">২ ঘণ্টা আগে</span>
          </div>
          <div class="activity-item">
            <span class="activity-icon green"><?= icon('settings', 'icon-sm') ?></span>
            <div class="activity-text"><p><strong>সেটিংস হালনাগাদ</strong> — যাচাইয়ের সময়সীমা ৩ দিন নির্ধারণ</p><span>সিস্টেম সেটিংস</span></div>
            <span class="activity-time">গতকাল</span>
          </div>
        </div>
      </section>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
