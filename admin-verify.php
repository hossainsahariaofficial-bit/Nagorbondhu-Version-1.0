<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_admin();

$adminPage = 'admin-verify.php';
$adminTitle = 'রিপোর্ট যাচাই';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $issueId = (int) ($_POST['issue_id'] ?? 0);
    $decision = (string) ($_POST['decision'] ?? '');
    $note = trim((string) ($_POST['note'] ?? ''));
    $redirect = 'admin-verify.php' . ($issueId > 0 ? '?id=' . $issueId : '');

    $decisions = [
        'verify'  => ['status' => 'verified', 'label' => 'যাচাই সম্পন্ন', 'note' => 'রিপোর্টের তথ্য যাচাই সম্পন্ন হয়েছে।'],
        'approve' => ['status' => 'verified', 'label' => 'অনুমোদন ও প্রকাশ', 'note' => 'রিপোর্টটি যাচাই করে অনুমোদন ও প্রকাশ করা হয়েছে।'],
        'return'  => ['status' => 'new', 'label' => 'সংশোধনের জন্য ফেরত', 'note' => 'রিপোর্টটি সংশোধনের জন্য রিপোর্টকারীর কাছে ফেরত দেওয়া হয়েছে।'],
        'reject'  => ['status' => 'rejected', 'label' => 'প্রত্যাখ্যান', 'note' => 'রিপোর্টটি যাচাইয়ের পর প্রত্যাখ্যান করা হয়েছে।'],
    ];

    if (!csrf_valid()) {
        flash_set('error', 'নিরাপত্তা যাচাই ব্যর্থ হয়েছে। আবার চেষ্টা করুন।');
    } elseif (!db_ok()) {
        flash_set('error', db_unavailable_message());
    } elseif (!isset($decisions[$decision])) {
        flash_set('error', 'সিদ্ধান্তটি সঠিকভাবে নির্বাচন করুন।');
    } elseif ($issueId < 1001) {
        flash_set('error', 'ডেমো রিপোর্টের অবস্থা পরিবর্তন করা যায় না। নতুন জমা দেওয়া রিপোর্টে এই কাজটি করুন।');
    } else {
        $meta = $decisions[$decision];
        $updated = db_exec('UPDATE issues SET status = ? WHERE id = ?', [$meta['status'], $issueId]);
        if ($updated < 0) {
            flash_set('error', 'রিপোর্টের অবস্থা হালনাগাদ করা যায়নি। আবার চেষ্টা করুন।');
        } else {
            db_exec(
                'INSERT INTO issue_updates (issue_id, admin_id, status, note) VALUES (?, ?, ?, ?)',
                [$issueId, (int) current_admin()['id'], $meta['status'], $note !== '' ? $note : $meta['note']]
            );
            flash_set('success', 'সিদ্ধান্ত সংরক্ষিত হয়েছে: ' . $meta['label'] . '।');
        }
    }

    header('Location: ' . $redirect);
    exit;
}

$allIssues = nb_issues();
$issueId = (int) ($_GET['id'] ?? 7);
$issue = $allIssues[$issueId] ?? $allIssues[7];
$cat = category_meta($issue['type']);

$queue = array_values(array_filter($allIssues, fn ($i) => in_array($i['status'], ['new', 'verifying'], true)));

include __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-page-head">
  <div>
    <div class="admin-breadcrumb-lite"><a href="admin.php">প্রশাসনিক প্যানেল</a><?= icon('chevron-right') ?><a href="admin-issues.php">সমস্যা ব্যবস্থাপনা</a><?= icon('chevron-right') ?><span>যাচাই</span></div>
    <h2><?= e($issue['code']) ?></h2>
    <p>রিপোর্টের তথ্য যাচাই করুন এবং পরবর্তী পদক্ষেপ নির্ধারণ করুন।</p>
  </div>
  <div class="admin-page-head-actions">
    <a class="btn btn-outline btn-sm" href="issue.php?id=<?= (int) $issue['id'] ?>" target="_blank" rel="noopener"><?= icon('eye', 'icon-sm') ?> পাবলিক ভিউ</a>
    <a class="btn btn-outline btn-sm" href="admin-issues.php"><?= icon('arrow-left', 'icon-sm') ?> তালিকায় ফিরুন</a>
  </div>
</div>

<div class="admin-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:18px">
  <div class="mini-stat"><strong><?= bn(($issue['status'] === 'new' ? 1 : 0) + 1) ?>/৫</strong><span>যাচাই ধাপ</span></div>
  <div class="mini-stat"><strong><?= bn($issue['support']) ?></strong><span>নাগরিক সমর্থন</span></div>
  <div class="mini-stat"><strong><?= bn($issue['comments']) ?></strong><span>মন্তব্য</span></div>
  <div class="mini-stat"><strong><?= bn($issue['views']) ?></strong><span>মোট ভিউ</span></div>
</div>

<div class="verify-layout">
  <div class="verify-main">
    <section class="admin-panel">
      <div class="verify-photo">
        <img src="<?= e($issue['image']) ?>" alt="<?= e($cat['label']) ?> সমস্যার ছবি">
        <div class="verify-id-row">
          <span><?= icon('camera', 'icon-xs') ?> নাগরিকের আপলোড করা ছবি · ১,৬০০ × ৯০০</span>
          <span><?= icon('map-pin', 'icon-xs') ?> <?= e($issue['lat']) ?>, <?= e($issue['lng']) ?></span>
        </div>
      </div>
    </section>

    <section class="admin-panel">
      <div class="verify-details">
        <div class="row-between" style="margin-bottom:8px">
          <?= status_badge($issue['status']) ?>
          <span class="detail-code"><?= e($issue['code']) ?></span>
        </div>
        <h2><?= e($issue['title']) ?></h2>
        <div class="detail-meta">
          <span><?= icon('user', 'icon-xs') ?> <?= e($issue['reporter']) ?></span>
          <span><?= icon('calendar', 'icon-xs') ?> <?= e($issue['date']) ?></span>
          <span><?= icon('map-pin', 'icon-xs') ?> <?= e($issue['address']) ?></span>
        </div>
        <p><?= e($issue['description']) ?></p>
        <p><?= e($issue['description2']) ?></p>

        <div class="verify-facts">
          <div class="verify-fact"><span>সমস্যার ধরন</span><strong><?= e($cat['label']) ?></strong></div>
          <div class="verify-fact"><span>জরুরি মাত্রা</span><strong><?= e(nb_urgency()[$issue['urgency']] ?? 'সাধারণ') ?></strong></div>
          <div class="verify-fact"><span>সংশ্লিষ্ট কর্তৃপক্ষ</span><strong><?= e($issue['authority']) ?></strong></div>
          <div class="verify-fact"><span>এরিয়া</span><strong><?= e($issue['area']) ?>, <?= e($issue['district']) ?></strong></div>
          <div class="verify-fact"><span>রিপোর্টকারীর এলাকা</span><strong><?= e($issue['reporter_area']) ?></strong></div>
          <div class="verify-fact"><span>যোগদান</span><strong><?= e($issue['reporter_joined']) ?></strong></div>
        </div>
      </div>
    </section>

    <div class="admin-grid cols-2" style="margin:0">
      <section class="admin-panel">
        <div class="admin-panel-head"><div><h3>যাচাই চেকলিস্ট</h3><p>প্রতিটি বিষয় নিশ্চিত করুন</p></div></div>
        <div class="admin-panel-body">
          <div style="display:flex;flex-direction:column;gap:10px">
            <label class="checkbox"><input type="checkbox" checked> ছবিটি রিপোর্টের বিবরণের সঙ্গে মিলছে</label>
            <label class="checkbox"><input type="checkbox" checked> অবস্থান ও এলাকার তথ্য সঠিক</label>
            <label class="checkbox"><input type="checkbox" checked> সমস্যাটি ইতিমধ্যে অন্য রিপোর্টে নেই</label>
            <label class="checkbox"><input type="checkbox"> স্থানীয় বাসিন্দার সঙ্গে যাচাই সম্পন্ন</label>
            <label class="checkbox"><input type="checkbox" <?= $issue['urgency'] === 'critical' ? 'checked' : '' ?>> জরুরি অগ্রাধিকার প্রয়োজন</label>
          </div>
          <div class="verify-note" style="margin-top:14px"><?= icon('alert-triangle', 'icon-sm') ?><div>ছবি বা অবস্থান সন্দেহজনক হলে রিপোর্টকারীকে সংশোধনের জন্য ফেরত দিন। প্রত্যাখ্যানের কারণ অবশ্যই লিখে দিন।</div></div>
        </div>
      </section>

      <section class="admin-panel">
        <div class="admin-panel-head"><div><h3>যাচাই ইতিহাস</h3><p>এই রিপোর্টে সংঘটিত পদক্ষেপ</p></div></div>
        <div class="admin-panel-body">
          <div class="history-list">
            <?php foreach (array_reverse($issue['timeline']) as $event): ?>
              <div class="history-item">
                <?= avatar('অ', $event['done'] ? 'green' : 'slate', 'sm') ?>
                <div>
                  <strong><?= e($event['title']) ?></strong>
                  <span><?= e($event['date']) ?> · প্রশাসনিক দল</span>
                  <p><?= e($event['note']) ?></p>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
    </div>

    <section class="admin-panel">
      <div class="admin-panel-head"><div><h3>অবস্থান যাচাই</h3><p>রিপোর্টে দেওয়া ভৌগোলিক অবস্থান</p></div><span class="badge badge-soft"><?= icon('map-pin', 'icon-xs') ?> <?= e($issue['area']) ?></span></div>
      <div class="admin-panel-body">
        <div class="detail-map">
          <?= nb_map_svg() ?>
        </div>
        <div class="admin-kv" style="margin-top:12px">
          <div><span>অক্ষাংশ</span><strong><?= e($issue['lat']) ?></strong></div>
          <div><span>দ্রাঘিমাংশ</span><strong><?= e($issue['lng']) ?></strong></div>
        </div>
      </div>
    </section>
  </div>

  <aside style="display:flex;flex-direction:column;gap:16px">
    <section class="admin-panel">
      <div class="admin-panel-head"><div><h3>যাচাই সিদ্ধান্ত</h3><p>উপযুক্ত পদক্ষেপ বেছে নিন</p></div><?= status_badge($issue['status']) ?></div>
      <div class="admin-panel-body">
        <form method="post" action="admin-verify.php?id=<?= (int) $issue['id'] ?>">
          <?= csrf_field() ?>
          <input type="hidden" name="issue_id" value="<?= (int) $issue['id'] ?>">
          <div class="verify-actions">
            <button class="btn btn-primary" type="submit" name="decision" value="verify"><?= icon('check-square', 'icon-sm') ?> যাচাই সম্পন্ন করুন</button>
            <button class="btn btn-soft" type="submit" name="decision" value="approve"><?= icon('check', 'icon-sm') ?> অনুমোদন ও প্রকাশ করুন</button>
            <button class="btn btn-outline" type="submit" name="decision" value="return"><?= icon('refresh', 'icon-sm') ?> সংশোধনের জন্য ফেরত</button>
            <button class="btn btn-danger-outline" type="submit" name="decision" value="reject"><?= icon('x-circle', 'icon-sm') ?> প্রত্যাখ্যান করুন</button>
          </div>
          <div class="field" style="margin-top:14px">
            <label class="field-label" for="verifyNote">যাচাই নোট</label>
            <textarea class="textarea" id="verifyNote" name="note" placeholder="সিদ্ধান্তের কারণ বা মাঠ পর্যায়ের মন্তব্য লিখুন…"></textarea>
          </div>
        </form>
      </div>
    </section>

    <section class="admin-panel">
      <div class="admin-panel-head"><div><h3>কর্তৃপক্ষ বরাদ্দ</h3><p>দায়িত্বপ্রাপ্ত সংস্থা নির্বাচন করুন</p></div></div>
      <div class="admin-panel-body">
        <form class="admin-form" data-demo-submit data-success-message="রিপোর্টটি কর্তৃপক্ষকে বরাদ্দ করা হয়েছে।" data-confirm-title="বরাদ্দ নিশ্চিত করুন" data-confirm-body="নির্বাচিত কর্তৃপক্ষকে এই রিপোর্টের দায়িত্ব দেওয়া হবে।">
          <div class="field">
            <label class="field-label" for="assignAuthority">কর্তৃপক্ষ</label>
            <select class="select" id="assignAuthority">
              <?php foreach (nb_authorities() as $authority): ?>
                <option <?= str_contains($issue['authority'], explode(' —', $authority['name'])[0]) ? 'selected' : '' ?>><?= e($authority['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="field">
            <label class="field-label" for="assignDeadline">সম্ভাব্য সমাপ্তি</label>
            <input class="input" id="assignDeadline" type="date" value="2026-11-20">
          </div>
          <div class="field">
            <label class="field-label" for="assignNote">নির্দেশনা</label>
            <textarea class="textarea" id="assignNote" placeholder="কর্তৃপক্ষের জন্য নির্দেশনা…"><?= e($issue['authority']) ?>-কে দ্রুত পরিদর্শন করে ব্যবস্থা নেওয়ার অনুরোধ করা হলো।</textarea>
          </div>
          <button class="btn btn-primary btn-block" type="submit"><?= icon('send', 'icon-sm') ?> বরাদ্দ পাঠান</button>
        </form>
      </div>
    </section>

    <div class="duplicate-box">
      <strong><?= icon('copy', 'icon-sm') ?> সম্ভাব্য ডুপ্লিকেট রিপোর্ট</strong>
      <p>একই এলাকায় একই ধরনের আরও <a href="admin-issues.php">২টি রিপোর্ট</a> পাওয়া গেছে। যাচাইয়ের আগে তুলনা করে দেখুন।</p>
    </div>

    <section class="admin-panel">
      <div class="admin-panel-head"><div><h3>যাচাই সারি</h3><p><?= bn(count($queue)) ?>টি রিপোর্ট অপেক্ষমাণ</p></div></div>
      <div class="activity-list">
        <?php foreach (array_slice($queue, 0, 4) as $item): ?>
          <a class="activity-item" href="admin-verify.php?id=<?= (int) $item['id'] ?>" style="color:inherit">
            <span class="activity-icon <?= $item['urgency'] === 'critical' ? 'amber' : '' ?>"><?= icon($item['urgency'] === 'critical' ? 'alert-triangle' : 'clock', 'icon-sm') ?></span>
            <div class="activity-text"><p><?= e(excerpt($item['title'], 42)) ?></p><span><?= e($item['code']) ?> · <?= e($item['date']) ?></span></div>
          </a>
        <?php endforeach; ?>
      </div>
    </section>
  </aside>
</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
