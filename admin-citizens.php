<?php
require_once __DIR__ . '/includes/bootstrap.php';

$adminPage = 'admin-citizens.php';
$adminTitle = 'নাগরিক ব্যবস্থাপনা';

$citizens = nb_citizens();
$activeCount = count(array_filter($citizens, fn ($c) => $c['status'] === 'সক্রিয়'));
$limitedCount = count(array_filter($citizens, fn ($c) => $c['status'] === 'সীমিত'));
$suspendedCount = count(array_filter($citizens, fn ($c) => $c['status'] === 'স্থগিত'));
$totalReports = array_sum(array_column($citizens, 'reports'));

include __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-page-head">
  <div>
    <div class="admin-breadcrumb-lite"><a href="admin.php">প্রশাসনিক প্যানেল</a><?= icon('chevron-right') ?><span>নাগরিক ব্যবস্থাপনা</span></div>
    <h2>নিবন্ধিত নাগরিক</h2>
    <p>নাগরিকদের অ্যাকাউন্ট, কার্যক্রম ও অবদান পর্যবেক্ষণ করুন।</p>
  </div>
  <div class="admin-page-head-actions">
    <button class="btn btn-outline btn-sm" type="button" data-export-table="#citizensTable" data-export-name="নগরবন্ধু-নাগরিক.csv"><?= icon('download', 'icon-sm') ?> এক্সপোর্ট</button>
    <a class="btn btn-primary btn-sm" href="register.php" target="_blank" rel="noopener"><?= icon('user-plus', 'icon-sm') ?> নাগরিক যোগ করুন</a>
  </div>
</div>

<div class="admin-stat-grid">
  <div class="admin-stat accent">
    <div class="admin-stat-top"><span class="admin-stat-label">মোট নিবন্ধিত নাগরিক</span><span class="admin-stat-icon"><?= icon('users', 'icon-sm') ?></span></div>
    <div class="admin-stat-value"><?= bn('২,৪৮৬') ?></div>
    <div class="admin-stat-foot"><span class="up"><?= icon('trending-up') ?> ৮.২%</span> গত মাসের তুলনায়</div>
  </div>
  <div class="admin-stat">
    <div class="admin-stat-top"><span class="admin-stat-label">সক্রিয় নাগরিক</span><span class="admin-stat-icon green"><?= icon('user', 'icon-sm') ?></span></div>
    <div class="admin-stat-value"><?= bn('২,৩১২') ?></div>
    <div class="admin-stat-foot"><?= bn('৯৩%') ?> সক্রিয়তা হার</div>
  </div>
  <div class="admin-stat">
    <div class="admin-stat-top"><span class="admin-stat-label">এই মাসে নতুন</span><span class="admin-stat-icon blue"><?= icon('user-plus', 'icon-sm') ?></span></div>
    <div class="admin-stat-value"><?= bn('১৪২') ?></div>
    <div class="admin-stat-foot"><span class="up"><?= icon('trending-up') ?> ১৮ জন</span> গত মাসের চেয়ে বেশি</div>
  </div>
  <div class="admin-stat">
    <div class="admin-stat-top"><span class="admin-stat-label">স্থগিত অ্যাকাউন্ট</span><span class="admin-stat-icon rose"><?= icon('ban', 'icon-sm') ?></span></div>
    <div class="admin-stat-value"><?= bn('১২') ?></div>
    <div class="admin-stat-foot">নীতিমালা লঙ্ঘনের কারণে</div>
  </div>
</div>

<div class="admin-grid cols-2-1">
  <section class="admin-panel" id="citizensPanel">
    <div class="admin-panel-head"><div><h3>নাগরিক তালিকা</h3><p>নমুনা হিসেবে <?= bn(count($citizens)) ?>টি সক্রিয় অ্যাকাউন্ট দেখানো হচ্ছে</p></div></div>
    <div class="admin-toolbar">
      <div class="toolbar-left">
        <div class="search-input">
          <?= icon('search') ?>
          <label class="sr-only" for="citizenSearch">নাগরিক খুঁজুন</label>
          <input class="input" id="citizenSearch" type="search" placeholder="নাম, মোবাইল বা এলাকা…" data-table-search>
        </div>
        <label class="sr-only" for="citizenStatus">অবস্থা</label>
        <select class="select" id="citizenStatus" data-table-filter="status">
          <option value="">সব অবস্থা</option>
          <option value="সক্রিয়">সক্রিয়</option>
          <option value="সীমিত">সীমিত</option>
          <option value="স্থগিত">স্থগিত</option>
        </select>
        <label class="sr-only" for="citizenArea">এলাকা</label>
        <select class="select" id="citizenArea" data-table-filter="area">
          <option value="">সব এলাকা</option>
          <option value="মিরপুর">মিরপুর</option>
          <option value="শাহবাগ">শাহবাগ</option>
          <option value="কাজীপাড়া">কাজীপাড়া</option>
          <option value="মোহাম্মদপুর">মোহাম্মদপুর</option>
          <option value="উত্তরা">উত্তরা</option>
          <option value="ধানমন্ডি">ধানমন্ডি</option>
          <option value="বনানী">বনানী</option>
          <option value="সায়েদাবাদ">সায়েদাবাদ</option>
        </select>
        <label class="sr-only" for="citizenSize">প্রতি পৃষ্ঠায়</label>
        <select class="select" id="citizenSize" data-table-size>
          <option value="10">১০টি</option>
          <option value="25">২৫টি</option>
          <option value="50">৫০টি</option>
        </select>
      </div>
    </div>
    <div class="bulk-bar" data-bulk-bar>
      <strong><span data-bulk-count>০</span>টি অ্যাকাউন্ট নির্বাচিত</strong>
      <button class="btn btn-sm btn-primary" type="button" data-admin-action="activate"><?= icon('check', 'icon-sm') ?> সক্রিয় করুন</button>
      <button class="btn btn-sm btn-danger-outline" type="button" data-admin-action="suspend"><?= icon('ban', 'icon-sm') ?> স্থগিত করুন</button>
    </div>
    <div class="table-scroll admin-table-wrap">
      <table class="data-table admin-table" id="citizensTable" data-admin-table="#citizensPanel">
        <thead>
          <tr>
            <th class="table-check"><input class="db-check" type="checkbox" data-select-all aria-label="সব নাগরিক নির্বাচন করুন"></th>
            <th>নাগরিক</th>
            <th>যোগাযোগ</th>
            <th>এলাকা</th>
            <th>যোগদান</th>
            <th>রিপোর্ট</th>
            <th>সমর্থন</th>
            <th>অবস্থা</th>
            <th class="cell-actions">কার্যক্রম</th>
          </tr>
        </thead>
        <tbody>
          <tr data-empty data-empty-row><td colspan="9"><?= empty_state('কোনো নাগরিক পাওয়া যায়নি', 'অন্য নাম বা ফিল্টার দিয়ে আবার চেষ্টা করুন।', 'users') ?></td></tr>
          <?php foreach ($citizens as $citizen): ?>
            <tr data-status="<?= e($citizen['status']) ?>" data-area="<?= e($citizen['area']) ?>">
              <td class="table-check"><input class="db-check" type="checkbox" value="<?= e($citizen['name']) ?>" aria-label="<?= e($citizen['name']) ?> নির্বাচন করুন"></td>
              <td>
                <div class="cell-person">
                  <?= avatar($citizen['initials'], $citizen['color'], 'sm') ?>
                  <div><strong><?= e($citizen['name']) ?></strong><span><?= e($citizen['email']) ?></span></div>
                </div>
              </td>
              <td class="table-sub"><?= e($citizen['phone']) ?></td>
              <td class="table-sub"><?= e($citizen['area']) ?></td>
              <td class="table-sub"><?= e($citizen['joined']) ?></td>
              <td><strong style="font-size:.76rem"><?= bn($citizen['reports']) ?></strong></td>
              <td class="table-sub"><?= bn($citizen['supports']) ?></td>
              <td><span class="badge badge-<?= e($citizen['tone']) ?>"><span class="status-dot"></span><?= e($citizen['status']) ?></span></td>
              <td class="cell-actions">
                <div class="row-menu" data-row-menu>
                  <button class="row-menu-btn icon-btn" type="button" aria-haspopup="true" aria-expanded="false" aria-label="কার্যক্রম মেনু"><?= icon('more-horizontal', 'icon-sm') ?></button>
                  <div class="row-dropdown">
                    <a href="profile.php" target="_blank" rel="noopener"><?= icon('user', 'icon-sm') ?> প্রোফাইল দেখুন</a>
                    <a href="my-reports.php" target="_blank" rel="noopener"><?= icon('file-text', 'icon-sm') ?> রিপোর্টের তালিকা</a>
                    <a href="mailto:<?= e($citizen['email']) ?>"><?= icon('mail', 'icon-sm') ?> ইমেইল করুন</a>
                    <div class="dropdown-sep"></div>
                    <?php if ($citizen['status'] === 'স্থগিত'): ?>
                      <button type="button" data-admin-action="activate"><?= icon('check', 'icon-sm') ?> সক্রিয় করুন</button>
                    <?php else: ?>
                      <button class="danger-item" type="button" data-admin-action="suspend"><?= icon('ban', 'icon-sm') ?> স্থগিত করুন</button>
                    <?php endif; ?>
                  </div>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="table-foot"><p data-table-info>দেখানো হচ্ছে <?= bn(count($citizens)) ?> জন নাগরিক</p><div class="pagination" data-table-pagination></div></div>
  </section>

  <div style="display:flex;flex-direction:column;gap:16px">
    <section class="admin-panel">
      <div class="admin-panel-head"><div><h3>শীর্ষ অবদানকারী</h3><p>এই মাসের সবচেয়ে সক্রিয় নাগরিক</p></div></div>
      <div class="activity-list">
        <?php foreach (array_slice($citizens, 0, 4) as $index => $citizen): ?>
          <div class="activity-item">
            <span class="activity-icon <?= $index === 0 ? 'amber' : '' ?>"><?= bn($index + 1) ?></span>
            <div class="activity-text"><p><strong><?= e($citizen['name']) ?></strong> · <?= bn($citizen['reports']) ?>টি রিপোর্ট</p><span><?= e($citizen['area']) ?></span></div>
            <span class="activity-time"><?= bn($citizen['supports']) ?> সমর্থন</span>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="admin-panel">
      <div class="admin-panel-head"><div><h3>নাগরিক সারসংক্ষেপ</h3><p>নমুনা অ্যাকাউন্টের বিভাজন</p></div></div>
      <div class="admin-panel-body">
        <div class="mini-stat-grid">
          <div class="mini-stat"><strong><?= bn(count($citizens)) ?></strong><span>মোট</span></div>
          <div class="mini-stat"><strong><?= bn($activeCount) ?></strong><span>সক্রিয়</span></div>
          <div class="mini-stat"><strong><?= bn($limitedCount) ?></strong><span>সীমিত</span></div>
          <div class="mini-stat"><strong><?= bn($suspendedCount) ?></strong><span>স্থগিত</span></div>
          <div class="mini-stat"><strong><?= bn($totalReports) ?></strong><span>মোট রিপোর্ট</span></div>
          <div class="mini-stat"><strong><?= bn('৪২') ?></strong><span>গড় সমর্থন</span></div>
        </div>
        <div class="verify-note" style="margin-top:14px"><?= icon('shield', 'icon-sm') ?><div>অ্যাকাউন্ট স্থগিত করার আগে নীতিমালা লঙ্ঘনের প্রমাণ সংরক্ষণ করুন। সিদ্ধান্ত নাগরিককে ইমেইলে জানানো হবে।</div></div>
      </div>
    </section>

    <section class="admin-panel">
      <div class="admin-panel-head"><div><h3>দ্রুত লিংক</h3></div></div>
      <div class="admin-panel-body">
        <div class="quick-actions">
          <a class="quick-action" href="admin-comments.php"><?= icon('message-circle', 'icon-md') ?><strong>মন্তব্য দেখুন</strong><span>পতাকাঙ্কিত মন্তব্য</span></a>
          <a class="quick-action" href="admin-reports.php"><?= icon('bar-chart', 'icon-md') ?><strong>নাগরিক প্রতিবেদন</strong><span>অংশগ্রহণের বিশ্লেষণ</span></a>
          <a class="quick-action" href="admin-notifications.php"><?= icon('bell', 'icon-md') ?><strong>ব্রডকাস্ট</strong><span>নাগরিকদের বার্তা</span></a>
          <a class="quick-action" href="admin-settings.php"><?= icon('settings', 'icon-md') ?><strong>নীতিমালা</strong><span>অ্যাকাউন্ট নিয়ম</span></a>
        </div>
      </div>
    </section>
  </div>
</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
