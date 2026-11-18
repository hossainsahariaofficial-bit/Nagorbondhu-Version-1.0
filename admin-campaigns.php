<?php
require_once __DIR__ . '/includes/bootstrap.php';

$adminPage = 'admin-campaigns.php';
$adminTitle = 'অনুদান কার্যক্রম';

$campaigns = nb_campaigns();
$activeCampaigns = array_filter($campaigns, fn ($c) => $c['status'] === 'চলমান');
$totalRaised = array_sum(array_column($campaigns, 'raised'));
$totalTarget = array_sum(array_column($campaigns, 'target'));
$totalSupporters = array_sum(array_column($campaigns, 'supporters'));

include __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-page-head">
  <div>
    <div class="admin-breadcrumb-lite"><a href="admin.php">প্রশাসনিক প্যানেল</a><?= icon('chevron-right') ?><span>অনুদান কার্যক্রম</span></div>
    <h2>অনুদান কার্যক্রম পরিচালনা</h2>
    <p>নতুন কার্যক্রম তৈরি করুন, অগ্রগতি হালনাগাদ করুন এবং স্বচ্ছতার তথ্য প্রকাশ করুন।</p>
  </div>
  <div class="admin-page-head-actions">
    <a class="btn btn-outline btn-sm" href="donate.php" target="_blank" rel="noopener"><?= icon('external-link', 'icon-sm') ?> পাবলিক পেজ</a>
    <button class="btn btn-primary btn-sm" type="button" onclick="document.getElementById('campaignTitle').focus()"><?= icon('plus', 'icon-sm') ?> নতুন কার্যক্রম</button>
  </div>
</div>

<div class="admin-stat-grid">
  <div class="admin-stat accent">
    <div class="admin-stat-top"><span class="admin-stat-label">মোট সংগ্রহ</span><span class="admin-stat-icon"><?= icon('wallet', 'icon-sm') ?></span></div>
    <div class="admin-stat-value"><?= taka($totalRaised) ?></div>
    <div class="admin-stat-foot"><span class="up"><?= icon('trending-up') ?> <?= bn(progress_percent($totalRaised, $totalTarget)) ?>%</span> মোট লক্ষ্যের</div>
  </div>
  <div class="admin-stat">
    <div class="admin-stat-top"><span class="admin-stat-label">চলমান কার্যক্রম</span><span class="admin-stat-icon green"><?= icon('target', 'icon-sm') ?></span></div>
    <div class="admin-stat-value"><?= bn(count($activeCampaigns)) ?></div>
    <div class="admin-stat-foot"><?= bn(count($campaigns)) ?>টির মধ্যে সক্রিয়</div>
  </div>
  <div class="admin-stat">
    <div class="admin-stat-top"><span class="admin-stat-label">মোট অনুদানদাতা</span><span class="admin-stat-icon blue"><?= icon('users', 'icon-sm') ?></span></div>
    <div class="admin-stat-value"><?= bn($totalSupporters) ?></div>
    <div class="admin-stat-foot">সব কার্যক্রমে সমর্থন</div>
  </div>
  <div class="admin-stat">
    <div class="admin-stat-top"><span class="admin-stat-label">সম্পন্ন কার্যক্রম</span><span class="admin-stat-icon violet"><?= icon('award', 'icon-sm') ?></span></div>
    <div class="admin-stat-value"><?= bn(count($campaigns) - count($activeCampaigns)) ?></div>
    <div class="admin-stat-foot">স্বচ্ছতা প্রতিবেদন প্রকাশিত</div>
  </div>
</div>

<div class="admin-grid cols-2-1">
  <section class="admin-panel">
    <div class="admin-panel-head"><div><h3>কার্যক্রমের তালিকা</h3><p>সব অনুদান উদ্যোগ ও তাদের অগ্রগতি</p></div><span class="badge badge-success"><?= icon('check-circle', 'icon-xs') ?> <?= bn(count($activeCampaigns)) ?>টি চলমান</span></div>
    <div class="table-scroll admin-table-wrap">
      <table class="data-table admin-table">
        <thead><tr><th class="cell-issue">কার্যক্রম</th><th>সংগঠক</th><th>সংগৃহীত / লক্ষ্য</th><th>অগ্রগতি</th><th>সমর্থক</th><th>সময়সীমা</th><th>অবস্থা</th><th class="cell-actions">কার্যক্রম</th></tr></thead>
        <tbody>
          <?php foreach ($campaigns as $campaign): ?>
            <?php $pct = progress_percent($campaign['raised'], $campaign['target']); $done = $campaign['status'] === 'সম্পন্ন'; ?>
            <tr>
              <td class="cell-issue">
                <div class="cell-person">
                  <img class="cell-thumb" src="<?= e($campaign['image']) ?>" alt="" loading="lazy">
                  <div><a class="cell-main" href="donation.php?id=<?= (int) $campaign['id'] ?>" target="_blank" rel="noopener"><?= e(excerpt($campaign['title'], 40)) ?></a><span class="cell-sub">কার্যক্রম #<?= bn($campaign['id']) ?> · <?= e($campaign['organizer_type']) ?></span></div>
                </div>
              </td>
              <td class="table-sub"><?= e($campaign['organizer']) ?></td>
              <td><strong style="font-size:.75rem"><?= taka($campaign['raised']) ?></strong><span class="cell-sub">লক্ষ্য <?= taka($campaign['target']) ?></span></td>
              <td style="min-width:110px">
                <div class="row-between" style="font-size:.64rem;color:var(--muted);margin-bottom:3px"><span><?= bn($pct) ?>%</span></div>
                <?= progress_bar($campaign['raised'], $campaign['target'], 'progress-thin') ?>
              </td>
              <td class="table-sub"><?= bn($campaign['supporters']) ?> জন</td>
              <td class="table-sub"><?= $done ? 'সম্পন্ন' : e($campaign['deadline']) ?></td>
              <td><span class="badge badge-<?= $done ? 'success' : 'warning' ?>"><span class="status-dot"></span><?= e($campaign['status']) ?></span></td>
              <td class="cell-actions">
                <div class="row-menu" data-row-menu>
                  <button class="row-menu-btn icon-btn" type="button" aria-haspopup="true" aria-expanded="false" aria-label="কার্যক্রম মেনু"><?= icon('more-horizontal', 'icon-sm') ?></button>
                  <div class="row-dropdown">
                    <a href="donation.php?id=<?= (int) $campaign['id'] ?>" target="_blank" rel="noopener"><?= icon('eye', 'icon-sm') ?> পাবলিক পেজ</a>
                    <a href="admin-campaigns.php#campaignForm"><?= icon('edit', 'icon-sm') ?> সম্পাদনা করুন</a>
                    <?php if (!$done): ?><button type="button" data-admin-action="complete"><?= icon('check-circle', 'icon-sm') ?> সম্পন্ন ঘোষণা</button><?php endif; ?>
                    <div class="dropdown-sep"></div>
                    <button class="danger-item" type="button" data-admin-action="reject"><?= icon('ban', 'icon-sm') ?> স্থগিত করুন</button>
                  </div>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="admin-panel-foot"><span>মোট <?= bn(count($campaigns)) ?>টি কার্যক্রম</span><a href="admin-donations.php">লেনদেন দেখুন <?= icon('arrow-right', 'icon-xs') ?></a></div>
  </section>

  <div id="campaignForm" style="display:flex;flex-direction:column;gap:16px">
    <section class="admin-panel">
      <div class="admin-panel-head"><div><h3>লাইভ প্রিভিউ</h3><p>পাবলিক কার্ডে যেভাবে দেখা যাবে</p></div></div>
      <div class="admin-panel-body">
        <div style="border:1px solid var(--line);border-radius:12px;overflow:hidden;background:#fff">
          <div style="background:linear-gradient(135deg,var(--green-dark),#0A6C4A);padding:18px;color:#fff">
            <span class="badge badge-white">চলমান</span>
            <h3 data-preview-title style="color:#fff;font-size:.95rem;margin-top:10px;line-height:1.5">কার্যক্রমের নাম এখানে দেখা যাবে</h3>
            <p data-preview-short style="font-size:.72rem;color:rgba(255,255,255,.72);line-height:1.65;margin-top:4px">সংক্ষিপ্ত বিবরণ এখানে দেখা যাবে।</p>
          </div>
          <div style="padding:12px 14px">
            <div class="progress progress-thin"><span style="width:0"></span></div>
            <div class="row-between" style="margin-top:7px;font-size:.64rem;color:var(--muted)"><span>সংগৃহীত ৳ ০</span><span>লক্ষ্য ৳ ০</span></div>
          </div>
        </div>
      </div>
    </section>

    <section class="admin-panel">
      <div class="admin-panel-head"><div><h3>নতুন কার্যক্রম</h3><p>তথ্য দিলে উপরের প্রিভিউ সাথে সাথে বদলাবে</p></div></div>
      <div class="admin-panel-body">
        <form class="admin-form" data-campaign-form data-demo-submit data-success-message="নতুন অনুদান কার্যক্রমটি খসড়া হিসেবে সংরক্ষণ করা হয়েছে।" data-confirm-title="কার্যক্রম তৈরি করবেন?" data-confirm-body="তথ্য যাচাই করে কার্যক্রমটি প্রকাশ করা হবে।">
          <div class="field">
            <label class="field-label" for="campaignTitle">কার্যক্রমের নাম <span class="req">*</span></label>
            <input class="input" id="campaignTitle" name="title" type="text" required placeholder="যেমন: মিরপুরে নিরাপদ সড়কবাতি">
          </div>
          <div class="field">
            <label class="field-label" for="campaignShort">সংক্ষিপ্ত বিবরণ <span class="req">*</span></label>
            <textarea class="textarea" id="campaignShort" name="short" required placeholder="এক বাক্যে কার্যক্রমের উদ্দেশ্য লিখুন…" style="min-height:70px"></textarea>
          </div>
          <div class="form-grid">
            <div class="field">
              <label class="field-label" for="campaignTarget">লক্ষ্য (টাকা) <span class="req">*</span></label>
              <input class="input" id="campaignTarget" name="target" type="number" min="1000" step="1000" required placeholder="50000">
            </div>
            <div class="field">
              <label class="field-label" for="campaignDeadline">সময়সীমা <span class="req">*</span></label>
              <input class="input" id="campaignDeadline" name="deadline" type="date" required>
            </div>
            <div class="field">
              <label class="field-label" for="campaignOrganizer">সংগঠক</label>
              <input class="input" id="campaignOrganizer" name="organizer" type="text" placeholder="সংগঠনের নাম">
            </div>
            <div class="field">
              <label class="field-label" for="campaignContact">যোগাযোগ</label>
              <input class="input" id="campaignContact" name="contact" type="text" placeholder="০১৭xx-xxxxxx">
            </div>
          </div>
          <div class="field">
            <label class="field-label" for="campaignDescription">বিস্তারিত বিবরণ</label>
            <textarea class="textarea" id="campaignDescription" name="description" placeholder="কার্যক্রমের পরিকল্পনা, ব্যয়ের খাত ও প্রত্যাশিত ফলাফল…"></textarea>
          </div>
          <div class="field">
            <label class="field-label" for="campaignImage">কভার ছবি</label>
            <input class="input" id="campaignImage" name="image" type="file" accept="image/*">
            <span class="field-hint">সর্বোচ্চ ৫MB। ১৬:৯ অনুপাতের ছবি সুপারিশ করা হয়।</span>
          </div>
          <label class="checkbox"><input type="checkbox" name="terms" value="1" required> কার্যক্রমের ব্যয় ও স্বচ্ছতা নীতিমালা মেনে চলার নিশ্চয়তা দিচ্ছি</label>
          <div class="form-actions-bar">
            <button class="btn btn-outline" type="button" data-save-draft="campaign"><?= icon('save', 'icon-sm') ?> খসড়া সংরক্ষণ</button>
            <button class="btn btn-primary" type="submit"><?= icon('send', 'icon-sm') ?> প্রকাশের জন্য জমা দিন</button>
          </div>
        </form>
      </div>
    </section>
  </div>
</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
