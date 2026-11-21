<?php

namespace App\Features\Notification\Controllers;

use App\Features\Notification\Actions\DeleteNotification;
use App\Features\Notification\Actions\GetUnreadCount;
use App\Features\Notification\Actions\ListNotifications;
use App\Features\Notification\Actions\MarkAllNotificationsAsRead;
use App\Features\Notification\Actions\MarkNotificationAsRead;
use App\Features\Notification\DTOs\NotificationFilterDTO;
use App\Features\Notification\Resources\NotificationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NotificationController
{
    /**
     * Index
     *
     * لیست اعلان‌های کاربر واردشده به‌همراه صفحه‌بندی
     *
     * @param Request $request
     * @param ListNotifications $action
     *
     * @return AnonymousResourceCollection
     */
    public function index(Request $request, ListNotifications $action): AnonymousResourceCollection
    {
        // get user
        $user = $request->user();

        // build filter from query string
        $filter = NotificationFilterDTO::fromRequest($request->all());

        $notifications = $action->handle($user, $filter);

        // return notifications as collection
        return NotificationResource::collection($notifications);
    }

    /**
     * UnreadCount
     *
     * تعداد اعلان‌های خوانده‌نشده برای نمایش badge
     *
     * @param Request $request
     * @param GetUnreadCount $action
     *
     * @return JsonResponse
     */
    public function unreadCount(Request $request, GetUnreadCount $action): JsonResponse
    {
        // count unread notifications of current user
        $count = $action->handle($request->user());

        // return count
        return response()->json([
            'count' => $count,
        ]);
    }

    /**
     * MarkAsRead
     *
     * علامت‌گذاری یک اعلان به‌عنوان خوانده‌شده
     *
     * @param Request $request
     * @param string $id notification uuid from url
     * @param MarkNotificationAsRead $action
     *
     * @return JsonResponse
     */
    public function markAsRead(Request $request, string $id, MarkNotificationAsRead $action): JsonResponse
    {
        // mark notification as read
        $notification = $action->handle($request->user(), $id);

        // return successful message along the notification info
        return response()->json([
            'message' => 'Notification marked as read successfully.',
            'data' => new NotificationResource($notification),
        ]);
    }

    /**
     * MarkAllAsRead
     *
     * علامت‌گذاری همهٔ اعلان‌های خوانده‌نشده
     *
     * @param Request $request
     * @param MarkAllNotificationsAsRead $action
     *
     * @return JsonResponse
     */
    public function markAllAsRead(Request $request, MarkAllNotificationsAsRead $action): JsonResponse
    {
        // update all unread notifications
        $updated = $action->handle($request->user());

        // return successful message along the updated rows count
        return response()->json([
            'message' => 'All notifications marked as read successfully.',
            'updated' => $updated,
        ]);
    }

    /**
     * Destroy
     *
     * حذف یک اعلان
     *
     * @param Request $request
     * @param string $id notification uuid from url
     * @param DeleteNotification $action
     *
     * @return JsonResponse
     */
    public function destroy(Request $request, string $id, DeleteNotification $action): JsonResponse
    {
        // delete notification
        $action->handle($request->user(), $id);

        // return successful message
        return response()->json([
            'message' => 'Notification deleted successfully.',
        ]);
    }
}
