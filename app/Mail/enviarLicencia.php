<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class enviarlicencia extends Mailable implements ShouldQueue
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

        /* if ($this->array['tipo'] == 1 || $this->array['tipo'] == 3) {
            return $this->view('emails.licenciaweb')
                ->from($this->array['from'], env('MAIL_FROM_NAME'))
                ->subject($this->array['subject']);


        } */

        if ($this->array['tipo'] == 1) {
            return $this->view('emails.licenciaQuito')
                ->from($this->array['from'], env('MAIL_FROM_NAME'))
                ->subject($this->array['subject']);
        } elseif ($this->array['tipo'] == 3) {

            return $this->view('emails.licenciaQuitoModificar')
                ->from($this->array['from'], env('MAIL_FROM_NAME'))
                ->subject($this->array['subject']);
        } elseif ($this->array['tipo'] == 2 || $this->array['tipo'] == 4) {

            return $this->view('emails.licenciapc')
                ->from($this->array['from'], env('MAIL_FROM_NAME'))
                ->subject($this->array['subject']);
        }

        if ($this->array['tipo'] == 5) {
            return $this->view('emails.envio_credenciales')
                ->from($this->array['from'], env('MAIL_FROM_NAME'))
                ->subject($this->array['subject'])->attach(public_path() . '/assets/media/Procedimiento Ingreso.pdf')->attach(public_path() . '/assets/media/Terminos y Condiciones.pdf');
        } elseif ($this->array['tipo'] == 9) {

            return $this->view('emails.envio_credenciales_facturito')
                ->from($this->array['from'], env('MAIL_FROM_NAME'))
                ->subject($this->array['subject']);
        }

        if ($this->array['tipo'] == 6) {
            return $this->view('emails.registro_demos')
                ->from($this->array['from'], env('MAIL_FROM_NAME'))
                ->subject($this->array['subject'])->attach(public_path() . '/assets/media/Procedimiento Ingreso.pdf')->attach(public_path() . '/assets/media/Terminos y Condiciones.pdf');
        } elseif ($this->array['tipo'] == 7) {

            return $this->view('emails.licenciaQuitoFacturito')
                ->from($this->array['from'], env('MAIL_FROM_NAME'))
                ->subject($this->array['subject']);
        } elseif ($this->array['tipo'] == 8) {

            return $this->view('emails.licenciaQuitoFacturitoModificar')
                ->from($this->array['from'], env('MAIL_FROM_NAME'))
                ->subject($this->array['subject']);
        } elseif ($this->array['tipo'] == 10 || $this->array['tipo'] == 11) {

            return $this->view('emails.licenciavps')
                ->from($this->array['from'], env('MAIL_FROM_NAME'))
                ->subject($this->array['subject']);
        }

        /* elseif ($this->array['tipo'] == 7 || $this->array['tipo'] == 8 ) {

            return $this->view('emails.licenciafacturito')
                ->from($this->array['from'], env('MAIL_FROM_NAME'))
                ->subject($this->array['subject']);
        } */
    }
}
