<?php
/**
 * নগরবন্ধু — ডেটাবেজ ইনস্টলেশন ও অবস্থা যাচাই
 *
 * প্রথমবার সাইট খুললেই includes/db.php স্বয়ংক্রিয়ভাবে ডেটাবেজ, টেবিল ও
 * ডেমো অ্যাকাউন্ট তৈরি করে। এই পেজটি শুধু সেটি যাচাই ও প্রয়োজনে পুনরায়
 * চালানোর জন্য। অটো-ইনস্টল বন্ধ থাকলে এখান থেকে ম্যানুয়ালি ইনস্টল করা যায়।
 */
require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'ডেটাবেজ ইনস্টলেশন — নগরবন্ধু';
$config = nb_db_config();
$notice = null;
$noticeTone = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'install') {
    if (!csrf_valid()) {
        $notice = 'নিরাপত্তা যাচাই ব্যর্থ হয়েছে। পেজটি রিফ্রেশ করে আবার চেষ্টা করুন।';
        $noticeTone = 'danger';
    } elseif (!db_ok()) {
        $notice = db_unavailable_message();
        $noticeTone = 'danger';
    } else {
        $ok = db_install_schema();
        db_upgrade_schema();
        db_seed_users();
        $notice = $ok
            ? 'ডেটাবেজ স্কিমা ও ডেমো অ্যাকাউন্ট সফলভাবে প্রস্তুত করা হয়েছে।'
            : 'স্কিমা চালানো যায়নি: ' . (db_error() !== '' ? db_error() : 'অজানা ত্রুটি');
        $noticeTone = $ok ? 'success' : 'danger';
    }
}

$pdo = db();
$connected = $pdo instanceof PDO;
$tables = ['users', 'issues', 'issue_images', 'issue_updates', 'supports', 'comments', 'donations'];
$counts = [];
if ($connected) {
    foreach ($tables as $table) {
        $counts[$table] = (int) db_val('SELECT COUNT(*) FROM `' . $table . '`');
    }
}

$uploadDir = dirname(__DIR__) . '/uploads/issues';
$uploadReady = is_dir($uploadDir) ? is_writable($uploadDir) : is_writable(dirname($uploadDir));
$schemaReady = is_file(dirname(__DIR__) . '/database/schema.sql');

include __DIR__ . '/includes/header.php';
?>
<div class="container" style="max-width:920px;padding-top:32px;padding-bottom:56px">
  <span class="section-kicker">সেটআপ</span>
  <h1 style="margin:6px 0 8px">ডেটাবেজ ইনস্টলেশন</h1>
  <p class="muted-note" style="margin-bottom:22px">নগরবন্ধুর ব্যবহারকারী, রিপোর্ট, অনুদান, সমর্থন ও মন্তব্য MySQL-এ সংরক্ষিত হয়। নিচে সংযোগ ও টেবিলের অবস্থা দেখুন।</p>

  <?php if ($notice): ?>
    <div class="alert alert-<?= e($noticeTone) ?>" style="margin-bottom:18px"><?= e($notice) ?></div>
  <?php endif; ?>

  <div class="panel" style="margin-bottom:20px">
    <div class="panel-head">
      <div><h2>সংযোগের অবস্থা</h2><p>includes/config.php অথবা includes/config.local.php থেকে পড়া হচ্ছে</p></div>
      <?php if ($connected): ?>
        <span class="badge badge-success"><?= icon('check-circle', 'icon-xs') ?> সংযুক্ত</span>
      <?php else: ?>
        <span class="badge badge-danger"><?= icon('alert-circle', 'icon-xs') ?> সংযুক্ত নয়</span>
      <?php endif; ?>
    </div>
    <div class="panel-body">
      <?php if (!$connected): ?>
        <div class="admin-empty" style="padding:24px 0">
          <?= icon('database', 'icon-md') ?>
          <p><?= e(db_unavailable_message()) ?></p>
          <?php if (db_error() !== ''): ?>
            <p class="muted-note" style="font-family:monospace;font-size:.8rem"><?= e(db_error()) ?></p>
          <?php endif; ?>
          <p class="muted-note">XAMPP Control Panel খুলে MySQL Start করুন, তারপর এই পেজটি রিফ্রেশ করুন।</p>
        </div>
      <?php else: ?>
        <div class="table-scroll">
          <table class="data-table">
            <tbody>
              <tr><th scope="row">হোস্ট</th><td><?= e($config['host']) ?>:<?= (int) $config['port'] ?></td></tr>
              <tr><th scope="row">ডেটাবেজ</th><td><?= e($config['name']) ?></td></tr>
              <tr><th scope="row">ব্যবহারকারী</th><td><?= e($config['user']) ?></td></tr>
              <tr><th scope="row">অটো-ইনস্টল</th><td><?= !empty($config['auto_install']) ? 'চালু' : 'বন্ধ' ?></td></tr>
              <tr><th scope="row">স্কিমা ফাইল</th><td><?= $schemaReady ? 'পাওয়া গেছে' : 'পাওয়া যায়নি' ?></td></tr>
              <tr><th scope="row">ছবি আপলোড ফোল্ডার</th><td><?= $uploadReady ? 'লেখার অনুমতি আছে' : 'লেখার অনুমতি নেই' ?></td></tr>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <?php if ($connected): ?>
    <div class="panel" style="margin-bottom:20px">
      <div class="panel-head">
        <div><h2>টেবিলের সারি</h2><p>প্রতিটি টেবিলে বর্তমানে সংরক্ষিত রেকর্ডের সংখ্যা</p></div>
      </div>
      <div class="panel-body">
        <div class="dash-stats" style="margin-bottom:0">
          <?php foreach ($tables as $table): ?>
            <div class="dash-stat tone-green">
              <div class="dash-stat-head"><span><?= e($table) ?></span><?= icon('database', 'icon-sm') ?></div>
              <strong><?= bn($counts[$table] ?? 0) ?></strong>
              <small><?= db_table_exists($table) ? 'টেবিল প্রস্তুত' : 'টেবিল নেই' ?></small>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <div class="panel" style="margin-bottom:20px">
      <div class="panel-head">
        <div><h2>ডেমো অ্যাকাউন্ট</h2><p>প্রথম ইনস্টলে স্বয়ংক্রিয়ভাবে তৈরি হয়</p></div>
      </div>
      <div class="panel-body">
        <div class="table-scroll">
          <table class="data-table">
            <thead><tr><th scope="col">ভূমিকা</th><th scope="col">ইমেইল</th><th scope="col">মোবাইল</th><th scope="col">পাসওয়ার্ড</th></tr></thead>
            <tbody>
              <tr><td>প্রশাসক</td><td>admin@nagorbondhu.gov.bd</td><td>01711000112</td><td>admin123</td></tr>
              <tr><td>নাগরিক</td><td>sadia.afrin@example.com</td><td>01711234567</td><td>123456</td></tr>
            </tbody>
          </table>
        </div>
        <p class="muted-note" style="margin-top:12px">নিরাপত্তার জন্য বাস্তব সার্ভারে ব্যবহারের আগে এই পাসওয়ার্ড বদলে নিন।</p>
      </div>
    </div>
  <?php endif; ?>

  <div class="panel">
    <div class="panel-body row-between">
      <div>
        <h3 style="font-size:.92rem;margin-bottom:3px">স্কিমা আবার চালাতে চান?</h3>
        <p class="muted-note">এটি ডেটা মুছে ফেলে না; শুধু অনুপস্থিত টেবিল তৈরি করে ও ডেমো অ্যাকাউন্ট নিশ্চিত করে।</p>
      </div>
      <form method="post" action="install.php">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="install">
        <button class="btn btn-primary" type="submit"<?= $connected ? '' : ' disabled' ?>><?= icon('database', 'icon-sm') ?> ইনস্টলার চালান</button>
      </form>
    </div>
  </div>

  <p style="margin-top:20px"><a class="btn btn-outline btn-sm" href="index.php"><?= icon('arrow-left', 'icon-sm') ?> হোমপেজে ফিরে যান</a></p>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
