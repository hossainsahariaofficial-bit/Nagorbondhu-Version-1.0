<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_admin();

$adminPage = 'admin-progress.php';
$adminTitle = 'সমাধান পর্যবেক্ষণ';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $issueId = (int) ($_POST['issue_id'] ?? 0);
    $stage = (string) ($_POST['stage'] ?? '');
    $note = trim((string) ($_POST['note'] ?? ''));
    $redirect = 'admin-progress.php' . ($issueId > 0 ? '?id=' . $issueId : '');

    $stages = [
        'verified' => ['status' => 'verified', 'label' => 'যাচাই সম্পন্ন'],
        'working'  => ['status' => 'working', 'label' => 'সমাধানের কাজ চলছে'],
        'partial'  => ['status' => 'working', 'label' => 'আংশিক সম্পন্ন'],
        'solved'   => ['status' => 'solved', 'label' => 'সমাধান সম্পন্ন'],
    ];

    if (!csrf_valid()) {
        flash_set('error', 'নিরাপত্তা যাচাই ব্যর্থ হয়েছে। আবার চেষ্টা করুন।');
    } elseif (!db_ok()) {
        flash_set('error', db_unavailable_message());
    } elseif (!isset($stages[$stage])) {
        flash_set('error', 'বর্তমান ধাপটি সঠিকভাবে নির্বাচন করুন।');
    } elseif ($note === '') {
        flash_set('error', 'হালনাগাদের বিবরণ লিখুন।');
    } elseif ($issueId < 1001) {
        flash_set('error', 'ডেমো রিপোর্টের অগ্রগতি হালনাগাদ করা যায় না। নতুন জমা দেওয়া রিপোর্টে কাজ করুন।');
    } else {
        $meta = $stages[$stage];
        $updated = db_exec('UPDATE issues SET status = ? WHERE id = ?', [$meta['status'], $issueId]);
        if ($updated < 0) {
            flash_set('error', 'অগ্রগতি হালনাগাদ করা যায়নি। আবার চেষ্টা করুন।');
        } else {
            db_exec(
                'INSERT INTO issue_updates (issue_id, admin_id, status, note) VALUES (?, ?, ?, ?)',
                [$issueId, (int) current_admin()['id'], $meta['status'], $meta['label'] . ': ' . $note]
            );
            flash_set('success', 'অগ্রগতির হালনাগাদ সংরক্ষণ করা হয়েছে এবং রিপোর্টের টাইমলাইনে প্রকাশিত হয়েছে।');
        }
    }

    header('Location: ' . $redirect);
    exit;
}

$allIssues = nb_issues();
$activeIssues = array_values(array_filter($allIssues, fn ($i) => in_array($i['status'], ['working', 'verified'], true)));
$solvedIssues = array_values(array_filter($allIssues, fn ($i) => $i['status'] === 'solved'));

$selectedId = (int) ($_GET['id'] ?? 1);
$selected = $allIssues[$selectedId] ?? ($activeIssues[0] ?? $allIssues[1]);

$doneSteps = count(array_filter($selected['timeline'], fn ($step) => !empty($step['done'])));
$progress = progress_percent($doneSteps, count($selected['timeline']));

include __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-page-head">
  <div>
    <div class="admin-breadcrumb-lite"><a href="admin.php">প্রশাসনিক প্যানেল</a><?= icon('chevron-right') ?><span>সমাধান পর্যবেক্ষণ</span></div>
    <h2>কাজের অগ্রগতি ও সময়সীমা</h2>
    <p>চলমান সমাধানগুলোর ধাপ, কর্তৃপক্ষের অগ্রগতি এবং সম্ভাব্য সমাপ্তি পর্যবেক্ষণ করুন।</p>
  </div>
  <div class="admin-page-head-actions">
    <a class="btn btn-outline btn-sm" href="admin-reports.php"><?= icon('bar-chart', 'icon-sm') ?> কর্মক্ষমতা প্রতিবেদন</a>
    <a class="btn btn-primary btn-sm" href="admin-issues.php?status=working"><?= icon('tools', 'icon-sm') ?> কাজের তালিকা</a>
  </div>
</div>

<div class="admin-stat-grid">
  <div class="admin-stat">
    <div class="admin-stat-top"><span class="admin-stat-label">চলমান সমাধান</span><span class="admin-stat-icon violet"><?= icon('tools', 'icon-sm') ?></span></div>
    <div class="admin-stat-value"><?= bn(count($activeIssues)) ?></div>
    <div class="admin-stat-foot"><span class="up"><?= icon('trending-up') ?> ৩টি</span> এই সপ্তাহে শুরু</div>
  </div>
  <div class="admin-stat">
    <div class="admin-stat-top"><span class="admin-stat-label">সময়ের মধ্যে</span><span class="admin-stat-icon green"><?= icon('clock', 'icon-sm') ?></span></div>
    <div class="admin-stat-value"><?= bn('৭৮%') ?></div>
    <div class="admin-stat-foot">নির্ধারিত সময়সীমা মেনে চলছে</div>
  </div>
  <div class="admin-stat">
    <div class="admin-stat-top"><span class="admin-stat-label">বিলম্বিত</span><span class="admin-stat-icon rose"><?= icon('alert-triangle', 'icon-sm') ?></span></div>
    <div class="admin-stat-value"><?= bn('৩') ?></div>
    <div class="admin-stat-foot"><span class="down">২টি</span> কর্তৃপক্ষের প্রতিক্রিয়ার অপেক্ষায়</div>
  </div>
  <div class="admin-stat">
    <div class="admin-stat-top"><span class="admin-stat-label">গড় সমাধান সময়</span><span class="admin-stat-icon blue"><?= icon('activity', 'icon-sm') ?></span></div>
    <div class="admin-stat-value"><?= bn('৯.৪') ?> দিন</div>
    <div class="admin-stat-foot"><span class="up"><?= icon('trending-up') ?> ১.২ দিন</span> দ্রুততর</div>
  </div>
</div>

<div class="admin-grid cols-2-1">
  <section class="admin-panel">
    <div class="admin-panel-head">
      <div><h3>চলমান কাজের অগ্রগতি</h3><p><?= bn(count($activeIssues)) ?>টি রিপোর্টে সমাধানের কাজ চলছে</p></div>
      <span class="badge badge-progress"><?= icon('activity', 'icon-xs') ?> লাইভ</span>
    </div>
    <div class="admin-panel-body">
      <?php if (!$activeIssues): ?>
        <?= empty_state('কোনো চলমান কাজ নেই', 'নতুন বরাদ্দ পাওয়া রিপোর্ট এখানে দেখা যাবে।', 'tools') ?>
      <?php endif; ?>
      <div style="display:flex;flex-direction:column;gap:16px">
        <?php foreach ($activeIssues as $item): ?>
          <?php
          $done = count(array_filter($item['timeline'], fn ($s) => !empty($s['done'])));
          $pct = progress_percent($done, count($item['timeline']));
          $currentStep = $item['timeline'][count($item['timeline']) - 1];
          ?>
          <div style="border:1px solid var(--line);border-radius:11px;padding:13px;background:<?= $item['id'] === $selected['id'] ? 'var(--green-tint)' : '#fff' ?>">
            <div class="cell-person" style="align-items:flex-start">
              <img class="cell-thumb" src="<?= e($item['image']) ?>" alt="" loading="lazy">
              <div style="flex:1;min-width:0">
                <a class="cell-main" href="admin-progress.php?id=<?= (int) $item['id'] ?>"><?= e($item['title']) ?></a>
                <span class="cell-sub"><?= e($item['code']) ?> · <?= e($item['authority']) ?></span>
              </div>
              <?= status_badge($item['status']) ?>
            </div>
            <div class="row-between" style="margin-top:11px;font-size:.68rem;color:var(--muted)">
              <span><?= icon('layers', 'icon-xs') ?> <?= bn($done) ?> / <?= bn(count($item['timeline'])) ?> ধাপ সম্পন্ন</span>
              <strong style="color:var(--green-dark)"><?= bn($pct) ?>%</strong>
            </div>
            <?= progress_bar($pct, 100, 'progress-thin') ?>
            <div class="row-between" style="margin-top:8px;font-size:.66rem;color:var(--muted-2)">
              <span><?= icon('clock', 'icon-xs') ?> <?= e($currentStep['date']) ?></span>
              <a class="panel-link" href="admin-progress.php?id=<?= (int) $item['id'] ?>">বিস্তারিত <?= icon('arrow-right', 'icon-xs') ?></a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="admin-panel">
    <div class="admin-panel-head"><div><h3>অগ্রগতি হালনাগাদ</h3><p><?= e($selected['code']) ?></p></div></div>
    <div class="admin-panel-body">
      <form class="admin-form" method="post" action="admin-progress.php?id=<?= (int) $selected['id'] ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="issue_id" value="<?= (int) $selected['id'] ?>">
        <?php
        $currentStage = 'working';
        if ($selected['status'] === 'verified') $currentStage = 'verified';
        if ($selected['status'] === 'solved') $currentStage = 'solved';
        ?>
        <div class="field">
          <label class="field-label" for="progressStage">বর্তমান ধাপ</label>
          <select class="select" id="progressStage" name="stage">
            <option value="verified" <?= $currentStage === 'verified' ? 'selected' : '' ?>>যাচাই সম্পন্ন</option>
            <option value="working" <?= $currentStage === 'working' ? 'selected' : '' ?>>সমাধানের কাজ চলছে</option>
            <option value="partial">আংশিক সম্পন্ন</option>
            <option value="solved" <?= $currentStage === 'solved' ? 'selected' : '' ?>>সমাধান সম্পন্ন</option>
          </select>
        </div>
        <div class="field">
          <label class="field-label" for="progressPercent">অগ্রগতি (শতাংশ)</label>
          <input class="input" id="progressPercent" type="number" min="0" max="100" value="<?= (int) $progress ?>" readonly>
          <?= progress_bar($progress, 100, 'progress-thin') ?>
        </div>
        <div class="field">
          <label class="field-label" for="progressNote">হালনাগাদের বিবরণ <span class="req">*</span></label>
          <textarea class="textarea" id="progressNote" name="note" required placeholder="কী কাজ হয়েছে, কী বাকি আছে এবং সম্ভাব্য সমাপ্তি কখন…"></textarea>
        </div>
        <div class="field">
          <label class="field-label" for="progressImage">ছবি সংযুক্ত করুন</label>
          <input class="input" id="progressImage" type="file" accept="image/*" disabled>
          <span class="field-hint">ছবি সংযুক্তকরণ শীঘ্রই চালু হবে। এখন বিবরণসংবলিত হালনাগাদ সংরক্ষণ করুন।</span>
        </div>
        <button class="btn btn-primary btn-block" type="submit"><?= icon('send', 'icon-sm') ?> হালনাগাদ প্রকাশ করুন</button>
      </form>
    </div>
  </section>
</div>

<div class="admin-grid cols-2-1">
  <section class="admin-panel">
    <div class="admin-panel-head">
      <div><h3>টাইমলাইন — <?= e($selected['code']) ?></h3><p><?= e($selected['title']) ?></p></div>
      <a class="panel-link" href="admin-issues.php">রিপোর্ট দেখুন <?= icon('arrow-right', 'icon-xs') ?></a>
    </div>
    <div class="admin-panel-body">
      <div class="status-timeline" aria-hidden="true">
        <?php
        $stepCount = count($selected['timeline']);
        foreach ($selected['timeline'] as $index => $step):
            $cls = !empty($step['done']) ? 'done' : ($index === $doneSteps ? 'current' : '');
        ?>
          <span class="st <?= $cls ?>"></span>
        <?php endforeach; ?>
      </div>
      <div class="timeline" style="margin-top:16px">
        <?php foreach ($selected['timeline'] as $index => $step): ?>
          <div class="timeline-item <?= !empty($step['done']) ? 'done' : ($index === $doneSteps ? 'current' : '') ?>">
            <h4><?= e($step['title']) ?><?= $index === $doneSteps ? '<span class="timeline-badge">চলমান</span>' : '' ?></h4>
            <span class="timeline-date"><?= e($step['date']) ?></span>
            <p><?= e($step['note']) ?></p>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="admin-note" style="margin-top:14px"><?= icon('info', 'icon-sm') ?><div>সমাধান সম্পন্ন চিহ্নিত করার আগে আগে ও পরের ছবি এবং কাজের বিবরণ যোগ করা আবশ্যক।</div></div>
      <div class="admin-actions-row" style="margin-top:14px">
        <button class="btn btn-sm btn-primary" type="button" data-admin-action="complete"><?= icon('check-circle', 'icon-sm') ?> সমাধান সম্পন্ন করুন</button>
        <button class="btn btn-sm btn-outline" type="button" data-admin-action="return"><?= icon('refresh', 'icon-sm') ?> সংশোধনের জন্য ফেরত</button>
      </div>
    </div>
  </section>

  <div style="display:flex;flex-direction:column;gap:16px">
    <section class="admin-panel">
      <div class="admin-panel-head"><div><h3>কাজের সারসংক্ষেপ</h3><p>দায়িত্ব ও সময়সীমা</p></div></div>
      <div class="admin-panel-body">
        <div class="admin-kv">
          <div><span>দায়িত্বপ্রাপ্ত কর্তৃপক্ষ</span><strong><?= e($selected['authority']) ?></strong></div>
          <div><span>বর্তমান অবস্থা</span><strong><?= e(status_meta($selected['status'])['label']) ?></strong></div>
          <div><span>জরুরি মাত্রা</span><strong><?= e(nb_urgency()[$selected['urgency']] ?? 'সাধারণ') ?></strong></div>
          <div><span>শুরু হয়েছে</span><strong><?= e($selected['date']) ?></strong></div>
          <div><span>সম্ভাব্য সমাপ্তি</span><strong><?= e($selected['timeline'][count($selected['timeline']) - 1]['date']) ?></strong></div>
          <div><span>নাগরিক সমর্থন</span><strong><?= bn($selected['support']) ?> জন</strong></div>
        </div>
        <div class="admin-note" style="margin-top:12px"><?= icon('users', 'icon-sm') ?><div>রিপোর্টকারী <strong><?= e($selected['reporter']) ?></strong> এবং <?= bn($selected['support']) ?> জন সমর্থক অগ্রগতির নোটিফিকেশন পাবেন।</div></div>
      </div>
    </section>

    <section class="admin-panel">
      <div class="admin-panel-head"><div><h3>সাম্প্রতিক সমাধান</h3><p>আগে ও পরের তুলনা প্রস্তুত</p></div></div>
      <div class="activity-list">
        <?php foreach (array_slice($solvedIssues, 0, 3) as $solved): ?>
          <a class="activity-item" href="issue.php?id=<?= (int) $solved['id'] ?>" style="color:inherit">
            <span class="activity-icon green"><?= icon('check-circle', 'icon-sm') ?></span>
            <div class="activity-text"><p><?= e(excerpt($solved['title'], 44)) ?></p><span><?= e($solved['code']) ?> · <?= e($solved['date']) ?></span></div>
          </a>
        <?php endforeach; ?>
      </div>
      <div class="admin-panel-foot"><span>মোট <?= bn(count($solvedIssues)) ?>টি সমাধান সম্পন্ন</span><a href="admin-issues.php?status=solved">সব দেখুন <?= icon('arrow-right', 'icon-xs') ?></a></div>
    </section>
  </div>
</div>

<section class="admin-panel">
  <div class="admin-panel-head"><div><h3>সকল চলমান কাজের সময়সীমা</h3><p>বিলম্ব ও অগ্রাধিকারভিত্তিক তালিকা</p></div><a class="panel-link" href="admin-issues.php?status=working">সম্পূর্ণ তালিকা <?= icon('arrow-right', 'icon-xs') ?></a></div>
  <div class="table-scroll">
    <table class="data-table admin-table">
      <thead><tr><th class="cell-issue">রিপোর্ট</th><th>কর্তৃপক্ষ</th><th>অগ্রগতি</th><th>সম্ভাব্য সমাপ্তি</th><th>অগ্রাধিকার</th><th class="cell-actions">কার্যক্রম</th></tr></thead>
      <tbody>
        <?php foreach ($activeIssues as $item): ?>
          <?php
          $done = count(array_filter($item['timeline'], fn ($s) => !empty($s['done'])));
          $pct = progress_percent($done, count($item['timeline']));
          ?>
          <tr>
            <td class="cell-issue">
              <div class="cell-person">
                <img class="cell-thumb" src="<?= e($item['image']) ?>" alt="" loading="lazy">
                <div><a class="cell-main" href="admin-progress.php?id=<?= (int) $item['id'] ?>"><?= e(excerpt($item['title'], 44)) ?></a><span class="cell-sub"><?= e($item['code']) ?> · <?= e($item['area']) ?></span></div>
              </div>
            </td>
            <td class="table-sub"><?= e($item['authority']) ?></td>
            <td style="min-width:130px">
              <div class="row-between" style="font-size:.64rem;color:var(--muted);margin-bottom:3px"><span><?= bn($pct) ?>%</span><span><?= bn($done) ?>/<?= bn(count($item['timeline'])) ?></span></div>
              <?= progress_bar($pct, 100, 'progress-thin') ?>
            </td>
            <td class="table-sub"><?= e($item['timeline'][count($item['timeline']) - 1]['date']) ?></td>
            <td><?= urgency_badge($item['urgency']) ?></td>
            <td class="cell-actions"><a class="btn btn-sm btn-outline" href="admin-progress.php?id=<?= (int) $item['id'] ?>">হালনাগাদ</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
