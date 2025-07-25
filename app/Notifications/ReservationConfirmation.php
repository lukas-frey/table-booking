<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Markdown;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationConfirmation extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected Reservation $reservation,
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line(Markdown::parse(__('Great news! Your table for **:guests** on **:date** at **:from - :to** is confirmed. We can’t wait to have you with us!', [
                'guests' => $this->reservation->guests,
                'date' => $this->reservation->starts_at->format('j. n. Y'),
                'from' => $this->reservation->starts_at->format('H:i'),
                'to' => $this->reservation->ends_at->format('H:i'),
            ])))
            ->line(__('If you need to make any changes, please cancel and create a new reservation.'))
            ->line(__('We look forward to serving you!'))
            ->action(__('View reservation'), route('reservations.index') . "#reservation-{$this->reservation->getKey()}")
        ;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
