<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use NotificationChannels\Telegram\TelegramMessage;

class WeatherNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private readonly array|Collection $data
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return $notifiable->notificationChannels ?? ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(User $notifiable): MailMessage
    {
        $citiesText = $this->data->keys()->join(', ', ' and ');

        $message = new MailMessage();
        $message->subject = "Weather notification for $citiesText";
        $message->greeting = "Hi, $notifiable->name!";

        $message->line("You have alerts for $citiesText.");

        $this->data->each(function ($item, $city) use ($message) {
            $message->line('-- ' . $city);
            $message->lineIf(isset($item['pop']), "Precipitation of {$item['type']}: {$item['pop_text']}.");
            $message->lineIf(isset($item['uvi']), "UV Index: {$item['uvi_text']}.");
        });

        $message->action('Manage Alert Conditions', route('settings.show'));
        $message->line('Thank you for using our application!');

        return $message;
    }

    public function toTelegram(User $notifiable): TelegramMessage
    {
        $chat_id = data_get($notifiable->settings, 'settings.weather.telegram_chat_id');
        $content = view('emails.weather.telegram')->with('cities', $this->data)->render();

        return TelegramMessage::create($content)
            ->to($chat_id)
            ->options([
                'parse_mode' => 'HTML', // Enables Telegram to parse HTML
                'disable_web_page_preview' => true,
            ]);
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
