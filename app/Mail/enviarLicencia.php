<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EnviarLicencia extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $array;

    public function __construct($array)
    {
        $this->array = $array;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->array['tipo'] == 1 || $this->array['tipo'] == 3) {

            return $this->view('emails.licenciaWeb')
                ->from($this->array['from'], env('MAIL_FROM_NAME'))
                ->subject($this->array['subject']);
        } elseif ($this->array['tipo'] == 2 || $this->array['tipo'] == 4) {

            return $this->view('emails.licenciaPc')
                ->from($this->array['from'], env('MAIL_FROM_NAME'))
                ->subject($this->array['subject']);
        }

        if ($this->array['tipo'] == 5) {
            return $this->view('emails.envio_credenciales')
                ->from($this->array['from'], env('MAIL_FROM_NAME'))
                ->subject($this->array['subject'])->attach(public_path() . '/assets/media/Procedimiento Ingreso.pdf')->attach(public_path() . '/assets/media/Términos y Condiciones.pdf');
        }
    }
}
