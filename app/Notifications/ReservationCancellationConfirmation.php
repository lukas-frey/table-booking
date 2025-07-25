<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Markdown;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationCancellationConfirmation extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected Reservation $reservation,
    )
    {
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
            ->line(Markdown::parse(__('Thank you for letting us know about your reservation cancellation for **:date** at **:from - :to**. We completely understand that plans can change!', [
                'date' => $this->reservation->starts_at->format('j. n. Y'),
                'from' => $this->reservation->starts_at->format('H:i'),
                'to' => $this->reservation->ends_at->format('H:i'),
            ])))
            ->line(__('If there’s anything we can do to assist you in the future, or if you’d like to reschedule, please don’t hesitate to reach out.'))
            ->line(__('We would love the opportunity to serve you again.'))
            ->action(__('Reschedule a table'), route('reservations.create'))
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
