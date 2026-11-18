<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_citizen();
$pageTitle = 'সমস্যা জানান — নগরবন্ধু';
$metaDescription = 'ছবি, বিবরণ ও মানচিত্রে অবস্থানসহ আপনার এলাকার সমস্যাটি কয়েক ধাপে জমা দিন। নগরবন্ধু রিপোর্টটি সংশ্লিষ্ট কর্তৃপক্ষের কাছে পৌঁছে দিতে সাহায্য করে।';
$citizen = current_citizen();
$reportError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim((string) ($_POST['title'] ?? ''));
    $type = (string) ($_POST['type'] ?? 'other');
    $area = trim((string) ($_POST['area'] ?? ''));
    $district = trim((string) ($_POST['district'] ?? 'ঢাকা'));
    $address = trim((string) ($_POST['address'] ?? ''));
    $landmark = trim((string) ($_POST['landmark'] ?? ''));
    $description = trim((string) ($_POST['description'] ?? ''));
    $urgency = (string) ($_POST['urgency'] ?? 'medium');
    $lat = (float) ($_POST['lat'] ?? 23.83820);
    $lng = (float) ($_POST['lng'] ?? 90.37360);
    $name = trim((string) ($_POST['name'] ?? ''));
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $consent = isset($_POST['consent']);

    if (!csrf_valid()) {
        $reportError = 'নিরাপত্তা যাচাই ব্যর্থ হয়েছে। পেজটি রিফ্রেশ করে আবার চেষ্টা করুন।';
    } elseif (!db_ok()) {
        $reportError = db_unavailable_message();
    } elseif ($title === '' || $area === '' || $address === '' || $description === '' || $name === '' || $phone === '') {
        $reportError = 'তারকা চিহ্নিত সব তথ্য পূরণ করুন।';
    } elseif (mb_strlen($title) < 8) {
        $reportError = 'সমস্যার শিরোনাম কমপক্ষে ৮ অক্ষরের হতে হবে।';
    } elseif (mb_strlen($description) < 20) {
        $reportError = 'বিস্তারিত বিবরণ কমপক্ষে ২০ অক্ষরের হতে হবে।';
    } elseif (!valid_bd_phone($phone)) {
        $reportError = 'সঠিক ১১ সংখ্যার বাংলাদেশি মোবাইল নম্বর লিখুন।';
    } elseif ($email !== '' && !valid_email($email)) {
        $reportError = 'সঠিক ইমেইল ঠিকানা লিখুন অথবা ঘরটি ফাঁকা রাখুন।';
    } elseif (!$consent) {
        $reportError = 'রিপোর্ট জমা দিতে শর্তাবলি ও গোপনীয়তা নীতিতে সম্মতি দিন।';
    } else {
        $categories = array_keys(nb_categories());
        if (!in_array($type, $categories, true)) {
            $type = 'other';
        }
        $urgencies = array_keys(nb_urgency());
        if (!in_array($urgency, $urgencies, true)) {
            $urgency = 'medium';
        }
        $districts = nb_districts();
        if (!in_array($district, $districts, true)) {
            $district = 'ঢাকা';
        }
        if ($lat < 20 || $lat > 27) { $lat = 23.83820; }
        if ($lng < 88 || $lng > 93) { $lng = 90.37360; }

        [$photos, $uploadErrors] = nb_store_issue_photos('photos', 4);
        if ($uploadErrors) {
            $reportError = implode(' ', array_unique($uploadErrors));
        } else {
            $issueId = db_insert(
                'INSERT INTO issues
                    (user_id, code, title, category, urgency, status, description, area, district, division,
                     address, landmark, lat, lng, reporter_name, reporter_phone, reporter_email, image_path)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    (int) $citizen['id'],
                    new_issue_code(),
                    $title,
                    $type,
                    $urgency,
                    'new',
                    $description,
                    $area,
                    $district,
                    $district,
                    $address,
                    $landmark !== '' ? $landmark : null,
                    $lat,
                    $lng,
                    $name,
                    normalize_phone($phone),
                    $email !== '' ? mb_strtolower($email) : null,
                    $photos[0] ?? null,
                ]
            );

            if ($issueId <= 0) {
                $reportError = 'রিপোর্ট সংরক্ষণ করা যায়নি। ' . (db_error() !== '' ? 'ডেটাবেজ ত্রুটি: ' . db_error() : 'আবার চেষ্টা করুন।');
            } else {
                foreach ($photos as $index => $path) {
                    db_exec('INSERT INTO issue_images (issue_id, path, sort_order) VALUES (?, ?, ?)', [$issueId, $path, $index]);
                }
                db_exec(
                    'INSERT INTO issue_updates (issue_id, admin_id, status, note) VALUES (?, NULL, ?, ?)',
                    [$issueId, 'new', 'নাগরিক ' . $name . ' ছবি, বিবরণ ও অবস্থানসহ রিপোর্টটি জমা দিয়েছেন।']
                );
                flash_set('success', 'আপনার রিপোর্ট সফলভাবে সংরক্ষিত হয়েছে। রিপোর্ট নম্বর দিয়ে অগ্রগতি দেখতে পারবেন।');
                header('Location: issue.php?id=' . $issueId);
                exit;
            }
        }
    }
}

$areas = array_values(array_unique(array_map(fn ($issue) => $issue['area'], nb_issues())));
sort($areas);
include __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <?= page_heading(
        'এলাকার সমস্যাটি জানান',
        'সঠিক ছবি, সংক্ষিপ্ত বিবরণ ও নির্ভুল অবস্থান দিলে রিপোর্টটি দ্রুত যাচাই করা সহজ হয়। চার ধাপে রিপোর্টটি সম্পন্ন করুন।',
        ['হোম' => 'index.php', 'সমস্যা জানান' => null]
    ) ?>
  </div>
</section>

<?php if ($reportError !== ''): ?>
  <div class="container">
    <div class="alert alert-danger page-alert" role="alert" style="margin-bottom:0"><?= icon('alert-circle', 'icon-sm') ?><div><?= e($reportError) ?></div></div>
  </div>
<?php endif; ?>

<div class="container report-layout" data-wizard>
  <div class="report-card">
    <div class="report-steps" aria-label="রিপোর্টের ধাপ">
      <div class="report-step active" data-step-indicator data-title="সমস্যার ধরন">
        <span class="step-num">১</span><span>সমস্যার ধরন</span>
      </div>
      <span class="report-step-line" aria-hidden="true"></span>
      <div class="report-step" data-step-indicator data-title="বিবরণ ও এলাকা">
        <span class="step-num">২</span><span>বিবরণ ও এলাকা</span>
      </div>
      <span class="report-step-line" aria-hidden="true"></span>
      <div class="report-step" data-step-indicator data-title="ছবি ও অবস্থান">
        <span class="step-num">৩</span><span>ছবি ও অবস্থান</span>
      </div>
      <span class="report-step-line" aria-hidden="true"></span>
      <div class="report-step" data-step-indicator data-title="যাচাই ও জমা">
        <span class="step-num">৪</span><span>যাচাই ও জমা</span>
      </div>
    </div>

    <form method="post" action="report.php" enctype="multipart/form-data" data-validate novalidate>
      <?= csrf_field() ?>

      <div class="step-panel">
        <div class="step-title">
          <?= icon('layers', 'icon-md') ?>
          <div><h2>কী ধরনের সমস্যা?</h2><p>সবচেয়ে কাছাকাছি ক্যাটাগরিটি বেছে নিন। এটি সঠিক কর্তৃপক্ষের কাছে রিপোর্ট পাঠাতে সাহায্য করে।</p></div>
        </div>

        <div class="report-type-grid">
          <?php foreach (nb_categories() as $key => $cat): ?>
            <div class="report-type">
              <input type="radio" id="type-<?= e($key) ?>" name="type" value="<?= e($key) ?>" <?= $key === 'pothole' ? 'checked' : '' ?>>
              <label for="type-<?= e($key) ?>">
                <?= icon($cat['icon'], 'icon-sm') ?>
                <strong><?= e($cat['label']) ?></strong>
                <small><?= e([
                    'pothole' => 'রাস্তার গর্ত, ভাঙা পিচ',
                    'waste' => 'ময়লা, ডাস্টবিন, দুর্গন্ধ',
                    'water' => 'জলাবদ্ধতা, পানি নিষ্কাশন',
                    'drain' => 'ভাঙা ড্রেন, খোলা ঢাকনা',
                    'light' => 'নষ্ট বাতি, অন্ধকার সড়ক',
                    'accident' => 'ঝুঁকিপূর্ণ মোড়, দুর্ঘটনা',
                    'other' => 'অন্যান্য নাগরিক সমস্যা',
                ][$key] ?? '') ?></small>
              </label>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="info-strip mt-3">
          <?= icon('info', 'icon-sm') ?>
          <span>নিশ্চিত না হলে “অন্যান্য সমস্যা” বেছে নিন। যাচাই দল প্রয়োজনে সঠিক ক্যাটাগরি নির্ধারণ করবে।</span>
        </div>

        <div class="step-actions">
          <a class="btn btn-text" href="issues.php"><?= icon('arrow-left', 'icon-sm') ?> বাতিল করুন</a>
          <button class="btn btn-primary" type="button" data-wizard-next>পরবর্তী ধাপ <?= icon('arrow-right', 'icon-sm') ?></button>
        </div>
      </div>

      <div class="step-panel" hidden>
        <div class="step-title">
          <?= icon('file-text', 'icon-md') ?>
          <div><h2>সমস্যার বিবরণ দিন</h2><p>যতটা সম্ভব নির্দিষ্ট তথ্য দিন—কী হয়েছে, কোথায় এবং কতদিন ধরে।</p></div>
        </div>

        <div class="form-grid">
          <div class="field span-2">
            <label class="field-label" for="reportTitle">সমস্যার শিরোনাম <span class="req">*</span></label>
            <input class="input" id="reportTitle" name="title" type="text" required minlength="8" placeholder="যেমন: মিরপুর ১০ গোলচত্বরে বড় গর্ত, প্রতিদিন দুর্ঘটনার ঝুঁকি">
            <span class="field-hint">সংক্ষেপে সমস্যার মূল বিষয়টি লিখুন।</span>
          </div>

          <div class="field">
            <label class="field-label" for="reportArea">এলাকা <span class="req">*</span></label>
            <select class="select" id="reportArea" name="area" required>
              <option value="">এলাকা নির্বাচন করুন</option>
              <?php foreach ($areas as $area): ?><option value="<?= e($area) ?>"><?= e($area) ?></option><?php endforeach; ?>
              <option value="অন্যান্য">অন্যান্য এলাকা</option>
            </select>
          </div>

          <div class="field">
            <label class="field-label" for="reportDistrict">জেলা / শহর</label>
            <select class="select" id="reportDistrict" name="district">
              <?php foreach (nb_districts() as $district): ?><option value="<?= e($district) ?>"><?= e($district) ?></option><?php endforeach; ?>
            </select>
          </div>

          <div class="field span-2">
            <label class="field-label" for="reportAddress">বিস্তারিত ঠিকানা <span class="req">*</span></label>
            <input class="input" id="reportAddress" name="address" type="text" required placeholder="যেমন: মিরপুর ১০ গোলচত্বর, পীরেরবাগ মোড়, ঢাকা ১২১৬">
            <span class="field-hint">নিকটস্থ ল্যান্ডমার্ক বা প্রতিষ্ঠানের নাম লিখলে যাচাই সহজ হয়।</span>
          </div>

          <div class="field span-2">
            <label class="field-label" for="reportDescription">বিস্তারিত বিবরণ <span class="req">*</span></label>
            <textarea class="textarea" id="reportDescription" name="description" rows="5" required minlength="20" placeholder="সমস্যাটি কোথায়, কতদিন ধরে হচ্ছে, কারা ক্ষতিগ্রস্ত হচ্ছেন এবং এখন পর্যন্ত কী পদক্ষেপ নেওয়া হয়েছে—এসব লিখুন…"></textarea>
            <span class="field-hint">কমপক্ষে ২০ অক্ষর। ব্যক্তিগত তথ্য বা কারও নাম অনুমতি ছাড়া লিখবেন না।</span>
          </div>
        </div>

        <div class="field mt-3">
          <span class="field-label">জরুরি মাত্রা</span>
          <div class="urgency-options">
            <?php foreach (nb_urgency() as $key => $label): ?>
              <div class="urgency-option">
                <input type="radio" id="urgency-<?= e($key) ?>" name="urgency" value="<?= e($key) ?>" <?= $key === 'medium' ? 'checked' : '' ?>>
                <label for="urgency-<?= e($key) ?>"><?= e($label) ?><small><?= e([
                    'normal' => 'অগ্রাধিকার নেই',
                    'medium' => 'শীঘ্রই দরকার',
                    'high' => 'দুর্ঘটনার ঝুঁকি',
                    'critical' => 'তাৎক্ষণিক পদক্ষেপ',
                ][$key] ?? '') ?></small></label>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="step-actions">
          <button class="btn btn-outline" type="button" data-wizard-prev><?= icon('arrow-left', 'icon-sm') ?> পূর্ববর্তী</button>
          <button class="btn btn-primary" type="button" data-wizard-next>পরবর্তী ধাপ <?= icon('arrow-right', 'icon-sm') ?></button>
        </div>
      </div>

      <div class="step-panel" hidden>
        <div class="step-title">
          <?= icon('camera', 'icon-md') ?>
          <div><h2>ছবি ও অবস্থান যোগ করুন</h2><p>সমস্যার স্পষ্ট ছবি এবং মানচিত্রে সঠিক অবস্থান রিপোর্টের বিশ্বাসযোগ্যতা বাড়ায়।</p></div>
        </div>

        <div class="field">
          <span class="field-label">সমস্যার ছবি <span class="text-muted" style="font-weight:400;font-size:.76rem">(ঐচ্ছিক, সর্বোচ্চ ৪টি)</span></span>
          <div class="upload-zone" data-upload-zone>
            <div class="upload-icon"><?= icon('upload-cloud', 'icon-md') ?></div>
            <strong>ছবি টেনে আনুন অথবা নির্বাচন করুন</strong>
            <p>JPG, PNG বা WEBP • প্রতিটি ছবি সর্বোচ্চ ৫ মেগাবাইট</p>
            <span class="btn btn-sm btn-outline"><?= icon('image', 'icon-sm') ?> ছবি বেছে নিন</span>
            <input type="file" id="reportImages" name="photos[]" accept="image/*" multiple data-image-input>
          </div>
          <div class="preview-grid" data-preview-grid></div>
          <p class="upload-limit"><?= icon('info', 'icon-xs') ?> সর্বোচ্চ ৪টি ছবি সংরক্ষণ করা হবে। ছবিগুলো রিপোর্টের সঙ্গে ডেটাবেজে যুক্ত হবে।</p>
        </div>

        <div class="field mt-3">
          <span class="field-label">মানচিত্রে অবস্থান</span>
          <div class="map-picker" data-map-picker>
            <div class="map-picker-canvas" tabindex="0" role="button" aria-label="মানচিত্রে সমস্যার অবস্থান চিহ্নিত করুন">
              <?= nb_map_svg() ?>
              <span class="map-picker-hint"><?= icon('map-pin', 'icon-xs') ?> মানচিত্রে ক্লিক করে পিন বসান</span>
              <span class="map-picker-pin" aria-hidden="true"><?= nb_pin_svg() ?></span>
            </div>
            <div class="map-picker-foot">
              <span class="coords" data-coords><?= icon('compass', 'icon-xs') ?> অক্ষাংশ ২৩.৮৩৮২০ · দ্রাঘিমাংশ ৯০.৩৭৩৬০</span>
              <span><?= icon('navigation', 'icon-xs') ?> কীবোর্ড তীর চিহ্নেও সরানো যায়</span>
            </div>
          </div>
          <input type="hidden" id="mapLat" name="lat" value="23.83820">
          <input type="hidden" id="mapLng" name="lng" value="90.37360">
        </div>

        <div class="field mt-2">
          <label class="field-label" for="reportLandmark">নিকটস্থ ল্যান্ডমার্ক (ঐচ্ছিক)</label>
          <input class="input" id="reportLandmark" name="landmark" type="text" placeholder="যেমন: মসজিদের পাশে, স্কুলের বিপরীতে">
        </div>

        <div class="step-actions">
          <button class="btn btn-outline" type="button" data-wizard-prev><?= icon('arrow-left', 'icon-sm') ?> পূর্ববর্তী</button>
          <button class="btn btn-primary" type="button" data-wizard-next>পরবর্তী ধাপ <?= icon('arrow-right', 'icon-sm') ?></button>
        </div>
      </div>

      <div class="step-panel" hidden>
        <div class="step-title">
          <?= icon('check-square', 'icon-md') ?>
          <div><h2>যাচাই করে জমা দিন</h2><p>যোগাযোগের তথ্য দিলে রিপোর্টের অগ্রগতি সম্পর্কে আপনাকে জানানো যাবে।</p></div>
        </div>

        <div class="form-grid">
          <div class="field">
            <label class="field-label" for="reporterName">আপনার নাম <span class="req">*</span></label>
            <input class="input" id="reporterName" name="name" type="text" required value="<?= e($citizen['name']) ?>" autocomplete="name">
          </div>
          <div class="field">
            <label class="field-label" for="reporterPhone">মোবাইল নম্বর <span class="req">*</span></label>
            <input class="input" id="reporterPhone" name="phone" type="tel" inputmode="tel" required data-rule="phone" value="<?= e($citizen['phone']) ?>" autocomplete="tel" placeholder="০১৭xxxxxxxx">
            <span class="field-hint">আপনার নম্বর কখনো publicly দেখানো হবে না।</span>
          </div>
          <div class="field span-2">
            <label class="field-label" for="reporterEmail">ইমেইল ঠিকানা</label>
            <input class="input" id="reporterEmail" name="email" type="email" value="<?= e($citizen['email']) ?>" autocomplete="email" placeholder="name@example.com">
          </div>
        </div>

        <div class="field mt-3" data-skip-validation>
          <label class="checkbox">
            <input type="checkbox" name="consent" required>
            <span>আমি নিশ্চিত করছি যে দেওয়া তথ্য আমার জানা অনুযায়ী সঠিক এবং আমি <a href="terms.php">ব্যবহারের শর্তাবলি</a> ও <a href="privacy.php">গোপনীয়তা নীতি</a> মেনে রিপোর্টটি জমা দিচ্ছি।</span>
          </label>
        </div>

        <div class="info-strip mt-2">
          <?= icon('shield', 'icon-sm') ?>
          <span>আপনার ফোন নম্বর ও ইমেইল শুধু রিপোর্টের অগ্রগতি জানাতে ব্যবহার করা হবে। রিপোর্টটি ডেটাবেজে সংরক্ষিত হবে।</span>
        </div>

        <div class="step-actions">
          <div class="left">
            <button class="btn btn-text" type="button" data-save-draft="report"><?= icon('save', 'icon-sm') ?> খসড়া সংরক্ষণ</button>
          </div>
          <div style="display:flex;gap:8px;flex-wrap:wrap">
            <button class="btn btn-outline" type="button" data-wizard-prev><?= icon('arrow-left', 'icon-sm') ?> পূর্ববর্তী</button>
            <button class="btn btn-primary" type="submit"><?= icon('send', 'icon-sm') ?> রিপোর্ট জমা দিন</button>
          </div>
        </div>
      </div>
    </form>
  </div>

  <aside class="report-side">
    <div class="side-card">
      <h3><?= icon('bulb', 'icon-sm') ?> ভালো রিপোর্টের লক্ষণ</h3>
      <ul class="tips-list">
        <li><?= icon('check-circle', 'icon-xs') ?><span>সমস্যাটি স্পষ্ট দেখা যায় এমন কাছের ও দূরের ছবি দিন।</span></li>
        <li><?= icon('check-circle', 'icon-xs') ?><span>সড়ক, বাসস্ট্যান্ড বা দোকানের নামসহ ঠিকানা লিখুন।</span></li>
        <li><?= icon('check-circle', 'icon-xs') ?><span>কতদিন ধরে সমস্যা এবং কারা ক্ষতিগ্রস্ত হচ্ছেন তা উল্লেখ করুন।</span></li>
        <li><?= icon('check-circle', 'icon-xs') ?><span>একই সমস্যা আগে রিপোর্ট হয়েছে কি না দেখে নিন।</span></li>
        <li><?= icon('check-circle', 'icon-xs') ?><span>মানচিত্রে সঠিক পিন বসান—এটি যাচাই দলের পথ সহজ করে।</span></li>
      </ul>
    </div>

    <div class="side-card side-card-green">
      <h3><?= icon('shield', 'icon-sm') ?> জরুরি পরিস্থিতি?</h3>
      <p>তাৎক্ষণিক বিপদ, আগুন, বৈদ্যুতিক দুর্ঘটনা বা জীবনঝুঁকি হলে প্ল্যাটফর্মে অপেক্ষা না করে সরাসরি জরুরি সেবায় কল করুন।</p>
      <a class="btn btn-white btn-block" href="tel:999"><?= icon('phone', 'icon-sm') ?> জাতীয় জরুরি সেবা ৯৯৯</a>
    </div>

    <div class="side-card">
      <h3><?= icon('activity', 'icon-sm') ?> এরপর কী হবে?</h3>
      <p><strong style="color:var(--ink)">১.</strong> রিপোর্ট যাচাই দলে যাবে।<br>
      <strong style="color:var(--ink)">২.</strong> প্রয়োজনে আপনি বা স্থানীয়রা যোগাযোগ করবেন।<br>
      <strong style="color:var(--ink)">৩.</strong> সংশ্লিষ্ট কর্তৃপক্ষকে বরাদ্দ দেওয়া হবে।<br>
      <strong style="color:var(--ink)">৪.</strong> প্রতিটি ধাপে নোটিফিকেশন পাবেন।</p>
      <a class="btn btn-outline btn-block mt-2" href="faq.php"><?= icon('help-circle', 'icon-sm') ?> সাধারণ জিজ্ঞাসা</a>
    </div>
  </aside>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
