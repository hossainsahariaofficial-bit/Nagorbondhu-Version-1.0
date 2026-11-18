<?php
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'যোগাযোগ — নগরবন্ধু';
$metaDescription = 'নগরবন্ধু সহায়তা দলের সঙ্গে যোগাযোগ করুন—প্রশ্ন, পরামর্শ, রিপোর্ট সংক্রান্ত সহায়তা বা অংশীদারিত্বের প্রস্তাব পাঠান।';
include __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <?= page_heading(
        'আমাদের সঙ্গে যোগাযোগ করুন',
        'প্রশ্ন, পরামর্শ, রিপোর্ট সংক্রান্ত সহায়তা বা অংশীদারিত্বের প্রস্তাব—যা-ই থাকুক, আমাদের লিখুন। সহায়তা দল সাধারণত এক কর্মদিবসের মধ্যে উত্তর দেয়।',
        ['হোম' => 'index.php', 'যোগাযোগ' => null]
    ) ?>
  </div>
</section>

<div class="container page-main">
  <div class="contact-grid">
    <div class="contact-info">
      <div class="contact-card">
        <span class="contact-icon"><?= icon('map-pin', 'icon-md') ?></span>
        <div>
          <h3>কার্যালয়ের ঠিকানা</h3>
          <p>১২/এ, রোড ৭, ধানমন্ডি<br>ঢাকা ১২০৫, বাংলাদেশ</p>
        </div>
      </div>

      <div class="contact-card">
        <span class="contact-icon"><?= icon('phone', 'icon-md') ?></span>
        <div>
          <h3>ফোন ও হটলাইন</h3>
          <p><a href="tel:+8809601234567">০৯৬১২-৩৪৫৬৭৮</a><br>জরুরি প্রয়োজনে: <a href="tel:999">৯৯৯</a></p>
        </div>
      </div>

      <div class="contact-card">
        <span class="contact-icon"><?= icon('mail', 'icon-md') ?></span>
        <div>
          <h3>ইমেইল</h3>
          <p><a href="mailto:hello@nagorbondhu.bd">hello@nagorbondhu.bd</a><br><a href="mailto:support@nagorbondhu.bd">support@nagorbondhu.bd</a></p>
        </div>
      </div>

      <div class="contact-card">
        <span class="contact-icon"><?= icon('clock', 'icon-md') ?></span>
        <div>
          <h3>সহায়তা সময়সূচি</h3>
          <div class="contact-hours">
            <div><span>শনিবার – বৃহস্পতিবার</span><b>সকাল ৯টা – সন্ধ্যা ৬টা</b></div>
            <div><span>শুক্রবার</span><b>বন্ধ</b></div>
            <div><span>জরুরি হটলাইন</span><b>২৪ ঘণ্টা</b></div>
          </div>
        </div>
      </div>

      <div class="contact-map" aria-label="কার্যালয়ের আনুমানিক অবস্থান">
        <?= nb_map_svg('map-bg') ?>
      </div>

      <div class="info-strip">
        <?= icon('info', 'icon-sm') ?>
        <span>ডেমো সাইটে পাঠানো বার্তা কোনো সার্ভারে সংরক্ষণ করা হয় না। বাস্তব সহায়তার জন্য ফোন বা ইমেইল ব্যবহার করুন।</span>
      </div>
    </div>

    <div class="contact-form-card">
      <h2>বার্তা পাঠান</h2>
      <p>নিচের ফর্মটি পূরণ করুন—প্রয়োজনে আপনার রিপোর্ট বা লেনদেন নম্বর উল্লেখ করুন।</p>

      <form method="post" action="contact.php" data-demo-submit
            data-confirm-title="বার্তাটি পাঠাবেন?"
            data-confirm-body="আপনার দেওয়া তথ্য একবার যাচাই করে নিন। ডেমোতে বার্তাটি কোথাও সংরক্ষণ করা হবে না।"
            data-confirm-yes="হ্যাঁ, পাঠান"
            data-success-message="ধন্যবাদ! আপনার বার্তাটি ডেমোতে গ্রহণ করা হয়েছে। বাস্তব সিস্টেমে সহায়তা দল এক কর্মদিবসের মধ্যে উত্তর দেবে।">
        <div class="form-grid">
          <div class="field">
            <label class="field-label" for="contactName">আপনার নাম <span class="req">*</span></label>
            <input class="input" id="contactName" name="name" type="text" required placeholder="পুরো নাম">
          </div>
          <div class="field">
            <label class="field-label" for="contactEmail">ইমেইল <span class="req">*</span></label>
            <input class="input" id="contactEmail" name="email" type="email" required placeholder="you@example.com">
          </div>
          <div class="field">
            <label class="field-label" for="contactPhone">মোবাইল নম্বর</label>
            <input class="input" id="contactPhone" name="phone" type="tel" data-rule="phone" placeholder="০১৭xxxxxxxx">
          </div>
          <div class="field">
            <label class="field-label" for="contactSubject">বিষয় <span class="req">*</span></label>
            <select class="select" id="contactSubject" name="subject" required>
              <option value="">বিষয় নির্বাচন করুন</option>
              <option>রিপোর্ট সংক্রান্ত সহায়তা</option>
              <option>অ্যাকাউন্ট ও প্রোফাইল</option>
              <option>অনুদান ও লেনদেন</option>
              <option>পরামর্শ বা অভিযোগ</option>
              <option>অংশীদারিত্বের প্রস্তাব</option>
              <option>অন্যান্য</option>
            </select>
          </div>
          <div class="field span-2">
            <label class="field-label" for="contactMessage">আপনার বার্তা <span class="req">*</span></label>
            <textarea class="input" id="contactMessage" name="message" rows="6" required minlength="15" placeholder="আপনার প্রশ্ন বা বিষয়টি সংক্ষেপে লিখুন…"></textarea>
            <span class="field-hint">সম্ভব হলে রিপোর্ট নম্বর বা লেনদেন নম্বর উল্লেখ করুন।</span>
          </div>
        </div>

        <label class="checkbox mt-2">
          <input type="checkbox" name="consent" required>
          <span><a href="privacy.php">গোপনীয়তা নীতি</a> অনুযায়ী আমার তথ্য ব্যবহারে সম্মত আছি।</span>
        </label>

        <button class="btn btn-primary btn-block mt-2" type="submit"><?= icon('send', 'icon-sm') ?> বার্তা পাঠান</button>
      </form>
    </div>
  </div>

  <div class="panel mt-3">
    <div class="panel-head">
      <div><h2>দ্রুত উত্তর খুঁজুন</h2><p>সহায়তা দলের সঙ্গে যোগাযোগের আগে সাধারণ প্রশ্নগুলো দেখে নিন</p></div>
      <a class="btn btn-outline btn-sm" href="faq.php"><?= icon('help-circle', 'icon-sm') ?> সাধারণ জিজ্ঞাসা</a>
    </div>
    <div class="panel-body">
      <div class="note-list">
        <div class="note-item"><?= icon('file-text', 'icon-xs') ?><span>রিপোর্টের অবস্থা ও অগ্রগতি দেখতে <a href="my-reports.php">আমার রিপোর্ট</a> পৃষ্ঠায় যান।</span></div>
        <div class="note-item"><?= icon('wallet', 'icon-xs') ?><span>অনুদান সংক্রান্ত তথ্যের জন্য <a href="donation-history.php">অনুদানের ইতিহাস</a> দেখুন।</span></div>
        <div class="note-item"><?= icon('shield', 'icon-xs') ?><span>জরুরি নিরাপত্তা ঝুঁকি হলে প্রথমে জাতীয় জরুরি সেবা <a href="tel:999">৯৯৯</a>-এ কল করুন।</span></div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
