<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_citizen();
$pageTitle = 'আমার ড্যাশবোর্ড — নগরবন্ধু';
$dashPage = 'dashboard.php';
$dashTitle = 'আমার ড্যাশবোর্ড';
$citizen = current_citizen();
$stats = user_stats((int) $citizen['id']);
$myIssues = array_slice(user_issues((int) $citizen['id']), 0, 3);
$notifications = nb_notifications();
$successRate = $stats['reports'] > 0 ? (int) round($stats['solved'] / $stats['reports'] * 100) : 0;
include __DIR__ . '/includes/header.php';
?>
<div class="container">
  <?php include __DIR__ . '/includes/dashboard-sidebar.php'; ?>

  <div class="dash-welcome">
    <div>
      <span class="dash-date"><?= icon('calendar', 'icon-xs') ?> আজ, <?= bn_date(date('Y-m-d')) ?> • <?= e(['রবিবার','সোমবার','মঙ্গলবার','বুধবার','বৃহস্পতিবার','শুক্রবার','শনিবার'][(int) date('w')]) ?></span>
      <h1>স্বাগতম, <?= e($citizen['first']) ?>!</h1>
      <p>আপনার <?= bn($stats['reports']) ?>টি রিপোর্ট, <?= bn($stats['supports']) ?>টি সমর্থন ও <?= bn($stats['donation_count']) ?>টি অনুদান এক নজরে দেখুন।</p>
    </div>
    <a class="btn btn-primary" href="report.php"><?= icon('plus', 'icon-sm') ?> নতুন সমস্যা জানান</a>
  </div>

  <div class="dash-stats">
    <div class="dash-stat tone-green">
      <div class="dash-stat-head"><span>আমার রিপোর্ট</span><?= icon('file-text', 'icon-sm') ?></div>
      <strong class="mono-bn"><?= bn($stats['reports']) ?></strong><small>ডেটাবেজে সংরক্ষিত</small>
    </div>
    <div class="dash-stat tone-blue">
      <div class="dash-stat-head"><span>সমাধান সম্পন্ন</span><?= icon('check-circle', 'icon-sm') ?></div>
      <strong class="mono-bn"><?= bn($stats['solved']) ?></strong><small><?= bn($successRate) ?>% সফলতার হার</small>
    </div>
    <div class="dash-stat tone-violet">
      <div class="dash-stat-head"><span>সমর্থন দিয়েছি</span><?= icon('heart', 'icon-sm') ?></div>
      <strong class="mono-bn"><?= bn($stats['supports']) ?></strong><small>রিপোর্টে দেওয়া সমর্থন</small>
    </div>
    <div class="dash-stat tone-amber">
      <div class="dash-stat-head"><span>অনুদান দিয়েছি</span><?= icon('wallet', 'icon-sm') ?></div>
      <strong class="mono-bn"><?= taka($stats['donation_total']) ?></strong><small><?= bn($stats['campaign_count']) ?>টি কার্যক্রমে</small>
    </div>
  </div>

  <div class="dash-grid">
    <div>
      <div class="panel">
        <div class="panel-head">
          <div><h2>আমার সাম্প্রতিক রিপোর্ট</h2><p>আপনার জমা দেওয়া সর্বশেষ রিপোর্টগুলো</p></div>
          <a class="panel-link" href="my-reports.php">সব দেখুন <?= icon('arrow-right', 'icon-xs') ?></a>
        </div>
        <div class="panel-body">
          <?php if (!$myIssues): ?>
            <?= empty_state('এখনও কোনো রিপোর্ট নেই', 'আপনার এলাকার প্রথম সমস্যাটি ছবি ও অবস্থানসহ জমা দিন।', 'file-text', '<a class="btn btn-primary btn-sm mt-2" href="report.php">নতুন রিপোর্ট করুন</a>') ?>
          <?php else: ?>
            <?php foreach ($myIssues as $issue): ?>
              <div class="mini-report">
                <img class="mini-report-thumb" src="<?= e($issue['image']) ?>" alt="<?= e(category_meta($issue['type'])['label']) ?>">
                <div class="mini-report-body">
                  <h4><a href="issue.php?id=<?= (int) $issue['id'] ?>"><?= e($issue['title']) ?></a></h4>
                  <span><?= icon('map-pin', 'icon-xs') ?> <?= e($issue['area']) ?> • <?= icon('users', 'icon-xs') ?> <?= bn($issue['support']) ?> সমর্থন</span>
                </div>
                <?= status_badge($issue['status']) ?>
              </div>
            <?php endforeach; ?>
            <p class="muted-note">আপনার সর্বশেষ রিপোর্টগুলোর সর্বশেষ অবস্থা উপরে দেখানো হচ্ছে। প্রতিটি পরিবর্তন ডেটাবেজে সংরক্ষিত থাকে।</p>
          <?php endif; ?>
        </div>
      </div>

      <div class="panel">
        <div class="panel-head">
          <div><h2>সাম্প্রতিক কার্যক্রম</h2><p>আপনার অ্যাকাউন্টের সর্বশেষ ঘটনাগুলো</p></div>
          <a class="panel-link" href="notifications.php">নোটিফিকেশন <?= icon('arrow-right', 'icon-xs') ?></a>
        </div>
        <div class="activity-list">
          <div class="activity-item">
            <span class="activity-icon blue"><?= icon('tools', 'icon-sm') ?></span>
            <div class="activity-text"><p>“মিরপুর ১০ গোলচত্বরে বড় গর্ত” রিপোর্টের অবস্থা <strong>সমাধানাধীন</strong>-এ পরিবর্তিত হয়েছে।</p><span>মিরপুর ১০, ঢাকা</span></div>
            <span class="activity-time">২ ঘণ্টা</span>
          </div>
          <div class="activity-item">
            <span class="activity-icon"><?= icon('check-circle', 'icon-sm') ?></span>
            <div class="activity-text"><p>রামপুরা স্কুলের সামনের সড়ক মেরামত সম্পন্ন হয়েছে—আগে ও পরের ছবি দেখুন।</p><span>রামপুরা, ঢাকা</span></div>
            <span class="activity-time">১ দিন</span>
          </div>
          <div class="activity-item">
            <span class="activity-icon amber"><?= icon('heart', 'icon-sm') ?></span>
            <div class="activity-text"><p>কাজীপাড়ার ড্রেন প্রকল্পে আপনার ২,০০০ টাকার অনুদান যাচাই সম্পন্ন হয়েছে।</p><span>লেনদেন-৮৮৪২</span></div>
            <span class="activity-time">৩ দিন</span>
          </div>
          <div class="activity-item">
            <span class="activity-icon blue"><?= icon('message-circle', 'icon-sm') ?></span>
            <div class="activity-text"><p>নুসরাত জাহান আপনার মন্তব্যের উত্তর দিয়েছেন।</p><span>মিরপুর ১০ গোলচত্বর</span></div>
            <span class="activity-time">৪ দিন</span>
          </div>
        </div>
      </div>
    </div>

    <div>
      <div class="panel">
        <div class="panel-head"><h2>দ্রুত কাজ</h2></div>
        <div class="panel-body">
          <div class="quick-actions">
            <a class="quick-action" href="report.php"><?= icon('camera', 'icon-sm') ?><strong>নতুন রিপোর্ট</strong><span>ছবিসহ সমস্যা জানান</span></a>
            <a class="quick-action" href="issues.php"><?= icon('search', 'icon-sm') ?><strong>সমস্যা খুঁজুন</strong><span>এলাকার রিপোর্ট দেখুন</span></a>
            <a class="quick-action" href="map.php"><?= icon('map', 'icon-sm') ?><strong>মানচিত্র দেখুন</strong><span>আশপাশের সমস্যা</span></a>
            <a class="quick-action" href="donate.php"><?= icon('heart', 'icon-sm') ?><strong>অনুদান দিন</strong><span>কমিউনিটি উদ্যোগে</span></a>
          </div>
        </div>
      </div>

      <div class="panel">
        <div class="panel-head"><h2>নোটিফিকেশন</h2><a class="panel-link" href="notifications.php">সব</a></div>
        <div class="panel-body">
          <div class="inline-list">
            <?php foreach (array_slice($notifications, 0, 3) as $n): ?>
              <a class="inline-list-item" href="<?= e($n['link']) ?>" style="text-decoration:none">
                <span class="list-icon"><?= icon($n['icon'], 'icon-sm') ?></span>
                <div class="list-text"><strong><?= e($n['title']) ?></strong><span><?= e($n['time']) ?></span></div>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <div class="panel">
        <div class="panel-head"><h2>আপনার অবদান</h2></div>
        <div class="panel-body">
          <div class="mini-stat-grid">
            <div class="mini-stat"><strong><?= bn($stats['reports']) ?></strong><span>রিপোর্ট</span></div>
            <div class="mini-stat"><strong><?= bn($stats['supports']) ?></strong><span>সমর্থন</span></div>
            <div class="mini-stat"><strong><?= bn($stats['donation_count']) ?></strong><span>অনুদান</span></div>
          </div>
          <div class="dash-reputation" style="margin-bottom:0"><span><?= icon('award', 'icon-xs') ?> সক্রিয় নাগরিক</span><strong>লেভেল ৩</strong></div>
          <p class="muted-note" style="margin-top:10px">আরও ৩টি যাচাইকৃত রিপোর্ট দিলে লেভেল ৪-এ উন্নীত হবেন।</p>
        </div>
      </div>
    </div>
  </div>

  </div>
</div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
