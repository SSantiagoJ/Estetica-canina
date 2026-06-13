<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class RecordatorioVacuna extends Notification
{
    use Queueable;

    protected $reserva;
    protected $usuario;
    protected $notificacionBD;

    public function __construct($reserva, $usuario = null, $notificacionBD = null)
    {
        $this->reserva = $reserva;
        $this->usuario = $usuario;
        $this->notificacionBD = $notificacionBD;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
{
    $nombre = $this->usuario->nombres ?? 'Cliente';
    $fecha = date('d/m/Y', strtotime($this->reserva->fecha ?? now()));

    // 🔥 Traer mensaje desde la BD POR TIPO
    $mensaje = $this->notificacionBD->mensaje
        ?? \App\Models\Notificacion::where('tipo', 'vacunas')->value('mensaje')
        ?? '';

    return (new MailMessage)
        ->subject('🐾 Recordatorio de Vacuna Antirrábica')
        ->view('emails.recordatorio_vacuna', [
            'nombre'  => $nombre,
            'fecha'   => $fecha,
            'mensaje' => $mensaje,   // <- 🔥 LO ESTAMOS ENVIANDO A LA VISTA
        ]);
}


}
