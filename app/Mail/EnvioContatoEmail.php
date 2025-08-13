<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EnvioContatoEmail extends Mailable
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
        $titulo = 'Contato Quero Auto';

        $email = $this->view('emails.envioContato')
            ->from($this->data['mailFrom'], $this->data['nameFrom'])
            ->replyTo($this->data['mailFrom'], $this->data['nameFrom'])
            ->subject($titulo)
            ->with([
                'nome' => $this->data['nome'],
                'email' => $this->data['email'],
                'telefone' => $this->data['telefone'],
                'assunto' => $this->data['assunto'],
                'mensagem' => $this->data['mensagem'],
            ]);

        // foreach ($this->data['files'] as $key => $file) {
        //     $email->attach($file);
        // }

        return $email;
    }
}
