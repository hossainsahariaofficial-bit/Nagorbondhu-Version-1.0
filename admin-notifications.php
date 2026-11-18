<?php
require_once __DIR__ . '/includes/bootstrap.php';

$adminPage = 'admin-notifications.php';
$adminTitle = 'নোটিফিকেশন';

$notifications = [
    ['title' => 'নতুন রিপোর্ট যাচাইয়ের অপেক্ষায়', 'body' => 'ধানমন্ডি ২৭ নম্বরে নতুন ড্রেন সংক্রান্ত রিপোর্ট জমা হয়েছে। যাচাই দলকে অবহিত করা হয়েছে।', 'audience' => 'প্রশাসনিক দল', 'channel' => 'সিস্টেম', 'time' => '১০ মিনিট আগে', 'status' => 'প্রেরিত', 'tone' => 'info'],
    ['title' => 'আপনার রিপোর্টের অবস্থা পরিবর্তন হয়েছে', 'body' => 'মিরপুর ১০ গোলচত্বরের রিপোর্ট এখন সমাধানাধীন। সড়ক বিভাগ কাজ শুরু করেছে।', 'audience' => 'সাদিয়া আফরিন', 'channel' => 'অ্যাপ + ইমেইল', 'time' => '২ ঘণ্টা আগে', 'status' => 'প্রেরিত', 'tone' => 'success'],
    ['title' => 'অনুদান যাচাই সম্পন্ন হয়েছে', 'body' => 'কাজীপাড়ার ড্রেন প্রকল্পে ২,০০০ টাকার অনুদান যাচাই সম্পন্ন হয়েছে।', 'audience' => 'সাদিয়া আফরিন', 'channel' => 'অ্যাপ', 'time' => '৩ ঘণ্টা আগে', 'status' => 'প্রেরিত', 'tone' => 'success'],
    ['title' => 'সাপ্তাহিক অগ্রগতি সারসংক্ষেপ', 'body' => 'এই সপ্তাহে ৩৪টি রিপোর্ট যাচাই সম্পন্ন হয়েছে এবং ১৮টি কাজ চলমান রয়েছে।', 'audience' => 'সব কর্তৃপক্ষ', 'channel' => 'ইমেইল', 'time' => '১ দিন আগে', 'status' => 'প্রেরিত', 'tone' => 'neutral'],
    ['title' => 'মন্তব্য পর্যালোচনার অনুরোধ', 'body' => 'মিরপুর ১০ রিপোর্টে একটি মন্তব্য পতাকাঙ্কিত হয়েছে। ২৪ ঘণ্টার মধ্যে পর্যালোচনা প্রয়োজন।', 'audience' => 'মডারেটর দল', 'channel' => 'সিস্টেম', 'time' => '১ দিন আগে', 'status' => 'প্রেরিত', 'tone' => 'warning'],
    ['title' => 'নতুন অনুদান কার্যক্রম প্রকাশনা', 'body' => '“স্কুলপথে নিরাপদ সড়কবাতি” কার্যক্রম আগামীকাল সকাল ১০টায় প্রকাশিত হবে।', 'audience' => 'সব নাগরিক', 'channel' => 'অ্যাপ + ইমেইল', 'time' => 'আগামীকাল সকাল ১০টা', 'status' => 'নির্ধারিত', 'tone' => 'warning'],
    ['title' => 'সিস্টেম রক্ষণাবেক্ষণ বিজ্ঞপ্তি', 'body' => '১০ নভেম্বর রাত ২টা থেকে ৩টা পর্যন্ত প্ল্যাটফর্মের রক্ষণাবেক্ষণ চলবে।', 'audience' => 'সব ব্যবহারকারী', 'channel' => 'অ্যাপ', 'time' => '৯ নভেম্বর রাত ৮টা', 'status' => 'নির্ধারিত', 'tone' => 'info'],
    ['title' => 'মাসিক স্বচ্ছতা প্রতিবেদন', 'body' => 'অক্টোবর মাসের অনুদান ও ব্যয়ের স্বচ্ছতা প্রতিবেদন প্রকাশের খসড়া।', 'audience' => 'সব নাগরিক', 'channel' => 'ইমেইল', 'time' => 'খসড়া', 'status' => 'খসড়া', 'tone' => 'neutral'],
];

$sentCount = count(array_filter($notifications, fn ($n) => $n['status'] === 'প্রেরিত'));
$scheduledCount = count(array_filter($notifications, fn ($n) => $n['status'] === 'নির্ধারিত'));
$draftCount = count(array_filter($notifications, fn ($n) => $n['status'] === 'খসড়া'));

$statusFilter = (string) ($_GET['status'] ?? '');
$rows = $statusFilter !== ''
    ? array_values(array_filter($notifications, fn ($n) => $n['status'] === $statusFilter))
    : $notifications;

include __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-page-head">
  <div>
    <div class="admin-breadcrumb-lite"><a href="admin.php">প্রশাসনিক প্যানেল</a><?= icon('chevron-right') ?><span>নোটিফিকেশন</span></div>
    <h2>বার্তা ও নোটিফিকেশন</h2>
    <p>নাগরিক, কর্তৃপক্ষ ও প্রশাসনিক দলের কাছে বার্তা পাঠান এবং প্রেরণের ফলাফল দেখুন।</p>
  </div>
  <div class="admin-page-head-actions">
    <button class="btn btn-outline btn-sm" type="button" data-export-table="#notificationsTable" data-export-name="নগরবন্ধু-নোটিফিকেশন.csv"><?= icon('download', 'icon-sm') ?> এক্সপোর্ট</button>
    <button class="btn btn-primary btn-sm" type="button" onclick="document.getElementById('notifTitle').focus()"><?= icon('plus', 'icon-sm') ?> নতুন বার্তা</button>
  </div>
</div>

<div class="admin-stat-grid">
  <div class="admin-stat accent">
    <div class="admin-stat-top"><span class="admin-stat-label">এই মাসে প্রেরিত</span><span class="admin-stat-icon"><?= icon('bell', 'icon-sm') ?></span></div>
    <div class="admin-stat-value"><?= bn_num(1248) ?></div>
    <div class="admin-stat-foot"><span class="up"><?= icon('trending-up') ?> ৮.৪%</span> গত মাসের তুলনায়</div>
  </div>
  <div class="admin-stat">
    <div class="admin-stat-top"><span class="admin-stat-label">ডেলিভারি হার</span><span class="admin-stat-icon green"><?= icon('check-circle', 'icon-sm') ?></span></div>
    <div class="admin-stat-value"><?= bn('৯৮.২') ?>%</div>
    <div class="admin-stat-foot"><?= bn('২২') ?>টি ব্যর্থ প্রেরণ</div>
  </div>
  <div class="admin-stat">
    <div class="admin-stat-top"><span class="admin-stat-label">পঠিত হয়েছে</span><span class="admin-stat-icon blue"><?= icon('eye', 'icon-sm') ?></span></div>
    <div class="admin-stat-value"><?= bn('৬৮') ?>%</div>
    <div class="admin-stat-foot">গড় খোলার হার</div>
  </div>
  <div class="admin-stat">
    <div class="admin-stat-top"><span class="admin-stat-label">নির্ধারিত</span><span class="admin-stat-icon amber"><?= icon('calendar', 'icon-sm') ?></span></div>
    <div class="admin-stat-value"><?= bn($scheduledCount) ?></div>
    <div class="admin-stat-foot">আগামী ৭ দিনে</div>
  </div>
</div>

<div class="admin-grid cols-2-1">
  <section class="admin-panel" id="notificationsPanel">
    <div class="admin-tabs">
      <a class="admin-tab <?= $statusFilter === '' ? 'active' : '' ?>" href="admin-notifications.php" <?= $statusFilter === '' ? 'aria-current="page"' : '' ?>>সব বার্তা <span class="count"><?= bn(count($notifications)) ?></span></a>
      <a class="admin-tab <?= $statusFilter === 'প্রেরিত' ? 'active' : '' ?>" href="admin-notifications.php?status=<?= rawurlencode('প্রেরিত') ?>" <?= $statusFilter === 'প্রেরিত' ? 'aria-current="page"' : '' ?>>প্রেরিত <span class="count"><?= bn($sentCount) ?></span></a>
      <a class="admin-tab <?= $statusFilter === 'নির্ধারিত' ? 'active' : '' ?>" href="admin-notifications.php?status=<?= rawurlencode('নির্ধারিত') ?>" <?= $statusFilter === 'নির্ধারিত' ? 'aria-current="page"' : '' ?>>নির্ধারিত <span class="count"><?= bn($scheduledCount) ?></span></a>
      <a class="admin-tab <?= $statusFilter === 'খসড়া' ? 'active' : '' ?>" href="admin-notifications.php?status=<?= rawurlencode('খসড়া') ?>" <?= $statusFilter === 'খসড়া' ? 'aria-current="page"' : '' ?>>খসড়া <span class="count"><?= bn($draftCount) ?></span></a>
    </div>
    <div class="admin-toolbar">
      <div class="toolbar-left">
        <div class="search-input">
          <?= icon('search') ?>
          <label class="sr-only" for="notifSearch">বার্তা খুঁজুন</label>
          <input class="input" id="notifSearch" type="search" placeholder="বার্তা বা প্রাপক…" data-table-search>
        </div>
        <label class="sr-only" for="notifStatus">অবস্থা</label>
        <select class="select" id="notifStatus" data-table-filter="status">
          <option value="">সব অবস্থা</option>
          <option value="প্রেরিত" <?= $statusFilter === 'প্রেরিত' ? 'selected' : '' ?>>প্রেরিত</option>
          <option value="নির্ধারিত" <?= $statusFilter === 'নির্ধারিত' ? 'selected' : '' ?>>নির্ধারিত</option>
          <option value="খসড়া" <?= $statusFilter === 'খসড়া' ? 'selected' : '' ?>>খসড়া</option>
        </select>
        <label class="sr-only" for="notifChannel">মাধ্যম</label>
        <select class="select" id="notifChannel" data-table-filter="channel">
          <option value="">সব মাধ্যম</option>
          <option value="অ্যাপ">অ্যাপ</option>
          <option value="ইমেইল">ইমেইল</option>
          <option value="সিস্টেম">সিস্টেম</option>
        </select>
      </div>
      <div class="toolbar-right"><span class="badge badge-warning"><?= icon('calendar', 'icon-xs') ?> <?= bn($scheduledCount) ?>টি নির্ধারিত</span></div>
    </div>
    <div class="table-scroll admin-table-wrap">
      <table class="data-table admin-table" id="notificationsTable" data-admin-table="#notificationsPanel">
        <thead><tr><th style="min-width:300px">বার্তা</th><th>প্রাপক</th><th>মাধ্যম</th><th>সময়</th><th>অবস্থা</th><th class="cell-actions">কার্যক্রম</th></tr></thead>
        <tbody>
          <tr data-empty data-empty-row><td colspan="6"><?= empty_state('কোনো বার্তা পাওয়া যায়নি', 'অন্য ফিল্টার দিয়ে আবার খুঁজুন।', 'bell') ?></td></tr>
          <?php foreach ($rows as $notif): ?>
            <tr data-status="<?= e($notif['status']) ?>" data-channel="<?= e($notif['channel']) ?>">
              <td>
                <div class="cell-person">
                  <span class="activity-icon" style="width:30px;height:30px"><?= icon($notif['tone'] === 'success' ? 'check-circle' : ($notif['tone'] === 'warning' ? 'clock' : 'bell'), 'icon-sm') ?></span>
                  <div><strong><?= e($notif['title']) ?></strong><p style="font-size:.68rem;color:var(--muted);line-height:1.6;max-width:340px"><?= e(excerpt($notif['body'], 80)) ?></p></div>
                </div>
              </td>
              <td class="table-sub"><?= e($notif['audience']) ?></td>
              <td><span class="badge badge-soft"><?= icon($notif['channel'] === 'ইমেইল' ? 'mail' : 'bell', 'icon-xs') ?> <?= e($notif['channel']) ?></span></td>
              <td class="table-sub"><?= e($notif['time']) ?></td>
              <td><span class="badge badge-<?= e($notif['tone']) ?>"><span class="status-dot"></span><?= e($notif['status']) ?></span></td>
              <td class="cell-actions">
                <div class="row-menu" data-row-menu>
                  <button class="row-menu-btn icon-btn" type="button" aria-haspopup="true" aria-expanded="false" aria-label="কার্যক্রম মেনু"><?= icon('more-horizontal', 'icon-sm') ?></button>
                  <div class="row-dropdown">
                    <a href="admin-notifications.php"><?= icon('eye', 'icon-sm') ?> বিস্তারিত দেখুন</a>
                    <button type="button" data-admin-action="approve"><?= icon('send', 'icon-sm') ?> এখনই পাঠান</button>
                    <button type="button" data-admin-action="hide"><?= icon('x', 'icon-sm') ?> বাতিল করুন</button>
                    <div class="dropdown-sep"></div>
                    <button class="danger-item" type="button" data-admin-action="reject"><?= icon('trash', 'icon-sm') ?> মুছে ফেলুন</button>
                  </div>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="table-foot"><p data-table-info>দেখানো হচ্ছে <?= bn(count($rows)) ?>টি বার্তা</p><div class="pagination" data-table-pagination></div></div>
  </section>

  <div style="display:flex;flex-direction:column;gap:16px">
    <section class="admin-panel">
      <div class="admin-panel-head"><div><h3>নতুন বার্তা</h3><p>প্রাপক ও মাধ্যম নির্বাচন করুন</p></div></div>
      <div class="admin-panel-body">
        <form class="admin-form" data-demo-submit data-success-message="বার্তাটি প্রেরণের জন্য প্রস্তুত করা হয়েছে।" data-confirm-title="বার্তা পাঠাবেন?" data-confirm-body="নির্বাচিত প্রাপকদের কাছে বার্তাটি পাঠানো হবে।" data-confirm-yes="হ্যাঁ, পাঠান">
          <div class="field">
            <label class="field-label" for="notifTitle">বার্তার শিরোনাম <span class="req">*</span></label>
            <input class="input" id="notifTitle" name="title" type="text" required placeholder="যেমন: সাপ্তাহিক অগ্রগতি হালনাগাদ">
          </div>
          <div class="field">
            <label class="field-label" for="notifBody">বার্তা <span class="req">*</span></label>
            <textarea class="textarea" id="notifBody" name="body" required placeholder="সংক্ষিপ্ত ও স্পষ্ট ভাষায় বার্তা লিখুন…"></textarea>
          </div>
          <div class="field">
            <label class="field-label" for="notifAudience">প্রাপক</label>
            <select class="select" id="notifAudience" name="audience">
              <option>সব নাগরিক</option>
              <option>নির্দিষ্ট এলাকার নাগরিক</option>
              <option>সব কর্তৃপক্ষ</option>
              <option>প্রশাসনিক দল</option>
              <option>মডারেটর দল</option>
            </select>
          </div>
          <div class="field">
            <label class="field-label">মাধ্যম</label>
            <div class="badge-row">
              <label class="checkbox"><input type="checkbox" name="channels[]" value="অ্যাপ" checked> অ্যাপ</label>
              <label class="checkbox"><input type="checkbox" name="channels[]" value="ইমেইল" checked> ইমেইল</label>
              <label class="checkbox"><input type="checkbox" name="channels[]" value="এসএমএস"> এসএমএস</label>
            </div>
          </div>
          <div class="field">
            <label class="field-label" for="notifSchedule">প্রেরণের সময়</label>
            <select class="select" id="notifSchedule" name="schedule">
              <option>এখনই পাঠান</option>
              <option>আজ রাত ৮টা</option>
              <option>আগামীকাল সকাল ১০টা</option>
              <option>নির্দিষ্ট সময় নির্বাচন করুন</option>
            </select>
          </div>
          <div class="form-actions-bar">
            <button class="btn btn-outline" type="button" data-save-draft="notification"><?= icon('save', 'icon-sm') ?> খসড়া</button>
            <button class="btn btn-primary" type="submit"><?= icon('send', 'icon-sm') ?> পাঠান</button>
          </div>
        </form>
      </div>
    </section>

    <section class="admin-panel">
      <div class="admin-panel-head"><div><h3>পাঠানোর পছন্দ</h3><p>স্বয়ংক্রিয় নোটিফিকেশন</p></div></div>
      <div class="admin-panel-body">
        <div class="toggle-row"><div><strong>নতুন রিপোর্ট</strong><p>যাচাই দলকে সাথে সাথে অবহিত করুন</p></div><label class="switch"><input type="checkbox" checked><span class="switch-track"></span></label></div>
        <div class="toggle-row"><div><strong>অবস্থা পরিবর্তন</strong><p>রিপোর্টকারী ও সমর্থকদের জানান</p></div><label class="switch"><input type="checkbox" checked><span class="switch-track"></span></label></div>
        <div class="toggle-row"><div><strong>সাপ্তাহিক সারসংক্ষেপ</strong><p>প্রতি সোমবার সকালে পাঠান</p></div><label class="switch"><input type="checkbox" checked><span class="switch-track"></span></label></div>
        <div class="toggle-row"><div><strong>অনুদান রসিদ</strong><p>প্রতিটি যাচাইকৃত অনুদানের রসিদ</p></div><label class="switch"><input type="checkbox" checked><span class="switch-track"></span></label></div>
        <button class="btn btn-primary btn-block btn-sm" type="button" data-pref-save style="margin-top:14px"><?= icon('save', 'icon-sm') ?> পছন্দ সংরক্ষণ করুন</button>
      </div>
    </section>
  </div>
</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
