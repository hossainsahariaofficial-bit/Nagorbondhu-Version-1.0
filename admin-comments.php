<?php
require_once __DIR__ . '/includes/bootstrap.php';

$adminPage = 'admin-comments.php';
$adminTitle = 'মন্তব্য ও অভিযোগ';

$rawComments = nb_all_comments();
$allIssues = nb_issues();
$comments = [];
$todayCount = 0;
$todayPrefix = date('Y-m-d');
foreach ($rawComments as $comment) {
    $comment['status'] = 'অনুমোদিত';
    $comment['tone'] = 'success';
    $comment['flag'] = '';
    $comment['issue'] = $allIssues[$comment['issue_id']] ?? nb_issue($comment['issue_id']);
    if (str_starts_with((string) $comment['created_at'], $todayPrefix)) {
        $todayCount++;
    }
    $comments[] = $comment;
}

$statusFilter = $_GET['status'] ?? '';
if ($statusFilter !== '') {
    $comments = array_values(array_filter($comments, fn ($c) => $c['status'] === $statusFilter));
}

$allComments = count($rawComments);
$pendingCount = 0;
$hiddenCount = 0;

include __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-page-head">
  <div>
    <div class="admin-breadcrumb-lite"><a href="admin.php">প্রশাসনিক প্যানেল</a><?= icon('chevron-right') ?><span>মন্তব্য ও অভিযোগ</span></div>
    <h2>মন্তব্য পর্যালোচনা</h2>
    <p>নাগরিকদের আলোচনা পর্যবেক্ষণ করুন, পতাকাঙ্কিত মন্তব্য যাচাই করুন এবং সম্প্রদায়ের পরিবেশ সুরক্ষিত রাখুন।</p>
  </div>
  <div class="admin-page-head-actions">
    <button class="btn btn-outline btn-sm" type="button" data-export-table="#commentsTable" data-export-name="নগরবন্ধু-মন্তব্য.csv"><?= icon('download', 'icon-sm') ?> এক্সপোর্ট</button>
    <a class="btn btn-primary btn-sm" href="admin-settings.php"><?= icon('shield', 'icon-sm') ?> মডারেশন নীতি</a>
  </div>
</div>

<div class="admin-stat-grid">
  <div class="admin-stat accent">
    <div class="admin-stat-top"><span class="admin-stat-label">মোট মন্তব্য</span><span class="admin-stat-icon"><?= icon('message-circle', 'icon-sm') ?></span></div>
    <div class="admin-stat-value"><?= bn((string) $allComments) ?></div>
    <div class="admin-stat-foot">ডেটাবেজে সংরক্ষিত</div>
  </div>
  <div class="admin-stat">
    <div class="admin-stat-top"><span class="admin-stat-label">আজকের মন্তব্য</span><span class="admin-stat-icon blue"><?= icon('activity', 'icon-sm') ?></span></div>
    <div class="admin-stat-value"><?= bn((string) $todayCount) ?></div>
    <div class="admin-stat-foot">আজ প্রকাশিত মন্তব্য</div>
  </div>
  <div class="admin-stat">
    <div class="admin-stat-top"><span class="admin-stat-label">পর্যালোচনার অপেক্ষায়</span><span class="admin-stat-icon amber"><?= icon('flag', 'icon-sm') ?></span></div>
    <div class="admin-stat-value"><?= bn($pendingCount) ?></div>
    <div class="admin-stat-foot">পতাকাঙ্কিত মন্তব্য</div>
  </div>
  <div class="admin-stat">
    <div class="admin-stat-top"><span class="admin-stat-label">লুকানো মন্তব্য</span><span class="admin-stat-icon rose"><?= icon('eye-off', 'icon-sm') ?></span></div>
    <div class="admin-stat-value"><?= bn((string) $hiddenCount) ?></div>
    <div class="admin-stat-foot">নীতিমালা লঙ্ঘনে লুকানো</div>
  </div>
</div>

<section class="admin-panel" id="commentsPanel">
  <div class="admin-tabs" role="tablist" aria-label="মন্তব্যের অবস্থা">
    <a class="admin-tab <?= $statusFilter === '' ? 'active' : '' ?>" href="admin-comments.php">সব মন্তব্য <span class="count"><?= bn($allComments) ?></span></a>
    <a class="admin-tab <?= $statusFilter === 'পর্যালোচনা প্রয়োজন' ? 'active' : '' ?>" href="admin-comments.php?status=<?= rawurlencode('পর্যালোচনা প্রয়োজন') ?>">পর্যালোচনার অপেক্ষায় <span class="count"><?= bn($pendingCount) ?></span></a>
    <a class="admin-tab <?= $statusFilter === 'অনুমোদিত' ? 'active' : '' ?>" href="admin-comments.php?status=<?= rawurlencode('অনুমোদিত') ?>">অনুমোদিত <span class="count"><?= bn((string) $allComments) ?></span></a>
    <a class="admin-tab <?= $statusFilter === 'লুকানো' ? 'active' : '' ?>" href="admin-comments.php?status=<?= rawurlencode('লুকানো') ?>">লুকানো <span class="count"><?= bn($hiddenCount) ?></span></a>
  </div>

  <div class="admin-toolbar">
    <div class="toolbar-left">
      <div class="search-input">
        <?= icon('search') ?>
        <label class="sr-only" for="commentSearch">মন্তব্য খুঁজুন</label>
        <input class="input" id="commentSearch" type="search" placeholder="মন্তব্য, নাগরিক বা রিপোর্ট…" data-table-search>
      </div>
      <label class="sr-only" for="commentStatus">অবস্থা</label>
      <select class="select" id="commentStatus" data-table-filter="status">
        <option value="">সব অবস্থা</option>
        <option value="পর্যালোচনা প্রয়োজন">পর্যালোচনার অপেক্ষায়</option>
        <option value="অনুমোদিত">অনুমোদিত</option>
        <option value="লুকানো">লুকানো</option>
      </select>
      <label class="sr-only" for="commentFlag">কারণ</label>
      <select class="select" id="commentFlag" data-table-filter="flag">
        <option value="">সব কারণ</option>
        <option value="ব্যক্তিগত আক্রমণ">ব্যক্তিগত আক্রমণ</option>
        <option value="স্প্যাম">স্প্যাম / বিজ্ঞাপন</option>
        <option value="অপ্রাসঙ্গিক">অপ্রাসঙ্গিক তথ্য</option>
      </select>
      <label class="sr-only" for="commentSize">প্রতি পৃষ্ঠায়</label>
      <select class="select" id="commentSize" data-table-size>
        <option value="10">১০টি</option>
        <option value="25">২৫টি</option>
        <option value="50">৫০টি</option>
      </select>
    </div>
    <div class="toolbar-right">
      <span class="badge badge-warning"><?= icon('flag', 'icon-xs') ?> <?= bn($pendingCount) ?>টি পর্যালোচনা প্রয়োজন</span>
    </div>
  </div>

  <div class="bulk-bar" data-bulk-bar>
    <strong><span data-bulk-count>০</span>টি মন্তব্য নির্বাচিত</strong>
    <button class="btn btn-sm btn-primary" type="button" data-admin-action="approve"><?= icon('check', 'icon-sm') ?> অনুমোদন</button>
    <button class="btn btn-sm btn-danger-outline" type="button" data-admin-action="hide"><?= icon('eye-off', 'icon-sm') ?> লুকান</button>
  </div>

  <div class="table-scroll admin-table-wrap">
    <table class="data-table admin-table" id="commentsTable" data-admin-table="#commentsPanel">
      <thead>
        <tr>
          <th class="table-check"><input class="db-check" type="checkbox" data-select-all aria-label="সব মন্তব্য নির্বাচন করুন"></th>
          <th style="min-width:320px">মন্তব্য</th>
          <th>নাগরিক</th>
          <th>সংশ্লিষ্ট রিপোর্ট</th>
          <th>পতাকা</th>
          <th>লাইক</th>
          <th>অবস্থা</th>
          <th>তারিখ</th>
          <th class="cell-actions">কার্যক্রম</th>
        </tr>
      </thead>
      <tbody>
        <tr data-empty data-empty-row><td colspan="9"><?= empty_state('কোনো মন্তব্য পাওয়া যায়নি', 'এই ফিল্টারে কোনো মন্তব্য নেই।', 'message-circle') ?></td></tr>
        <?php foreach ($comments as $comment): ?>
          <tr data-status="<?= e($comment['status']) ?>" data-flag="<?= e($comment['flag']) ?>">
            <td class="table-check"><input class="db-check" type="checkbox" value="<?= e($comment['name']) ?>" aria-label="মন্তব্য নির্বাচন করুন"></td>
            <td>
              <p style="font-size:.75rem;line-height:1.65;color:var(--ink-2);max-width:380px">“<?= e($comment['body']) ?>”</p>
              <span class="table-sub"><?= icon('heart', 'icon-xs') ?> <?= bn($comment['likes']) ?> জন পছন্দ করেছেন</span>
            </td>
            <td>
              <div class="cell-person">
                <?= avatar($comment['initials'], $comment['color'], 'sm') ?>
                <div><strong><?= e($comment['name']) ?></strong><span><?= $comment['verified'] ? 'যাচাইকৃত নাগরিক' : 'নিবন্ধিত নাগরিক' ?></span></div>
              </div>
            </td>
            <td><a class="panel-link" href="issue.php?id=<?= (int) $comment['issue']['id'] ?>" target="_blank" rel="noopener"><?= e($comment['issue']['code']) ?></a><span class="table-sub"><?= e(excerpt($comment['issue']['title'], 34)) ?></span></td>
            <td><?php if ($comment['flag'] !== ''): ?><span class="badge badge-warning"><?= icon('flag', 'icon-xs') ?> <?= e($comment['flag']) ?></span><?php else: ?><span class="table-sub">—</span><?php endif; ?></td>
            <td class="table-sub"><?= bn($comment['likes']) ?></td>
            <td><span class="badge badge-<?= e($comment['tone']) ?>"><span class="status-dot"></span><?= e($comment['status']) ?></span></td>
            <td class="table-sub"><?= e($comment['date']) ?></td>
            <td class="cell-actions">
              <div class="row-menu" data-row-menu>
                <button class="row-menu-btn icon-btn" type="button" aria-haspopup="true" aria-expanded="false" aria-label="কার্যক্রম মেনু"><?= icon('more-horizontal', 'icon-sm') ?></button>
                <div class="row-dropdown">
                  <a href="issue.php?id=<?= (int) $comment['issue']['id'] ?>" target="_blank" rel="noopener"><?= icon('external-link', 'icon-sm') ?> প্রসঙ্গে দেখুন</a>
                  <button type="button" data-admin-action="approve"><?= icon('check', 'icon-sm') ?> অনুমোদন করুন</button>
                  <button type="button" data-admin-action="hide"><?= icon('eye-off', 'icon-sm') ?> লুকান</button>
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
  <div class="table-foot"><p data-table-info>দেখানো হচ্ছে <?= bn(count($comments)) ?>টি মন্তব্য</p><div class="pagination" data-table-pagination></div></div>
</section>

<div class="admin-grid cols-2" style="margin-top:16px">
  <section class="admin-panel">
    <div class="admin-panel-head"><div><h3>মডারেশন নির্দেশিকা</h3><p>মন্তব্য লুকানো বা মুছার আগে বিবেচনা করুন</p></div></div>
    <div class="admin-panel-body">
      <div style="display:flex;flex-direction:column;gap:11px;font-size:.76rem;line-height:1.7;color:var(--ink-2)">
        <p><?= icon('check-circle', 'icon-sm') ?> <strong>অনুমোদন করুন:</strong> সমস্যার বিষয়ে তথ্যবহুল, সম্মানজনক ও স্থানীয় অভিজ্ঞতা ভাগ করে এমন মন্তব্য।</p>
        <p><?= icon('eye-off', 'icon-sm') ?> <strong>লুকান:</strong> ব্যক্তিগত আক্রমণ, অশ্লীল ভাষা বা অপ্রমাণিত অভিযোগ।</p>
        <p><?= icon('trash', 'icon-sm') ?> <strong>মুছুন:</strong> স্প্যাম, বিজ্ঞাপন বা বারবার পোস্ট করা একই মন্তব্য।</p>
      </div>
    </div>
  </section>
  <section class="admin-panel">
    <div class="admin-panel-head"><div><h3>সক্রিয় মডারেটর</h3><p>আজকের পর্যালোচনা দল</p></div></div>
    <div class="activity-list">
      <div class="activity-item"><span class="activity-icon green"><?= icon('shield', 'icon-sm') ?></span><div class="activity-text"><p><strong>ফাহমিদা আক্তার</strong> — ১৮টি মন্তব্য পর্যালোচনা</p><span>প্রধান মডারেটর</span></div><span class="activity-time">সক্রিয়</span></div>
      <div class="activity-item"><span class="activity-icon blue"><?= icon('shield', 'icon-sm') ?></span><div class="activity-text"><p><strong>তাসনিম আরা</strong> — ১১টি মন্তব্য পর্যালোচনা</p><span>সহকারী মডারেটর</span></div><span class="activity-time">সক্রিয়</span></div>
      <div class="activity-item"><span class="activity-icon amber"><?= icon('shield', 'icon-sm') ?></span><div class="activity-text"><p><strong>সাইফুল ইসলাম</strong> — ৭টি মন্তব্য পর্যালোচনা</p><span>কমিউনিটি মডারেটর</span></div><span class="activity-time">বিরতিতে</span></div>
    </div>
  </section>
</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
