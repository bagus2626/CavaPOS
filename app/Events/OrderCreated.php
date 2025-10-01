<?php

namespace App\Events;

use App\Models\Transaction\BookingOrder; // <-- pakai model kamu
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public bool $afterCommit = true;
    public string $broadcastQueue = 'broadcasts';

    public function __construct(public BookingOrder $order) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("partner.{$this->order->partner_id}.orders");
    }

    public function broadcastAs(): string
    {
        return 'OrderCreated';
    }

    public function broadcastWith(): array
    {
        return [
            'id'         => $this->order->id,
            'code'       => $this->order->booking_order_code ?? null,
            'customer'   => $this->order->customer_name ?? null,
            'total'      => $this->order->total_order_value ?? null,
            'order_status' => $this->order->order_status ?? null,
            'partner_id' => $this->order->partner_id ?? null,
            'payment_method' => $this->order->payment_method ?? null,
            'created_at' => optional($this->order->created_at)->toDateTimeString(),
        ];
    }
}
