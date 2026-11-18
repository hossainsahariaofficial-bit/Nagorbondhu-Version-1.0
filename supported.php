<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_citizen();
$pageTitle = 'সমর্থন করা সমস্যা — নগরবন্ধু';
$dashPage = 'supported.php';
$dashTitle = 'সমর্থন করা সমস্যা';
$citizen = current_citizen();
$supported = user_supported_issues((int) $citizen['id']);
$totalSupported = count($supported);
$solvedSupported = count(array_filter($supported, fn ($i) => $i['status'] === 'solved'));
$activeSupported = $totalSupported - $solvedSupported;
$area = trim((string) ($citizen['area'] ?? ''));
$areaLabel = $area !== '' ? $area : 'আপনার এলাকা';
$areaReports = $area !== ''
    ? count(array_filter(nb_issues(), fn ($i) => $i['area'] === $area && $i['status'] !== 'solved'))
    : 0;
include __DIR__ . '/includes/header.php';
?>
<div class="container">
  <?php include __DIR__ . '/includes/dashboard-sidebar.php'; ?>

  <div class="dash-welcome">
    <div>
      <span class="dash-date"><?= icon('heart', 'icon-xs') ?> <?= bn($totalSupported) ?>টি সমর্থন • <?= bn($activeSupported) ?>টি সক্রিয় সমস্যা</span>
      <h1>সমর্থন করা সমস্যা</h1>
      <p>আপনি যে সমস্যাগুলোতে সমর্থন দিয়েছেন সেগুলোর অগ্রগতি এখানে দেখুন।</p>
    </div>
    <a class="btn btn-outline" href="issues.php"><?= icon('search', 'icon-sm') ?> আরও সমস্যা দেখুন</a>
  </div>

  <div class="panel">
    <div class="panel-head">
      <div><h2>আপনার সমর্থনের তালিকা</h2><p>সর্বশেষ অগ্রগতি অনুযায়ী সাজানো</p></div>
      <?php if ($solvedSupported > 0): ?>
        <span class="badge badge-success"><?= icon('check-circle', 'icon-xs') ?> <?= bn($solvedSupported) ?>টি সমাধান সম্পন্ন</span>
      <?php else: ?>
        <span class="badge badge-soft"><?= icon('heart', 'icon-xs') ?> <?= bn($totalSupported) ?>টি সমর্থন</span>
      <?php endif; ?>
    </div>
    <div class="panel-body">
      <?php if ($supported): ?>
        <div class="issue-grid" style="grid-template-columns:1fr 1fr">
          <?php foreach ($supported as $issue): ?>
            <?php echo issue_card($issue); ?>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="admin-empty">
          <?= icon('heart', 'icon-md') ?>
          <p>আপনি এখনও কোনো রিপোর্টে সমর্থন জানাননি। সমস্যাগুলো দেখে সমর্থন দিলে সেগুলো দ্রুত সমাধানের অগ্রাধিকার পায়।</p>
          <a class="btn btn-primary btn-sm mt-2" href="issues.php"><?= icon('search', 'icon-sm') ?> সমস্যা দেখুন</a>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="panel" style="margin-top:20px">
    <div class="panel-body row-between">
      <div>
        <h3 style="font-size:.92rem;margin-bottom:3px">আপনার এলাকার সমস্যাগুলো দেখুন</h3>
        <p class="muted-note"><?= e($areaLabel) ?> এলাকায় <?= bn($areaReports) ?>টি সক্রিয় রিপোর্ট রয়েছে। সমর্থন দিলে অগ্রাধিকার বাড়ে।</p>
      </div>
      <a class="btn btn-primary btn-sm" href="issues.php<?= $area !== '' ? '?area=' . urlencode($area) : '' ?>"><?= icon('map-pin', 'icon-sm') ?> এলাকার সমস্যা</a>
    </div>
  </div>

  </div>
</div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
