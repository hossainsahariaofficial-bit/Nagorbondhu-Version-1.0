<?php
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'নগরবন্ধু — নাগরিকের কণ্ঠ, নগরের পরিবর্তন';
$metaDescription = 'নগরবন্ধু — বাংলাদেশের নাগরিকদের জন্য স্থানীয় সমস্যা রিপোর্ট, কমিউনিটি অংশগ্রহণ, স্বচ্ছ সমাধান পর্যবেক্ষণ ও অনুদান কার্যক্রমের প্ল্যাটফর্ম।';
$issues = nb_issues();
$campaigns = nb_campaigns();
$highlight = [$issues[1], $issues[3], $issues[6], $issues[9], $issues[11], $issues[5]];
include __DIR__ . '/includes/header.php';
?>

<section class="hero">
  <div class="container hero-grid">
    <div class="hero-copy">
      <span class="eyebrow"><span class="eyebrow-dot"></span> নাগরিকের কণ্ঠ, নগরের পরিবর্তন</span>
      <h1>আপনার পাড়ার সমস্যা,<br><em>সবার উদ্যোগে</em> সমাধান</h1>
      <p class="hero-lead">রাস্তার গর্ত, জমে থাকা ময়লা কিংবা নষ্ট সড়কবাতি—আপনার চোখে পড়া সমস্যাটি জানান। প্রতিবেশীদের সমর্থন নিন, কর্তৃপক্ষের পদক্ষেপ অনুসরণ করুন এবং বদলে ফেলুন নিজের এলাকা।</p>
      <div class="hero-actions">
        <a class="btn btn-primary btn-lg" href="report.php"><?= icon('plus', 'icon-sm') ?> সমস্যা জানান</a>
        <a class="btn btn-outline btn-lg" href="issues.php">সমস্যাসমূহ দেখুন <?= icon('arrow-right', 'icon-sm') ?></a>
      </div>
      <div class="hero-trust">
        <div class="avatar-stack">
          <?= avatar('সা', 'green', 'sm') ?><?= avatar('রা', 'blue', 'sm') ?><?= avatar('মা', 'amber', 'sm') ?><?= avatar('নু', 'violet', 'sm') ?>
        </div>
        <p><strong>২,৪০০+ নাগরিক</strong>ইতিমধ্যে নিজের এলাকার জন্য কাজ করছেন</p>
      </div>
      <div class="hero-points">
        <span><?= icon('check-circle', 'icon-xs') ?> যাচাইকৃত রিপোর্ট</span>
        <span><?= icon('check-circle', 'icon-xs') ?> স্বচ্ছ অগ্রগতি</span>
        <span><?= icon('check-circle', 'icon-xs') ?> বিনামূল্যে ব্যবহার</span>
      </div>
    </div>

    <div class="hero-visual">
      <span class="hero-shape" aria-hidden="true"></span>
      <span class="hero-shape-2" aria-hidden="true"></span>
      <div class="hero-photo">
        <img src="assets/img/hero-city.svg" alt="ঢাকা শহরের রাস্তা, ভবন ও নাগরিকদের সম্মিলিত উদ্যোগের চিত্র">
        <div class="hero-photo-label"><?= icon('map-pin', 'icon-sm') ?> মিরপুর ১০ গোলচত্বর — আজ সকালে রিপোর্ট করা হয়েছে</div>
      </div>
      <div class="floating-card float-report">
        <span class="float-icon green"><?= icon('check-circle', 'icon-sm') ?></span>
        <div><strong>সমাধান সম্পন্ন</strong><small>রামপুরা স্কুলের সামনের গর্ত</small></div>
      </div>
      <div class="floating-card float-solved">
        <span class="float-icon amber"><?= icon('heart', 'icon-sm') ?></span>
        <div><strong>১২৮ জন সমর্থন দিয়েছেন</strong><small>মিরপুরের একটি সমস্যায়</small></div>
      </div>
    </div>
  </div>
</section>

<div class="container stats-wrap">
  <div class="stats-band">
    <div class="stat-card">
      <span class="stat-icon green"><?= icon('file-text', 'icon-md') ?></span>
      <div><div class="stat-value mono-bn">১,২৮৪</div><div class="stat-label">মোট নাগরিক রিপোর্ট</div><span class="stat-trend"><?= icon('trending-up', 'icon-xs') ?> গত মাসে ১৮% বৃদ্ধি</span></div>
    </div>
    <div class="stat-card">
      <span class="stat-icon blue"><?= icon('check-circle', 'icon-md') ?></span>
      <div><div class="stat-value mono-bn">৮৬৭</div><div class="stat-label">সমাধান সম্পন্ন হয়েছে</div><span class="stat-trend"><?= icon('trending-up', 'icon-xs') ?> ৬৮% সফলতার হার</span></div>
    </div>
    <div class="stat-card">
      <span class="stat-icon violet"><?= icon('users', 'icon-md') ?></span>
      <div><div class="stat-value mono-bn">২,৪০০+</div><div class="stat-label">সক্রিয় নাগরিক</div><span class="stat-trend"><?= icon('trending-up', 'icon-xs') ?> ৯৮টি এলাকায় সক্রিয়</span></div>
    </div>
    <div class="stat-card">
      <span class="stat-icon amber"><?= icon('wallet', 'icon-md') ?></span>
      <div><div class="stat-value mono-bn">৳ ৩.২ লাখ</div><div class="stat-label">কমিউনিটি অনুদান সংগ্রহ</div><span class="stat-trend"><?= icon('heart', 'icon-xs') ?> ৪টি চলমান উদ্যোগ</span></div>
    </div>
  </div>
</div>

<section class="section section-tint" aria-labelledby="categoryTitle">
  <div class="container">
    <div class="section-head">
      <div class="section-copy">
        <span class="section-kicker">কী ধরনের সমস্যা জানাতে পারেন</span>
        <h2 id="categoryTitle">আপনার এলাকার সমস্যাটি বেছে নিন</h2>
        <p>সাতটি প্রধান ক্যাটাগরিতে রিপোর্ট করুন। প্রতিটি রিপোর্ট নির্দিষ্ট কর্তৃপক্ষের কাছে পাঠানো হয়।</p>
      </div>
      <div class="section-actions"><a class="btn btn-light" href="issues.php">সব ক্যাটাগরি <?= icon('arrow-right', 'icon-sm') ?></a></div>
    </div>
    <div class="category-grid">
      <?php foreach (nb_categories() as $key => $cat): ?>
        <a class="category-tile" href="issues.php?type=<?= e($key) ?>">
          <span class="category-icon"><?= icon($cat['icon'], 'icon-md') ?></span>
          <strong><?= e($cat['label']) ?></strong>
          <span><?= bn(array_sum(array_map(fn ($i) => $i['type'] === $key ? 1 : 0, $issues))) ?>টি চলমান রিপোর্ট</span>
          <?= icon('chevron-right', 'icon-sm', 'category-arrow') ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section" id="recentIssues" aria-labelledby="recentTitle">
  <div class="container">
    <div class="section-head">
      <div class="section-copy">
        <span class="section-kicker">পাড়ার খবর রাখুন</span>
        <h2 id="recentTitle">আপনার আশপাশে যা ঘটছে</h2>
        <p>সাম্প্রতিক নাগরিক রিপোর্টগুলো দেখুন, সমর্থন দিন এবং সমাধানের পথে পাশে থাকুন।</p>
      </div>
      <div class="section-actions"><a class="btn btn-light" href="issues.php">সব সমস্যা দেখুন <?= icon('arrow-right', 'icon-sm') ?></a></div>
    </div>
    <div class="issue-grid">
      <?php foreach ($highlight as $issue) echo issue_card($issue); ?>
    </div>
    <div class="center-action">
      <a class="btn btn-outline" href="issues.php">আরও ১২টি রিপোর্ট দেখুন <?= icon('arrow-right', 'icon-sm') ?></a>
    </div>
  </div>
</section>

<section class="section section-bg" aria-labelledby="campaignTitle">
  <div class="container">
    <div class="section-head">
      <div class="section-copy">
        <span class="section-kicker">কমিউনিটি অনুদান</span>
        <h2 id="campaignTitle">একটু সহযোগিতায় বদলে যেতে পারে একটি পাড়া</h2>
        <p>স্থানীয় নাগরিকদের উদ্যোগে পরিচালিত কার্যক্রমে অংশ নিন। প্রতিটি খরচের হিসাব স্বচ্ছভাবে প্রকাশ করা হয়।</p>
      </div>
      <div class="section-actions"><a class="btn btn-light" href="donate.php">সব উদ্যোগ <?= icon('arrow-right', 'icon-sm') ?></a></div>
    </div>
    <div class="campaign-grid">
      <?php foreach (array_slice($campaigns, 0, 3) as $campaign) echo campaign_card($campaign); ?>
    </div>
  </div>
</section>

<section class="section" aria-labelledby="howTitle">
  <div class="container">
    <div class="section-head">
      <div class="section-copy">
        <span class="section-kicker">সহজ তিন ধাপ</span>
        <h2 id="howTitle">অভিযোগ থেকে সমাধান, একসঙ্গে</h2>
        <p>নগরবন্ধুতে একটি রিপোর্ট কীভাবে সমাধানে পৌঁছায়, তা জেনে নিন।</p>
      </div>
    </div>
    <div class="steps">
      <div class="step">
        <span class="step-icon"><?= icon('camera', 'icon-md') ?></span>
        <h3>১. সমস্যা জানান</h3>
        <p>ছবি, সংক্ষিপ্ত বিবরণ ও নির্ভুল অবস্থান দিয়ে এলাকার সমস্যাটি জমা দিন। মাত্র দুই মিনিটে রিপোর্ট সম্পন্ন হবে।</p>
      </div>
      <div class="step">
        <span class="step-icon"><?= icon('users', 'icon-md') ?></span>
        <h3>২. প্রতিবেশীদের সমর্থন নিন</h3>
        <p>একই সমস্যায় ভুগছে এমন নাগরিকরা সমর্থন জানাবেন ও প্রয়োজনীয় তথ্য যোগ করবেন। বেশি সমর্থন মানে বেশি অগ্রাধিকার।</p>
      </div>
      <div class="step">
        <span class="step-icon"><?= icon('check-circle', 'icon-md') ?></span>
        <h3>৩. সমাধান অনুসরণ করুন</h3>
        <p>যাচাই, বরাদ্দ ও কাজের অগ্রগতি প্রতিটি ধাপে দেখুন। সমাধান শেষে আগে ও পরের ছবিসহ প্রমাণ প্রকাশ করা হয়।</p>
      </div>
    </div>
  </div>
</section>

<section class="section section-tint impact-section" aria-labelledby="impactTitle">
  <div class="container impact-grid">
    <div>
      <div class="compare-frame" data-compare>
        <img class="compare-after" src="assets/img/after-pothole.svg" alt="মেরামতের পর মসৃণ রাস্তা">
        <img class="compare-before" src="assets/img/before-pothole.svg" alt="মেরামতের আগে গর্তযুক্ত রাস্তা">
        <span class="compare-label before">আগে</span>
        <span class="compare-label after">পরে</span>
        <span class="compare-handle" aria-hidden="true"></span>
        <input type="range" min="0" max="100" value="52" step="1" aria-label="আগে ও পরের ছবি তুলনা করুন" data-compare-range>
      </div>
      <p class="compare-note"><?= icon('sliders', 'icon-xs') ?> টেনে দেখুন আগে ও পরে — রামপুরা স্কুলের সামনের সড়ক</p>
    </div>
    <div class="impact-copy">
      <span class="section-kicker">বাস্তব পরিবর্তন</span>
      <h2 id="impactTitle">রিপোর্ট শুধু কাগজে থাকে না, বদলে দেয় বাস্তবতা</h2>
      <p>গত এক বছরে নাগরিকদের সম্মিলিত প্রচেষ্টায় শহরের নানা প্রান্তে ছোট ছোট পরিবর্তন এসেছে। প্রতিটি সমাধানের পেছনে রয়েছে নাগরিকদের ধৈর্য, সমর্থন ও কর্তৃপক্ষের দায়বদ্ধতা।</p>
      <div class="impact-stats">
        <div class="impact-stat"><strong>৮৬৭</strong><span>সমাধান সম্পন্ন রিপোর্ট</span></div>
        <div class="impact-stat"><strong>৩২ ঘণ্টা</strong><span>গড় যাচাই সময়</span></div>
        <div class="impact-stat"><strong>৯৮টি</strong><span>সক্রিয় এলাকা</span></div>
        <div class="impact-stat"><strong>৬৮%</strong><span>সমাধানের সফলতার হার</span></div>
      </div>
      <div class="impact-case"><?= icon('map-pin', 'icon-sm') ?> সর্বশেষ সমাধান: রামপুরা স্কুলের সামনের বিপজ্জনক গর্ত মেরামত সম্পন্ন</div>
      <a class="btn btn-primary" href="issues.php?status=solved">সমাধানের গল্পগুলো দেখুন <?= icon('arrow-right', 'icon-sm') ?></a>
    </div>
  </div>
</section>

<section class="section" aria-labelledby="voiceTitle">
  <div class="container">
    <div class="section-head">
      <div class="section-copy">
        <span class="section-kicker">নাগরিকদের কথা</span>
        <h2 id="voiceTitle">যাঁরা বদল এনেছেন</h2>
        <p>নগরবন্ধু ব্যবহার করে নিজের এলাকায় পরিবর্তন এনেছেন এমন কয়েকজন নাগরিকের অভিজ্ঞতা।</p>
      </div>
    </div>
    <div class="testimonial-grid">
      <figure class="testimonial-card">
        <?= stars(5) ?>
        <blockquote>“রিপোর্ট করার দুই সপ্তাহের মধ্যে রাস্তার গর্তটি মেরামত হয়েছে। প্রতিটি ধাপে নোটিফিকেশন পেয়েছি—এমন স্বচ্ছতা আগে দেখিনি।”</blockquote>
        <figcaption><?= avatar('সা', 'green', 'md') ?><div><strong>সাদিয়া আফরিন</strong><span>মিরপুর ১০, ঢাকা</span></div></figcaption>
      </figure>
      <figure class="testimonial-card">
        <?= stars(5) ?>
        <blockquote>“আমাদের গলির ড্রেন পরিষ্কারের কাজ শুরু হয়েছে। প্রতিবেশীদের সমর্থন আর কমিউনিটি অনুদানের হিসাব সবই দেখা যায়।”</blockquote>
        <figcaption><?= avatar('মা', 'amber', 'md') ?><div><strong>মাহমুদুল ইসলাম</strong><span>কাজীপাড়া, ঢাকা</span></div></figcaption>
      </figure>
      <figure class="testimonial-card">
        <?= stars(4) ?>
        <blockquote>“শাহবাগের ময়লার সমস্যাটি আমরা একসঙ্গে জানিয়েছি। এখন নিয়মিত সংগ্রহ শুরু হয়েছে। সবার অংশগ্রহণই আসল শক্তি।”</blockquote>
        <figcaption><?= avatar('রা', 'blue', 'md') ?><div><strong>রাকিব হাসান</strong><span>শাহবাগ, ঢাকা</span></div></figcaption>
      </figure>
    </div>
  </div>
</section>

<section class="cta-band">
  <div class="container cta-band-inner">
    <div>
      <h2>আপনার পাড়ার পরিবর্তন শুরু হোক আপনার হাতেই</h2>
      <p>একটি রিপোর্ট, একটি সমর্থন—পরিবর্তনের পথে ছোট্ট পদক্ষেপই যথেষ্ট।</p>
    </div>
    <div class="hero-actions" style="margin:0">
      <a class="btn btn-white" href="report.php"><?= icon('plus', 'icon-sm') ?> সমস্যা জানান</a>
      <a class="btn btn-ghost-light" href="register.php">নাগরিক হিসেবে যোগ দিন</a>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
