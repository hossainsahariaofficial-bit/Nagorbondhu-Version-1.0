<?php
require_once __DIR__ . '/includes/bootstrap.php';

$adminPage = 'admin-reports.php';
$adminTitle = 'প্রতিবেদন ও বিশ্লেষণ';

$monthly = nb_monthly_reports();
$issues = nb_issues();
$totalReports = array_sum(array_column($monthly, 'count'));
$totalSolved = array_sum(array_column($monthly, 'solved'));
$maxCount = max(array_column($monthly, 'count'));
$lastMonth = end($monthly);
$prevMonth = $monthly[count($monthly) - 2];

$lineData = array_slice($monthly, -10);
$w = 640; $h = 190; $padL = 38; $padR = 14; $padT = 12; $padB = 28;
$plotW = $w - $padL - $padR;
$plotH = $h - $padT - $padB;
$n = count($lineData);
$countPoints = [];
$solvedPoints = [];
$lineLabels = [];
foreach ($lineData as $i => $row) {
    $x = $padL + ($n > 1 ? $i * $plotW / ($n - 1) : 0);
    $yCount = $padT + $plotH - ($row['count'] / $maxCount * $plotH);
    $ySolved = $padT + $plotH - ($row['solved'] / $maxCount * $plotH);
    $countPoints[] = round($x, 1) . ',' . round($yCount, 1);
    $solvedPoints[] = round($x, 1) . ',' . round($ySolved, 1);
    $lineLabels[] = ['x' => round($x, 1), 'label' => $row['month']];
}

$categoryCounts = [];
foreach ($issues as $issue) {
    $categoryCounts[$issue['type']] = ($categoryCounts[$issue['type']] ?? 0) + 1;
}
$totalCategory = array_sum($categoryCounts);
$palette = ['#16845B', '#2563EB', '#D97706', '#7C3AED', '#BE185D', '#64748B', '#6F9DB7'];
$gradientParts = [];
$cumulative = 0;
$ci = 0;
$categoryLegend = [];
foreach ($categoryCounts as $type => $count) {
    $pct = round($count / $totalCategory * 100, 1);
    $start = $cumulative;
    $cumulative += $pct;
    $gradientParts[] = $palette[$ci % count($palette)] . ' ' . $start . '% ' . min(100, $cumulative) . '%';
    $categoryLegend[] = ['label' => category_meta($type)['label'], 'icon' => category_meta($type)['icon'], 'count' => $count, 'pct' => $pct, 'color' => $palette[$ci % count($palette)]];
    $ci++;
}

$areas = [];
foreach ($issues as $issue) {
    $areas[$issue['area']] = ($areas[$issue['area']] ?? 0) + 1;
}
arsort($areas);
$maxArea = max($areas);

include __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-page-head">
  <div>
    <div class="admin-breadcrumb-lite"><a href="admin.php">প্রশাসনিক প্যানেল</a><?= icon('chevron-right') ?><span>প্রতিবেদন ও বিশ্লেষণ</span></div>
    <h2>প্ল্যাটফর্ম বিশ্লেষণ</h2>
    <p>রিপোর্টের ধারা, এলাকাভিত্তিক চিত্র, কর্তৃপক্ষের কর্মক্ষমতা ও মাসিক অগ্রগতি।</p>
  </div>
  <div class="admin-page-head-actions">
    <label class="sr-only" for="reportRange">সময়কাল</label>
    <select class="select" id="reportRange" style="min-height:38px;font-size:.76rem;padding:5px 32px 5px 11px;width:auto">
      <option>শেষ ১২ মাস</option>
      <option>এই বছর</option>
      <option>শেষ ৩০ দিন</option>
    </select>
    <button class="btn btn-outline btn-sm" type="button" data-export-table="#monthlyTable" data-export-name="নগরবন্ধু-মাসিক-বিশ্লেষণ.csv"><?= icon('download', 'icon-sm') ?> এক্সপোর্ট</button>
    <button class="btn btn-primary btn-sm" type="button" data-print><?= icon('file-text', 'icon-sm') ?> প্রিন্ট</button>
  </div>
</div>

<div class="admin-stat-grid">
  <div class="admin-stat accent">
    <div class="admin-stat-top"><span class="admin-stat-label">মোট রিপোর্ট (১২ মাস)</span><span class="admin-stat-icon"><?= icon('file-text', 'icon-sm') ?></span></div>
    <div class="admin-stat-value"><?= bn_num($totalReports) ?></div>
    <div class="admin-stat-foot"><span class="up"><?= icon('trending-up') ?> <?= bn(round(($lastMonth['count'] - $prevMonth['count']) / max(1, $prevMonth['count']) * 100, 1)) ?>%</span> গত মাসের তুলনায়</div>
  </div>
  <div class="admin-stat">
    <div class="admin-stat-top"><span class="admin-stat-label">মোট সমাধান</span><span class="admin-stat-icon green"><?= icon('check-circle', 'icon-sm') ?></span></div>
    <div class="admin-stat-value"><?= bn_num($totalSolved) ?></div>
    <div class="admin-stat-foot"><span class="up"><?= icon('trending-up') ?> <?= bn(round($totalSolved / $totalReports * 100, 1)) ?>%</span> সামগ্রিক সমাধান হার</div>
  </div>
  <div class="admin-stat">
    <div class="admin-stat-top"><span class="admin-stat-label">এই মাসের রিপোর্ট</span><span class="admin-stat-icon blue"><?= icon('calendar', 'icon-sm') ?></span></div>
    <div class="admin-stat-value"><?= bn($lastMonth['count']) ?></div>
    <div class="admin-stat-foot"><?= e($lastMonth['month']) ?> মাস · <?= bn($lastMonth['solved']) ?>টি সমাধান</div>
  </div>
  <div class="admin-stat">
    <div class="admin-stat-top"><span class="admin-stat-label">গড় সমাধান সময়</span><span class="admin-stat-icon violet"><?= icon('clock', 'icon-sm') ?></span></div>
    <div class="admin-stat-value"><?= bn('৯.৪') ?> দিন</div>
    <div class="admin-stat-foot"><span class="up"><?= icon('trending-up') ?> ১.২ দিন</span> উন্নতি</div>
  </div>
</div>

<div class="admin-grid cols-2-1">
  <section class="admin-panel">
    <div class="admin-panel-head"><div><h3>মাসভিত্তিক রিপোর্টের ধারা</h3><p>মোট রিপোর্ট ও সমাধানের তুলনা (শেষ ১০ মাস)</p></div><span class="badge badge-soft">লাইন চার্ট</span></div>
    <div class="chart-area">
      <div class="line-chart">
        <svg viewBox="0 0 <?= $w ?> <?= $h ?>" role="img" aria-label="মাসভিত্তিক রিপোর্ট ও সমাধানের রেখাচিত্র">
          <?php for ($g = 0; $g <= 4; $g++): ?>
            <?php $gy = $padT + $plotH - ($g / 4 * $plotH); ?>
            <line class="grid-line" x1="<?= $padL ?>" y1="<?= round($gy, 1) ?>" x2="<?= $w - $padR ?>" y2="<?= round($gy, 1) ?>" />
            <text class="axis-label" x="<?= $padL - 7 ?>" y="<?= round($gy + 3, 1) ?>" text-anchor="end"><?= bn(round($maxCount / 4 * $g)) ?></text>
          <?php endfor; ?>
          <polyline points="<?= implode(' ', $countPoints) ?>" fill="none" stroke="#16845B" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
          <polyline points="<?= implode(' ', $solvedPoints) ?>" fill="none" stroke="#2563EB" stroke-width="2" stroke-dasharray="5 4" stroke-linecap="round" stroke-linejoin="round" />
          <?php foreach ($countPoints as $i => $point): ?>
            <?php [$cx, $cy] = explode(',', $point); ?>
            <circle cx="<?= $cx ?>" cy="<?= $cy ?>" r="3.4" fill="#16845B" stroke="#fff" stroke-width="1.5" />
          <?php endforeach; ?>
          <?php foreach ($lineLabels as $label): ?>
            <text class="axis-label" x="<?= $label['x'] ?>" y="<?= $h - 7 ?>" text-anchor="middle"><?= e($label['label']) ?></text>
          <?php endforeach; ?>
        </svg>
      </div>
    </div>
    <div class="chart-legend">
      <span><i class="legend-swatch"></i> মোট রিপোর্ট</span>
      <span><i class="legend-swatch" style="background:#2563EB"></i> সমাধান সম্পন্ন</span>
      <span style="margin-left:auto">১২ মাসে <?= bn_num($totalReports) ?>টি রিপোর্ট</span>
    </div>
  </section>

  <section class="admin-panel">
    <div class="admin-panel-head"><div><h3>ধরনভিত্তিক বিভাজন</h3><p>নমুনা রিপোর্টের সমস্যার ধরন</p></div></div>
    <div class="admin-panel-body">
      <div class="donut-wrap">
        <div class="donut" style="background:conic-gradient(<?= implode(', ', $gradientParts) ?>)">
          <div class="donut-center"><strong><?= bn($totalCategory) ?></strong><span>রিপোর্ট</span></div>
        </div>
        <div class="donut-legend">
          <?php foreach ($categoryLegend as $item): ?>
            <div><span><i class="legend-dot-lg" style="background:<?= e($item['color']) ?>"></i> <?= e($item['label']) ?></span><b><?= bn($item['count']) ?></b></div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="admin-note" style="margin-top:16px"><?= icon('info', 'icon-sm') ?><div>সবচেয়ে বেশি রিপোর্ট আসছে <strong><?= e($categoryLegend[0]['label']) ?></strong> বিভাগে — মোট <?= bn($categoryLegend[0]['count']) ?>টি (<?= bn($categoryLegend[0]['pct']) ?>%)।</div></div>
    </div>
  </section>
</div>

<div class="admin-grid cols-2">
  <section class="admin-panel">
    <div class="admin-panel-head"><div><h3>এলাকাভিত্তিক রিপোর্ট</h3><p>সর্বোচ্চ রিপোর্ট পাওয়া এলাকা</p></div></div>
    <div class="admin-panel-body">
      <div class="hbar-list">
        <?php foreach (array_slice($areas, 0, 6, true) as $area => $count): ?>
          <?php $pct = (int) round($count / $maxArea * 100); ?>
          <div class="hbar-item">
            <div class="hbar-top"><span><?= icon('map-pin', 'icon-xs') ?> <?= e($area) ?></span><b><?= bn($count) ?>টি</b></div>
            <div class="hbar-track"><span class="hbar-fill" style="width:<?= $pct ?>%"></span></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="admin-panel">
    <div class="admin-panel-head"><div><h3>কর্তৃপক্ষের কর্মক্ষমতা</h3><p>বরাদ্দকৃত রিপোর্টের সমাধান হার</p></div><a class="panel-link" href="admin-authorities.php">সব সংস্থা <?= icon('arrow-right', 'icon-xs') ?></a></div>
    <div class="admin-panel-body">
      <div class="hbar-list">
        <?php foreach (nb_authorities() as $authority): ?>
          <?php $pct = progress_percent($authority['resolved'], $authority['assigned']); ?>
          <div class="hbar-item">
            <div class="hbar-top"><span><?= icon('landmark', 'icon-xs') ?> <?= e(excerpt($authority['name'], 34)) ?></span><b><?= bn($pct) ?>%</b></div>
            <div class="hbar-track"><span class="hbar-fill <?= $pct >= 70 ? '' : ($pct >= 50 ? 'amber' : 'rose') ?>" style="width:<?= $pct ?>%"></span></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
</div>

<section class="admin-panel">
  <div class="admin-panel-head"><div><h3>মাসিক বিস্তারিত প্রতিবেদন</h3><p>রিপোর্ট, সমাধান, সফলতার হার ও পরিবর্তনের ধারা</p></div><button class="btn btn-outline btn-sm" type="button" data-export-table="#monthlyTable"><?= icon('download', 'icon-sm') ?> CSV</button></div>
  <div class="table-scroll">
    <table class="data-table admin-table" id="monthlyTable">
      <thead><tr><th>মাস</th><th>মোট রিপোর্ট</th><th>সমাধান সম্পন্ন</th><th>সফলতার হার</th><th>পরিবর্তন</th><th>অবস্থা</th></tr></thead>
      <tbody>
        <?php foreach ($monthly as $index => $row): ?>
          <?php
          $rate = round($row['solved'] / max(1, $row['count']) * 100, 1);
          $prev = $index > 0 ? $monthly[$index - 1]['count'] : $row['count'];
          $change = round(($row['count'] - $prev) / max(1, $prev) * 100, 1);
          ?>
          <tr>
            <td><strong style="font-size:.76rem"><?= e($row['month']) ?> ২০২৬</strong></td>
            <td><?= bn($row['count']) ?></td>
            <td><?= bn($row['solved']) ?></td>
            <td><div class="row-between" style="font-size:.64rem;color:var(--muted);margin-bottom:3px"><span><?= bn($rate) ?>%</span></div><?= progress_bar($row['solved'], $row['count'], 'progress-thin') ?></td>
            <td><span class="<?= $change >= 0 ? 'up' : 'down' ?>" style="font-size:.72rem;font-weight:600;color:<?= $change >= 0 ? 'var(--green)' : 'var(--danger)' ?>"><?= $change >= 0 ? '↑' : '↓' ?> <?= bn(abs($change)) ?>%</span></td>
            <td><?= $rate >= 70 ? status_badge('solved') : ($rate >= 55 ? status_badge('working') : status_badge('verifying')) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
