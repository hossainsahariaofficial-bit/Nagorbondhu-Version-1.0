<?php
require_once __DIR__ . '/includes/bootstrap.php';

$adminPage = 'admin-authorities.php';
$adminTitle = 'কর্তৃপক্ষ ও সংস্থা';

$authorities = nb_authorities();
$issues = nb_issues();
$totalAssigned = array_sum(array_column($authorities, 'assigned'));
$totalResolved = array_sum(array_column($authorities, 'resolved'));
$activeAuthorities = count(array_filter($authorities, fn ($a) => $a['status'] === 'সক্রিয়'));

include __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-page-head">
  <div>
    <div class="admin-breadcrumb-lite"><a href="admin.php">প্রশাসনিক প্যানেল</a><?= icon('chevron-right') ?><span>কর্তৃপক্ষ ও সংস্থা</span></div>
    <h2>দায়িত্বপ্রাপ্ত কর্তৃপক্ষ</h2>
    <p>সিটি কর্পোরেশন, বিভাগ ও সংগঠনের সঙ্গে সমন্বয় এবং কাজের বরাদ্দ পর্যবেক্ষণ করুন।</p>
  </div>
  <div class="admin-page-head-actions">
    <a class="btn btn-outline btn-sm" href="admin-progress.php"><?= icon('activity', 'icon-sm') ?> কাজের অগ্রগতি</a>
    <button class="btn btn-primary btn-sm" type="button" onclick="document.getElementById('authorityForm').scrollIntoView({behavior:'smooth'})"><?= icon('plus', 'icon-sm') ?> সংস্থা যোগ করুন</button>
  </div>
</div>

<div class="admin-stat-grid">
  <div class="admin-stat">
    <div class="admin-stat-top"><span class="admin-stat-label">মোট সংস্থা</span><span class="admin-stat-icon green"><?= icon('landmark', 'icon-sm') ?></span></div>
    <div class="admin-stat-value"><?= bn(count($authorities)) ?></div>
    <div class="admin-stat-foot"><?= bn($activeAuthorities) ?>টি সক্রিয়</div>
  </div>
  <div class="admin-stat">
    <div class="admin-stat-top"><span class="admin-stat-label">বরাদ্দকৃত রিপোর্ট</span><span class="admin-stat-icon blue"><?= icon('file-text', 'icon-sm') ?></span></div>
    <div class="admin-stat-value"><?= bn($totalAssigned) ?></div>
    <div class="admin-stat-foot">সব সংস্থায় মোট</div>
  </div>
  <div class="admin-stat">
    <div class="admin-stat-top"><span class="admin-stat-label">সমাধান সম্পন্ন</span><span class="admin-stat-icon violet"><?= icon('check-circle', 'icon-sm') ?></span></div>
    <div class="admin-stat-value"><?= bn($totalResolved) ?></div>
    <div class="admin-stat-foot"><span class="up"><?= icon('trending-up') ?> <?= bn(progress_percent($totalResolved, $totalAssigned)) ?>%</span> সফলতার হার</div>
  </div>
  <div class="admin-stat">
    <div class="admin-stat-top"><span class="admin-stat-label">গড় প্রতিক্রিয়া</span><span class="admin-stat-icon amber"><?= icon('clock', 'icon-sm') ?></span></div>
    <div class="admin-stat-value"><?= bn('২.৪') ?> দিন</div>
    <div class="admin-stat-foot"><span class="up"><?= icon('trending-up') ?> ০.৩ দিন</span> দ্রুততর</div>
  </div>
</div>

<section class="admin-panel">
  <div class="admin-panel-head"><div><h3>সংস্থার তালিকা</h3><p>দায়িত্ব, কর্মক্ষমতা ও যোগাযোগের তথ্য</p></div><span class="badge badge-soft"><?= icon('refresh', 'icon-xs') ?> সর্বশেষ হালনাগাদ: আজ</span></div>
  <div class="admin-panel-body">
    <div class="authority-grid">
      <?php foreach ($authorities as $authority): ?>
        <?php $pct = progress_percent($authority['resolved'], $authority['assigned']); ?>
        <article class="authority-card">
          <div class="authority-logo"><?= e($authority['initials']) ?></div>
          <div class="authority-body">
            <div class="row-between">
              <div>
                <h3><?= e($authority['name']) ?></h3>
                <span><?= e($authority['type']) ?> · <?= e($authority['area']) ?></span>
              </div>
              <span class="badge badge-<?= e($authority['tone']) ?>"><span class="status-dot"></span><?= e($authority['status']) ?></span>
            </div>
            <div class="authority-meta">
              <span><?= icon('user', 'icon-xs') ?> <?= e($authority['person']) ?></span>
              <span><?= icon('phone', 'icon-xs') ?> <?= e($authority['phone']) ?></span>
              <span><?= icon('mail', 'icon-xs') ?> <?= e($authority['email']) ?></span>
            </div>
            <div style="margin-top:9px">
              <div class="row-between" style="font-size:.65rem;color:var(--muted);margin-bottom:3px"><span>সমাধানের হার</span><span><b><?= bn($authority['resolved']) ?></b> / <?= bn($authority['assigned']) ?></span></div>
              <?= progress_bar($authority['resolved'], $authority['assigned'], 'progress-thin') ?>
            </div>
            <div class="authority-foot">
              <div class="col"><span>বরাদ্দ</span><b><?= bn($authority['assigned']) ?></b></div>
              <div class="col"><span>সমাধান</span><b><?= bn($authority['resolved']) ?></b></div>
              <div class="col"><span>সাফল্য</span><b><?= bn($pct) ?>%</b></div>
              <div class="authority-actions">
                <a class="btn btn-sm btn-outline" href="admin-issues.php?q=<?= urlencode($authority['name']) ?>" title="এই কর্তৃপক্ষের রিপোর্ট"><?= icon('file-text', 'icon-sm') ?></a>
                <a class="btn btn-sm btn-outline" href="mailto:<?= e($authority['email']) ?>" title="ইমেল করুন"><?= icon('mail', 'icon-sm') ?></a>
              </div>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<div class="admin-grid cols-2-1" style="margin-top:16px">
  <section class="admin-panel">
    <div class="admin-panel-head"><div><h3>সাম্প্রতিক বরাদ্দ</h3><p>কর্তৃপক্ষভিত্তিক চলমান রিপোর্ট</p></div><a class="panel-link" href="admin-issues.php">সব রিপোর্ট <?= icon('arrow-right', 'icon-xs') ?></a></div>
    <div class="table-scroll">
      <table class="data-table admin-table">
        <thead><tr><th class="cell-issue">রিপোর্ট</th><th>কর্তৃপক্ষ</th><th>অবস্থা</th><th>তারিখ</th><th class="cell-actions">কার্যক্রম</th></tr></thead>
        <tbody>
          <?php foreach (array_slice($issues, 0, 6) as $issue): ?>
            <tr>
              <td class="cell-issue">
                <div class="cell-person">
                  <img class="cell-thumb" src="<?= e($issue['image']) ?>" alt="" loading="lazy">
                  <div><a class="cell-main" href="admin-verify.php?id=<?= (int) $issue['id'] ?>"><?= e(excerpt($issue['title'], 42)) ?></a><span class="cell-sub"><?= e($issue['code']) ?></span></div>
                </div>
              </td>
              <td class="table-sub" style="max-width:200px"><?= e($issue['authority']) ?></td>
              <td><?= status_badge($issue['status']) ?></td>
              <td class="table-sub"><?= e($issue['date']) ?></td>
              <td class="cell-actions"><a class="btn btn-sm btn-outline" href="admin-progress.php?id=<?= (int) $issue['id'] ?>">অগ্রগতি</a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>

  <section class="admin-panel" id="authorityForm">
    <div class="admin-panel-head"><div><h3>নতুন সংস্থা যোগ করুন</h3><p>দায়িত্বপ্রাপ্ত প্রতিষ্ঠানের তথ্য</p></div></div>
    <div class="admin-panel-body">
      <form class="admin-form" data-demo-submit data-success-message="নতুন সংস্থাটি যোগ করা হয়েছে এবং রিপোর্ট বরাদ্দের তালিকায় যুক্ত হয়েছে।" data-confirm-title="সংস্থা যোগ করবেন?" data-confirm-body="নতুন সংস্থাটি রিপোর্ট বরাদ্দের তালিকায় দেখা যাবে।">
        <div class="field">
          <label class="field-label" for="authorityName">সংস্থার নাম <span class="req">*</span></label>
          <input class="input" id="authorityName" type="text" required placeholder="যেমন: ঢাকা উত্তর সিটি কর্পোরেশন">
        </div>
        <div class="field">
          <label class="field-label" for="authorityType">ধরন</label>
          <select class="select" id="authorityType">
            <option>সরকারি প্রতিষ্ঠান</option>
            <option>সরকারি সংস্থা</option>
            <option>স্বেচ্ছাসেবী সংগঠন</option>
            <option>নাগরিক প্ল্যাটফর্ম</option>
          </select>
        </div>
        <div class="form-grid">
          <div class="field">
            <label class="field-label" for="authorityPerson">দায়িত্বপ্রাপ্ত ব্যক্তি</label>
            <input class="input" id="authorityPerson" type="text" placeholder="নাম">
          </div>
          <div class="field">
            <label class="field-label" for="authorityPhone">মোবাইল</label>
            <input class="input" id="authorityPhone" type="text" placeholder="০১৭xx-xxxxxx">
          </div>
        </div>
        <div class="field">
          <label class="field-label" for="authorityEmail">ইমেইল</label>
          <input class="input" id="authorityEmail" type="email" placeholder="info@example.gov.bd">
        </div>
        <div class="field">
          <label class="field-label" for="authorityCategories">দায়িত্বের খাত</label>
          <input class="input" id="authorityCategories" type="text" placeholder="সড়ক, ড্রেন, বর্জ্য…">
        </div>
        <button class="btn btn-primary btn-block" type="submit"><?= icon('plus', 'icon-sm') ?> সংস্থা যোগ করুন</button>
      </form>
    </div>
  </section>
</div>

<section class="admin-panel">
  <div class="admin-panel-head"><div><h3>সংস্থাভিত্তিক কর্মক্ষমতা</h3><p>বরাদ্দ ও সমাধানের তুলনামূলক চিত্র</p></div></div>
  <div class="admin-panel-body">
    <div class="hbar-list">
      <?php foreach ($authorities as $authority): ?>
        <?php $pct = progress_percent($authority['resolved'], $authority['assigned']); ?>
        <div class="hbar-item">
          <div class="hbar-top"><span><?= icon('landmark', 'icon-xs') ?> <?= e($authority['name']) ?></span><b><?= bn($pct) ?>% (<?= bn($authority['resolved']) ?>/<?= bn($authority['assigned']) ?>)</b></div>
          <div class="hbar-track"><span class="hbar-fill <?= $pct >= 70 ? '' : ($pct >= 50 ? 'amber' : 'rose') ?>" style="width:<?= $pct ?>%"></span></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
