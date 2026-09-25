<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class CustomVerifyEmail extends VerifyEmailBase
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verifikasi Alamat Email Anda - AlwaysChat')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Selamat datang di AlwaysChat! Silahkan klik tombol untuk memverifikasi alamat email anda.')
            ->action('Verifikasi Email Saya', $verificationUrl)
            ->line('Jika anda tidak merasa membuat akun di AlwaysChat, tidak ada tindakan lebih lanjut yang di perlukan.')
            ->salutation('Salam hangat, Team AlwaysChat');
    }
}
