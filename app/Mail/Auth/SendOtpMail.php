<?php

namespace App\Mail\Auth;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendOtpMail extends Mailable implements ShouldQueue
{
  use Queueable, SerializesModels;

  public User $user;

  /**
   * Create a new message instance.
   */
  public function __construct(User $user)
  {
    $this->user = $user;
  }

  /**
   * Email Subject
   */
  public function envelope(): Envelope
  {
    return new Envelope(
      subject: 'Your OTP Verification Code',
    );
  }

  /**
   * Email Body View + Data
   */
  public function content(): Content
  {
    return new Content(
      view: 'emails.otp',
      with: [
        'otp' => $this->user->otp,
        'name' => $this->user->name,
      ]
    );
  }

  /**
   * Attachments (none)
   */
  public function attachments(): array
  {
    return [];
  }
}
