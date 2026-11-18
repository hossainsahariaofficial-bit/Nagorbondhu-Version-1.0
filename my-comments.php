<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_citizen();
$pageTitle = 'আমার মন্তব্য — নগরবন্ধু';
$dashPage = 'my-comments.php';
$dashTitle = 'আমার মন্তব্য';
$citizen = current_citizen();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    if (!csrf_valid()) {
        flash_set('error', 'নিরাপত্তা যাচাই ব্যর্থ হয়েছে। পেজটি রিফ্রেশ করে আবার চেষ্টা করুন।');
    } elseif (!db_ok()) {
        flash_set('error', db_unavailable_message());
    } else {
        $commentId = (int) ($_POST['comment_id'] ?? 0);
        $deleted = db_exec(
            'DELETE FROM comments WHERE id = ? AND user_id = ?',
            [$commentId, (int) $citizen['id']]
        );
        if ($deleted > 0) {
            flash_set('success', 'আপনার মন্তব্যটি মুছে ফেলা হয়েছে।');
        } else {
            flash_set('error', 'মন্তব্যটি পাওয়া যায়নি বা মুছে ফেলা যায়নি।');
        }
    }
    header('Location: my-comments.php');
    exit;
}

$allIssues = nb_issues();
$myComments = user_comments((int) $citizen['id']);
$totalComments = count($myComments);
$discussionIds = array_unique(array_map(fn ($c) => (int) $c['issue_id'], $myComments));
$discussionCount = count($discussionIds);
$likesTotal = array_sum(array_map(fn ($c) => (int) $c['likes'], $myComments));
$monthStart = strtotime(date('Y-m-01 00:00:00'));
$thisMonth = count(array_filter($myComments, fn ($c) => ($ts = strtotime($c['created_at'])) !== false && $ts >= $monthStart));
include __DIR__ . '/includes/header.php';
?>
<div class="container">
  <?php include __DIR__ . '/includes/dashboard-sidebar.php'; ?>

  <div class="dash-welcome">
    <div>
      <span class="dash-date"><?= icon('message-circle', 'icon-xs') ?> সর্বমোট <?= bn($totalComments) ?>টি মন্তব্য • <?= bn($discussionCount) ?>টি আলোচনায়</span>
      <h1>আমার মন্তব্য</h1>
      <p>আপনার দেওয়া মন্তব্য, প্রতিক্রিয়া ও আলোচনার অগ্রগতি এক জায়গায় দেখুন।</p>
    </div>
    <a class="btn btn-primary" href="issues.php"><?= icon('search', 'icon-sm') ?> আলোচনায় যোগ দিন</a>
  </div>

  <div class="dash-stats">
    <div class="dash-stat tone-green"><div class="dash-stat-head"><span>মোট মন্তব্য</span><?= icon('message-circle', 'icon-sm') ?></div><strong><?= bn($totalComments) ?></strong><small>ডেটাবেজে সংরক্ষিত</small></div>
    <div class="dash-stat tone-blue"><div class="dash-stat-head"><span>আলোচনায় অংশগ্রহণ</span><?= icon('users', 'icon-sm') ?></div><strong><?= bn($discussionCount) ?></strong><small>ভিন্ন রিপোর্টে</small></div>
    <div class="dash-stat tone-violet"><div class="dash-stat-head"><span>এই মাসে</span><?= icon('send', 'icon-sm') ?></div><strong><?= bn($thisMonth) ?></strong><small>নতুন মন্তব্য</small></div>
    <div class="dash-stat tone-amber"><div class="dash-stat-head"><span>সহায়ক ভোট</span><?= icon('star', 'icon-sm') ?></div><strong><?= bn($likesTotal) ?></strong><small>আপনার মন্তব্যে</small></div>
  </div>

  <div class="panel">
    <div class="panel-head">
      <div><h2>সাম্প্রতিক মন্তব্য</h2><p>আপনার আলোচনায় সর্বশেষ কার্যক্রম অনুযায়ী সাজানো</p></div>
      <a class="panel-link" href="my-reports.php">আমার রিপোর্ট <?= icon('arrow-right', 'icon-xs') ?></a>
    </div>
    <div class="panel-body">
      <?php if ($myComments): ?>
        <div class="comment-list">
          <?php foreach ($myComments as $comment): ?>
            <?php $issue = $allIssues[(int) $comment['issue_id']] ?? null; ?>
            <div class="comment-item">
              <?= avatar($citizen['initials'], $citizen['color'], 'md') ?>
              <div class="comment-body">
                <div class="comment-head">
                  <strong><?= e($citizen['name']) ?></strong>
                  <span class="badge badge-success">আপনি</span>
                  <time datetime="<?= e(date('Y-m-d', strtotime($comment['created_at']) ?: time())) ?>"><?= e($comment['time']) ?></time>
                </div>
                <p><?= e($comment['body']) ?></p>
                <p class="muted-note" style="margin-bottom:8px">আলোচনা:
                  <?php if ($issue): ?>
                    <a href="issue.php?id=<?= (int) $issue['id'] ?>" class="text-link"><?= e(excerpt($issue['title'], 70)) ?></a>
                  <?php else: ?>
                    <span>রিপোর্টটি আর পাওয়া যাচ্ছে না</span>
                  <?php endif; ?>
                </p>
                <div class="comment-actions">
                  <button type="button" data-action-like data-likes="<?= (int) $comment['likes'] ?>" aria-pressed="false"><?= icon('heart', 'icon-xs') ?> সহায়ক (<span data-like-count><?= bn($comment['likes']) ?></span>)</button>
                  <?php if ($issue): ?>
                    <a class="muted-note" href="issue.php?id=<?= (int) $issue['id'] ?>#comments"><?= icon('message-circle', 'icon-xs') ?> আলোচনা দেখুন</a>
                  <?php endif; ?>
                  <form method="post" action="my-comments.php" style="display:inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="comment_id" value="<?= (int) $comment['id'] ?>">
                    <button type="submit" data-confirm="এই মন্তব্যটি মুছে ফেলতে চান? মুছে ফেললে তা আর ফিরে পাওয়া যাবে না।" data-confirm-danger data-confirm-title="মন্তব্য মুছে ফেলুন" data-confirm-yes="হ্যাঁ, মুছে ফেলুন"><?= icon('trash', 'icon-xs') ?> মুছে ফেলুন</button>
                  </form>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="admin-empty">
          <?= icon('message-circle', 'icon-md') ?>
          <p>আপনি এখনও কোনো মন্তব্য করেননি। কোনো রিপোর্টের আলোচনায় যোগ দিলে আপনার মন্তব্য এখানে দেখা যাবে।</p>
          <a class="btn btn-primary btn-sm mt-2" href="issues.php"><?= icon('search', 'icon-sm') ?> আলোচনায় যোগ দিন</a>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="panel">
    <div class="panel-body row-between">
      <div>
        <h3 style="font-size:.92rem;margin-bottom:3px">আলোচনায় ভদ্র ও তথ্যপূর্ণ থাকুন</h3>
        <p class="muted-note">মন্তব্য প্রকাশ্যে দেখা যায়। ব্যক্তিগত তথ্য বা অপ্রমাণিত অভিযোগ না দেওয়ার অনুরোধ রইল।</p>
      </div>
      <a class="btn btn-outline btn-sm" href="faq.php"><?= icon('help-circle', 'icon-sm') ?> মন্তব্য নীতিমালা</a>
    </div>
  </div>

  </div>
</div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
