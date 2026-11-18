<?php
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'সমস্যাসমূহ — নগরবন্ধু';
$metaDescription = 'আপনার এলাকার নাগরিক রিপোর্টগুলো খুঁজুন, ধরন ও অবস্থা অনুযায়ী ফিল্টার করুন এবং সমাধানের অগ্রগতি অনুসরণ করুন।';
$issues = nb_issues();
$areas = array_values(array_unique(array_map(fn ($issue) => $issue['area'], $issues)));
sort($areas);
include __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <?= page_heading(
        'আপনার এলাকার সমস্যাগুলো',
        'সমস্যা খুঁজুন, সমর্থন জানান এবং কোন রিপোর্ট কোন ধাপে আছে তা এক নজরে দেখুন। প্রতিটি সমর্থন একটি সমস্যাকে অগ্রাধিকার তালিকায় এগিয়ে নেয়।',
        ['হোম' => 'index.php', 'সমস্যাসমূহ' => null]
    ) ?>
  </div>
</section>

<div class="container page-main" data-issues-page>
  <div class="filter-panel">
    <div class="filter-top">
      <div class="search-input">
        <?= icon('search', 'icon-sm') ?>
        <label class="sr-only" for="issueSearch">সমস্যা, এলাকা বা রিপোর্ট নম্বর দিয়ে খুঁজুন</label>
        <input class="input" id="issueSearch" type="search" placeholder="সমস্যার শিরোনাম, এলাকা বা রিপোর্ট নম্বর লিখুন…" autocomplete="off">
      </div>
      <a class="btn btn-primary" href="report.php"><?= icon('plus', 'icon-sm') ?> নতুন রিপোর্ট</a>
    </div>
    <div class="filter-grid">
      <div class="field">
        <label class="sr-only" for="filterType">সমস্যার ধরন</label>
        <select class="select" id="filterType">
          <option value="">সব ধরন</option>
          <?php foreach (nb_categories() as $key => $cat): ?>
            <option value="<?= e($key) ?>"><?= e($cat['label']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label class="sr-only" for="filterArea">এলাকা</label>
        <select class="select" id="filterArea">
          <option value="">সব এলাকা</option>
          <?php foreach ($areas as $area): ?>
            <option value="<?= e($area) ?>"><?= e($area) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label class="sr-only" for="filterStatus">অবস্থা</label>
        <select class="select" id="filterStatus">
          <option value="">সব অবস্থা</option>
          <?php foreach (nb_statuses() as $key => $status): ?>
            <option value="<?= e($key) ?>"><?= e($status['label']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label class="sr-only" for="filterUrgency">জরুরি মাত্রা</label>
        <select class="select" id="filterUrgency">
          <option value="">সব অগ্রাধিকার</option>
          <?php foreach (nb_urgency() as $key => $label): ?>
            <option value="<?= e($key) ?>"><?= e($label) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label class="sr-only" for="sortIssues">সাজানোর ক্রম</label>
        <select class="select" id="sortIssues">
          <option value="newest">সর্বশেষ আগে</option>
          <option value="oldest">পুরোনো আগে</option>
          <option value="support">সর্বাধিক সমর্থিত</option>
        </select>
      </div>
    </div>
    <div class="filter-bottom">
      <div class="active-filters" id="activeFilters" aria-live="polite"></div>
      <button class="btn btn-sm btn-text" type="button" id="resetFilters" hidden><?= icon('x', 'icon-xs') ?> ফিল্টার মুছুন</button>
    </div>
  </div>

  <div class="results-bar">
    <p class="results-count" id="issueCount"><strong><?= bn(count($issues)) ?></strong>টি সমস্যা পাওয়া গেছে</p>
    <div class="view-toggle" role="group" aria-label="দেখার ধরন">
      <button type="button" class="active" data-view="grid" aria-label="কার্ড ভিউ" aria-pressed="true"><?= icon('grid', 'icon-sm') ?></button>
      <button type="button" data-view="list" aria-label="তালিকা ভিউ" aria-pressed="false"><?= icon('list', 'icon-sm') ?></button>
    </div>
  </div>

  <div class="issue-grid" id="issueGrid">
    <?php foreach ($issues as $issue) echo issue_card($issue); ?>
  </div>

  <div id="issueEmpty" hidden>
    <?= empty_state('কোনো সমস্যা পাওয়া যায়নি', 'আপনার ফিল্টার বা অনুসন্ধান শব্দ পরিবর্তন করে আবার চেষ্টা করুন।', 'search', '<a class="btn btn-primary btn-sm" href="report.php">' . icon('plus', 'icon-sm') . ' নতুন সমস্যা জানান</a>') ?>
  </div>

  <p class="pagination-info">ডেমো তালিকা — সব রিপোর্ট একই পৃষ্ঠায় দেখানো হয়েছে। অনুসন্ধান ও ফিল্টার সঙ্গে সঙ্গে ফলাফল হালনাগাদ করে।</p>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
