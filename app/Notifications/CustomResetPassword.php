<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordBase;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends ResetPasswordBase
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct($token)
    {
        parent::__construct($token);
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
        $url = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        return (new MailMessage)
            ->subject('Permintaan Atur Ulang Kata Sandi - AlwaysChat')
            ->greeting('Halo, ' . $notifiable->name)
            ->line('Kami menerima permintaan untuk mengatur ulang kata sandi akun AlwaysChat Anda.')
            ->action('Atur Ulang Kata Sandi', $url)
            ->line('Tautan reset kata sandi ini akan kadaluwarsa dalam ' . config('auth.passwords.' . config('auth.defaults.passwords') . '.expire') . ' menit.')
            ->line('Jika Anda tidak meminta atur ulang kata sandi, abaikan email ini.')
            ->salutation('Salam, Team AlwaysChat');
    }
}
