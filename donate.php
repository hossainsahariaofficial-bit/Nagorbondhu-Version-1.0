<?php
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'অনুদান কার্যক্রম — নগরবন্ধু';
$metaDescription = 'স্থানীয় নাগরিকদের পরিচালিত কমিউনিটি উদ্যোগগুলো দেখুন, খরচের স্বচ্ছ হিসাব জানুন এবং নিজের সামর্থ্য অনুযায়ী অংশ নিন।';
$campaigns = nb_campaigns();
$totalRaised = array_sum(array_column($campaigns, 'raised'));
$totalTarget = array_sum(array_column($campaigns, 'target'));
$totalSupporters = array_sum(array_column($campaigns, 'supporters'));
$activeCampaigns = count(array_filter($campaigns, fn ($c) => $c['status'] === 'চলমান'));
include __DIR__ . '/includes/header.php';
?>

<section class="donate-hero">
  <div class="container donate-hero-inner">
    <div>
      <span class="eyebrow"><?= icon('heart', 'icon-xs') ?> কমিউনিটি অনুদান</span>
      <h1>একটু সহযোগিতায় বদলে যেতে পারে একটি পাড়া</h1>
      <p>স্থানীয় নাগরিকদের উদ্যোগে পরিচালিত কার্যক্রমে অংশ নিন। প্রতিটি উদ্যোগের লক্ষ্য, ব্যয়ের পরিকল্পনা ও অগ্রগতি স্বচ্ছভাবে প্রকাশ করা হয়। এখানে কোনো বাস্তব পেমেন্ট গেটওয়ে নেই—সব লেনদেন ডেমো।</p>
      <div class="section-actions">
        <a class="btn btn-white" href="#campaigns"><?= icon('heart', 'icon-sm') ?> উদ্যোগ দেখুন</a>
        <a class="btn btn-ghost-light" href="donation-history.php"><?= icon('wallet', 'icon-sm') ?> আমার অনুদান</a>
      </div>
    </div>
    <div class="donate-stats">
      <div class="donate-stat"><strong><?= taka($totalRaised) ?></strong><span>মোট সংগৃহীত</span></div>
      <div class="donate-stat"><strong><?= bn($activeCampaigns) ?>টি</strong><span>সক্রিয় উদ্যোগ</span></div>
      <div class="donate-stat"><strong><?= bn($totalSupporters) ?> জন</strong><span>সমর্থক</span></div>
    </div>
  </div>
</section>

<div class="container page-main" id="campaigns">
  <div class="section-head">
    <div class="section-copy">
      <span class="section-kicker">চলমান ও সম্পন্ন উদ্যোগ</span>
      <h2>আপনার পাড়ার উদ্যোগে পাশে দাঁড়ান</h2>
      <p>প্রতিটি কার্যক্রমের জন্য নির্ধারিত লক্ষ্য, সংগৃহীত অর্থ ও ব্যয়ের পরিকল্পনা দেখে সিদ্ধান্ত নিন। ইচ্ছা হলে যেকোনো উদ্যোগের বিস্তারিত পৃষ্ঠায় গিয়ে অনুদানের আগ্রহ জানাতে পারেন।</p>
    </div>
  </div>

  <div class="campaign-grid">
    <?php foreach ($campaigns as $campaign) echo campaign_card($campaign); ?>
  </div>

  <div class="mini-stat-grid mt-3">
    <div class="mini-stat"><strong><?= taka($totalTarget) ?></strong><span>সব উদ্যোগের মিলিত লক্ষ্য</span></div>
    <div class="mini-stat"><strong><?= bn(progress_percent($totalRaised, $totalTarget)) ?>%</strong><span>মিলিত অগ্রগতি</span></div>
    <div class="mini-stat"><strong><?= bn(count($campaigns)) ?>টি</strong><span>মোট উদ্যোগ</span></div>
  </div>

  <div class="section-head mt-3">
    <div class="section-copy">
      <span class="section-kicker">স্বচ্ছতা ও জবাবদিহি</span>
      <h2>টাকার প্রতিটি হিসাব খোলা বইয়ের মতো</h2>
      <p>কোনো উদ্যোগে অনুদান দেওয়ার আগে ব্যয়ের পরিকল্পনা ও হালনাগাদ কাজের তথ্য দেখে নিন।</p>
    </div>
  </div>

  <div class="value-grid">
    <article class="value-card">
      <span class="value-icon"><?= icon('file-text', 'icon-md') ?></span>
      <h3>পরিকল্পিত ব্যয় প্রকাশ</h3>
      <p>প্রতিটি উদ্যোগের পৃষ্ঠায় উপকরণ, শ্রম ও তদারকির আনুমানিক ব্যয়ের তালিকা প্রকাশ করা হয়।</p>
    </article>
    <article class="value-card">
      <span class="value-icon"><?= icon('activity', 'icon-md') ?></span>
      <h3>নিয়মিত অগ্রগতি হালনাগাদ</h3>
      <p>কাজ শুরুর তারিখ, সম্পন্ন অংশ ও পরবর্তী ধাপ নাগরিকদের সঙ্গে শেয়ার করা হয়।</p>
    </article>
    <article class="value-card">
      <span class="value-icon"><?= icon('shield', 'icon-md') ?></span>
      <h3>যাচাইকৃত স্বেচ্ছাসেবী দল</h3>
      <p>স্থানীয় সংগঠক ও নাগরিক কমিটি মিলে কাজের তদারকি করে, যাতে অংশগ্রহণ কার্যকর হয়।</p>
    </article>
  </div>

  <div class="panel mt-3">
    <div class="panel-head">
      <div>
        <h2>অনুদান দেওয়ার নিয়ম</h2>
        <p>ডেমো প্ল্যাটফর্মে কোনো বাস্তব অর্থ লেনদেন হয় না</p>
      </div>
    </div>
    <div class="panel-body">
      <div class="steps">
        <div class="step">
          <span class="step-icon"><?= icon('search', 'icon-md') ?></span>
          <h3>১. উদ্যোগ বেছে নিন</h3>
          <p>লক্ষ্য, এলাকা ও ব্যয়ের পরিকল্পনা দেখে আপনার পাড়ার জন্য গুরুত্বপূর্ণ উদ্যোগটি নির্বাচন করুন।</p>
        </div>
        <div class="step">
          <span class="step-icon"><?= icon('heart', 'icon-md') ?></span>
          <h3>২. পরিমাণ নির্ধারণ করুন</h3>
          <p>সামর্থ্য অনুযায়ী পরিমাণ লিখুন বা প্রস্তাবিত যেকোনো অঙ্কে ক্লিক করুন।</p>
        </div>
        <div class="step">
          <span class="step-icon"><?= icon('check-circle', 'icon-md') ?></span>
          <h3>৩. ডেমো অনুরোধ নিশ্চিত করুন</h3>
          <p>তথ্য জমা দিলে শুধু একটি ডেমো অনুরোধ নথিভুক্ত হয়; কোনো টাকা কাটা হয় না।</p>
        </div>
      </div>
      <div class="admin-note mt-3">
        <?= icon('info', 'icon-sm') ?>
        <span>এই সাইটটি একটি প্রদর্শনী (ডেমো) সংস্করণ। অনুদান ফর্মে দেওয়া তথ্য কোনো সার্ভারে সংরক্ষণ করা হয় না এবং কোনো পেমেন্ট গেটওয়ের সঙ্গে সংযুক্ত নয়।</span>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
