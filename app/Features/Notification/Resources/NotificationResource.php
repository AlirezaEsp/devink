<?php

namespace App\Features\Notification\Resources;

use App\Features\Notification\Enums\NotificationType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = $this->data ?? [];
        $type = NotificationType::tryFrom((string) ($data['type'] ?? ''));

        return [
            'id'        => $this->id,
            'type'      => $type?->value ?? ($data['type'] ?? null),
            'group'     => $type?->group()->value,
            'title'     => $data['title'] ?? $type?->title(),
            'body'      => $data['body'] ?? null,
            'actor'     => $this->actor($data),
            'url'       => $data['payload']['url'] ?? null,
            'payload'   => $data['payload'] ?? [],
            'is_read'   => $this->read_at !== null,
            'read_at'   => $this->read_at?->toIso8601String(),
            'created_at'=> $this->created_at?->toIso8601String(),
        ];
    }

    /**
     * اعلان‌های اجتماعی یک فرستنده دارند، بقیه ندارند.
     */
    private function actor(array $data): ?array
    {
        $actor = $data['payload']['actor'] ?? null;

        if (! is_array($actor) || ! isset($actor['id'])) {
            return null;
        }

        return [
            'id'     => $actor['id'],
            'name'   => $actor['name'] ?? null,
            'avatar' => $actor['avatar'] ?? null,
        ];
    }
}
