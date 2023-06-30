<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Mail\WeeklyAttendanceSummaryMail;

class WeeklyAttendanceSummaryNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $startDate = date_format(date_create($this->details->start_date), 'l, F j, Y');
        $endDate = date_format(date_create($this->details->end_date), 'l, F j, Y');
        $config = (object)[
            'subject' => $this->details->subject,
            'greetings' => $this->details->subject,
            'departments' => $this->details->departments,
            'date_range' => $startDate . ' - ' . $endDate,
        ];
        return (new WeeklyAttendanceSummaryMail($config))->to($notifiable->email);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
