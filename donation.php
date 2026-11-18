<?php
require_once __DIR__ . '/includes/bootstrap.php';
$campaigns = nb_campaigns();
$requestedId = (int) ($_POST['campaign_id'] ?? $_GET['id'] ?? 1);
$campaign = $campaigns[$requestedId] ?? null;
if (!$campaign) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(404);
    }
    $campaign = $campaigns[1];
}
$id = (int) $campaign['id'];
$donationError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) {
        $donationError = 'নিরাপত্তা যাচাই ব্যর্থ হয়েছে। পেজটি রিফ্রেশ করে আবার চেষ্টা করুন।';
    } elseif (!is_citizen()) {
        flash_set('error', 'অনুদান নথিভুক্ত করতে প্রথমে প্রবেশ করুন বা নতুন অ্যাকাউন্ট তৈরি করুন।');
        header('Location: login.php');
        exit;
    } elseif (!db_ok()) {
        $donationError = db_unavailable_message();
    } else {
        $amount = (float) preg_replace('/[^0-9.]/', '', en((string) ($_POST['amount'] ?? '')));
        $donorName = trim((string) ($_POST['name'] ?? ''));
        $donorPhone = trim((string) ($_POST['phone'] ?? ''));
        $method = trim((string) ($_POST['method'] ?? ''));
        $transaction = trim((string) ($_POST['transaction'] ?? ''));
        $consent = isset($_POST['consent']);
        $methods = ['বিকাশ', 'নগদ', 'রকেট', 'ব্যাংক স্থানান্তর'];

        if ($campaign['status'] === 'সম্পন্ন') {
            $donationError = 'এই কার্যক্রমের লক্ষ্য ইতিমধ্যে পূর্ণ হয়েছে, তাই নতুন অনুদান নেওয়া হচ্ছে না।';
        } elseif ($amount < 10) {
            $donationError = 'সর্বনিম্ন ১০ টাকা অনুদান দিতে হবে।';
        } elseif ($amount > 10000000) {
            $donationError = 'একবারে অনুদানের পরিমাণ অতিরিক্ত বড়। কম পরিমাণে আবার চেষ্টা করুন।';
        } elseif ($donorName === '') {
            $donationError = 'আপনার নাম লিখুন।';
        } elseif ($donorPhone !== '' && !valid_bd_phone($donorPhone)) {
            $donationError = 'সঠিক ১১ সংখ্যার মোবাইল নম্বর লিখুন অথবা ঘরটি ফাঁকা রাখুন।';
        } elseif (!in_array($method, $methods, true)) {
            $donationError = 'পেমেন্ট মাধ্যম সঠিকভাবে নির্বাচন করুন।';
        } elseif (!$consent) {
            $donationError = 'অনুদান নথিভুক্ত করতে শর্তাবলি ও গোপনীয়তা নীতিতে সম্মতি দিন।';
        } else {
            $citizenId = (int) current_citizen()['id'];
            $donationId = db_insert(
                'INSERT INTO donations
                    (user_id, campaign_id, campaign_title, donor_name, donor_phone, amount, method, transaction_id, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $citizenId,
                    $id,
                    (string) $campaign['title'],
                    $donorName,
                    $donorPhone !== '' ? normalize_phone($donorPhone) : null,
                    $amount,
                    $method,
                    $transaction !== '' ? $transaction : null,
                    'pending',
                ]
            );

            if ($donationId <= 0) {
                $donationError = 'অনুদানের অনুরোধ সংরক্ষণ করা যায়নি। ' . (db_error() !== '' ? 'ডেটাবেজ ত্রুটি: ' . db_error() : 'আবার চেষ্টা করুন।');
            } else {
                flash_set('success', 'আপনার অনুদানের অনুরোধ সংরক্ষিত হয়েছে। যাচাই সম্পন্ন হলে ইতিহাসে অবস্থা হালনাগাদ হবে।');
                header('Location: donation-history.php');
                exit;
            }
        }
    }
}

$pct = progress_percent($campaign['raised'], $campaign['target']);
$remaining = max(0, (int) $campaign['target'] - (int) $campaign['raised']);
$expenseTotal = array_sum(array_column($campaign['expenses'], 'amount'));
$done = $campaign['status'] === 'সম্পন্ন';
$citizen = current_citizen();
$issue = nb_issue((int) $campaign['issue_id']);
$related = array_slice(array_values(array_filter($campaigns, fn ($c) => $c['id'] !== $id)), 0, 3);
$pageTitle = $campaign['title'] . ' — অনুদান — নগরবন্ধু';
$metaDescription = $campaign['short'];
include __DIR__ . '/includes/header.php';
?>

<div class="container donation-page">
  <nav class="breadcrumb" aria-label="আপনি এখানে আছেন">
    <a href="index.php">হোম</a><?= icon('chevron-right', 'icon-xs') ?>
    <a href="donate.php">অনুদান কার্যক্রম</a><?= icon('chevron-right', 'icon-xs') ?>
    <span aria-current="page"><?= e(excerpt($campaign['title'], 42)) ?></span>
  </nav>

  <?php if ($donationError !== ''): ?>
    <div class="alert alert-danger" role="alert" style="margin-bottom:18px"><?= icon('alert-circle', 'icon-sm') ?><div><?= e($donationError) ?></div></div>
  <?php endif; ?>

  <div class="donation-layout">
    <div class="donation-main">
      <div class="donation-cover">
        <img src="<?= e($campaign['image']) ?>" alt="<?= e($campaign['title']) ?>">
      </div>

      <div class="badge-row">
        <span class="badge badge-<?= $done ? 'success' : 'warning' ?>"><?= e($campaign['status']) ?></span>
        <span class="badge badge-soft"><?= icon('heart', 'icon-xs') ?> কমিউনিটি উদ্যোগ</span>
        <?= category_badge($issue['type']) ?>
      </div>

      <h1><?= e($campaign['title']) ?></h1>

      <div class="donation-org-line">
        <span><?= icon('building', 'icon-sm') ?> <?= e($campaign['organizer']) ?></span>
        <span><?= icon('map-pin', 'icon-sm') ?> <?= e($issue['area']) ?>, <?= e($issue['district']) ?></span>
        <span><?= icon('users', 'icon-sm') ?> <?= bn($campaign['supporters']) ?> জন সমর্থক</span>
        <span><?= icon('clock', 'icon-sm') ?> <?= $done ? 'সম্পন্ন হয়েছে' : e($campaign['deadline']) . ' পর্যন্ত' ?></span>
      </div>

      <div class="donation-body">
        <section>
          <h2>উদ্যোগের বিবরণ</h2>
          <p><?= e($campaign['description']) ?></p>
        </section>

        <section>
          <h2>ব্যয়ের পরিকল্পনা</h2>
          <div class="expense-list">
            <?php foreach ($campaign['expenses'] as $expense): ?>
              <div class="expense-item">
                <span><?= e($expense['label']) ?></span>
                <strong><?= taka($expense['amount']) ?></strong>
              </div>
            <?php endforeach; ?>
          </div>
          <div class="expense-total">
            <span>পরিকল্পিত মোট ব্যয়</span>
            <span><?= taka($expenseTotal) ?></span>
          </div>
          <?php if ($expenseTotal < (int) $campaign['target']): ?>
            <p class="muted-note mt-2">লক্ষ্যের অবশিষ্ট <?= taka((int) $campaign['target'] - $expenseTotal) ?> অপ্রত্যাশিত খরচ ও জরুরি তহবিলের জন্য রাখা হয়েছে।</p>
          <?php endif; ?>
        </section>

        <section>
          <h2>অগ্রগতির হালনাগাদ</h2>
          <div class="update-feed">
            <?php foreach ($campaign['updates'] as $update): ?>
              <div class="update-item">
                <h4><?= e($update['title']) ?></h4>
                <time><?= icon('calendar', 'icon-xs') ?> <?= e($update['date']) ?></time>
                <p><?= e($update['note']) ?></p>
              </div>
            <?php endforeach; ?>
          </div>
        </section>

        <section>
          <h2>কেন এই উদ্যোগ?</h2>
          <p>এই উদ্যোগটি সরাসরি <a href="issue.php?id=<?= (int) $issue['id'] ?>">“<?= e($issue['title']) ?>”</a> সমস্যার সঙ্গে যুক্ত। নাগরিকদের সমর্থন ও স্থানীয় সংগঠকের উদ্যোগে সমস্যাটি দীর্ঘমেয়াদে সমাধানের লক্ষ্যে কাজ করা হচ্ছে।</p>
        </section>
      </div>
    </div>

    <aside class="donate-side">
      <div class="donate-box">
        <div class="donate-numbers">
          <strong><?= taka($campaign['raised']) ?></strong>
          <span>লক্ষ্য <?= taka($campaign['target']) ?></span>
        </div>
        <?= progress_bar($campaign['raised'], $campaign['target']) ?>
        <div class="donate-meta">
          <span><?= bn($pct) ?>% পূর্ণ</span>
          <span>বাকি <?= taka($remaining) ?></span>
        </div>
        <div class="donate-meta">
          <span><?= icon('users', 'icon-xs') ?> <?= bn($campaign['supporters']) ?> জন</span>
          <span><?= icon('clock', 'icon-xs') ?> <?= $done ? 'সম্পন্ন' : bn($campaign['days_left']) . ' দিন বাকি' ?></span>
        </div>

        <?php if ($done): ?>
          <div class="success-panel mt-2">
            <span><?= icon('check-circle', 'icon-md') ?></span>
            <h2>লক্ষ্য পূরণ হয়েছে</h2>
            <p>এই উদ্যোগের প্রয়োজনীয় অর্থ সংগ্রহ সম্পন্ন হয়েছে। কাজের ফলাফল ও ব্যয়ের বিবরণ উপরে দেখুন।</p>
          </div>
          <a class="btn btn-outline btn-block mt-2" href="issue.php?id=<?= (int) $issue['id'] ?>"><?= icon('arrow-right', 'icon-sm') ?> সমাধানের বিস্তারিত</a>
        <?php else: ?>
          <div class="field mt-2">
            <span class="field-label">প্রস্তাবিত পরিমাণ</span>
            <div class="amount-grid">
              <?php foreach ([100, 200, 500, 1000, 2000, 5000] as $amount): ?>
                <button class="amount-btn <?= $amount === 500 ? 'active' : '' ?>" type="button" data-amount="<?= $amount ?>">৳ <?= bn(number_format($amount)) ?></button>
              <?php endforeach; ?>
            </div>
          </div>

          <form class="donate-form" method="post" action="donation.php?id=<?= (int) $id ?>" data-donation-form>
            <?= csrf_field() ?>
            <input type="hidden" name="campaign_id" value="<?= (int) $id ?>">

            <div class="field">
              <label class="field-label" for="donationAmount">অনুদানের পরিমাণ <span class="req">*</span></label>
              <input class="input" id="donationAmount" name="amount" type="text" inputmode="numeric" value="500" data-rule="amount" required>
              <span class="field-hint">সর্বনিম্ন ১০ টাকা। শুধু সংখ্যা লিখুন।</span>
            </div>

            <div class="field">
              <label class="field-label" for="donorName">আপনার নাম <span class="req">*</span></label>
              <input class="input" id="donorName" name="name" type="text" value="<?= e($citizen['name']) ?>" required>
            </div>

            <div class="field">
              <label class="field-label" for="donorPhone">মোবাইল নম্বর</label>
              <input class="input" id="donorPhone" name="phone" type="tel" value="<?= e($citizen['phone']) ?>" data-rule="phone" placeholder="০১৭xxxxxxxx">
            </div>

            <div class="field">
              <label class="field-label" for="paymentMethod">পেমেন্ট মাধ্যম <span class="req">*</span></label>
              <select class="select" id="paymentMethod" name="method" required>
                <option value="বিকাশ">বিকাশ</option>
                <option value="নগদ">নগদ</option>
                <option value="রকেট">রকেট</option>
                <option value="ব্যাংক স্থানান্তর">ব্যাংক স্থানান্তর</option>
              </select>
            </div>

            <div class="field" data-transaction-row hidden>
              <label class="field-label" for="transactionId">লেনদেন নম্বর</label>
              <input class="input" id="transactionId" name="transaction" type="text" placeholder="বিকাশ/নগদ ট্রানজেকশন আইডি লিখুন">
            </div>

            <div class="donate-summary" id="donationSummary">
              <span>আপনার অনুদান</span><strong>৳ ৫০০</strong>
            </div>

            <label class="checkbox">
              <input type="checkbox" name="consent" required>
              <span><a href="terms.php">ব্যবহারের শর্তাবলি</a> ও <a href="privacy.php">গোপনীয়তা নীতি</a> পড়েছি এবং সম্মত আছি।</span>
            </label>

            <button class="btn btn-primary btn-block" type="submit"><?= icon('heart', 'icon-sm') ?> অনুদানের আগ্রহ জানান</button>

            <div class="demo-note">
              <?= icon('info', 'icon-sm') ?>
              <span>এটি অনলাইন পেমেন্ট গেটওয়ে নয়। আপনার অনুদানের অনুরোধ প্রশাসনিক যাচাইয়ের জন্য ডেটাবেজে সংরক্ষিত হবে; কোনো বাস্তব টাকা এখনই কাটা হবে না।</span>
            </div>
          </form>
        <?php endif; ?>
      </div>

      <div class="donate-box">
        <h2 style="font-size:.9rem;margin-bottom:12px">আয়োজক</h2>
        <div class="organizer-card">
          <?= avatar(mb_substr($campaign['organizer'], 0, 2, 'UTF-8'), 'green', 'md') ?>
          <div>
            <strong><?= e($campaign['organizer']) ?></strong>
            <span><?= e($campaign['organizer_type']) ?></span>
            <p><?= icon('phone', 'icon-xs') ?> <?= e($campaign['organizer_contact']) ?></p>
          </div>
        </div>
        <div class="transparency-box mt-3">
          <strong><?= icon('shield', 'icon-xs') ?> স্বচ্ছতার প্রতিশ্রুতি</strong>
          সংগৃহীত অর্থের প্রতিটি খরচের হিসাব কার্যক্রমের পৃষ্ঠায় প্রকাশ করা হয়। সন্দেহজনক কিছু দেখলে <a href="contact.php">সহায়তা দলে জানান</a>।
        </div>
      </div>

      <div class="donate-box">
        <h3 style="font-size:.86rem;margin-bottom:7px">অনুদানের আগে জেনে নিন</h3>
        <div class="note-list">
          <div class="note-item"><?= icon('info', 'icon-xs') ?><span>অনুদানের অনুরোধ ডেটাবেজে সংরক্ষিত হয় এবং প্রশাসনিক যাচাইয়ের পর নিশ্চিত করা হয়।</span></div>
          <div class="note-item"><?= icon('file-text', 'icon-xs') ?><span>ব্যয়ের পরিকল্পনা ও অগ্রগতির হালনাগাদ নিয়মিত দেখা যায়।</span></div>
          <div class="note-item"><?= icon('message-circle', 'icon-xs') ?><span>কোনো প্রশ্ন থাকলে <a href="faq.php">সাধারণ জিজ্ঞাসা</a> দেখুন।</span></div>
        </div>
        <a class="btn btn-outline btn-block mt-2" href="donation-history.php"><?= icon('wallet', 'icon-sm') ?> আমার অনুদানের ইতিহাস</a>
      </div>
    </aside>
  </div>

  <?php if ($related): ?>
    <div class="section-head mt-3">
      <div class="section-copy">
        <span class="section-kicker">আরও উদ্যোগ</span>
        <h2>অন্য যেসব কার্যক্রমে অংশ নিতে পারেন</h2>
      </div>
      <div class="section-actions"><a class="btn btn-light" href="donate.php">সব উদ্যোগ <?= icon('arrow-right', 'icon-sm') ?></a></div>
    </div>
    <div class="campaign-grid">
      <?php foreach ($related as $item) echo campaign_card($item); ?>
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
