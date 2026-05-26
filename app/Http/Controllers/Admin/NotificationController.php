<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BroadcastNotificationRequest;
use App\Models\Notification;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    use ApiResponse;

    /**
     * Broadcast a notification to all users or a specific set of users.
     *
     * POST /api/admin/notifications/broadcast
     */
    public function broadcast(BroadcastNotificationRequest $request): JsonResponse
    {
        $userIds = $request->user_ids;

        $query = User::where('is_active', true)->where('role', 'student');

        if ($userIds !== 'all') {
            $query->whereIn('id', $userIds);
        }

        $title = strip_tags($request->title);
        $body  = strip_tags($request->body);

        $recipients = $query->pluck('id');

        $notifications = $recipients->map(fn ($userId) => [
            'user_id'    => $userId,
            'title'      => $title,
            'body'       => $body,
            'type'       => 'broadcast',
            'data'       => null,
            'read_at'    => null,
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray();

        // Batch insert in chunks to avoid query size limits
        collect($notifications)->chunk(500)->each(fn ($chunk) => Notification::insert($chunk->toArray()));

        return $this->successResponse([
            'recipients_count' => count($notifications),
        ], 'Notification broadcast to ' . count($notifications) . ' users.');
    }
}
