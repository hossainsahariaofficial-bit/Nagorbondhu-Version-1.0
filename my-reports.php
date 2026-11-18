<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_citizen();
$pageTitle = 'আমার রিপোর্ট — নগরবন্ধু';
$dashPage = 'my-reports.php';
$dashTitle = 'আমার রিপোর্ট';
$citizen = current_citizen();
$rows = user_issues((int) $citizen['id']);
$totalReports = count($rows);
$verifyingReports = count(array_filter($rows, fn ($i) => in_array($i['status'], ['new', 'verifying'], true)));
$workingReports = count(array_filter($rows, fn ($i) => in_array($i['status'], ['verified', 'working'], true)));
$solvedReports = count(array_filter($rows, fn ($i) => $i['status'] === 'solved'));
include __DIR__ . '/includes/header.php';
?>
<div class="container">
  <?php include __DIR__ . '/includes/dashboard-sidebar.php'; ?>

  <div class="dash-welcome">
    <div>
      <span class="dash-date"><?= icon('file-text', 'icon-xs') ?> সর্বমোট <?= bn($totalReports) ?>টি রিপোর্ট</span>
      <h1>আমার রিপোর্ট</h1>
      <p>আপনার জমা দেওয়া সব রিপোর্টের অবস্থা, সমর্থন ও অগ্রগতি এক জায়গায় দেখুন।</p>
    </div>
    <a class="btn btn-primary" href="report.php"><?= icon('plus', 'icon-sm') ?> নতুন রিপোর্ট</a>
  </div>

  <div class="dash-stats">
    <div class="dash-stat tone-green"><div class="dash-stat-head"><span>মোট রিপোর্ট</span><?= icon('file-text', 'icon-sm') ?></div><strong><?= bn($totalReports) ?></strong><small>ডেটাবেজে সংরক্ষিত</small></div>
    <div class="dash-stat tone-amber"><div class="dash-stat-head"><span>যাচাই চলছে</span><?= icon('clock', 'icon-sm') ?></div><strong><?= bn($verifyingReports) ?></strong><small>নতুন ও যাচাইাধীন</small></div>
    <div class="dash-stat tone-violet"><div class="dash-stat-head"><span>সমাধানাধীন</span><?= icon('tools', 'icon-sm') ?></div><strong><?= bn($workingReports) ?></strong><small>কাজ চলছে</small></div>
    <div class="dash-stat tone-blue"><div class="dash-stat-head"><span>সমাধান সম্পন্ন</span><?= icon('check-circle', 'icon-sm') ?></div><strong><?= bn($solvedReports) ?></strong><small>সম্পন্ন হয়েছে</small></div>
  </div>

  <div class="table-card" id="myReportsScope">
    <div class="table-card-head">
      <div><h2>রিপোর্টের তালিকা</h2><p>আপনার জমা দেওয়া সব রিপোর্ট ডেটাবেজ থেকে দেখানো হচ্ছে</p></div>
      <div class="table-tools">
        <div class="search-input">
          <?= icon('search', 'icon-sm') ?>
          <label class="sr-only" for="myReportSearch">রিপোর্ট খুঁজুন</label>
          <input class="input" id="myReportSearch" type="search" placeholder="শিরোনাম বা এলাকা খুঁজুন…" data-table-search>
        </div>
        <label class="sr-only" for="myReportStatus">অবস্থা অনুযায়ী ফিল্টার</label>
        <select class="select" id="myReportStatus" data-table-filter="status">
          <option value="">সব অবস্থা</option>
          <option value="সমাধান সম্পন্ন">সমাধান সম্পন্ন</option>
          <option value="সমাধানাধীন">সমাধানাধীন</option>
          <option value="যাচাই">যাচাই</option>
          <option value="জমা">জমা দেওয়া</option>
        </select>
        <label class="sr-only" for="myReportSize">প্রতি পৃষ্ঠায়</label>
        <select class="select" id="myReportSize" data-table-size>
          <option value="5">৫টি</option>
          <option value="10" selected>১০টি</option>
          <option value="20">২০টি</option>
        </select>
      </div>
    </div>
    <div class="table-scroll">
      <table class="data-table admin-table" data-admin-table="#myReportsScope">
        <thead>
          <tr>
            <th scope="col">রিপোর্ট</th>
            <th scope="col">এলাকা</th>
            <th scope="col">জমার তারিখ</th>
            <th scope="col">অবস্থা</th>
            <th scope="col">সমর্থন</th>
            <th scope="col" class="cell-actions">কার্যক্রম</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $issue): ?>
            <tr data-status="<?= e(status_meta($issue['status'])['label']) ?>">
              <td>
                <div class="cell-issue">
                  <img class="cell-thumb" src="<?= e($issue['image']) ?>" alt="">
                  <div><span class="cell-main"><?= e(excerpt($issue['title'], 46)) ?></span><span class="cell-sub"><?= e($issue['code']) ?></span></div>
                </div>
              </td>
              <td><span class="cell-main" style="font-weight:500"><?= e($issue['area']) ?></span><span class="cell-sub"><?= e($issue['district']) ?></span></td>
              <td><?= e($issue['date']) ?></td>
              <td class="cell-badge"><?= status_badge($issue['status']) ?></td>
              <td><span class="cell-main"><?= bn($issue['support']) ?> জন</span></td>
              <td class="cell-actions">
                <a class="btn btn-sm btn-outline" href="issue.php?id=<?= (int) $issue['id'] ?>">বিস্তারিত</a>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$rows): ?>
            <tr><td colspan="6"><div class="admin-empty"><?= icon('file-text', 'icon-md') ?><p>আপনি এখনও কোনো রিপোর্ট জমা দেননি। প্রথম রিপোর্টটি তৈরি করুন।</p><a class="btn btn-primary btn-sm mt-2" href="report.php">নতুন রিপোর্ট করুন</a></div></td></tr>
          <?php endif; ?>
          <tr data-empty data-empty-row hidden><td colspan="6"><div class="admin-empty"><?= icon('search', 'icon-md') ?><p>আপনার অনুসন্ধান বা ফিল্টারে কোনো রিপোর্ট মেলেনি।</p></div></td></tr>
        </tbody>
      </table>
    </div>
    <div class="table-foot">
      <p data-table-info>দেখানো হচ্ছে ১–<?= bn($totalReports) ?> (মোট <?= bn($totalReports) ?>টি)</p>
      <div class="pagination" data-table-pagination aria-label="রিপোর্ট পৃষ্ঠা"></div>
    </div>
  </div>

  <div class="panel" style="margin-top:20px">
    <div class="panel-body row-between">
      <div>
        <h3 style="font-size:.92rem;margin-bottom:3px">একই সমস্যা আগে রিপোর্ট হয়েছে?</h3>
        <p class="muted-note">নতুন রিপোর্ট করার আগে অনুসন্ধান করুন। একই সমস্যায় সমর্থন দিলে দ্রুত সমাধান হয়।</p>
      </div>
      <a class="btn btn-outline btn-sm" href="issues.php"><?= icon('search', 'icon-sm') ?> রিপোর্ট খুঁজুন</a>
    </div>
  </div>

  </div>
</div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
