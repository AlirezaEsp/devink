<?php

namespace App\Features\Notification\Enums;

enum NotificationType: string
{
    // محتوا
    case ArticlePublished   = 'article_published'; //مقاله پابلیش یا منتشر شد
    case ArticleSubmitted   = 'article_drafted'; //مقاله جهت تایید ارسال شد
    case ArticleRejected    = 'article_rejected'; //مقاله رد شد
    case ArticleAccepted    = 'article_accepted'; //مقاله تایید شد
    case ArticleEdited      = 'article_edited'; //مقاله تغییر یافت
    case ArticleFlagged     = 'article_flagged'; //مقاله فلگ شد (مثلا این مقاله رو توی سرچ اول بیاره)

        // اجتماعی
    case NewComment         = 'new_comment'; //نظر جدید
    case CommentReply       = 'comment_reply'; //پاسخ به نظر
    case CommentMention     = 'comment_mention'; //کاربر در نظر منشن شده
    case NewFollower        = 'new_follower'; //دنبال کننده جدید
    case ArticleLiked    = 'article_liked'; //واکنش داده شدن به مقاله
    case NewArticleFromFollowing = 'new_article_from_following'; //شخصی که فالو کردن پست جدید گذاشته

        // ادیتوریال
    case ReviewRequested    = 'review_requested'; //درخواست بازبینی مقاله
    case ContentReported    = 'content_reported'; //گزارش شدن محتوا مثل نظر مقاله و ...

        // حساب
    case PasswordChanged    = 'password_changed'; //پسورد تغییر یافت
    case NewDeviceLogin     = 'new_device_login'; //دستگاه جدید با اکنت شما وارد شد
    case AccountRestricted  = 'account_restricted'; //حساب شما بسته یا محدود شد

    public function group(): NotificationGroup
    {
        return match ($this) {
            self::ArticlePublished,
            self::ArticleSubmitted,
            self::ArticleRejected,
            self::ArticleAccepted,
            self::ArticleEdited,
            self::ArticleFlagged => NotificationGroup::Content,

            self::NewComment,
            self::CommentReply,
            self::CommentMention,
            self::NewFollower,
            self::ArticleLiked,
            self::NewArticleFromFollowing => NotificationGroup::Social,

            self::ReviewRequested,
            self::ContentReported => NotificationGroup::Editorial,

            self::PasswordChanged,
            self::NewDeviceLogin,
            self::AccountRestricted => NotificationGroup::Account,
        };
    }

    public function title(): string
    {
        return match ($this) {
            self::ArticlePublished        => 'مقاله منتشر شد',
            self::ArticleSubmitted        => 'مقاله برای بازبینی ارسال شد',
            self::ArticleRejected         => 'مقاله رد شد',
            self::ArticleAccepted         => 'مقاله تأیید شد',
            self::ArticleEdited           => 'مقاله ویرایش شد',
            self::ArticleFlagged          => 'مقاله گزارش شد',
            self::NewComment              => 'نظر جدید',
            self::CommentReply            => 'پاسخ به نظر',
            self::CommentMention          => 'منشن شدید',
            self::NewFollower             => 'دنبال‌کننده جدید',
            self::ArticleLiked          => 'واکنش جدید به مقاله',
            self::NewArticleFromFollowing => 'مقاله جدید از دنبال‌شده‌ها',
            self::ReviewRequested         => 'درخواست بازبینی',
            self::ContentReported         => 'گزارش محتوا',
            self::PasswordChanged         => 'تغییر رمز عبور',
            self::NewDeviceLogin          => 'ورود از دستگاه جدید',
            self::AccountRestricted       => 'محدودیت حساب',
        };
    }

    /** اعلان‌هایی که کاربر اجازهٔ خاموش‌کردنشان را ندارد */
    public function isMandatory(): bool
    {
        return match ($this) {
            self::PasswordChanged,
            self::NewDeviceLogin,
            self::AccountRestricted => true,
            default => false,
        };
    }

    public function defaultChannels(): array
    {
        return match ($this) {
            self::NewFollower,
            self::ArticleLiked,
            self::NewArticleFromFollowing => ['database'],

            self::PasswordChanged,
            self::NewDeviceLogin,
            self::AccountRestricted,
            self::ArticleRejected => ['database', 'mail'],

            default => ['database'],
        };
    }
}
