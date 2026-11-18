<?php
require_once __DIR__ . '/includes/bootstrap.php';

$adminPage = 'admin-settings.php';
$adminTitle = 'সিস্টেম সেটিংস';

include __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-page-head">
  <div>
    <div class="admin-breadcrumb-lite"><a href="admin.php">প্রশাসনিক প্যানেল</a><?= icon('chevron-right') ?><span>সিস্টেম সেটিংস</span></div>
    <h2>প্ল্যাটফর্ম কনফিগারেশন</h2>
    <p>সাইট, রিপোর্ট, অনুদান, নোটিফিকেশন, নিরাপত্তা ও ইন্টিগ্রেশন সংক্রান্ত সেটিংস পরিচালনা করুন।</p>
  </div>
  <div class="admin-page-head-actions">
    <span class="admin-date"><?= icon('shield', 'icon-xs') ?> সর্বশেষ পরিবর্তন: <strong><?= bn('২ নভেম্বর ২০২৬') ?></strong></span>
    <a class="btn btn-outline btn-sm" href="admin-profile.php"><?= icon('user', 'icon-sm') ?> প্রোফাইল</a>
  </div>
</div>

<div class="admin-note" style="margin-bottom:16px"><?= icon('info', 'icon-sm') ?><div>এটি একটি ডেমো সেটিংস প্যানেল। পরিবর্তনগুলো এই সেশনে দেখানো হয়, কিন্তু বাস্তব সার্ভারে সংরক্ষিত হয় না।</div></div>

<div class="settings-layout">
  <nav class="settings-menu" data-settings-menu aria-label="সেটিংস বিভাগ">
    <a href="#general" class="active"><?= icon('settings') ?> সাধারণ</a>
    <a href="#reports"><?= icon('file-text') ?> রিপোর্ট</a>
    <a href="#donations"><?= icon('wallet') ?> অনুদান</a>
    <a href="#notifications"><?= icon('bell') ?> নোটিফিকেশন</a>
    <a href="#security"><?= icon('shield') ?> নিরাপত্তা</a>
    <a href="#integrations"><?= icon('link') ?> ইন্টিগ্রেশন</a>
  </nav>

  <div class="settings-body">
    <section class="settings-section" id="general" data-settings-section="general">
      <div class="settings-section-head"><h2>সাধারণ সেটিংস</h2><p>সাইটের মৌলিক তথ্য ও আঞ্চলিক পছন্দ</p></div>
      <div class="settings-section-body">
        <form class="admin-form" data-demo-submit data-success-message="সাধারণ সেটিংস সংরক্ষণ করা হয়েছে।">
          <div class="form-grid">
            <div class="field">
              <label class="field-label" for="siteName">প্ল্যাটফর্মের নাম</label>
              <input class="input" id="siteName" type="text" value="নগরবন্ধু">
            </div>
            <div class="field">
              <label class="field-label" for="siteTagline">ট্যাগলাইন</label>
              <input class="input" id="siteTagline" type="text" value="আমাদের পাড়া, আমাদের দায়িত্ব">
            </div>
            <div class="field">
              <label class="field-label" for="siteEmail">যোগাযোগের ইমেইল</label>
              <input class="input" id="siteEmail" type="email" value="hello@nagorbondhu.gov.bd">
            </div>
            <div class="field">
              <label class="field-label" for="sitePhone">হেল্পলাইন</label>
              <input class="input" id="sitePhone" type="text" value="১৬২৬৩">
            </div>
            <div class="field">
              <label class="field-label" for="siteArea">প্রধান এলাকা</label>
              <select class="select" id="siteArea">
                <option selected>ঢাকা মহানগর</option>
                <option>চট্টগ্রাম মহানগর</option>
                <option>সিলেট মহানগর</option>
                <option>রাজশাহী মহানগর</option>
              </select>
            </div>
            <div class="field">
              <label class="field-label" for="siteLanguage">ডিফল্ট ভাষা</label>
              <select class="select" id="siteLanguage">
                <option selected>বাংলা</option>
                <option>বাংলা ও ইংরেজি</option>
              </select>
            </div>
          </div>
          <div class="field">
            <label class="field-label" for="siteAddress">কার্যালয়ের ঠিকানা</label>
            <textarea class="textarea" id="siteAddress">নগর ভবন, ৩৫ নম্বর সড়ক, শাহবাগ, ঢাকা ১০০০</textarea>
          </div>
          <div class="toggle-row"><div><strong>রক্ষণাবেক্ষণ মোড</strong><p>চালু করলে সাধারণ নাগরিকরা প্ল্যাটফর্মে প্রবেশ করতে পারবেন না।</p></div><label class="switch"><input type="checkbox"><span class="switch-track"></span></label></div>
          <div class="toggle-row"><div><strong>নতুন নিবন্ধন চালু</strong><p>নতুন নাগরিক অ্যাকাউন্ট তৈরি করার অনুমতি দিন।</p></div><label class="switch"><input type="checkbox" checked><span class="switch-track"></span></label></div>
          <div class="form-actions-bar"><button class="btn btn-primary" type="submit"><?= icon('save', 'icon-sm') ?> পরিবর্তন সংরক্ষণ</button></div>
        </form>
      </div>
    </section>

    <section class="settings-section" id="reports" data-settings-section="reports" hidden>
      <div class="settings-section-head"><h2>রিপোর্ট সেটিংস</h2><p>যাচাই, বরাদ্দ ও সময়সীমা সংক্রান্ত নিয়ম</p></div>
      <div class="settings-section-body">
        <form class="admin-form" data-demo-submit data-success-message="রিপোর্ট সেটিংস সংরক্ষণ করা হয়েছে।">
          <div class="form-grid">
            <div class="field">
              <label class="field-label" for="autoAssign">স্বয়ংক্রিয় বরাদ্দ</label>
              <select class="select" id="autoAssign">
                <option selected>বিভাগ অনুযায়ী স্বয়ংক্রিয়</option>
                <option>ম্যানুয়াল বরাদ্দ</option>
                <option>যাচাইয়ের পর বরাদ্দ</option>
              </select>
            </div>
            <div class="field">
              <label class="field-label" for="slaDays">যাচাইয়ের সময়সীমা (দিন)</label>
              <input class="input" id="slaDays" type="number" min="1" max="30" value="3">
            </div>
            <div class="field">
              <label class="field-label" for="solveDays">সমাধানের লক্ষ্য (দিন)</label>
              <input class="input" id="solveDays" type="number" min="1" max="90" value="14">
            </div>
            <div class="field">
              <label class="field-label" for="minSupport">সমর্থনের ন্যূনতম সীমা</label>
              <input class="input" id="minSupport" type="number" min="0" value="5">
            </div>
          </div>
          <div class="field">
            <label class="field-label">সক্রিয় সমস্যার ধরন</label>
            <div class="setting-tag-list">
              <?php foreach (nb_categories() as $key => $cat): ?>
                <span class="setting-tag"><?= icon($cat['icon'], 'icon-xs') ?> <?= e($cat['label']) ?><button type="button" data-remove-tag aria-label="<?= e($cat['label']) ?> সরান"><?= icon('x') ?></button></span>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="toggle-row"><div><strong>ডুপ্লিকেট রিপোর্ট শনাক্তকরণ</strong><p>একই এলাকায় একই ধরনের রিপোর্ট স্বয়ংক্রিয়ভাবে চিহ্নিত করুন।</p></div><label class="switch"><input type="checkbox" checked><span class="switch-track"></span></label></div>
          <div class="toggle-row"><div><strong>ছবি বাধ্যতামূলক</strong><p>নতুন রিপোর্টে অন্তত একটি ছবি আবশ্যক করুন।</p></div><label class="switch"><input type="checkbox" checked><span class="switch-track"></span></label></div>
          <div class="toggle-row"><div><strong>সমাধানের আগে-পরে ছবি</strong><p>সমাধান সম্পন্ন চিহ্নিত করার আগে দুই ধরনের ছবি বাধ্যতামূলক।</p></div><label class="switch"><input type="checkbox" checked><span class="switch-track"></span></label></div>
          <div class="form-actions-bar"><button class="btn btn-primary" type="submit"><?= icon('save', 'icon-sm') ?> সংরক্ষণ করুন</button></div>
        </form>
      </div>
    </section>

    <section class="settings-section" id="donations" data-settings-section="donations" hidden>
      <div class="settings-section-head"><h2>অনুদান সেটিংস</h2><p>পেমেন্ট মাধ্যম, সীমা ও স্বচ্ছতার নিয়ম</p></div>
      <div class="settings-section-body">
        <form class="admin-form" data-demo-submit data-success-message="অনুদান সেটিংস সংরক্ষণ করা হয়েছে।">
          <div class="field">
            <label class="field-label">সক্রিয় পেমেন্ট মাধ্যম</label>
            <div class="badge-row">
              <label class="checkbox"><input type="checkbox" checked> বিকাশ</label>
              <label class="checkbox"><input type="checkbox" checked> নগদ</label>
              <label class="checkbox"><input type="checkbox" checked> রকেট</label>
              <label class="checkbox"><input type="checkbox" checked> ব্যাংক</label>
              <label class="checkbox"><input type="checkbox"> কার্ড</label>
            </div>
          </div>
          <div class="form-grid">
            <div class="field">
              <label class="field-label" for="minDonation">ন্যূনতম অনুদান (টাকা)</label>
              <input class="input" id="minDonation" type="number" min="1" value="100">
            </div>
            <div class="field">
              <label class="field-label" for="maxDonation">সর্বোচ্চ অনুদান (টাকা)</label>
              <input class="input" id="maxDonation" type="number" min="100" value="100000">
            </div>
            <div class="field">
              <label class="field-label" for="receiptPrefix">রসিদ নম্বরের উপসর্গ</label>
              <input class="input" id="receiptPrefix" type="text" value="NB-DON-">
            </div>
            <div class="field">
              <label class="field-label" for="payoutCycle">নিষ্পত্তির চক্র</label>
              <select class="select" id="payoutCycle">
                <option selected>সাপ্তাহিক</option>
                <option>দ্বি-সাপ্তাহিক</option>
                <option>মাসিক</option>
              </select>
            </div>
          </div>
          <div class="toggle-row"><div><strong>স্বয়ংক্রিয় রসিদ</strong><p>যাচাই সম্পন্ন হলে অনুদানদাতাকে ইমেইলে রসিদ পাঠান।</p></div><label class="switch"><input type="checkbox" checked><span class="switch-track"></span></label></div>
          <div class="toggle-row"><div><strong>ব্যয়ের বিবরণ প্রকাশ</strong><p>কার্যক্রমের প্রতিটি ব্যয়ের খাত পাবলিক পেজে দেখান।</p></div><label class="switch"><input type="checkbox" checked><span class="switch-track"></span></label></div>
          <div class="toggle-row"><div><strong>অনুদানদাতার নাম গোপন রাখার সুযোগ</strong><p>নাগরিক চাইলে বেনামে অনুদান দিতে পারবেন।</p></div><label class="switch"><input type="checkbox" checked><span class="switch-track"></span></label></div>
          <div class="form-actions-bar"><button class="btn btn-primary" type="submit"><?= icon('save', 'icon-sm') ?> সংরক্ষণ করুন</button></div>
        </form>
      </div>
    </section>

    <section class="settings-section" id="notifications" data-settings-section="notifications" hidden>
      <div class="settings-section-head"><h2>নোটিফিকেশন সেটিংস</h2><p>স্বয়ংক্রিয় বার্তা ও ডেলিভারি পছন্দ</p></div>
      <div class="settings-section-body">
        <form class="admin-form" data-demo-submit data-success-message="নোটিফিকেশন সেটিংস সংরক্ষণ করা হয়েছে।">
          <div class="toggle-row"><div><strong>ইমেইল নোটিফিকেশন</strong><p>গুরুত্বপূর্ণ অবস্থা পরিবর্তনে ইমেইল পাঠান।</p></div><label class="switch"><input type="checkbox" checked><span class="switch-track"></span></label></div>
          <div class="toggle-row"><div><strong>এসএমএস নোটিফিকেশন</strong><p>জরুরি রিপোর্টের জন্য এসএমএস পাঠান।</p></div><label class="switch"><input type="checkbox" checked><span class="switch-track"></span></label></div>
          <div class="toggle-row"><div><strong>পুশ নোটিফিকেশন</strong><p>মোবাইল অ্যাপে তাৎক্ষণিক বার্তা পাঠান।</p></div><label class="switch"><input type="checkbox"><span class="switch-track"></span></label></div>
          <div class="toggle-row"><div><strong>সাপ্তাহিক সারসংক্ষেপ</strong><p>প্রতি সোমবার সকাল ৯টায় সারসংক্ষেপ পাঠান।</p></div><label class="switch"><input type="checkbox" checked><span class="switch-track"></span></label></div>
          <div class="form-grid">
            <div class="field">
              <label class="field-label" for="senderName">প্রেরকের নাম</label>
              <input class="input" id="senderName" type="text" value="নগরবন্ধু">
            </div>
            <div class="field">
              <label class="field-label" for="senderEmail">প্রেরকের ইমেইল</label>
              <input class="input" id="senderEmail" type="email" value="no-reply@nagorbondhu.gov.bd">
            </div>
          </div>
          <div class="form-actions-bar"><button class="btn btn-primary" type="submit"><?= icon('save', 'icon-sm') ?> সংরক্ষণ করুন</button></div>
        </form>
      </div>
    </section>

    <section class="settings-section" id="security" data-settings-section="security" hidden>
      <div class="settings-section-head"><h2>নিরাপত্তা ও অ্যাক্সেস</h2><p>প্রশাসনিক অ্যাকাউন্টের সুরক্ষা</p></div>
      <div class="settings-section-body">
        <form class="admin-form" data-demo-submit data-success-message="নিরাপত্তা সেটিংস সংরক্ষণ করা হয়েছে।">
          <div class="form-grid">
            <div class="field">
              <label class="field-label" for="minPassword">পাসওয়ার্ডের ন্যূনতম দৈর্ঘ্য</label>
              <input class="input" id="minPassword" type="number" min="6" max="32" value="8">
            </div>
            <div class="field">
              <label class="field-label" for="sessionTimeout">সেশন সময়সীমা (মিনিট)</label>
              <input class="input" id="sessionTimeout" type="number" min="5" max="240" value="30">
            </div>
          </div>
          <div class="toggle-row"><div><strong>দুই-ধাপ যাচাই</strong><p>প্রশাসকদের জন্য ওটিপি ভিত্তিক অতিরিক্ত নিরাপত্তা।</p></div><label class="switch"><input type="checkbox" checked><span class="switch-track"></span></label></div>
          <div class="toggle-row"><div><strong>অজানা ডিভাইস থেকে প্রবেশে সতর্কতা</strong><p>নতুন ডিভাইস শনাক্ত হলে ইমেইল পাঠান।</p></div><label class="switch"><input type="checkbox" checked><span class="switch-track"></span></label></div>
          <div class="toggle-row"><div><strong>লগইন প্রচেষ্টা সীমিত করুন</strong><p>পরপর ৫টি ব্যর্থ চেষ্টায় অস্থায়ী লক।</p></div><label class="switch"><input type="checkbox" checked><span class="switch-track"></span></label></div>
          <div class="form-actions-bar"><button class="btn btn-primary" type="submit"><?= icon('save', 'icon-sm') ?> সংরক্ষণ করুন</button></div>
        </form>
        <div class="admin-panel" style="margin-top:16px">
          <div class="admin-panel-head"><div><h3>প্রশাসনিক দল</h3><p>বর্তমান অ্যাক্সেসধারীদের তালিকা</p></div><button class="btn btn-outline btn-sm" type="button" data-admin-action="approve"><?= icon('user-plus', 'icon-sm') ?> সদস্য যোগ</button></div>
          <div class="table-scroll">
            <table class="data-table admin-table">
              <thead><tr><th>সদস্য</th><th>ভূমিকা</th><th>সর্বশেষ প্রবেশ</th><th>অবস্থা</th><th class="cell-actions">কার্যক্রম</th></tr></thead>
              <tbody>
                <tr><td><div class="cell-person"><?= avatar('আ', 'green', 'sm') ?><div><strong>মোহাম্মদ আরিফুল ইসলাম</strong><span>admin@nagorbondhu.gov.bd</span></div></div></td><td>প্রধান প্রশাসক</td><td class="table-sub">আজ, সকাল ৯:১৪</td><td><?= status_badge('verified') ?></td><td class="cell-actions"><button class="btn btn-sm btn-outline" type="button" data-admin-action="suspend">স্থগিত</button></td></tr>
                <tr><td><div class="cell-person"><?= avatar('ফা', 'blue', 'sm') ?><div><strong>ফাহমিদা আক্তার</strong><span>fahmida@nagorbondhu.gov.bd</span></div></div></td><td>রিপোর্ট ম্যানেজার</td><td class="table-sub">আজ, সকাল ৮:৪০</td><td><?= status_badge('verified') ?></td><td class="cell-actions"><button class="btn btn-sm btn-outline" type="button" data-admin-action="suspend">স্থগিত</button></td></tr>
                <tr><td><div class="cell-person"><?= avatar('তা', 'violet', 'sm') ?><div><strong>তাসনিম আরা</strong><span>tasnim@nagorbondhu.gov.bd</span></div></div></td><td>মডারেটর</td><td class="table-sub">গতকাল, বিকাল ৫:২০</td><td><?= status_badge('verified') ?></td><td class="cell-actions"><button class="btn btn-sm btn-outline" type="button" data-admin-action="suspend">স্থগিত</button></td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>

    <section class="settings-section" id="integrations" data-settings-section="integrations" hidden>
      <div class="settings-section-head"><h2>ইন্টিগ্রেশন ও এপিআই</h2><p>বহিরাগত সেবার সঙ্গে সংযোগ</p></div>
      <div class="settings-section-body">
        <form class="admin-form" data-demo-submit data-success-message="ইন্টিগ্রেশন সেটিংস সংরক্ষণ করা হয়েছে।">
          <div class="form-grid">
            <div class="field">
              <label class="field-label" for="mapProvider">মানচিত্র সেবা</label>
              <select class="select" id="mapProvider"><option selected>সরকারি জিআইএস সার্ভার</option><option>ওপেনস্ট্রিটম্যাপ</option><option>গুগল ম্যাপস</option></select>
            </div>
            <div class="field">
              <label class="field-label" for="smsGateway">এসএমএস গেটওয়ে</label>
              <select class="select" id="smsGateway"><option selected>জাতীয় এসএমএস গেটওয়ে</option><option>বিকাশ এসএমএস</option><option>কাস্টম গেটওয়ে</option></select>
            </div>
            <div class="field">
              <label class="field-label" for="apiKey">এপিআই কী</label>
              <input class="input" id="apiKey" type="text" value="nb_live_••••••••••••8f2a" readonly>
            </div>
            <div class="field">
              <label class="field-label" for="webhook">ওয়েবহুক ইউআরএল</label>
              <input class="input" id="webhook" type="url" value="https://api.nagorbondhu.gov.bd/hooks/reports">
            </div>
          </div>
          <div class="toggle-row"><div><strong>কর্তৃপক্ষের এপিআই অ্যাক্সেস</strong><p>নিবন্ধিত সংস্থাকে রিপোর্ট ডেটা পড়ার অনুমতি দিন।</p></div><label class="switch"><input type="checkbox" checked><span class="switch-track"></span></label></div>
          <div class="toggle-row"><div><strong>স্বয়ংক্রিয় ব্যাকআপ</strong><p>প্রতিদিন রাত ২টায় ডেটাবেজ ব্যাকআপ নিন।</p></div><label class="switch"><input type="checkbox" checked><span class="switch-track"></span></label></div>
          <div class="form-actions-bar">
            <button class="btn btn-outline" type="button" data-admin-action="return"><?= icon('refresh', 'icon-sm') ?> কী পুনরায় তৈরি</button>
            <button class="btn btn-primary" type="submit"><?= icon('save', 'icon-sm') ?> সংরক্ষণ করুন</button>
          </div>
        </form>
        <div class="admin-panel" style="margin-top:16px">
          <div class="admin-panel-head"><div><h3>সিস্টেম তথ্য</h3><p>বর্তমান সংস্করণ ও পরিবেশ</p></div></div>
          <div class="admin-panel-body">
            <div class="admin-kv">
              <div><span>প্ল্যাটফর্ম সংস্করণ</span><strong>২.৪.১</strong></div>
              <div><span>পিএইচপি সংস্করণ</span><strong><?= e(PHP_VERSION) ?></strong></div>
              <div><span>সার্ভার সময়</span><strong><?= bn(date('d M Y, H:i')) ?></strong></div>
              <div><span>ডেটা মোড</span><strong>ডেমো / ইন-মেমোরি</strong></div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
