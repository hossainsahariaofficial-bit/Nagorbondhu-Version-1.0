<?php
require_once __DIR__ . '/includes/bootstrap.php';

$adminPage = 'admin.php';
$adminTitle = 'ড্যাশবোর্ড';
$adminSubtitle = 'নাগরিক রিপোর্ট, অনুদান ও কর্তৃপক্ষের সমন্বিত চিত্র।';

$issues = nb_issues();
$recentIssues = array_slice($issues, 0, 7, true);
$monthly = nb_monthly_reports();
$chartMonths = array_slice($monthly, -7);
$maxCount = max(array_column($chartMonths, 'count'));
$authorities = nb_authorities();

$statusCounts = [];
foreach ($issues as $issue) {
    $statusCounts[$issue['status']] = ($statusCounts[$issue['status']] ?? 0) + 1;
}

include __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-page-head">
  <div>
    <div class="admin-breadcrumb-lite"><a href="admin.php">প্রশাসনিক প্যানেল</a><?= icon('chevron-right') ?><span>সারসংক্ষেপ</span></div>
    <h2>স্বাগতম, <?= e($admin['first']) ?>!</h2>
    <p>আজ <?= bn('৯ নভেম্বর ২০২৬') ?>, সোমবার — প্ল্যাটফর্মের সর্বশেষ অবস্থা এক নজরে দেখুন।</p>
  </div>
  <div class="admin-page-head-actions">
    <span class="admin-date"><?= icon('calendar', 'icon-xs') ?> সর্বশেষ হালনাগাদ: <strong><?= bn('১০ মিনিট আগে') ?></strong></span>
    <a class="btn btn-outline btn-sm" href="admin-reports.php"><?= icon('download', 'icon-sm') ?> প্রতিবেদন</a>
    <a class="btn btn-primary btn-sm" href="admin-issues.php"><?= icon('file-text', 'icon-sm') ?> রিপোর্ট দেখুন</a>
  </div>
</div>

<div class="admin-stat-grid">
  <div class="admin-stat accent">
    <div class="admin-stat-top">
      <span class="admin-stat-label">মোট নাগরিক রিপোর্ট</span>
      <span class="admin-stat-icon"><?= icon('file-text', 'icon-sm') ?></span>
    </div>
    <div class="admin-stat-value"><?= bn('১,২৮৪') ?></div>
    <div class="admin-stat-foot"><span class="up"><?= icon('trending-up') ?> ১২.৪%</span> গত মাসের তুলনায়</div>
  </div>
  <div class="admin-stat">
    <div class="admin-stat-top">
      <span class="admin-stat-label">সমাধান সম্পন্ন</span>
      <span class="admin-stat-icon green"><?= icon('check-circle', 'icon-sm') ?></span>
    </div>
    <div class="admin-stat-value"><?= bn('৮৬৭') ?></div>
    <div class="admin-stat-foot"><span class="up"><?= icon('trending-up') ?> ৬৭.৫%</span> সফলতার হার</div>
  </div>
  <div class="admin-stat">
    <div class="admin-stat-top">
      <span class="admin-stat-label">যাচাইয়ের অপেক্ষায়</span>
      <span class="admin-stat-icon amber"><?= icon('clock', 'icon-sm') ?></span>
    </div>
    <div class="admin-stat-value"><?= bn('৪২') ?></div>
    <div class="admin-stat-foot"><span class="down">৮টি জরুরি</span> অগ্রাধিকার প্রয়োজন</div>
  </div>
  <div class="admin-stat">
    <div class="admin-stat-top">
      <span class="admin-stat-label">মোট অনুদান সংগ্রহ</span>
      <span class="admin-stat-icon blue"><?= icon('wallet', 'icon-sm') ?></span>
    </div>
    <div class="admin-stat-value"><?= taka(320000) ?></div>
    <div class="admin-stat-foot"><span class="up"><?= icon('trending-up') ?> ৯.৮%</span> ৪টি চলমান কার্যক্রমে</div>
  </div>
</div>

<div class="admin-grid cols-2-1">
  <section class="admin-panel">
    <div class="admin-panel-head">
      <div><h3>মাসিক রিপোর্ট ও সমাধানের ধারা</h3><p>শেষ সাত মাসের নাগরিক রিপোর্ট এবং সমাধান</p></div>
      <div class="panel-actions">
        <select class="select" aria-label="সময়কাল নির্বাচন">
          <option>শেষ ৭ মাস</option>
          <option>শেষ ১২ মাস</option>
          <option>এই বছর</option>
        </select>
      </div>
    </div>
    <div class="chart-area">
      <div class="bar-chart" role="img" aria-label="মাসভিত্তিক রিপোর্ট ও সমাধানের স্তম্ভচিত্র">
        <?php foreach ($chartMonths as $row): ?>
          <div class="bar-col">
            <div class="bars">
              <span class="bar" data-height="<?= (int) round($row['count'] / $maxCount * 100) ?>" title="<?= e($row['month']) ?>: <?= bn($row['count']) ?>টি রিপোর্ট"></span>
              <span class="bar alt" data-height="<?= (int) round($row['solved'] / $maxCount * 100) ?>" title="<?= e($row['month']) ?>: <?= bn($row['solved']) ?>টি সমাধান"></span>
            </div>
            <small><?= e($row['month']) ?></small>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="chart-legend">
      <span><i class="legend-swatch"></i> মোট রিপোর্ট</span>
      <span><i class="legend-swatch alt"></i> সমাধান সম্পন্ন</span>
      <span style="margin-left:auto">নভেম্বর মাস চলমান</span>
    </div>
  </section>

  <section class="admin-panel">
    <div class="admin-panel-head">
      <div><h3>রিপোর্টের অবস্থা</h3><p>সর্বমোট <?= bn(count($issues)) ?>টি নমুনা রিপোর্টের বিভাজন</p></div>
      <a class="panel-link" href="admin-issues.php">সব দেখুন <?= icon('arrow-right', 'icon-xs') ?></a>
    </div>
    <div class="admin-panel-body">
      <div class="donut-wrap">
        <div class="donut" style="background:conic-gradient(var(--green) 0 62%, var(--info) 62% 78%, var(--warning) 78% 90%, #B7E3CE 90% 100%)">
          <div class="donut-center"><strong><?= bn('৬৮%') ?></strong><span>সমাধান হার</span></div>
        </div>
        <div class="donut-legend">
          <div><span><i class="legend-dot-lg" style="background:var(--green)"></i> সমাধান সম্পন্ন</span><b><?= bn('৬২%') ?></b></div>
          <div><span><i class="legend-dot-lg" style="background:var(--info)"></i> যাচাই সম্পন্ন</span><b><?= bn('১৬%') ?></b></div>
          <div><span><i class="legend-dot-lg" style="background:var(--warning)"></i> সমাধানাধীন</span><b><?= bn('১২%') ?></b></div>
          <div><span><i class="legend-dot-lg" style="background:#B7E3CE"></i> অন্যান্য</span><b><?= bn('১০%') ?></b></div>
        </div>
      </div>
      <div class="admin-note" style="margin-top:16px"><?= icon('info', 'icon-sm') ?><div>গত সপ্তাহে <strong><?= bn('৩৪') ?></strong>টি রিপোর্ট নতুন করে যাচাই সম্পন্ন হয়েছে। প্রতিক্রিয়া সময় গড়ে <strong><?= bn('২.৪') ?> দিন</strong>।</div></div>
    </div>
  </section>
</div>

<div class="admin-grid cols-2-1">
  <section class="admin-panel">
    <div class="admin-panel-head">
      <div><h3>সাম্প্রতিক রিপোর্ট</h3><p>নাগরিকদের সর্বশেষ জমা দেওয়া সমস্যার তালিকা</p></div>
      <a class="panel-link" href="admin-issues.php">সব রিপোর্ট <?= icon('arrow-right', 'icon-xs') ?></a>
    </div>
    <div class="table-scroll">
      <table class="data-table admin-table">
        <thead>
          <tr><th class="cell-issue">রিপোর্ট</th><th>নাগরিক</th><th>ধরন</th><th>অবস্থা</th><th>তারিখ</th><th class="cell-actions">কার্যক্রম</th></tr>
        </thead>
        <tbody>
          <?php foreach ($recentIssues as $issue): ?>
            <?php $cat = category_meta($issue['type']); ?>
            <tr>
              <td class="cell-issue">
                <div class="cell-person">
                  <img class="cell-thumb" src="<?= e($issue['image']) ?>" alt="" loading="lazy">
                  <div>
                    <a class="cell-main" href="issue.php?id=<?= (int) $issue['id'] ?>"><?= e(excerpt($issue['title'], 48)) ?></a>
                    <span class="cell-sub"><?= e($issue['code']) ?> · <?= e($issue['area']) ?></span>
                  </div>
                </div>
              </td>
              <td><?= e($issue['reporter']) ?></td>
              <td><?= category_badge($issue['type']) ?></td>
              <td><?= status_badge($issue['status']) ?></td>
              <td class="table-sub"><?= e($issue['date']) ?></td>
              <td class="cell-actions"><a class="btn btn-sm btn-outline" href="admin-verify.php?id=<?= (int) $issue['id'] ?>">দেখুন</a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>

  <div style="display:flex;flex-direction:column;gap:16px">
    <section class="admin-panel">
      <div class="admin-panel-head"><div><h3>দ্রুত কার্যক্রম</h3><p>সাধারণ প্রশাসনিক কাজ</p></div></div>
      <div class="admin-panel-body">
        <div class="quick-actions">
          <a class="quick-action" href="admin-verify.php"><?= icon('check-square', 'icon-md') ?><strong>রিপোর্ট যাচাই</strong><span><?= bn('৪২') ?>টি অপেক্ষমাণ</span></a>
          <a class="quick-action" href="admin-comments.php"><?= icon('message-circle', 'icon-md') ?><strong>মন্তব্য পর্যালোচনা</strong><span><?= bn('৫') ?>টি পতাকাঙ্কিত</span></a>
          <a class="quick-action" href="admin-campaigns.php"><?= icon('target', 'icon-md') ?><strong>নতুন কার্যক্রম</strong><span>অনুদান উদ্যোগ খুলুন</span></a>
          <a class="quick-action" href="admin-authorities.php"><?= icon('landmark', 'icon-md') ?><strong>কর্তৃপক্ষ বরাদ্দ</strong><span><?= bn('৭') ?>টি সংস্থা</span></a>
        </div>
      </div>
    </section>

    <section class="admin-panel">
      <div class="admin-panel-head"><div><h3>সাম্প্রতিক কার্যক্রম</h3><p>সিস্টেমের সর্বশেষ পদক্ষেপ</p></div></div>
      <div class="activity-list">
        <div class="activity-item">
          <span class="activity-icon green"><?= icon('check-circle', 'icon-sm') ?></span>
          <div class="activity-text"><p>রিপোর্ট <strong>নগর-২০২৬-১০২৪</strong> যাচাই সম্পন্ন হয়েছে</p><span>আরিফুল ইসলাম</span></div>
          <span class="activity-time">১০ মিনিট</span>
        </div>
        <div class="activity-item">
          <span class="activity-icon blue"><?= icon('wallet', 'icon-sm') ?></span>
          <div class="activity-text"><p><?= taka(10000) ?> অনুদান যাচাই করা হয়েছে</p><span>অনুদান দল</span></div>
          <span class="activity-time">৪২ মিনিট</span>
        </div>
        <div class="activity-item">
          <span class="activity-icon amber"><?= icon('alert-triangle', 'icon-sm') ?></span>
          <div class="activity-text"><p>একটি মন্তব্য পর্যালোচনার জন্য পতাকাঙ্কিত হয়েছে</p><span>মিরপুর ১০</span></div>
          <span class="activity-time">১ ঘণ্টা</span>
        </div>
        <div class="activity-item">
          <span class="activity-icon green"><?= icon('target', 'icon-sm') ?></span>
          <div class="activity-text"><p>“স্কুলপথে নিরাপদ সড়কবাতি” কার্যক্রম প্রকাশিত</p><span>কার্যক্রম দল</span></div>
          <span class="activity-time">২ ঘণ্টা</span>
        </div>
      </div>
      <div class="admin-panel-foot"><span>আজ <?= bn('১৮') ?>টি কার্যক্রম</span><a href="admin-reports.php">বিস্তারিত লগ <?= icon('arrow-right', 'icon-xs') ?></a></div>
    </section>
  </div>
</div>

<div class="admin-grid cols-2">
  <section class="admin-panel">
    <div class="admin-panel-head">
      <div><h3>কর্তৃপক্ষের কর্মক্ষমতা</h3><p>বরাদ্দকৃত রিপোর্টের সমাধান হার</p></div>
      <a class="panel-link" href="admin-authorities.php">সব সংস্থা <?= icon('arrow-right', 'icon-xs') ?></a>
    </div>
    <div class="admin-panel-body">
      <div class="hbar-list">
        <?php foreach (array_slice($authorities, 0, 4) as $authority): ?>
          <?php $pct = progress_percent($authority['resolved'], $authority['assigned']); ?>
          <div class="hbar-item">
            <div class="hbar-top"><span><?= icon('landmark', 'icon-xs') ?> <?= e($authority['name']) ?></span><b><?= bn($pct) ?>%</b></div>
            <div class="hbar-track"><span class="hbar-fill" style="width:<?= $pct ?>%"></span></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="admin-panel">
    <div class="admin-panel-head"><div><h3>বিভাগভিত্তিক রিপোর্ট</h3><p>সমস্যার ধরন অনুযায়ী মোট রিপোর্ট</p></div></div>
    <div class="admin-panel-body">
      <div class="hbar-list">
        <?php
        $typeTotals = [];
        foreach ($issues as $issue) {
            $typeTotals[$issue['type']] = ($typeTotals[$issue['type']] ?? 0) + 1;
        }
        $maxType = max($typeTotals);
        $tones = ['', 'amber', 'blue', 'violet', 'rose', 'slate', 'amber'];
        $ti = 0;
        foreach ($typeTotals as $type => $total):
            $cat = category_meta($type);
            $pct = (int) round($total / $maxType * 100);
        ?>
          <div class="hbar-item">
            <div class="hbar-top"><span><?= icon($cat['icon'], 'icon-xs') ?> <?= e($cat['label']) ?></span><b><?= bn($total) ?>টি</b></div>
            <div class="hbar-track"><span class="hbar-fill <?= e($tones[$ti % count($tones)]) ?>" style="width:<?= $pct ?>%"></span></div>
          </div>
        <?php $ti++; endforeach; ?>
      </div>
    </div>
  </section>
</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
