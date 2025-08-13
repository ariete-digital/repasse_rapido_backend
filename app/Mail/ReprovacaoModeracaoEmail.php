<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReprovacaoModeracaoEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $titulo = 'Anúncio reprovado pela moderação';

        return $this->view('emails.reprovacaoModeracao')
                    ->from($this->data['mailFrom'], $this->data['nameFrom'])
                    ->replyTo($this->data['mailFrom'], $this->data['nameFrom'])
                    ->subject($titulo)
                    ->with([
                        'nome' => $this->data['nome'],
                        'obs' => $this->data['obs'],
                    ]);
    }
}
