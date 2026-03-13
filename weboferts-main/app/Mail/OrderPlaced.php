<?php

namespace App\Mail;

use App\Models\Pedido;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use PDF;

class OrderPlaced extends Mailable
{
    use Queueable, SerializesModels;

    public $pedido;

    public function __construct(Pedido $pedido)
    {
        $this->pedido = $pedido;
    }

    public function build()
    {
        $pdf = PDF::loadView('pdf.invoice', ['pedido' => $this->pedido]);

        return $this->subject('Confirmación de Pedido #' . str_pad($this->pedido->id, 6, '0', STR_PAD_LEFT))
                    ->view('mails.order_placed')
                    ->attachData($pdf->output(), 'factura.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}
