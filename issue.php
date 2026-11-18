<?php
require_once __DIR__ . '/includes/bootstrap.php';
$id = (int) ($_GET['id'] ?? 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');
    $back = 'issue.php?id=' . $id;

    if (!csrf_valid()) {
        flash_set('error', 'নিরাপত্তা যাচাই ব্যর্থ হয়েছে। পেজটি রিফ্রেশ করে আবার চেষ্টা করুন।');
        header('Location: ' . $back);
        exit;
    }
    if (!is_citizen()) {
        flash_set('error', 'এই কাজটি করতে প্রথমে প্রবেশ করুন বা নতুন অ্যাকাউন্ট তৈরি করুন।');
        header('Location: login.php');
        exit;
    }
    if (!db_ok()) {
        flash_set('error', db_unavailable_message());
        header('Location: ' . $back);
        exit;
    }

    $userId = (int) current_citizen()['id'];

    if ($action === 'support') {
        $result = db_exec('INSERT IGNORE INTO supports (issue_id, user_id) VALUES (?, ?)', [$id, $userId]);
        if ($result < 0) {
            flash_set('error', 'সমর্থন সংরক্ষণ করা যায়নি। আবার চেষ্টা করুন।');
        } elseif ($result === 0) {
            flash_set('success', 'আপনি ইতিমধ্যে এই রিপোর্টে সমর্থন জানিয়েছেন।');
        } else {
            flash_set('success', 'আপনার সমর্থন সংরক্ষিত হয়েছে। এই সমস্যাটি এখন আরও মানুষের নজরে আসবে।');
        }
        header('Location: ' . $back);
        exit;
    }

    if ($action === 'comment') {
        $body = trim((string) ($_POST['body'] ?? ''));
        if (mb_strlen($body) < 3) {
            flash_set('error', 'মন্তব্য কমপক্ষে ৩ অক্ষরের হতে হবে।');
        } elseif (mb_strlen($body) > 500) {
            flash_set('error', 'মন্তব্য সর্বোচ্চ ৫০০ অক্ষরের হতে হবে।');
        } else {
            $created = db_insert('INSERT INTO comments (issue_id, user_id, body) VALUES (?, ?, ?)', [$id, $userId, $body]);
            if ($created <= 0) {
                flash_set('error', 'মন্তব্য সংরক্ষণ করা যায়নি। আবার চেষ্টা করুন।');
            } else {
                flash_set('success', 'আপনার মন্তব্য প্রকাশ করা হয়েছে।');
            }
        }
        header('Location: ' . $back . '#comments');
        exit;
    }

    header('Location: ' . $back);
    exit;
}

if ($id >= 1001) {
    db_exec('UPDATE issues SET views = views + 1 WHERE id = ?', [$id]);
}

$issue = nb_issue($id);
$pageTitle = $issue['title'] . ' — নগরবন্ধু';
$metaDescription = excerpt($issue['description'], 150);
$cat = category_meta($issue['type']);
$citizen = current_citizen();
$comments = nb_comments((int) $issue['id']);
$supported = false;
if (is_citizen()) {
    $supported = (int) db_val(
        'SELECT COUNT(*) FROM supports WHERE issue_id = ? AND user_id = ?',
        [(int) $issue['id'], (int) $citizen['id']]
    ) > 0;
}
$timelineCurrent = null;
foreach ($issue['timeline'] as $index => $step) {
    if (empty($step['done'])) { $timelineCurrent = $index; break; }
}
$related = array_values(array_filter(nb_issues(), fn ($item) => $item['id'] !== $issue['id'] && ($item['type'] === $issue['type'] || $item['area'] === $issue['area'])));
$related = array_slice($related, 0, 3);
$shareScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$shareHost = $_SERVER['HTTP_HOST'] ?? 'localhost';
$sharePath = $_SERVER['PHP_SELF'] ?? '/issue.php';
$shareUrl = $shareScheme . '://' . $shareHost . $sharePath . '?id=' . $issue['id'];
include __DIR__ . '/includes/header.php';
?>

<div class="container detail-page">
  <div class="detail-layout">
    <div class="detail-main">
      <a class="back-link" href="issues.php"><?= icon('arrow-left', 'icon-sm') ?> সব সমস্যায় ফিরে যান</a>

      <div class="detail-head">
        <div>
          <span class="section-kicker"><?= e($cat['label']) ?></span>
          <h1><?= e($issue['title']) ?></h1>
          <div class="detail-meta">
            <span><?= icon('map-pin', 'icon-sm') ?> <?= e($issue['address']) ?></span>
            <span><?= icon('calendar', 'icon-sm') ?> <?= e($issue['date']) ?></span>
            <span><?= icon('eye', 'icon-sm') ?> <?= bn($issue['views']) ?> বার দেখা হয়েছে</span>
            <span class="detail-code"><?= e($issue['code']) ?></span>
          </div>
        </div>
        <div class="badge-row" style="flex:0 0 auto"><?= urgency_badge($issue['urgency']) ?><?= status_badge($issue['status']) ?></div>
      </div>

      <div class="detail-image-wrap">
        <img src="<?= e($issue['image']) ?>" alt="<?= e($cat['label']) ?> — <?= e($issue['area']) ?>">
        <span class="image-count"><?= icon('camera', 'icon-xs') ?> নাগরিকের তোলা ছবি</span>
      </div>
      <p class="image-caption">রিপোর্টের সঙ্গে সংযুক্ত ছবি — <?= e($issue['reporter']) ?>, <?= e($issue['date']) ?></p>

      <section class="detail-panel">
        <h2><?= icon('file-text', 'icon-sm') ?> সমস্যার বিবরণ</h2>
        <p><?= e($issue['description']) ?></p>
        <p><?= e($issue['description2']) ?></p>
        <div class="detail-facts">
          <div class="detail-fact"><?= icon('building', 'icon-sm') ?><div><span>দায়িত্বপ্রাপ্ত কর্তৃপক্ষ</span><strong><?= e($issue['authority']) ?></strong></div></div>
          <div class="detail-fact"><?= icon('activity', 'icon-sm') ?><div><span>জরুরি মাত্রা</span><strong><?= e(nb_urgency()[$issue['urgency']] ?? 'সাধারণ') ?></strong></div></div>
          <div class="detail-fact"><?= icon('users', 'icon-sm') ?><div><span>নাগরিক সমর্থন</span><strong><?= bn($issue['support']) ?> জন</strong></div></div>
          <div class="detail-fact"><?= icon('clock', 'icon-sm') ?><div><span>সর্বশেষ হালনাগাদ</span><strong><?= e(end($issue['timeline'])['date'] ?? $issue['date']) ?></strong></div></div>
        </div>
      </section>

      <section class="detail-panel">
        <h2><?= icon('map', 'icon-sm') ?> অবস্থান</h2>
        <div class="detail-map">
          <?= nb_map_svg() ?>
          <span style="position:absolute;left:<?= (int) $issue['map_x'] ?>%;top:<?= (int) $issue['map_y'] ?>%;width:30px;height:38px;transform:translate(-50%,-100%);color:var(--danger);filter:drop-shadow(0 3px 4px rgba(0,0,0,.25))"><?= nb_pin_svg() ?></span>
          <a class="map-open" href="map.php?issue=<?= (int) $issue['id'] ?>"><?= icon('maximize', 'icon-xs') ?> মানচিত্রে দেখুন</a>
        </div>
        <div class="detail-facts">
          <div class="detail-fact"><?= icon('compass', 'icon-sm') ?><div><span>অক্ষাংশ</span><strong><?= e($issue['lat']) ?></strong></div></div>
          <div class="detail-fact"><?= icon('compass', 'icon-sm') ?><div><span>দ্রাঘিমাংশ</span><strong><?= e($issue['lng']) ?></strong></div></div>
        </div>
      </section>

      <section class="detail-panel">
        <h2><?= icon('trending-up', 'icon-sm') ?> সমাধানের অগ্রগতি</h2>
        <div class="timeline">
          <?php foreach ($issue['timeline'] as $index => $step): ?>
            <div class="timeline-item <?= !empty($step['done']) ? 'done' : '' ?> <?= $timelineCurrent === $index ? 'current' : '' ?>">
              <h4><?= e($step['title']) ?><?= $timelineCurrent === $index ? '<span class="timeline-badge">বর্তমান ধাপ</span>' : '' ?></h4>
              <span class="timeline-date"><?= icon('calendar', 'icon-xs') ?> <?= e($step['date']) ?></span>
              <p><?= e($step['note']) ?></p>
              <?php if (!empty($step['image'])): ?>
                <div class="timeline-photo"><img src="<?= e($step['image']) ?>" alt="<?= e($step['title']) ?>"></div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </section>

      <?php if (!empty($issue['resolution'])): ?>
        <section class="detail-panel solution-card">
          <h2><?= icon('check-circle', 'icon-sm') ?> সমাধানের বিবরণ</h2>
          <p><?= e($issue['resolution']) ?></p>
          <?php if (!empty($issue['before']) && !empty($issue['after'])): ?>
            <div class="before-after-pair">
              <figure><img src="<?= e($issue['before']) ?>" alt="সমাধানের আগে"><figcaption>আগে</figcaption></figure>
              <figure><img src="<?= e($issue['after']) ?>" alt="সমাধানের পরে"><figcaption>পরে</figcaption></figure>
            </div>
          <?php endif; ?>
          <div class="info-strip"><?= icon('shield', 'icon-sm') ?><span>সমাধানটি স্থানীয় নাগরিকদের যাচাইয়ের ভিত্তিতে সম্পন্ন হিসেবে চিহ্নিত করা হয়েছে।</span></div>
        </section>
      <?php endif; ?>

      <section class="detail-panel" id="comments">
        <h2><?= icon('message-circle', 'icon-sm') ?> নাগরিক আলোচনা <span class="badge badge-soft" data-comment-count data-count="<?= (int) $issue['comments'] ?>"><?= bn($issue['comments']) ?></span></h2>
        <form class="comment-form" method="post" action="issue.php?id=<?= (int) $issue['id'] ?>#comments">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="comment">
          <?= avatar($citizen['initials'], $citizen['color'], 'md') ?>
          <div class="comment-input-wrap">
            <label class="sr-only" for="commentText">আপনার মন্তব্য</label>
            <textarea class="textarea" id="commentText" name="body" rows="3" maxlength="500" placeholder="এই সমস্যা সম্পর্কে আপনার অভিজ্ঞতা বা তথ্য শেয়ার করুন…" data-counter="commentCounter" data-counter-max="500" required></textarea>
            <div class="comment-form-foot">
              <span id="commentCounter">০ / ৫০০</span>
              <button class="btn btn-primary btn-sm" type="submit"><?= icon('send', 'icon-sm') ?> মন্তব্য প্রকাশ করুন</button>
            </div>
          </div>
        </form>
        <div class="comment-list" data-comment-list>
          <?php foreach ($comments as $comment): ?>
            <div class="comment-item">
              <?= avatar($comment['initials'], $comment['color'], 'md') ?>
              <div class="comment-body">
                <div class="comment-head">
                  <strong><?= e($comment['name']) ?></strong>
                  <?php if ($comment['verified']): ?><span class="badge badge-success"><?= icon('check-circle', 'icon-xs') ?> যাচাইকৃত</span><?php endif; ?>
                  <time><?= e($comment['time']) ?></time>
                </div>
                <p><?= e($comment['body']) ?></p>
                <div class="comment-actions">
                  <button type="button" data-action-like data-likes="<?= (int) $comment['likes'] ?>" aria-pressed="false"><?= icon('heart', 'icon-xs') ?> সহায়ক (<span data-like-count><?= bn($comment['likes']) ?></span>)</button>
                  <button type="button" data-reply-to="<?= e($comment['name']) ?>"><?= icon('message-circle', 'icon-xs') ?> উত্তর দিন</button>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
    </div>

    <aside class="detail-side">
      <div class="support-card">
        <div class="support-top"><span>এই সমস্যায় সমর্থন জানান</span><?= urgency_badge($issue['urgency']) ?></div>
        <strong class="support-big" data-support-count data-support-total data-total="<?= (int) $issue['support'] ?>"><?= bn($issue['support']) ?></strong>
        <div class="support-avatars">
          <div class="avatar-stack"><?= avatar('রা', 'blue', 'sm') ?><?= avatar('মা', 'amber', 'sm') ?><?= avatar('নু', 'violet', 'sm') ?></div>
          <p>জন নাগরিক ইতিমধ্যে<br>সমর্থন জানিয়েছেন</p>
        </div>
        <?php if ($supported): ?>
          <button class="btn btn-soft support-btn" type="button" aria-pressed="true" disabled><?= icon('check-circle', 'icon-sm') ?> আপনি সমর্থন করেছেন</button>
        <?php else: ?>
          <form method="post" action="issue.php?id=<?= (int) $issue['id'] ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="support">
            <button class="btn btn-primary support-btn" type="submit"><?= icon('heart', 'icon-sm') ?> আমিও সমর্থন জানাই</button>
          </form>
        <?php endif; ?>
        <p class="support-note"><?= icon('shield', 'icon-xs') ?> যাচাইকৃত নাগরিকদের সমর্থন গণনা করা হয়</p>
        <div class="side-mini-stats">
          <div><span>মন্তব্য</span><b><?= bn($issue['comments']) ?></b></div>
          <div><span>রিপোর্ট নম্বর</span><b><?= e($issue['code']) ?></b></div>
          <div><span>দায়িত্বপ্রাপ্ত</span><b><?= e(excerpt($issue['authority'], 26)) ?></b></div>
        </div>
      </div>

      <div class="side-card">
        <h3><?= icon('user', 'icon-sm') ?> রিপোর্টকারী</h3>
        <div class="reporter-card">
          <?= avatar(mb_substr($issue['reporter'], 0, 2), 'green', 'md') ?>
          <div><strong><?= e($issue['reporter']) ?></strong><span><?= e($issue['reporter_area']) ?></span></div>
        </div>
        <p class="reporter-foot"><?= icon('clock', 'icon-xs') ?> যোগদান: <?= e($issue['reporter_joined']) ?></p>
      </div>

      <?php if ($related): ?>
        <div class="side-card">
          <h3><?= icon('link', 'icon-sm') ?> সংশ্লিষ্ট সমস্যা</h3>
          <div class="related-list">
            <?php foreach ($related as $item): ?>
              <a class="related-item" href="issue.php?id=<?= (int) $item['id'] ?>">
                <img src="<?= e($item['image']) ?>" alt="">
                <div><strong><?= e(excerpt($item['title'], 48)) ?></strong><span><?= e($item['area']) ?> • <?= e(status_meta($item['status'])['label']) ?></span></div>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <div class="side-card">
        <h3><?= icon('share', 'icon-sm') ?> শেয়ার করুন</h3>
        <div class="share-row">
          <span>আপনার এলাকার মানুষকে জানান:</span>
        </div>
        <div class="share-row">
          <button class="share-btn" type="button" data-copy="<?= e($shareUrl) ?>" aria-label="লিংক কপি করুন"><?= icon('copy', 'icon-sm') ?></button>
          <button class="share-btn" type="button" data-share="facebook" data-share-url="<?= e($shareUrl) ?>" aria-label="ফেসবুকে শেয়ার"><?= icon('facebook', 'icon-sm') ?></button>
          <button class="share-btn" type="button" data-share="whatsapp" data-share-url="<?= e($shareUrl) ?>" aria-label="হোয়াটসঅ্যাপে শেয়ার"><?= icon('send', 'icon-sm') ?></button>
        </div>
      </div>

      <a class="btn btn-outline btn-block" href="report.php"><?= icon('plus', 'icon-sm') ?> নতুন সমস্যা জানান</a>
    </aside>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
