<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConfirmacaoContaEmail extends Mailable
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
        $titulo = 'Confirmação da conta Quero Auto';
        $mailFrom = env('MAIL_FROM_ADDRESS');
        $nameFrom = env('MAIL_FROM_NAME');

        return $this->view('emails.confirmacaoConta')
                    ->from($mailFrom, $nameFrom)
                    ->replyTo($mailFrom, $nameFrom)
                    ->subject($titulo)
                    ->with([
                        'nome' => $this->data['nome'],
                        'dataHora' => $this->data['dataHora'],
                        'url' => $this->data['url'],
                    ]);
    }
}
