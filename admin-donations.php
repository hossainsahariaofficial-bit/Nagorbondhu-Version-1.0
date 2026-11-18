<?php
require_once __DIR__ . '/includes/bootstrap.php';

$adminPage = 'admin-donations.php';
$adminTitle = 'অনুদান ব্যবস্থাপনা';

$transactions = nb_transactions();
$campaigns = nb_campaigns();
$verified = array_filter($transactions, fn ($t) => $t['status'] === 'যাচাইকৃত');
$pending = array_filter($transactions, fn ($t) => $t['status'] === 'অপেক্ষমাণ');
$cancelled = array_filter($transactions, fn ($t) => $t['status'] === 'বাতিল');
$totalVerified = array_sum(array_column($verified, 'amount'));
$totalPending = array_sum(array_column($pending, 'amount'));
$totalRaised = array_sum(array_column($campaigns, 'raised'));
$totalTarget = array_sum(array_column($campaigns, 'target'));

$statusFilter = $_GET['status'] ?? '';
$rows = $statusFilter !== ''
    ? array_values(array_filter($transactions, fn ($t) => $t['status'] === $statusFilter))
    : $transactions;

include __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-page-head">
  <div>
    <div class="admin-breadcrumb-lite"><a href="admin.php">প্রশাসনিক প্যানেল</a><?= icon('chevron-right') ?><span>অনুদান ব্যবস্থাপনা</span></div>
    <h2>অনুদান ও লেনদেন</h2>
    <p>নাগরিকদের অনুদান যাচাই করুন, লেনদেনের স্বচ্ছতা নিশ্চিত করুন এবং কার্যক্রমভিত্তিক হিসাব দেখুন।</p>
  </div>
  <div class="admin-page-head-actions">
    <button class="btn btn-outline btn-sm" type="button" data-export-table="#donationsTable" data-export-name="নগরবন্ধু-অনুদান.csv"><?= icon('download', 'icon-sm') ?> এক্সপোর্ট</button>
    <a class="btn btn-primary btn-sm" href="admin-campaigns.php"><?= icon('target', 'icon-sm') ?> কার্যক্রম দেখুন</a>
  </div>
</div>

<div class="admin-stat-grid">
  <div class="admin-stat accent">
    <div class="admin-stat-top"><span class="admin-stat-label">মোট সংগ্রহ</span><span class="admin-stat-icon"><?= icon('wallet', 'icon-sm') ?></span></div>
    <div class="admin-stat-value"><?= taka($totalRaised) ?></div>
    <div class="admin-stat-foot"><span class="up"><?= icon('trending-up') ?> <?= bn(progress_percent($totalRaised, $totalTarget)) ?>%</span> মোট লক্ষ্যের</div>
  </div>
  <div class="admin-stat">
    <div class="admin-stat-top"><span class="admin-stat-label">যাচাইকৃত লেনদেন</span><span class="admin-stat-icon green"><?= icon('check-circle', 'icon-sm') ?></span></div>
    <div class="admin-stat-value"><?= taka($totalVerified) ?></div>
    <div class="admin-stat-foot"><?= bn(count($verified)) ?>টি সফল লেনদেন</div>
  </div>
  <div class="admin-stat">
    <div class="admin-stat-top"><span class="admin-stat-label">যাচাইয়ের অপেক্ষায়</span><span class="admin-stat-icon amber"><?= icon('clock', 'icon-sm') ?></span></div>
    <div class="admin-stat-value"><?= taka($totalPending) ?></div>
    <div class="admin-stat-foot"><span class="down"><?= bn(count($pending)) ?>টি</span> লেনদেন অপেক্ষমাণ</div>
  </div>
  <div class="admin-stat">
    <div class="admin-stat-top"><span class="admin-stat-label">বাতিল / ফেরত</span><span class="admin-stat-icon rose"><?= icon('x-circle', 'icon-sm') ?></span></div>
    <div class="admin-stat-value"><?= bn(count($cancelled)) ?></div>
    <div class="admin-stat-foot">অযাচিত বা ভুল তথ্যের কারণে</div>
  </div>
</div>

<section class="admin-panel" id="donationsPanel">
  <div class="admin-tabs">
    <a class="admin-tab <?= $statusFilter === '' ? 'active' : '' ?>" href="admin-donations.php">সব লেনদেন <span class="count"><?= bn(count($transactions)) ?></span></a>
    <a class="admin-tab <?= $statusFilter === 'অপেক্ষমাণ' ? 'active' : '' ?>" href="admin-donations.php?status=<?= rawurlencode('অপেক্ষমাণ') ?>">অপেক্ষমাণ <span class="count"><?= bn(count($pending)) ?></span></a>
    <a class="admin-tab <?= $statusFilter === 'যাচাইকৃত' ? 'active' : '' ?>" href="admin-donations.php?status=<?= rawurlencode('যাচাইকৃত') ?>">যাচাইকৃত <span class="count"><?= bn(count($verified)) ?></span></a>
    <a class="admin-tab <?= $statusFilter === 'বাতিল' ? 'active' : '' ?>" href="admin-donations.php?status=<?= rawurlencode('বাতিল') ?>">বাতিল <span class="count"><?= bn(count($cancelled)) ?></span></a>
  </div>

  <div class="admin-toolbar">
    <div class="toolbar-left">
      <div class="search-input">
        <?= icon('search') ?>
        <label class="sr-only" for="donationSearch">লেনদেন খুঁজুন</label>
        <input class="input" id="donationSearch" type="search" placeholder="লেনদেন নম্বর, অনুদানদাতা বা কার্যক্রম…" data-table-search>
      </div>
      <label class="sr-only" for="donationStatus">অবস্থা</label>
      <select class="select" id="donationStatus" data-table-filter="status">
        <option value="">সব অবস্থা</option>
        <option value="যাচাইকৃত">যাচাইকৃত</option>
        <option value="অপেক্ষমাণ">অপেক্ষমাণ</option>
        <option value="বাতিল">বাতিল</option>
      </select>
      <label class="sr-only" for="donationMethod">পদ্ধতি</label>
      <select class="select" id="donationMethod" data-table-filter="method">
        <option value="">সব পদ্ধতি</option>
        <option value="বিকাশ">বিকাশ</option>
        <option value="নগদ">নগদ</option>
        <option value="রকেট">রকেট</option>
        <option value="ব্যাংক">ব্যাংক</option>
      </select>
      <label class="sr-only" for="donationSize">প্রতি পৃষ্ঠায়</label>
      <select class="select" id="donationSize" data-table-size>
        <option value="10">১০টি</option>
        <option value="25">২৫টি</option>
        <option value="50">৫০টি</option>
      </select>
    </div>
    <div class="toolbar-right">
      <span class="badge badge-warning"><?= icon('clock', 'icon-xs') ?> <?= bn(count($pending)) ?>টি যাচাই বাকি</span>
    </div>
  </div>

  <div class="bulk-bar" data-bulk-bar>
    <strong><span data-bulk-count>০</span>টি লেনদেন নির্বাচিত</strong>
    <button class="btn btn-sm btn-primary" type="button" data-admin-action="verify"><?= icon('check', 'icon-sm') ?> যাচাই সম্পন্ন</button>
    <button class="btn btn-sm btn-danger-outline" type="button" data-admin-action="reject"><?= icon('x', 'icon-sm') ?> বাতিল করুন</button>
    <button class="btn btn-sm btn-outline" type="button" data-export-table="#donationsTable"><?= icon('download', 'icon-sm') ?> এক্সপোর্ট</button>
  </div>

  <div class="table-scroll admin-table-wrap">
    <table class="data-table admin-table" id="donationsTable" data-admin-table="#donationsPanel">
      <thead>
        <tr>
          <th class="table-check"><input class="db-check" type="checkbox" data-select-all aria-label="সব লেনদেন নির্বাচন করুন"></th>
          <th>লেনদেন</th>
          <th>অনুদানদাতা</th>
          <th>কার্যক্রম</th>
          <th>পরিমাণ</th>
          <th>পদ্ধতি</th>
          <th>তারিখ</th>
          <th>অবস্থা</th>
          <th class="cell-actions">কার্যক্রম</th>
        </tr>
      </thead>
      <tbody>
        <tr data-empty data-empty-row><td colspan="9"><?= empty_state('কোনো লেনদেন পাওয়া যায়নি', 'অন্য অবস্থা বা পদ্ধতি নির্বাচন করে দেখুন।', 'wallet') ?></td></tr>
        <?php foreach ($rows as $tx): ?>
          <tr data-status="<?= e($tx['status']) ?>" data-method="<?= e($tx['method']) ?>">
            <td class="table-check"><input class="db-check" type="checkbox" value="<?= e($tx['id']) ?>" aria-label="<?= e($tx['id']) ?> নির্বাচন করুন"></td>
            <td><a class="cell-main" href="admin-donations.php"><?= e($tx['id']) ?></a><span class="cell-sub">অনলাইন অনুদান</span></td>
            <td>
              <div class="cell-person">
                <?= avatar(mb_substr($tx['donor'], 0, 1), 'green', 'sm') ?>
                <div><strong><?= e($tx['donor']) ?></strong><span>যাচাইকৃত নাগরিক</span></div>
              </div>
            </td>
            <td><span class="table-sub" style="max-width:210px;display:block"><?= e($tx['campaign']) ?></span></td>
            <td><strong style="font-size:.78rem"><?= taka($tx['amount']) ?></strong></td>
            <td><span class="badge badge-soft"><?= icon('credit-card', 'icon-xs') ?> <?= e($tx['method']) ?></span></td>
            <td class="table-sub"><?= e($tx['date']) ?></td>
            <td><span class="badge badge-<?= e($tx['tone']) ?>"><span class="status-dot"></span><?= e($tx['status']) ?></span></td>
            <td class="cell-actions">
              <div class="row-menu" data-row-menu>
                <button class="row-menu-btn icon-btn" type="button" aria-haspopup="true" aria-expanded="false" aria-label="কার্যক্রম মেনু"><?= icon('more-horizontal', 'icon-sm') ?></button>
                <div class="row-dropdown">
                  <a href="donation.php?id=1" target="_blank" rel="noopener"><?= icon('external-link', 'icon-sm') ?> কার্যক্রম দেখুন</a>
                  <a href="donation-history.php" target="_blank" rel="noopener"><?= icon('file-text', 'icon-sm') ?> রসিদ দেখুন</a>
                  <div class="dropdown-sep"></div>
                  <button type="button" data-admin-action="verify"><?= icon('check', 'icon-sm') ?> যাচাই সম্পন্ন</button>
                  <button class="danger-item" type="button" data-admin-action="reject"><?= icon('x', 'icon-sm') ?> বাতিল করুন</button>
                </div>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="table-foot"><p data-table-info>দেখানো হচ্ছে <?= bn(count($rows)) ?>টি লেনদেন</p><div class="pagination" data-table-pagination></div></div>
</section>

<div class="admin-grid cols-2-1" style="margin-top:16px">
  <section class="admin-panel">
    <div class="admin-panel-head"><div><h3>কার্যক্রমভিত্তিক সংগ্রহ</h3><p>লক্ষ্য অনুযায়ী অগ্রগতি</p></div><a class="panel-link" href="admin-campaigns.php">সব কার্যক্রম <?= icon('arrow-right', 'icon-xs') ?></a></div>
    <div class="admin-panel-body">
      <div style="display:flex;flex-direction:column;gap:14px">
        <?php foreach ($campaigns as $campaign): ?>
          <?php $pct = progress_percent($campaign['raised'], $campaign['target']); ?>
          <div>
            <div class="row-between" style="font-size:.75rem;margin-bottom:5px">
              <span style="display:inline-flex;align-items:center;gap:5px"><?= icon('target', 'icon-xs') ?> <?= e(excerpt($campaign['title'], 40)) ?></span>
              <strong style="color:var(--green-dark)"><?= taka($campaign['raised']) ?></strong>
            </div>
            <?= progress_bar($campaign['raised'], $campaign['target'], 'progress-thin') ?>
            <div class="row-between" style="font-size:.64rem;color:var(--muted);margin-top:4px"><span>লক্ষ্য <?= taka($campaign['target']) ?></span><span><?= bn($pct) ?>% সম্পন্ন</span></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <div style="display:flex;flex-direction:column;gap:16px">
    <section class="admin-panel">
      <div class="admin-panel-head"><div><h3>নিষ্পত্তি সারসংক্ষেপ</h3><p>এই মাসের হিসাব</p></div></div>
      <div class="admin-panel-body">
        <div class="admin-kv">
          <div><span>মোট সংগ্রহ</span><strong><?= taka($totalRaised) ?></strong></div>
          <div><span>মোট লক্ষ্য</span><strong><?= taka($totalTarget) ?></strong></div>
          <div><span>যাচাইকৃত</span><strong><?= taka($totalVerified) ?></strong></div>
          <div><span>অপেক্ষমাণ</span><strong><?= taka($totalPending) ?></strong></div>
        </div>
        <div class="admin-note" style="margin-top:12px"><?= icon('shield', 'icon-sm') ?><div>প্রতিটি লেনদেন যাচাইয়ের পর <strong>লেনদেন নম্বর</strong> ও রসিদ অনুদানদাতাকে পাঠানো হয়।</div></div>
      </div>
    </section>
    <section class="admin-panel">
      <div class="admin-panel-head"><div><h3>দ্রুত কার্যক্রম</h3></div></div>
      <div class="admin-panel-body">
        <div class="quick-actions">
          <a class="quick-action" href="admin-campaigns.php"><?= icon('target', 'icon-md') ?><strong>নতুন কার্যক্রম</strong><span>অনুদান উদ্যোগ</span></a>
          <a class="quick-action" href="admin-reports.php"><?= icon('bar-chart', 'icon-md') ?><strong>আর্থিক প্রতিবেদন</strong><span>স্বচ্ছতা প্রতিবেদন</span></a>
        </div>
      </div>
    </section>
  </div>
</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
