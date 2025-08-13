<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RecuperacaoSenhaEmail extends Mailable
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
        $titulo = 'Redefinição de senha';

        return $this->view('emails.recuperacaoSenha')
                    ->from($this->data['mailFrom'], $this->data['nameFrom'])
                    ->replyTo($this->data['mailFrom'], $this->data['nameFrom'])
                    ->subject($titulo)
                    ->with([
                        'nome' => $this->data['nome'],
                        'dataHora' => $this->data['dataHora'],
                        'token' => $this->data['token'],
                        'url' => $this->data['url'],
                    ]);
    }
}
