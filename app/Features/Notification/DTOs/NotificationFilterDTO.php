<?php

namespace App\Features\Notification\DTOs;

use App\Features\Notification\Enums\NotificationGroup;
use App\Features\Notification\Enums\NotificationType;

final readonly class NotificationFilterDTO
{
    public function __construct(
        public int $perPage = 15,
        public ?bool $onlyUnread = null,
        public ?NotificationType $type = null,
        public ?NotificationGroup $group = null,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            perPage: min(max((int) ($data['per_page'] ?? 15), 1), 100),
            onlyUnread: array_key_exists('only_unread', $data)
                ? filter_var($data['only_unread'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
                : null,
            type: isset($data['type'])
                ? NotificationType::tryFrom((string) $data['type'])
                : null,
            group: isset($data['group'])
                ? NotificationGroup::tryFrom((string) $data['group'])
                : null,
        );
    }

    /** @return NotificationType[] */
    public function types(): array
    {
        if ($this->type !== null) {
            return [$this->type];
        }

        if ($this->group !== null) {
            return $this->group->types();
        }

        return [];
    }
}
