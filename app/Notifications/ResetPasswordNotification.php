<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseResetPassword
{
    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        // Choose a route name depending on the notifiable's model so the generated
        // link points to the correct student/admin reset route.
        $routeName = 'password.reset';
        try {
            if ($notifiable instanceof \App\Models\StudentAccount) {
                $routeName = 'student.password.reset';
            } elseif ($notifiable instanceof \App\Models\Admin) {
                $routeName = 'admin.password.reset';
            }
        } catch (\Throwable $e) {
            // Fall back to generic route if models can't be resolved for any reason
            $routeName = 'password.reset';
        }

        $resetUrl = url(route($routeName, [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Password Reset Request')
            ->view('emails.reset_password', [
                'user' => $notifiable,
                'resetUrl' => $resetUrl,
            ]);
    }
}
