<?php
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'সমস্যার মানচিত্র — নগরবন্ধু';
$metaDescription = 'ঢাকা মহানগরের নাগরিক রিপোর্টগুলো মানচিত্রে দেখুন, ধরন ও অবস্থা অনুযায়ী ফিল্টার করুন এবং এলাকাভিত্তিক সমস্যা খুঁজে বের করুন।';
$issues = nb_issues();
$pinColors = [
    'neutral' => '#64748B',
    'warning' => '#D97706',
    'info' => '#2563EB',
    'progress' => '#7C3AED',
    'success' => '#16845B',
    'danger' => '#DC2626',
];
include __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <?= page_heading(
        'সমস্যার মানচিত্র',
        'ঢাকা মহানগরের নাগরিক রিপোর্টগুলো এক নজরে দেখুন। পিনে ক্লিক করলে সমস্যার সংক্ষিপ্ত তথ্য ও বিস্তারিত পৃষ্ঠার লিংক পাবেন।',
        ['হোম' => 'index.php', 'মানচিত্র' => null]
    ) ?>
  </div>
</section>

<div class="container map-page" data-map-page>
  <div class="map-layout">
    <div class="map-shell">
      <div class="map-toolbar">
        <div class="search-input">
          <?= icon('search', 'icon-sm') ?>
          <label class="sr-only" for="mapSearch">সমস্যা বা এলাকা খুঁজুন</label>
          <input class="input" id="mapSearch" type="search" placeholder="সমস্যা বা এলাকা খুঁজুন…" autocomplete="off">
        </div>
        <label class="sr-only" for="mapType">সমস্যার ধরন</label>
        <select class="select" id="mapType">
          <option value="">সব ধরন</option>
          <?php foreach (nb_categories() as $key => $cat): ?>
            <option value="<?= e($key) ?>"><?= e($cat['label']) ?></option>
          <?php endforeach; ?>
        </select>
        <label class="sr-only" for="mapStatus">সমস্যার অবস্থা</label>
        <select class="select" id="mapStatus">
          <option value="">সব অবস্থা</option>
          <?php foreach (nb_statuses() as $key => $status): ?>
            <option value="<?= e($key) ?>"><?= e($status['label']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="map-canvas-wrap">
        <div class="map-zoom-layer" id="mapZoomLayer">
          <?= nb_map_svg('map-bg') ?>
          <?php foreach ($issues as $issue): ?>
            <?php
              $status = status_meta($issue['status']);
              $color = $pinColors[$status['tone']] ?? '#16845B';
            ?>
            <button class="map-pin-btn" type="button"
                    style="left:<?= (int) $issue['map_x'] ?>%;top:<?= (int) $issue['map_y'] ?>%"
                    data-id="<?= (int) $issue['id'] ?>"
                    data-type="<?= e($issue['type']) ?>"
                    data-status="<?= e($issue['status']) ?>"
                    data-search="<?= e($issue['title'] . ' ' . $issue['area'] . ' ' . category_meta($issue['type'])['label'] . ' ' . $status['label']) ?>"
                    data-image="<?= e($issue['image']) ?>"
                    data-title="<?= e($issue['title']) ?>"
                    data-area="<?= e($issue['area']) ?>"
                    data-support="<?= bn($issue['support']) ?>"
                    data-tone="<?= e($status['tone']) ?>"
                    data-status-label="<?= e($status['label']) ?>"
                    aria-label="<?= e($issue['title']) ?> — <?= e($issue['area']) ?>">
              <span class="pin-label"><?= e($issue['area']) ?></span>
              <svg viewBox="0 0 30 38" fill="none" aria-hidden="true">
                <path d="M15 36.5C15 36.5 28.5 22.7 28.5 13.7a13.5 13.5 0 1 0-27 0C1.5 22.7 15 36.5 15 36.5Z" fill="<?= e($color) ?>" stroke="#fff" stroke-width="2.2"/>
                <circle cx="15" cy="13.7" r="5" fill="#fff"/>
              </svg>
            </button>
          <?php endforeach; ?>
        </div>

        <div class="map-searching" id="mapSearching"><?= icon('search', 'icon-sm') ?> খোঁজা হচ্ছে…</div>

        <div class="map-zoom" aria-label="মানচিত্র জুম">
          <button type="button" data-map-zoom="in" aria-label="জুম বাড়ান"><?= icon('plus', 'icon-sm') ?></button>
          <button type="button" data-map-zoom="out" aria-label="জুম কমান"><?= icon('minus', 'icon-sm') ?></button>
          <button type="button" data-map-zoom="reset" aria-label="জুম রিসেট"><?= icon('refresh', 'icon-sm') ?></button>
        </div>

        <div class="map-legend" aria-hidden="true">
          <span><i class="legend-dot" style="background:#16845B"></i> সমাধান সম্পন্ন</span>
          <span><i class="legend-dot" style="background:#7C3AED"></i> সমাধানাধীন</span>
          <span><i class="legend-dot" style="background:#D97706"></i> যাচাই চলছে</span>
          <span><i class="legend-dot" style="background:#64748B"></i> জমা দেওয়া হয়েছে</span>
        </div>
      </div>

      <div class="map-foot">
        <span><?= icon('info', 'icon-xs') ?> ডেমো মানচিত্র — পিনের অবস্থান আনুমানিক</span>
        <span><strong id="mapCount"><?= bn(count($issues)) ?></strong>টি রিপোর্ট দেখানো হচ্ছে</span>
      </div>
    </div>

    <aside class="map-side">
      <div id="mapPopup" class="map-popup" hidden aria-live="polite"></div>

      <div class="map-side-head">
        <h2>কাছের রিপোর্ট</h2>
        <span>এলাকা অনুযায়ী</span>
      </div>

      <div class="map-list" id="mapList">
        <?php foreach ($issues as $issue): ?>
          <?php
            $status = status_meta($issue['status']);
            $cat = category_meta($issue['type']);
          ?>
          <button class="map-list-item" type="button"
                  data-id="<?= (int) $issue['id'] ?>"
                  data-type="<?= e($issue['type']) ?>"
                  data-status="<?= e($issue['status']) ?>"
                  data-search="<?= e($issue['title'] . ' ' . $issue['area'] . ' ' . $cat['label'] . ' ' . $status['label']) ?>">
            <img src="<?= e($issue['image']) ?>" alt="" loading="lazy">
            <span class="map-list-body">
              <strong><?= e($issue['title']) ?></strong>
              <span><?= icon('map-pin', 'icon-xs') ?> <?= e($issue['area']) ?> · <?= bn($issue['support']) ?> সমর্থন</span>
              <span class="badge badge-<?= e($status['tone']) ?>" style="margin-top:5px"><?= e($status['label']) ?></span>
            </span>
          </button>
        <?php endforeach; ?>
      </div>

      <div id="mapEmpty" hidden>
        <?= empty_state('কোনো রিপোর্ট মেলেনি', 'অনুসন্ধান শব্দ বা ফিল্টার পরিবর্তন করে আবার চেষ্টা করুন।', 'map') ?>
      </div>

      <a class="btn btn-primary btn-block" href="report.php"><?= icon('plus', 'icon-sm') ?> নতুন সমস্যা জানান</a>
      <p class="muted-note">মানচিত্রে দেখানো সব রিপোর্ট ডেমো ডেটা। বাস্তব সিস্টেমে পিনের অবস্থান যাচাই দল নিশ্চিত করবে।</p>
    </aside>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
