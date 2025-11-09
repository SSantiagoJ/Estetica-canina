<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PromocionNueva extends Notification
{
    use Queueable;

    protected $promo;     // array con nombre, descripcion, imagen_ref, fecha_inicio/fin, descuento
    protected $servicios; // array de servicios [nombre_servicio, costo, imagen_referencial]

    public function __construct(array $promo, array $servicios = [])
    {
        $this->promo = $promo;
        $this->servicios = $servicios;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🐾 Nueva promoción en PetSpa: ' . $this->promo['nombre_promocion'])
            ->view('emails.promocion_nueva', [
                'promo'     => $this->promo,
                'servicios' => $this->servicios,
            ]);
    }
}
