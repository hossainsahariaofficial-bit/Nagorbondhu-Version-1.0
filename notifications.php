<?php
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'নোটিফিকেশন — নগরবন্ধু';
$metaDescription = 'আপনার রিপোর্ট, সমর্থন, মন্তব্য ও অনুদান সম্পর্কিত সব হালনাগাদ এক জায়গায় দেখুন।';
$notifications = nb_notifications();
$unread = count(array_filter($notifications, fn ($n) => !$n['read']));
$groups = [
    'সাম্প্রতিক' => [1, 2],
    'গত কয়েক দিন' => [3, 4, 5, 6],
    'আগের হালনাগাদ' => [7, 8],
];
$byId = [];
foreach ($notifications as $n) $byId[$n['id']] = $n;
include __DIR__ . '/includes/header.php';
?>

<div class="container notif-page">
  <div class="notif-layout">
    <div class="notif-head">
      <div>
        <span class="section-kicker"><?= icon('bell', 'icon-xs') ?> আপনার ইনবক্স</span>
        <h1>নোটিফিকেশন</h1>
        <p>আপনার রিপোর্ট, সমর্থন ও অনুদান সংক্রান্ত হালনাগাদ এখানে দেখুন। অপঠিত আছে <strong data-unread-count><?= bn($unread) ?></strong>টি।</p>
      </div>
      <button class="btn btn-outline btn-sm" type="button" data-mark-all-read><?= icon('check', 'icon-sm') ?> সব পঠিত করুন</button>
    </div>

    <div class="notif-tabs" role="tablist" aria-label="নোটিফিকেশনের ধরন">
      <button class="notif-tab active" type="button" role="tab" aria-selected="true" data-notif-tab="all">সব</button>
      <button class="notif-tab" type="button" role="tab" aria-selected="false" data-notif-tab="unread">অপঠিত <span class="count"><?= bn($unread) ?></span></button>
      <button class="notif-tab" type="button" role="tab" aria-selected="false" data-notif-tab="status">অবস্থা</button>
      <button class="notif-tab" type="button" role="tab" aria-selected="false" data-notif-tab="donation">অনুদান</button>
      <button class="notif-tab" type="button" role="tab" aria-selected="false" data-notif-tab="system">সিস্টেম</button>
    </div>

    <div class="notif-list" data-notif-list>
      <?php foreach ($groups as $label => $ids): ?>
        <div class="notif-group-label"><?= e($label) ?></div>
        <?php foreach ($ids as $nid): ?>
          <?php $n = $byId[$nid] ?? null; if (!$n) continue; ?>
          <article class="notif-item <?= $n['read'] ? 'read' : 'unread' ?>" data-type="<?= e($n['type']) ?>">
            <span class="notif-icon tone-<?= e($n['tone']) ?>"><?= icon($n['icon'], 'icon-sm') ?></span>
            <div class="notif-body">
              <h3><a href="<?= e($n['link']) ?>"><?= e($n['title']) ?></a></h3>
              <p><?= e($n['body']) ?></p>
              <time><?= icon('clock', 'icon-xs') ?> <?= e($n['time']) ?> · <?= e($n['date']) ?></time>
            </div>
            <div class="notif-end">
              <span class="notif-unread-dot" aria-hidden="true"></span>
              <button class="icon-btn" type="button" data-mark-read aria-label="পঠিত হিসেবে চিহ্নিত করুন"><?= icon('check', 'icon-sm') ?></button>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endforeach; ?>
    </div>

    <div class="info-strip mt-2">
      <?= icon('info', 'icon-sm') ?>
      <span>এই নোটিফিকেশনগুলো ডেমো ডেটা। বাস্তব সিস্টেমে আপনার পছন্দ অনুযায়ী ইমেইল ও এসএমএস সতর্কতা পাঠানো হবে।</span>
    </div>

    <div class="center-action">
      <a class="btn btn-text" href="dashboard.php"><?= icon('arrow-left', 'icon-sm') ?> ড্যাশবোর্ডে ফিরে যান</a>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
