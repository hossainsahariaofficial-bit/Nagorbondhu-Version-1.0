<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_citizen();
$pageTitle = 'অনুদানের ইতিহাস — নগরবন্ধু';
$dashPage = 'donation-history.php';
$dashTitle = 'অনুদানের ইতিহাস';
$citizen = current_citizen();
$rows = user_donations((int) $citizen['id']);
$totalDonation = array_sum(array_column($rows, 'amount'));
$verifiedDonation = array_sum(array_map(fn ($r) => $r['raw_status'] === 'paid' ? (float) $r['amount'] : 0, $rows));
$pendingDonation = array_sum(array_map(fn ($r) => $r['raw_status'] === 'pending' ? (float) $r['amount'] : 0, $rows));
$campaignTotals = [];
foreach ($rows as $row) {
    $cid = (int) $row['campaign_id'];
    $campaignTotals[$cid] = ($campaignTotals[$cid] ?? 0) + (float) $row['amount'];
}
$campaignCount = count($campaignTotals);
$latestDate = $rows[0]['date'] ?? null;
include __DIR__ . '/includes/header.php';
?>
<div class="container"> 
  <?php include __DIR__ . '/includes/dashboard-sidebar.php'; ?>

  <div class="dash-welcome">
    <div>
      <span class="dash-date"><?= icon('wallet', 'icon-xs') ?> সর্বশেষ লেনদেন: <?= $latestDate ? e($latestDate) : 'এখনও কোনো অনুদান নেই' ?></span>
      <h1>অনুদানের ইতিহাস</h1>
      <p>আপনার করা অনুদান, যাচাইয়ের অবস্থা এবং কার্যক্রমের অগ্রগতি দেখুন।</p>
    </div>
    <a class="btn btn-primary" href="donate.php"><?= icon('heart', 'icon-sm') ?> নতুন অনুদান</a>
  </div>

  <div class="dash-stats">
    <div class="dash-stat tone-green"><div class="dash-stat-head"><span>মোট অনুদান</span><?= icon('wallet', 'icon-sm') ?></div><strong><?= taka($totalDonation) ?></strong><small><?= bn($campaignCount) ?>টি কার্যক্রমে</small></div>
    <div class="dash-stat tone-blue"><div class="dash-stat-head"><span>যাচাইকৃত</span><?= icon('check-circle', 'icon-sm') ?></div><strong><?= taka($verifiedDonation) ?></strong><small>যাচাই সম্পন্ন</small></div>
    <div class="dash-stat tone-amber"><div class="dash-stat-head"><span>যাচাই চলছে</span><?= icon('clock', 'icon-sm') ?></div><strong><?= taka($pendingDonation) ?></strong><small>সাধারণত ১–২ কর্মদিবস</small></div>
    <div class="dash-stat tone-violet"><div class="dash-stat-head"><span>সমর্থিত কার্যক্রম</span><?= icon('target', 'icon-sm') ?></div><strong><?= bn($campaignCount) ?></strong><small>মোট কার্যক্রম</small></div>
  </div>

  <div class="table-card" id="donationScope">
    <div class="table-card-head">
      <div><h2>লেনদেনের তালিকা</h2><p>ডেটাবেজে সংরক্ষিত অনুদানের অনুরোধ ও যাচাইয়ের অবস্থা</p></div>
      <div class="table-tools">
        <div class="search-input">
          <?= icon('search', 'icon-sm') ?>
          <label class="sr-only" for="donationSearch">লেনদেন খুঁজুন</label>
          <input class="input" id="donationSearch" type="search" placeholder="লেনদেন নম্বর বা কার্যক্রম…" data-table-search>
        </div>
        <label class="sr-only" for="donationStatus">অবস্থা অনুযায়ী ফিল্টার</label>
        <select class="select" id="donationStatus" data-table-filter="status">
          <option value="">সব অবস্থা</option>
          <option value="যাচাইকৃত">যাচাইকৃত</option>
          <option value="অপেক্ষমাণ">অপেক্ষমাণ</option>
          <option value="বাতিল">বাতিল</option>
        </select>
        <label class="sr-only" for="donationSize">প্রতি পৃষ্ঠায়</label>
        <select class="select" id="donationSize" data-table-size>
          <option value="5" selected>৫টি</option>
          <option value="10">১০টি</option>
        </select>
      </div>
    </div>
    <div class="table-scroll">
      <table class="data-table admin-table" data-admin-table="#donationScope">
        <thead>
          <tr>
            <th scope="col">লেনদেন</th>
            <th scope="col">কার্যক্রম</th>
            <th scope="col">পরিমাণ</th>
            <th scope="col">মাধ্যম</th>
            <th scope="col">তারিখ</th>
            <th scope="col">অবস্থা</th>
            <th scope="col" class="cell-actions">কার্যক্রম</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $row): ?>
            <?php $campaign = nb_campaign($row['campaign_id']); ?>
            <tr data-status="<?= e($row['status']) ?>">
              <td><span class="cell-main"><?= e($row['id']) ?></span><span class="cell-sub">আইডি: <?= e($row['transaction'] !== '' ? $row['transaction'] : '—') ?></span></td>
              <td><a class="cell-main" href="donation.php?id=<?= (int) $campaign['id'] ?>"><?= e(excerpt($campaign['title'], 42)) ?></a><span class="cell-sub"><?= e($campaign['organizer']) ?></span></td>
              <td><span class="cell-main"><?= taka($row['amount']) ?></span></td>
              <td><?= e($row['method']) ?></td>
              <td><?= e($row['date']) ?></td>
              <td class="cell-badge"><span class="badge badge-<?= e($row['tone']) ?>"><?= e($row['status']) ?></span></td>
              <td class="cell-actions"><a class="btn btn-sm btn-outline" href="donation.php?id=<?= (int) $campaign['id'] ?>">বিস্তারিত</a></td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$rows): ?>
            <tr><td colspan="7"><div class="admin-empty"><?= icon('wallet', 'icon-md') ?><p>আপনি এখনও কোনো অনুদান দেননি। কমিউনিটি উদ্যোগে অংশ নিন।</p><a class="btn btn-primary btn-sm mt-2" href="donate.php">অনুদান দিন</a></div></td></tr>
          <?php endif; ?>
          <tr data-empty data-empty-row hidden><td colspan="7"><div class="admin-empty"><?= icon('search', 'icon-md') ?><p>আপনার অনুসন্ধান বা ফিল্টারে কোনো লেনদেন মেলেনি।</p></div></td></tr>
        </tbody>
      </table>
    </div>
    <div class="table-foot">
      <p data-table-info>দেখানো হচ্ছে ১–<?= bn(count($rows)) ?> (মোট <?= bn(count($rows)) ?>টি)</p>
      <div class="pagination" data-table-pagination aria-label="লেনদেনের পৃষ্ঠা"></div>
    </div>
  </div>

  <div class="dash-grid" style="margin-top:20px">
    <div class="panel">
      <div class="panel-head"><div><h2>কার্যক্রমভিত্তিক সারসংক্ষেপ</h2><p>আপনি যে উদ্যোগগুলোতে অবদান রেখেছেন</p></div></div>
      <div class="panel-body">
        <?php if (!$campaignTotals): ?>
          <p class="muted-note">সারসংক্ষেপ দেখানোর জন্য এখনও কোনো অনুদান নেই।</p>
        <?php endif; ?>
        <?php foreach ($campaignTotals as $cid => $amount): ?>
          <?php $campaign = nb_campaign((int) $cid); ?>
          <div style="margin-bottom:16px">
            <div class="row-between" style="margin-bottom:5px">
              <a class="cell-main" href="donation.php?id=<?= (int) $cid ?>"><?= e(excerpt($campaign['title'], 52)) ?></a>
              <span class="muted-note"><?= taka($amount) ?></span>
            </div>
            <?= progress_bar($campaign['raised'], $campaign['target']) ?>
            <p class="muted-note" style="margin-top:4px"><?= bn(progress_percent($campaign['raised'], $campaign['target'])) ?>% পূর্ণ • <?= e($campaign['status']) ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="panel">
      <div class="panel-head"><h2>গুরুত্বপূর্ণ তথ্য</h2></div>
      <div class="panel-body">
        <div class="note-list">
          <div class="note-item"><?= icon('info', 'icon-xs') ?> <span>এই প্ল্যাটফর্মে পেমেন্ট গেটওয়ে সংযুক্ত নয়। সব লেনদেন ডেমো রেকর্ড।</span></div>
          <div class="note-item"><?= icon('shield', 'icon-xs') ?> <span>যাচাইকৃত অনুদানের রসিদ ও কার্যক্রমের ব্যয়ের বিবরণ সংরক্ষিত থাকে।</span></div>
          <div class="note-item"><?= icon('alert-triangle', 'icon-xs') ?> <span>কোনো লেনদেন সন্দেহজনক মনে হলে সহায়তা দলে জানান।</span></div>
        </div>
        <a class="btn btn-outline btn-block mt-2" href="contact.php"><?= icon('message-circle', 'icon-sm') ?> সহায়তা নিন</a>
      </div>
    </div>
  </div>

  </div>
</div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
