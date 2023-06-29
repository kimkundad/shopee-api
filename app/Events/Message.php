<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Message implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public ?int $id,
        public ?int $sender_id,
        public ?int $recived_id,
        public ?int $user_id,
        public ?int $shop_id,
        public ?string $message,
        public ?string $img_message,
        public ?int $status,
        public ?string $created_at,
        public ?string $updated_at,
        public ?string $avatar,
        public ?string $img_shop
    )
    {
    }

    public function broadcastOn()
    {
        return ['chat.'.$this->user_id.'.'.$this->shop_id];
    }

    public function broadcastAs()
    {
        return 'message.'.$this->user_id.'.'.$this->shop_id;
    }
}
