<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class RecordatorioReserva extends Notification
{
    use Queueable;

    protected $reserva;
    protected $usuario;

    public function __construct($reserva, $usuario = null)
    {
        $this->reserva = $reserva;
        $this->usuario = $usuario;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $nombre = $this->usuario->nombres ?? 'Cliente';
        $hora = date('H:i', strtotime($this->reserva->hora));

        return (new MailMessage)
            ->subject('🐾 Recordatorio de tu cita en PetSpa')
            ->view('emails.recordatorio_reserva', [
                'nombre' => $nombre,
                'hora' => $hora,
                'fecha' => $this->reserva->fecha,
            ]);
    }
}
