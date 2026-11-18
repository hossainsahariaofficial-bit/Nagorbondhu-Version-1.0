<?php
require_once __DIR__ . '/includes/bootstrap.php';

$adminPage = 'admin-issues.php';
$adminTitle = 'সমস্যা ব্যবস্থাপনা';

$allIssues = nb_issues();
$statusFilter = $_GET['status'] ?? '';
$query = trim((string) ($_GET['q'] ?? ''));

$issues = array_filter($allIssues, function ($issue) use ($statusFilter, $query) {
    if ($statusFilter !== '' && $issue['status'] !== $statusFilter) return false;
    if ($query !== '') {
        $haystack = $issue['title'] . ' ' . $issue['code'] . ' ' . $issue['area'] . ' ' . $issue['reporter'];
        if (mb_stripos($haystack, $query) === false) return false;
    }
    return true;
});

$statusCounts = ['all' => count($allIssues)];
foreach ($allIssues as $issue) {
    $statusCounts[$issue['status']] = ($statusCounts[$issue['status']] ?? 0) + 1;
}
$tabMap = [
    '' => ['সব রিপোর্ট', 'all'],
    'new' => ['জমা দেওয়া', 'new'],
    'verifying' => ['যাচাই চলছে', 'verifying'],
    'verified' => ['যাচাই সম্পন্ন', 'verified'],
    'working' => ['সমাধানাধীন', 'working'],
    'solved' => ['সমাধান সম্পন্ন', 'solved'],
    'rejected' => ['প্রত্যাখ্যাত', 'rejected'],
];

include __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-page-head">
  <div>
    <div class="admin-breadcrumb-lite"><a href="admin.php">প্রশাসনিক প্যানেল</a><?= icon('chevron-right') ?><span>সমস্যা ব্যবস্থাপনা</span></div>
    <h2>সকল নাগরিক রিপোর্ট</h2>
    <p>রিপোর্ট খুঁজুন, ফিল্টার করুন, যাচাই করুন এবং কর্তৃপক্ষকে বরাদ্দ দিন।</p>
  </div>
  <div class="admin-page-head-actions">
    <a class="btn btn-outline btn-sm" href="admin-reports.php"><?= icon('bar-chart', 'icon-sm') ?> বিশ্লেষণ</a>
    <a class="btn btn-primary btn-sm" href="report.php"><?= icon('plus', 'icon-sm') ?> নতুন রিপোর্ট</a>
  </div>
</div>

<div class="admin-stat-grid">
  <div class="admin-stat">
    <div class="admin-stat-top"><span class="admin-stat-label">মোট রিপোর্ট</span><span class="admin-stat-icon green"><?= icon('file-text', 'icon-sm') ?></span></div>
    <div class="admin-stat-value"><?= bn(count($allIssues)) ?></div>
    <div class="admin-stat-foot"><span class="up"><?= icon('trending-up') ?> ৪টি</span> এই সপ্তাহে নতুন</div>
  </div>
  <div class="admin-stat">
    <div class="admin-stat-top"><span class="admin-stat-label">যাচাইয়ের অপেক্ষায়</span><span class="admin-stat-icon amber"><?= icon('clock', 'icon-sm') ?></span></div>
    <div class="admin-stat-value"><?= bn(($statusCounts['new'] ?? 0) + ($statusCounts['verifying'] ?? 0)) ?></div>
    <div class="admin-stat-foot">গড় অপেক্ষা <strong><?= bn('১.৮') ?> দিন</strong></div>
  </div>
  <div class="admin-stat">
    <div class="admin-stat-top"><span class="admin-stat-label">সমাধানাধীন</span><span class="admin-stat-icon violet"><?= icon('tools', 'icon-sm') ?></span></div>
    <div class="admin-stat-value"><?= bn($statusCounts['working'] ?? 0) ?></div>
    <div class="admin-stat-foot">কর্তৃপক্ষের কাছে বরাদ্দকৃত</div>
  </div>
  <div class="admin-stat">
    <div class="admin-stat-top"><span class="admin-stat-label">সমাধান সম্পন্ন</span><span class="admin-stat-icon blue"><?= icon('check-circle', 'icon-sm') ?></span></div>
    <div class="admin-stat-value"><?= bn($statusCounts['solved'] ?? 0) ?></div>
    <div class="admin-stat-foot"><span class="up"><?= icon('trending-up') ?> <?= bn(progress_percent($statusCounts['solved'] ?? 0, count($allIssues))) ?>%</span> সফলতার হার</div>
  </div>
</div>

<section class="admin-panel" id="issuesPanel">
  <div class="admin-tabs" role="tablist" aria-label="রিপোর্টের অবস্থা">
    <?php foreach ($tabMap as $key => [$label, $countKey]): ?>
      <?php $isActive = ($statusFilter === $key); ?>
      <a class="admin-tab <?= $isActive ? 'active' : '' ?>" href="admin-issues.php<?= $key !== '' ? '?status=' . e($key) : '' ?>" <?= $isActive ? 'aria-current="page"' : '' ?>>
        <?= e($label) ?><span class="count"><?= bn($statusCounts[$countKey] ?? 0) ?></span>
      </a>
    <?php endforeach; ?>
  </div>

  <div class="admin-toolbar">
    <div class="toolbar-left">
      <div class="search-input">
        <?= icon('search') ?>
        <label class="sr-only" for="issueSearch">রিপোর্ট খুঁজুন</label>
        <input class="input" id="issueSearch" type="search" placeholder="শিরোনাম, নম্বর বা নাগরিক…" data-table-search>
      </div>
      <label class="sr-only" for="filterStatus">অবস্থা</label>
      <select class="select" id="filterStatus" data-table-filter="status">
        <option value="">সব অবস্থা</option>
        <?php foreach (nb_statuses() as $key => $meta): ?>
          <option value="<?= e($key) ?>"><?= e($meta['label']) ?></option>
        <?php endforeach; ?>
      </select>
      <label class="sr-only" for="filterType">ধরন</label>
      <select class="select" id="filterType" data-table-filter="type">
        <option value="">সব ধরন</option>
        <?php foreach (nb_categories() as $key => $cat): ?>
          <option value="<?= e($key) ?>"><?= e($cat['label']) ?></option>
        <?php endforeach; ?>
      </select>
      <label class="sr-only" for="filterUrgency">জরুরি মাত্রা</label>
      <select class="select" id="filterUrgency" data-table-filter="urgency">
        <option value="">সব অগ্রাধিকার</option>
        <?php foreach (nb_urgency() as $key => $label): ?>
          <option value="<?= e($key) ?>"><?= e($label) ?></option>
        <?php endforeach; ?>
      </select>
      <label class="sr-only" for="filterSize">প্রতি পৃষ্ঠায়</label>
      <select class="select" id="filterSize" data-table-size>
        <option value="10">১০টি</option>
        <option value="25">২৫টি</option>
        <option value="50">৫০টি</option>
      </select>
    </div>
    <div class="toolbar-right">
      <button class="btn btn-outline btn-sm" type="button" data-export-table="#issuesTable" data-export-name="নগরবন্ধু-রিপোর্ট.csv"><?= icon('download', 'icon-sm') ?> এক্সপোর্ট</button>
      <button class="btn btn-outline btn-sm" type="button" data-print><?= icon('file-text', 'icon-sm') ?> প্রিন্ট</button>
    </div>
  </div>

  <div class="bulk-bar" data-bulk-bar>
    <strong><span data-bulk-count>০</span>টি রিপোর্ট নির্বাচিত</strong>
    <button class="btn btn-sm btn-primary" type="button" data-admin-action="approve"><?= icon('check', 'icon-sm') ?> অনুমোদন</button>
    <button class="btn btn-sm btn-outline" type="button" data-admin-action="return"><?= icon('refresh', 'icon-sm') ?> ফেরত দিন</button>
    <button class="btn btn-sm btn-danger-outline" type="button" data-admin-action="reject"><?= icon('x', 'icon-sm') ?> প্রত্যাখ্যান</button>
    <button class="btn btn-sm btn-outline" type="button" data-export-table="#issuesTable"><?= icon('download', 'icon-sm') ?> এক্সপোর্ট</button>
  </div>

  <div class="table-scroll admin-table-wrap">
    <table class="data-table admin-table" id="issuesTable" data-admin-table="#issuesPanel">
      <thead>
        <tr>
          <th class="table-check"><input class="db-check" type="checkbox" data-select-all aria-label="সব রিপোর্ট নির্বাচন করুন"></th>
          <th class="cell-issue">রিপোর্ট</th>
          <th>নাগরিক</th>
          <th>ধরন</th>
          <th>অগ্রাধিকার</th>
          <th>অবস্থা</th>
          <th>সমর্থন</th>
          <th>তারিখ</th>
          <th class="cell-actions">কার্যক্রম</th>
        </tr>
      </thead>
      <tbody>
        <tr data-empty data-empty-row <?= $issues ? 'hidden' : '' ?>>
          <td colspan="9"><?= empty_state('কোনো রিপোর্ট পাওয়া যায়নি', 'এই ফিল্টারে কোনো রিপোর্ট নেই। অন্য অবস্থা বা ধরন নির্বাচন করে দেখুন।', 'inbox') ?></td>
        </tr>
        <?php foreach ($issues as $issue): ?>
          <tr data-status="<?= e($issue['status']) ?>" data-type="<?= e($issue['type']) ?>" data-urgency="<?= e($issue['urgency']) ?>" data-area="<?= e($issue['area']) ?>">
            <td class="table-check"><input class="db-check" type="checkbox" value="<?= (int) $issue['id'] ?>" aria-label="<?= e($issue['code']) ?> নির্বাচন করুন"></td>
            <td class="cell-issue">
              <div class="cell-person">
                <img class="cell-thumb" src="<?= e($issue['image']) ?>" alt="" loading="lazy">
                <div>
                  <a class="cell-main" href="admin-verify.php?id=<?= (int) $issue['id'] ?>"><?= e(excerpt($issue['title'], 52)) ?></a>
                  <span class="cell-sub"><?= e($issue['code']) ?> · <?= e($issue['area']) ?>, <?= e($issue['district']) ?></span>
                </div>
              </div>
            </td>
            <td>
              <div class="cell-person">
                <?= avatar($issue['reporter'] === '' ? '?' : mb_substr($issue['reporter'], 0, 1), 'green', 'sm') ?>
                <div><strong><?= e($issue['reporter']) ?></strong><span><?= e($issue['reporter_area']) ?></span></div>
              </div>
            </td>
            <td><?= category_badge($issue['type']) ?></td>
            <td><?= urgency_badge($issue['urgency']) ?></td>
            <td><?= status_badge($issue['status']) ?></td>
            <td><span class="cell-support" style="font-size:.72rem;color:var(--muted)"><?= bn($issue['support']) ?> জন</span></td>
            <td class="table-sub"><?= e($issue['date']) ?></td>
            <td class="cell-actions">
              <div class="row-menu" data-row-menu>
                <button class="row-menu-btn icon-btn" type="button" aria-haspopup="true" aria-expanded="false" aria-label="কার্যক্রম মেনু"><?= icon('more-horizontal', 'icon-sm') ?></button>
                <div class="row-dropdown">
                  <a href="issue.php?id=<?= (int) $issue['id'] ?>" target="_blank" rel="noopener"><?= icon('eye', 'icon-sm') ?> পাবলিক পেজ দেখুন</a>
                  <a href="admin-verify.php?id=<?= (int) $issue['id'] ?>"><?= icon('check-square', 'icon-sm') ?> যাচাই করুন</a>
                  <a href="admin-progress.php?id=<?= (int) $issue['id'] ?>"><?= icon('activity', 'icon-sm') ?> অগ্রগতি হালনাগাদ</a>
                  <a href="admin-authorities.php"><?= icon('landmark', 'icon-sm') ?> কর্তৃপক্ষকে বরাদ্দ</a>
                  <div class="dropdown-sep"></div>
                  <button class="danger-item" type="button" data-admin-action="reject"><?= icon('ban', 'icon-sm') ?> প্রত্যাখ্যান করুন</button>
                </div>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="table-foot">
    <p data-table-info>দেখানো হচ্ছে <?= bn(count($issues)) ?>টি রিপোর্ট</p>
    <div class="pagination" data-table-pagination aria-label="পৃষ্ঠা পরিবর্তন"></div>
  </div>
</section>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
