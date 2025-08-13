<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EnvioNotificacaoRepasseEmail extends Mailable
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
        $titulo = 'Quero receber ofertas de repasse da Quero Auto';

        $email = $this->view('emails.envioNotificacaoRepasse')
            ->from($this->data['mailFrom'], $this->data['nameFrom'])
            ->replyTo($this->data['mailFrom'], $this->data['nameFrom'])
            ->subject($titulo)
            ->with([
                'nome' => $this->data['nome'],
                'telefone' => $this->data['telefone'],
                'estado' => $this->data['estado'],
            ]);

        // foreach ($this->data['files'] as $key => $file) {
        //     $email->attach($file);
        // }

        return $email;
    }
}
