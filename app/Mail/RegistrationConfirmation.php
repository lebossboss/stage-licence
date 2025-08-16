<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Inscription;

class RegistrationConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $inscription;
    public $password;

    /**
     * Create a new message instance.
     */
    public function __construct(Inscription $inscription, string $password)
    {
        $this->inscription = $inscription;
        $this->password = $password;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->view('mail.registration_confirmation', [
            'password' => $this->password,
        ]);
    }
}
