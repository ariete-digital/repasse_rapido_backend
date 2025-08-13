<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConfirmacaoTrocaSenhaEmail extends Mailable
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
        $titulo = 'Redefinição de senha concluída com sucesso!';

        return $this->view('emails.confirmacaoTrocaSenha')
                    ->from($this->data['mailFrom'], $this->data['nameFrom'])
                    ->replyTo($this->data['mailFrom'], $this->data['nameFrom'])
                    ->subject($titulo)
                    ->with([
                        'nome' => $this->data['nome'],
                        'dataHora' => $this->data['dataHora'],
                    ]);
    }
}
