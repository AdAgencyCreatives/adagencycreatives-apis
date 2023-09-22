<?php

namespace App\Mail\Order;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConfirmationAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function envelope()
    {
        return new Envelope(
            subject: sprintf('%s placed a new order #%d on your store', $this->data['username'], $this->data['order_no']),
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.order.alert-admin',
        );
    }

    public function attachments()
    {
        return [];
    }
}
