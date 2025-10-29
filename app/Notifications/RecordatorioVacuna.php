<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class RecordatorioVacuna extends Notification
{
    use Queueable;

    public function __construct($reserva)
    {
        $this->reserva = $reserva;
    }

    public function via($notifiable)
    {
        return ['mail']; // puedes agregar 'database' si usas notificaciones internas
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Recordatorio de Vacuna Antirrábica')
            ->greeting('Hola ' . $notifiable->persona->nombres . ' 🐾')
            ->line('Tu mascota recibió la Vacuna Antirrábica hace casi un año.')
            ->line('Te recomendamos agendar una nueva cita para mantenerla protegida.')
            ->action('Agendar Cita', url('127.0.0.1:8000/reservas/seleccion-mascota'))
            ->line('Gracias por confiar en PetSpa 🐶💉');
    }
}
