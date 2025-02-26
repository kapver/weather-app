<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeatherAlert extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Weather Notification',
        );
    }



    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $citiesText = $this->data->keys()->join(', ', ' and ');

        return new Content(
            view: 'emails.weather.alert', // Specify your view file
            with: [
                'citiesText' => $citiesText,
                'data' => $this->data,
                'recipientName' => $this->recipientName,
            ]
        );
    }

    /**
     * Get the email's subject.
     * @param $subject
     */
    public function subject($subject): string
    {
        $citiesText = $this->data->keys()->join(', ', ' and ');
        return "Weather notification for $citiesText";
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
