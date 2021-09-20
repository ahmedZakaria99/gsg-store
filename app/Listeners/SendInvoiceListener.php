<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Mail\OrderInvoice;
use App\Models\User;
use App\Notifications\OrderCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class SendInvoiceListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param OrderCreated $event
     * @return void
     */
    public function handle(OrderCreated $event)
    {
        $order = $event->order;

        // Sending notification to just one Known user

        $user = Auth::user();
        $user->notify(new OrderCreatedNotification($order));


        // Sending notification to one or more Known users
        /*
         * The first method
         * $users = User::all();
         * foreach ($users as $user) {
         * $user->notify(new OrderCreatedNotification($order));
         * }
         *
         * The second method
         * $users = User::all();
         * Notification::send($users,new OrderCreatedNotification($order));
         */

        // Sending notification to just one unKnown user or more than one

        Notification::route('mail', ['aaa@example.com', 'bbb@example.com'])
            ->notify(new OrderCreatedNotification($order));


        //Mail::to($order->billing_email)->send(new OrderInvoice($order));
    }
}
