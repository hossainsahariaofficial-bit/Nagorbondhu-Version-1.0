# নগরবন্ধু — PHP + MySQL citizen issue platform

এটি Figma-র নগরবন্ধু নাগরিক-সমস্যা UI-এর বাংলা, responsive PHP implementation। Home, issues list, issue detail, report form, donation, login/register, about এবং admin dashboard অন্তর্ভুক্ত। এখন ব্যবহারকারী, রিপোর্ট, সমর্থন, মন্তব্য ও অনুদান MySQL ডেটাবেজে সংরক্ষিত হয়।

## XAMPP দিয়ে চালানো
1. `nagorbondhu-delivery-1` ফোল্ডারটি `C:\xampp\htdocs\`-এ রাখুন।
2. XAMPP Control Panel-এ **Apache** এবং **MySQL** Start করুন।
3. ব্রাউজারে খুলুন: `http://localhost/nagorbondhu-delivery-1/`

প্রথমবার সাইট খোলার সময় `includes/db.php` স্বয়ংক্রিয়ভাবে:
- `nagorbondhu` ডেটাবেজ তৈরি করে
- `database/schema.sql` থেকে সব টেবিল তৈরি করে
- দুটি ডেমো অ্যাকাউন্ট তৈরি করে

ইনস্টলেশনের অবস্থা দেখতে ও প্রয়োজনে আবার চালাতে: `http://localhost/nagorbondhu-delivery-1/install.php`

## ডেটাবেজ কনফিগারেশন
XAMPP-এর ডিফল্ট মান `includes/config.php`-এ আছে:

| সেটিং | মান |
| --- | --- |
| Host | `127.0.0.1` |
| Port | `3306` |
| Database | `nagorbondhu` |
| User | `root` |
| Password | ফাঁকা |

নিজের সার্ভারে আলাদা তথ্য লাগলে `includes/config.local.php` তৈরি করুন (এই ফাইলটি Git-এ রাখার দরকার নেই):

```php
<?php
return [
    'host'         => '127.0.0.1',
    'port'         => 3306,
    'name'         => 'nagorbondhu',
    'user'         => 'root',
    'pass'         => 'your-password',
    'auto_install' => true,
];
```

`config.local.php`-এর মান `config.php`-কে override করে।

> **পুরোনো ডেটাবেজ থাকলে:** আগের কোনো পরীক্ষামূলক সংস্করণের অসামঞ্জস্যপূর্ণ `nagorbondhu` ডেটাবেজ থাকলে phpMyAdmin থেকে সেটি ড্রপ করুন, তারপর সাইট বা `install.php` রিফ্রেশ করুন। সঠিক স্কিমা ও ডেমো অ্যাকাউন্ট আবার তৈরি হবে।

## ডেমো অ্যাকাউন্ট
| ভূমিকা | ইমেইল | মোবাইল | পাসওয়ার্ড |
| --- | --- | --- | --- |
| প্রশাসক | `admin@nagorbondhu.gov.bd` | `01711000112` | `admin123` |
| নাগরিক | `sadia.afrin@example.com` | `01711234567` | `123456` |

নাগরিক হিসেবে ইমেইল বা মোবাইল — যেকোনোটি দিয়ে লগইন করা যায়। নিরাপত্তার জন্য বাস্তব সার্ভারে ব্যবহারের আগে পাসওয়ার্ড বদলে নিন।

## কী কী ডেটাবেজে সংরক্ষিত হয়
- **নিবন্ধন ও লগইন** — `users` টেবিল, `password_hash()`/`password_verify()`, সেশন রিজেনারেশন
- **সমস্যা রিপোর্ট** — `issues`, `issue_images` (সর্বোচ্চ ৪টি ছবি), `issue_updates`
- **সমর্থন** — `supports` (একজন ব্যবহারকারী একটি রিপোর্টে একবার)
- **মন্তব্য** — `comments`; নিজের মন্তব্য `my-comments.php` থেকে মুছে ফেলা যায়
- **অনুদান** — `donations` (প্রাথমিক অবস্থা `pending`, প্রশাসন যাচাই করলে `paid`)
- **প্রশাসনিক কার্যক্রম** — রিপোর্ট যাচাই, স্ট্যাটাস ও অগ্রগতি আপডেট

ডেমো রিপোর্ট (আইডি ১–১২) ও ডেমো অনুদান কার্যক্রম (আইডি ১–৪) UI-তে দেখানোর জন্য রাখা হয়েছে। নতুন রিপোর্টের আইডি ১০০১ থেকে শুরু, তাই সংঘর্ষ হয় না। ডেটাবেজ বন্ধ থাকলে সাইটের ডেমো পেজগুলো আগের মতোই চলবে, আর ডেটাবেজ-নির্ভর কাজগুলো বাংলায় পরিষ্কার ত্রুটি বার্তা দেখাবে।

## ছবি আপলোড
রিপোর্টের ছবি `uploads/issues/` ফোল্ডারে JPG, PNG বা WEBP হিসেবে (প্রতিটি সর্বোচ্চ ৫ MB) সংরক্ষিত হয়। `.htaccess` ফাইলটি এই ফোল্ডারে স্ক্রিপ্ট চালানো ও ডিরেক্টরি লিস্টিং বন্ধ রাখে। Apache-এ `uploads/issues/` ফোল্ডারে লেখার অনুমতি থাকতে হবে।

## PHP built-in server (ঐচ্ছিক)
MySQL চালু থাকলে project folder-এ terminal খুলে চালান:
```bash
php -S localhost:8000
```
তারপর `http://localhost:8000` খুলুন।

## পেজসমূহ
### নাগরিক অংশ
- `index.php` — হোমপেজ
- `issues.php` — সমস্যা তালিকা ও সার্চ/ফিল্টার
- `issue.php?id=1` — সমস্যা বিস্তারিত, সমর্থন, মন্তব্য ও অগ্রগতি
- `map.php` — মানচিত্রভিত্তিক সমস্যা ব্রাউজ
- `report.php` — ৪ ধাপের রিপোর্ট ফর্ম (লগইন আবশ্যক)
- `donate.php`, `donation.php?id=1` — অনুদান কার্যক্রম ও অনুদান ফর্ম (লগইন আবশ্যক)
- `about.php`, `faq.php`, `contact.php`, `privacy.php`, `terms.php` — তথ্যপূর্ণ পেজ
- `login.php`, `register.php` — অ্যাকাউন্ট লগইন ও নিবন্ধন
- `dashboard.php`, `profile.php`, `my-reports.php`, `my-comments.php`, `notifications.php`, `supported.php`, `donation-history.php` — নাগরিক ড্যাশবোর্ড

### প্রশাসনিক অংশ
- `admin-login.php` — প্রশাসনিক প্রবেশ
- `admin.php` — প্রশাসনিক ড্যাশবোর্ড
- `admin-issues.php`, `admin-verify.php`, `admin-progress.php` — রিপোর্ট ব্যবস্থাপনা
- `admin-citizens.php`, `admin-comments.php` — নাগরিক ও মন্তব্য ব্যবস্থাপনা
- `admin-donations.php`, `admin-campaigns.php`, `admin-authorities.php` — অনুদান ও সংস্থা
- `admin-reports.php`, `admin-notifications.php`, `admin-settings.php`, `admin-profile.php` — বিশ্লেষণ ও সিস্টেম

## নিরাপত্তা
- সব ফর্মে CSRF টোকেন যাচাই
- পাসওয়ার্ড `password_hash()` দিয়ে সংরক্ষিত, কখনোই plain text নয়
- PDO prepared statement দিয়ে সব কুয়েরি
- আপলোড করা ফাইলের MIME ধরন ও আকার যাচাই
- লগইন করা ভূমিকা অনুযায়ী পেজে প্রবেশ নিয়ন্ত্রণ

## Figma fidelity note
প্রাপ্ত design context-এ নগরবন্ধুর Bengali UI, green/cream palette, rounded cards, three issue cards, progress/fundraiser section এবং admin dashboard structure দেখা গেছে। এই project সেই design language ও visible community section-কে অনুসরণ করে responsive pages বানায়। Figma থেকে original image assetগুলো এই runtime-এ download করা যায়নি, তাই `assets/img/*.svg`-এ editable local illustrations রাখা হয়েছে; একেবারে pixel-perfect মিলের জন্য original Figma image fills/icons দিয়ে সেগুলো প্রতিস্থাপন করুন।
