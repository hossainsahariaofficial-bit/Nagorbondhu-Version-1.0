<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_citizen();
$pageTitle = 'আমার প্রোফাইল — নগরবন্ধু';
$dashPage = 'profile.php';
$dashTitle = 'আমার প্রোফাইল';
$citizen = current_citizen();
$userId = (int) $citizen['id'];
$profileStats = user_stats($userId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');

    if (!csrf_valid()) {
        flash_set('error', 'নিরাপত্তা যাচাই ব্যর্থ হয়েছে। আবার চেষ্টা করুন।');
    } elseif (!db_ok()) {
        flash_set('error', db_unavailable_message());
    } else {
        $user = db_one('SELECT * FROM users WHERE id = ?', [$userId]);

        if (!$user) {
            flash_set('error', 'আপনার অ্যাকাউন্টটি খুঁজে পাওয়া যায়নি। আবার প্রবেশ করুন।');
        } elseif ($action === 'profile') {
            $name = trim((string) ($_POST['name'] ?? ''));
            $phone = trim((string) ($_POST['phone'] ?? ''));
            $email = trim((string) ($_POST['email'] ?? ''));

            if (mb_strlen($name) < 3) {
                flash_set('error', 'পূর্ণ নাম কমপক্ষে ৩ অক্ষরের হতে হবে।');
            } elseif (!valid_bd_phone($phone)) {
                flash_set('error', 'সঠিক ১১ সংখ্যার মোবাইল নম্বর লিখুন।');
            } elseif ($email !== '' && !valid_email($email)) {
                flash_set('error', 'সঠিক ইমেইল ঠিকানা লিখুন।');
            } else {
                $phone = normalize_phone($phone);
                $email = $email !== '' ? mb_strtolower($email) : (string) $user['email'];
                $duplicate = (int) db_val(
                    'SELECT COUNT(*) FROM users WHERE id <> ? AND (phone = ? OR email = ?)',
                    [$userId, $phone, $email]
                );
                if ($duplicate > 0) {
                    flash_set('error', 'এই মোবাইল নম্বর বা ইমেইল অন্য অ্যাকাউন্টে ব্যবহৃত হচ্ছে।');
                } else {
                    $updated = db_exec(
                        'UPDATE users SET name = ?, phone = ?, email = ? WHERE id = ?',
                        [$name, $phone, $email, $userId]
                    );
                    if ($updated < 0) {
                        flash_set('error', 'তথ্য সংরক্ষণ করা যায়নি। আবার চেষ্টা করুন।');
                    } else {
                        $fresh = db_one('SELECT * FROM users WHERE id = ?', [$userId]);
                        if ($fresh) {
                            $_SESSION['citizen'] = user_session_payload($fresh);
                        }
                        flash_set('success', 'আপনার ব্যক্তিগত তথ্য সফলভাবে সংরক্ষণ করা হয়েছে।');
                    }
                }
            }
        } elseif ($action === 'address') {
            $area = trim((string) ($_POST['area'] ?? ''));
            $district = trim((string) ($_POST['district'] ?? ''));
            if ($area === '') {
                flash_set('error', 'এলাকা / ওয়ার্ড লিখুন।');
            } else {
                $areaValue = $area . ($district !== '' ? ', ' . $district : '');
                $updated = db_exec('UPDATE users SET area = ? WHERE id = ?', [$areaValue, $userId]);
                if ($updated < 0) {
                    flash_set('error', 'ঠিকানার তথ্য সংরক্ষণ করা যায়নি। আবার চেষ্টা করুন।');
                } else {
                    $fresh = db_one('SELECT * FROM users WHERE id = ?', [$userId]);
                    if ($fresh) {
                        $_SESSION['citizen'] = user_session_payload($fresh);
                    }
                    flash_set('success', 'আপনার ঠিকানা ও এলাকার তথ্য সংরক্ষণ করা হয়েছে।');
                }
            }
        } elseif ($action === 'password') {
            $current = (string) ($_POST['current_password'] ?? '');
            $new = (string) ($_POST['new_password'] ?? '');
            $confirm = (string) ($_POST['confirm_password'] ?? '');

            if (!password_verify($current, (string) $user['password_hash'])) {
                flash_set('error', 'বর্তমান পাসওয়ার্ড সঠিক নয়।');
            } elseif (mb_strlen($new) < 8) {
                flash_set('error', 'নতুন পাসওয়ার্ড কমপক্ষে ৮ অক্ষরের হতে হবে।');
            } elseif ($new !== $confirm) {
                flash_set('error', 'নতুন পাসওয়ার্ড দুটি মিলছে না।');
            } else {
                $updated = db_exec(
                    'UPDATE users SET password_hash = ? WHERE id = ?',
                    [password_hash($new, PASSWORD_DEFAULT), $userId]
                );
                if ($updated < 0) {
                    flash_set('error', 'পাসওয়ার্ড পরিবর্তন করা যায়নি। আবার চেষ্টা করুন।');
                } else {
                    flash_set('success', 'আপনার পাসওয়ার্ড সফলভাবে পরিবর্তন করা হয়েছে।');
                }
            }
        }
    }

    header('Location: profile.php');
    exit;
}

include __DIR__ . '/includes/header.php';
?>
<div class="container">
  <?php include __DIR__ . '/includes/dashboard-sidebar.php'; ?>

  <div class="dash-welcome">
    <div>
      <span class="dash-date"><?= icon('user', 'icon-xs') ?> সদস্য হয়েছেন <?= e($citizen['joined']) ?></span>
      <h1>আমার প্রোফাইল</h1>
      <p>আপনার ব্যক্তিগত তথ্য, যোগাযোগের ঠিকানা ও অ্যাকাউন্টের পছন্দসমূহ হালনাগাদ করুন।</p>
    </div>
    <span class="badge badge-success" style="align-self:center"><?= icon('check-circle', 'icon-xs') ?> যাচাইকৃত নাগরিক</span>
  </div>

  <div class="panel" style="margin-bottom:20px">
    <div class="panel-body">
      <div class="profile-head-card" style="border:0;padding:0">
        <div class="reporter-card">
          <?= avatar($citizen['initials'], $citizen['color'], 'lg') ?>
          <div>
            <h2 style="font-size:1.1rem;margin-bottom:2px"><?= e($citizen['name']) ?></h2>
            <span><?= icon('map-pin', 'icon-xs') ?> <?= e($citizen['area']) ?> • যোগদান <?= e($citizen['joined']) ?></span>
            <div class="badge-row" style="margin-top:7px">
              <span class="badge badge-success">সক্রিয় নাগরিক</span>
              <span class="badge badge-soft">লেভেল ৩</span>
              <span class="badge badge-info"><?= bn($profileStats['reports']) ?>টি রিপোর্ট</span>
            </div>
          </div>
        </div>
        <a class="btn btn-outline btn-sm" href="my-reports.php"><?= icon('file-text', 'icon-sm') ?> আমার রিপোর্ট</a>
      </div>
    </div>
  </div>

  <div class="panel" data-tabs>
    <div class="settings-nav" role="tablist" aria-label="প্রোফাইল বিভাগ">
      <button type="button" class="active" data-tab="info" role="tab" aria-selected="true"><?= icon('user', 'icon-sm') ?> ব্যক্তিগত তথ্য</button>
      <button type="button" data-tab="contact" role="tab" aria-selected="false"><?= icon('map-pin', 'icon-sm') ?> ঠিকানা ও এলাকা</button>
      <button type="button" data-tab="security" role="tab" aria-selected="false"><?= icon('lock', 'icon-sm') ?> নিরাপত্তা</button>
      <button type="button" data-tab="prefs" role="tab" aria-selected="false"><?= icon('bell', 'icon-sm') ?> পছন্দসমূহ</button>
    </div>

    <div class="panel-body" data-tab-panel="info">
      <form class="profile-form" method="post" action="profile.php" data-validate>
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="profile">
        <div class="form-grid">
          <div class="field">
            <label class="field-label" for="profileName">পূর্ণ নাম <span class="req">*</span></label>
            <input class="input" id="profileName" name="name" type="text" value="<?= e($citizen['name']) ?>" required minlength="3">
            <span class="field-error"><?= icon('alert-circle', 'icon-xs') ?><span>পূর্ণ নাম লিখুন।</span></span>
          </div>
          <div class="field">
            <label class="field-label" for="profilePhone">মোবাইল নম্বর <span class="req">*</span></label>
            <input class="input" id="profilePhone" name="phone" type="tel" value="<?= e($citizen['phone']) ?>" data-rule="phone" required>
            <span class="field-hint">যাচাইকৃত নম্বর পরিবর্তন করলে পুনরায় যাচাই করতে হবে।</span>
            <span class="field-error"><?= icon('alert-circle', 'icon-xs') ?><span>সঠিক ১১ সংখ্যার নম্বর লিখুন।</span></span>
          </div>
          <div class="field">
            <label class="field-label" for="profileEmail">ইমেইল ঠিকানা</label>
            <input class="input" id="profileEmail" name="email" type="email" value="<?= e($citizen['email']) ?>">
            <span class="field-error"><?= icon('alert-circle', 'icon-xs') ?><span>সঠিক ইমেইল ঠিকানা লিখুন।</span></span>
          </div>
          <div class="field">
            <label class="field-label" for="profileDob">জন্মতারিখ</label>
            <input class="input" id="profileDob" name="dob" type="text" value="১৫ মার্চ ১৯৯৪" inputmode="numeric">
            <span class="field-hint">বয়স যাচাইয়ের জন্য শুধু মাস ও বছর ব্যবহার করা হয়।</span>
          </div>
          <div class="field span-2">
            <label class="field-label" for="profileBio">নিজের সম্পর্কে সংক্ষেপে</label>
            <textarea class="textarea" id="profileBio" name="bio" rows="3" maxlength="240">মিরপুর ১০ এলাকার বাসিন্দা। পাড়ার পরিষ্কার-পরিচ্ছন্নতা ও নিরাপদ চলাচলে আগ্রহী।</textarea>
            <span class="field-hint">সর্বোচ্চ ২৪০ অক্ষর। এটি আপনার প্রকাশ্য প্রোফাইলে দেখা যাবে।</span>
          </div>
        </div>
        <div class="form-actions-bar">
          <span class="left muted-note">সর্বশেষ হালনাগাদ: ২৮ অক্টোবর ২০২৬</span>
          <button class="btn btn-outline" type="reset">পরিবর্তন বাতিল</button>
          <button class="btn btn-primary" type="submit"><?= icon('save', 'icon-sm') ?> সংরক্ষণ করুন</button>
        </div>
      </form>
    </div>

    <div class="panel-body" data-tab-panel="contact" hidden>
      <form class="profile-form" method="post" action="profile.php" data-validate>
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="address">
        <div class="info-strip"><?= icon('info', 'icon-sm') ?><span>আপনার এলাকার তথ্য ব্যবহার করে কাছাকাছি সমস্যা ও কার্যক্রম দেখানো হবে। সঠিক এলাকা নির্বাচন করুন।</span></div>
        <div class="form-grid">
          <div class="field">
            <label class="field-label" for="profileDivision">বিভাগ <span class="req">*</span></label>
            <select class="select" id="profileDivision" name="division" required>
              <?php foreach (nb_divisions() as $division): ?>
                <option <?= $division === 'ঢাকা' ? 'selected' : '' ?>><?= e($division) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="field">
            <label class="field-label" for="profileDistrict">জেলা <span class="req">*</span></label>
            <select class="select" id="profileDistrict" name="district" required>
              <?php foreach (nb_districts() as $district): ?>
                <option <?= $district === 'ঢাকা' ? 'selected' : '' ?>><?= e($district) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="field">
            <label class="field-label" for="profileArea">এলাকা / ওয়ার্ড <span class="req">*</span></label>
            <input class="input" id="profileArea" name="area" type="text" value="মিরপুর ১০" required>
          </div>
          <div class="field">
            <label class="field-label" for="profilePostcode">পোস্ট কোড</label>
            <input class="input" id="profilePostcode" name="postcode" type="text" value="১২১৬" inputmode="numeric">
          </div>
          <div class="field span-2">
            <label class="field-label" for="profileAddress">বিস্তারিত ঠিকানা</label>
            <textarea class="textarea" id="profileAddress" name="address" rows="2">বাড়ি ১২, রোড ৭, পীরেরবাগ, মিরপুর ১০, ঢাকা</textarea>
            <span class="field-hint">এই ঠিকানা শুধু যাচাই ও জরুরি যোগাযোগে ব্যবহৃত হবে, প্রকাশ্যে দেখা যাবে না।</span>
          </div>
        </div>
        <div class="form-actions-bar">
          <button class="btn btn-outline" type="reset">পরিবর্তন বাতিল</button>
          <button class="btn btn-primary" type="submit"><?= icon('save', 'icon-sm') ?> ঠিকানা সংরক্ষণ</button>
        </div>
      </form>
    </div>

    <div class="panel-body" data-tab-panel="security" hidden>
      <div class="admin-note" style="margin-bottom:16px"><?= icon('shield', 'icon-sm') ?><span>নিরাপত্তার জন্য শক্তিশালী পাসওয়ার্ড ব্যবহার করুন এবং কারও সঙ্গে শেয়ার করবেন না। পাসওয়ার্ড ডেটাবেজে হ্যাশ হিসেবে সংরক্ষিত হয়।</span></div>
      <form class="profile-form" method="post" action="profile.php" data-validate>
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="password">
        <div class="form-grid">
          <div class="field span-2">
            <label class="field-label" for="currentPassword">বর্তমান পাসওয়ার্ড <span class="req">*</span></label>
            <div class="input-group">
              <input class="input" id="currentPassword" name="current_password" type="password" required minlength="6" autocomplete="current-password">
              <button class="password-toggle" type="button" data-password-toggle="currentPassword" aria-label="পাসওয়ার্ড দেখুন"><?= icon('eye', 'icon-sm eye-open') ?><?= icon('eye-off', 'icon-sm eye-off') ?></button>
            </div>
          </div>
          <div class="field">
            <label class="field-label" for="newPassword">নতুন পাসওয়ার্ড <span class="req">*</span></label>
            <div class="input-group">
              <input class="input" id="newPassword" name="new_password" type="password" required minlength="8" data-rule="minlength" data-minlength="8" autocomplete="new-password">
              <button class="password-toggle" type="button" data-password-toggle="newPassword" aria-label="পাসওয়ার্ড দেখুন"><?= icon('eye', 'icon-sm eye-open') ?><?= icon('eye-off', 'icon-sm eye-off') ?></button>
            </div>
            <span class="field-hint">কমপক্ষে ৮ অক্ষর, একটি সংখ্যা ও একটি বড় হাতের অক্ষর।</span>
          </div>
          <div class="field">
            <label class="field-label" for="confirmPassword">নতুন পাসওয়ার্ড নিশ্চিত করুন <span class="req">*</span></label>
            <div class="input-group">
              <input class="input" id="confirmPassword" name="confirm_password" type="password" required data-rule="match" data-match-target="#newPassword" autocomplete="new-password">
              <button class="password-toggle" type="button" data-password-toggle="confirmPassword" aria-label="পাসওয়ার্ড দেখুন"><?= icon('eye', 'icon-sm eye-open') ?><?= icon('eye-off', 'icon-sm eye-off') ?></button>
            </div>
            <span class="field-error"><?= icon('alert-circle', 'icon-xs') ?><span>পাসওয়ার্ড দুটি মিলছে না।</span></span>
          </div>
        </div>
        <div class="form-actions-bar">
          <button class="btn btn-primary" type="submit"><?= icon('lock', 'icon-sm') ?> পাসওয়ার্ড পরিবর্তন</button>
        </div>
      </form>

      <hr class="form-divider">

      <div class="row-between">
        <div>
          <strong style="display:block;font-size:.88rem">সক্রিয় সেশন</strong>
          <p class="muted-note">ঢাকা • ক্রোম ব্রাউজার • বর্তমান ডিভাইস</p>
        </div>
        <button class="btn btn-danger-outline btn-sm" type="button" data-confirm="অন্য সব ডিভাইস থেকে প্রবেশ বন্ধ করতে চান?" data-confirm-danger data-confirm-title="সেশন বন্ধ করুন" data-confirm-yes="হ্যাঁ, বন্ধ করুন"><?= icon('log-out', 'icon-sm') ?> অন্য সেশন বন্ধ করুন</button>
      </div>
    </div>

    <div class="panel-body" data-tab-panel="prefs" hidden>
      <div class="toggle-row">
        <div><strong>রিপোর্টের অবস্থা পরিবর্তনের নোটিফিকেশন</strong><p>আপনার জমা দেওয়া রিপোর্টের প্রতিটি ধাপে ইমেইল ও অ্যাপ নোটিফিকেশন পাবেন।</p></div>
        <label class="switch"><input type="checkbox" checked><span class="switch-track" aria-hidden="true"></span><span class="sr-only">রিপোর্টের অবস্থা পরিবর্তনের নোটিফিকেশন</span></label>
      </div>
      <div class="toggle-row">
        <div><strong>আশপাশের নতুন সমস্যার সতর্কতা</strong><p>আপনার নির্বাচিত এলাকায় নতুন রিপোর্ট জমা হলে জানানো হবে।</p></div>
        <label class="switch"><input type="checkbox" checked><span class="switch-track" aria-hidden="true"></span><span class="sr-only">আশপাশের নতুন সমস্যার সতর্কতা</span></label>
      </div>
      <div class="toggle-row">
        <div><strong>অনুদান কার্যক্রমের হালনাগাদ</strong><p>আপনি যে কার্যক্রমে অবদান রেখেছেন তার অগ্রগতি ও ব্যয়ের তথ্য পাবেন।</p></div>
        <label class="switch"><input type="checkbox" checked><span class="switch-track" aria-hidden="true"></span><span class="sr-only">অনুদান কার্যক্রমের হালনাগাদ</span></label>
      </div>
      <div class="toggle-row">
        <div><strong>সাপ্তাহিক সারসংক্ষেপ</strong><p>প্রতি সপ্তাহে এলাকার রিপোর্ট ও সমাধানের সারসংক্ষেপ ইমেইলে পাঠানো হবে।</p></div>
        <label class="switch"><input type="checkbox"><span class="switch-track" aria-hidden="true"></span><span class="sr-only">সাপ্তাহিক সারসংক্ষেপ</span></label>
      </div>
      <div class="form-actions-bar" style="margin-top:16px">
        <button class="btn btn-primary" type="button" data-pref-save><?= icon('save', 'icon-sm') ?> পছন্দ সংরক্ষণ</button>
      </div>
    </div>
  </div>

  <div class="panel" style="margin-top:20px">
    <div class="panel-body row-between">
      <div>
        <h3 style="font-size:.9rem;margin-bottom:3px">অ্যাকাউন্ট মুছে ফেলতে চান?</h3>
        <p class="muted-note">অ্যাকাউন্ট মুছে ফেললে আপনার রিপোর্ট ও মন্তব্য সংরক্ষিত থাকবে, তবে আর প্রবেশ করতে পারবেন না।</p>
      </div>
      <button class="btn btn-danger-outline btn-sm" type="button" data-confirm="আপনার অ্যাকাউন্ট মুছে ফেলার অনুরোধ পাঠানো হবে। এই কাজটি ফিরিয়ে নেওয়া যাবে না।" data-confirm-danger data-confirm-title="অ্যাকাউন্ট মুছে ফেলুন" data-confirm-yes="অনুরোধ পাঠান"><?= icon('trash', 'icon-sm') ?> অ্যাকাউন্ট মুছুন</button>
    </div>
  </div>

  </div>
</div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
