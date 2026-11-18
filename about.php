<?php
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'আমাদের সম্পর্কে — নগরবন্ধু';
$metaDescription = 'নগরবন্ধু একটি নাগরিক উদ্যোগ—স্থানীয় সমস্যা তুলে ধরা, নাগরিকদের একত্র করা এবং স্বচ্ছভাবে সমাধান অনুসরণ করা আমাদের লক্ষ্য।';
$team = [
    ['name' => 'রেহানা পারভীন', 'role' => 'প্রধান সমন্বয়ক', 'initials' => 'রে', 'color' => 'green', 'note' => 'নাগরিক অংশগ্রহণ ও কমিউনিটি সংগঠন'],
    ['name' => 'আরিফুল ইসলাম', 'role' => 'প্রযুক্তি ও তথ্য', 'initials' => 'আ', 'color' => 'blue', 'note' => 'প্ল্যাটফর্ম উন্নয়ন ও তথ্য নিরাপত্তা'],
    ['name' => 'নুসরাত জাহান', 'role' => 'যাচাই ও গবেষণা', 'initials' => 'নু', 'color' => 'violet', 'note' => 'রিপোর্ট যাচাই ও এলাকাভিত্তিক বিশ্লেষণ'],
    ['name' => 'তানভীর আহমেদ', 'role' => 'স্বচ্ছতা ও যোগাযোগ', 'initials' => 'তা', 'color' => 'amber', 'note' => 'কর্তৃপক্ষের সঙ্গে সমন্বয় ও প্রকাশনা'],
];
$values = [
    ['icon' => 'eye', 'title' => 'সমস্যা দৃশ্যমান করা', 'text' => 'ছবি, বিবরণ ও অবস্থানসহ এলাকার সমস্যাগুলো এক জায়গায় তুলে ধরা, যাতে সেগুলো উপেক্ষিত না থাকে।'],
    ['icon' => 'users', 'title' => 'নাগরিকদের এক করা', 'text' => 'সমর্থন, আলোচনা ও স্থানীয় তথ্যের মাধ্যমে সমস্যার গুরুত্ব বোঝানো এবং একসঙ্গে সমাধানের দাবি তৈরি করা।'],
    ['icon' => 'check-circle', 'title' => 'সমাধান অনুসরণ', 'text' => 'রিপোর্টের অবস্থা, বরাদ্দ ও কাজের অগ্রগতি প্রতিটি ধাপে নাগরিকদের সামনে পরিষ্কার রাখা।'],
];
include __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <?= page_heading(
        'নাগরিকের কণ্ঠ, সবার উদ্যোগ',
        'নগরবন্ধু এমন একটি নাগরিক প্ল্যাটফর্ম, যেখানে স্থানীয় সমস্যা রিপোর্ট, সমর্থন, অগ্রগতি অনুসরণ এবং কমিউনিটি উদ্যোগে অংশগ্রহণ করা যায়।',
        ['হোম' => 'index.php', 'আমাদের সম্পর্কে' => null]
    ) ?>
  </div>
</section>

<div class="container page-main">
  <div class="about-grid">
    <div class="about-visual">
      <img src="assets/img/hero-city.svg" alt="নাগরিক উদ্যোগে বদলে যাওয়া একটি শহরের দৃশ্য">
    </div>
    <div class="about-copy">
      <span class="section-kicker">আমাদের লক্ষ্য</span>
      <h2>শহরের সমস্যা নাগরিকের হাতেই সমাধানের পথে</h2>
      <p>নগরবন্ধুর যাত্রা শুরু হয় একটি সরল বিশ্বাস থেকে—এলাকার সমস্যা সবচেয়ে ভালো বোঝেন সেখানকার বাসিন্দারাই। একটি গর্ত, বন্ধ ড্রেন বা অন্ধকার গলি যখন একাধিক নাগরিকের কণ্ঠে পৌঁছায়, তখন সেটি উপেক্ষিত থাকার সুযোগ কমে যায়।</p>
      <p>আমরা নাগরিকের রিপোর্ট, স্বেচ্ছাসেবী যাচাই এবং কর্তৃপক্ষের প্রতিক্রিয়া—এই তিনটি ধারাকে একসঙ্গে যুক্ত করে একটি স্বচ্ছ, অংশগ্রহণমূলক ও তথ্যভিত্তিক নাগরিক সেবা গড়ে তুলতে চাই।</p>
      <ul class="about-points">
        <li><?= icon('check-circle', 'icon-sm') ?><span>প্রতিটি রিপোর্ট গুরুত্বপূর্ণ—ছোট সমস্যাও বড় পরিবর্তনের শুরু হতে পারে।</span></li>
        <li><?= icon('check-circle', 'icon-sm') ?><span>স্বচ্ছতা ও জবাবদিহি—অগ্রগতি ও খরচের হিসাব নাগরিকদের সামনে প্রকাশ।</span></li>
        <li><?= icon('check-circle', 'icon-sm') ?><span>শান্তিপূর্ণ অংশগ্রহণ—পরিবেশ ও সম্প্রদায়ের নিরাপত্তাকে সর্বোচ্চ গুরুত্ব।</span></li>
      </ul>
      <div class="section-actions">
        <a class="btn btn-primary" href="report.php"><?= icon('plus', 'icon-sm') ?> সমস্যা জানান</a>
        <a class="btn btn-outline" href="contact.php"><?= icon('message-circle', 'icon-sm') ?> আমাদের লিখুন</a>
      </div>
    </div>
  </div>

  <div class="about-stat-band mt-3">
    <div><strong><?= bn(count(nb_issues())) ?>+</strong><span>নাগরিক রিপোর্ট</span></div>
    <div><strong><?= bn(count(nb_solved_issues())) ?>টি</strong><span>সমাধান সম্পন্ন</span></div>
    <div><strong><?= bn(count(nb_campaigns())) ?>টি</strong><span>কমিউনিটি উদ্যোগ</span></div>
    <div><strong><?= bn(count(nb_citizens())) ?> জন</strong><span>সক্রিয় নাগরিক</span></div>
  </div>

  <div class="section-head mt-3">
    <div class="section-copy">
      <span class="section-kicker">আমরা যা করি</span>
      <h2>তিনটি মূল কাজে আমাদের মনোযোগ</h2>
      <p>সমস্যা দৃশ্যমান করা, মানুষকে এক করা এবং সমাধান অনুসরণ—এই তিন ধাপে আমরা এগিয়ে চলি।</p>
    </div>
  </div>

  <div class="value-grid">
    <?php foreach ($values as $value): ?>
      <article class="value-card">
        <span class="value-icon"><?= icon($value['icon'], 'icon-md') ?></span>
        <h3><?= e($value['title']) ?></h3>
        <p><?= e($value['text']) ?></p>
      </article>
    <?php endforeach; ?>
  </div>

  <div class="section-head mt-3">
    <div class="section-copy">
      <span class="section-kicker">আমাদের পদ্ধতি</span>
      <h2>যেভাবে কাজ করে নগরবন্ধু</h2>
      <p>একটি রিপোর্ট কেবল একটি অভিযোগ নয়—এটি যাচাই, বরাদ্দ ও সমাধানের একটি স্বচ্ছ যাত্রা।</p>
    </div>
  </div>

  <div class="steps">
    <div class="step">
      <span class="step-icon"><?= icon('camera', 'icon-md') ?></span>
      <h3>১. নাগরিক রিপোর্ট</h3>
      <p>ছবি, বিবরণ ও মানচিত্রে অবস্থানসহ সমস্যাটি জমা পড়ে। একই সমস্যায় অন্যরা সমর্থন ও প্রয়োজনীয় তথ্য যোগ করেন।</p>
    </div>
    <div class="step">
      <span class="step-icon"><?= icon('shield', 'icon-md') ?></span>
      <h3>২. স্বেচ্ছাসেবী যাচাই</h3>
      <p>স্থানীয় স্বেচ্ছাসেবী ও যাচাই দল তথ্য পর্যালোচনা করেন। নির্ভুল রিপোর্ট সংশ্লিষ্ট কর্তৃপক্ষের কাছে অগ্রাধিকার পায়।</p>
    </div>
    <div class="step">
      <span class="step-icon"><?= icon('check-circle', 'icon-md') ?></span>
      <h3>৩. সমাধান ও প্রকাশ</h3>
      <p>কাজের প্রতিটি ধাপ হালনাগাদ করা হয়। সমাধান শেষে আগে ও পরের ছবি এবং ব্যয়ের বিবরণ প্রকাশ করা হয়।</p>
    </div>
  </div>

  <div class="section-head mt-3">
    <div class="section-copy">
      <span class="section-kicker">আমাদের দল</span>
      <h2>একদল নাগরিক, একটি সাধারণ লক্ষ্যে</h2>
      <p>ডেমো দলের সদস্যরা বিভিন্ন পেশা থেকে যোগ দিয়েছেন—সমন্বয়, প্রযুক্তি, গবেষণা ও যোগাযোগে।</p>
    </div>
  </div>

  <div class="team-grid">
    <?php foreach ($team as $member): ?>
      <article class="team-card">
        <?= avatar($member['initials'], $member['color'], 'md') ?>
        <h3><?= e($member['name']) ?></h3>
        <p><?= e($member['role']) ?></p>
        <span><?= e($member['note']) ?></span>
      </article>
    <?php endforeach; ?>
  </div>

  <div class="panel mt-3">
    <div class="panel-body">
      <div class="row-between">
        <div>
          <h2 style="font-size:1.1rem;margin-bottom:4px">আপনার পাড়ার পরিবর্তনে অংশ নিন</h2>
          <p class="muted-note">একটি রিপোর্ট, একটি সমর্থন বা একটি অনুদান—যেভাবেই হোক, আপনার অংশগ্রহণ গুরুত্বপূর্ণ।</p>
        </div>
        <div class="section-actions">
          <a class="btn btn-primary" href="report.php"><?= icon('plus', 'icon-sm') ?> রিপোর্ট করুন</a>
          <a class="btn btn-outline" href="donate.php"><?= icon('heart', 'icon-sm') ?> উদ্যোগ দেখুন</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
