<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use App\Models\User;
use App\Models\Configuration;

class ValidationEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($config, $user)
    {
        $this->config = $config;
        $this->user  = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
      return $this->from('noerply@example.com', $this->config->nameSite)
              ->subject($this->config->nameSite.' Confirmation')
              ->view('mails.validation')
              ->with([
                'config' => $this->config,
                'user' => $this->user,
                'name' => 'New '.$this->config->nameSite.' User',
                'link' => 'https://mailtrap.io/inboxes'
              ]);
    }
}
