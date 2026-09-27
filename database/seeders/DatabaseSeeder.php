<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Administrador (contraseña: password)
        User::factory()->admin()->create([
            'name' => 'Administrador',
            'email' => 'admin@barberia.test',
        ]);

        // 3 barberos (contraseña: password)
        $barbers = collect([
            ['name' => 'Carlos Barbero', 'email' => 'carlos@barberia.test'],
            ['name' => 'Luis Barbero', 'email' => 'luis@barberia.test'],
            ['name' => 'Pedro Barbero', 'email' => 'pedro@barberia.test'],
        ])->map(fn ($b) => User::factory()->barber()->create($b));

        // 5 servicios
        $services = collect([
            ['name' => 'Corte clásico', 'description' => 'Corte a tijera y máquina', 'price' => 10, 'duration' => 30],
            ['name' => 'Barba', 'description' => 'Perfilado y arreglo de barba', 'price' => 7, 'duration' => 30],
            ['name' => 'Combo', 'description' => 'Corte + barba', 'price' => 15, 'duration' => 60],
            ['name' => 'Corte degradado', 'description' => 'Fade / degradado', 'price' => 12, 'duration' => 45],
            ['name' => 'Lavado y peinado', 'description' => 'Lavado con champú y peinado', 'price' => 5, 'duration' => 30],
        ])->map(fn ($s) => Service::create($s + ['is_active' => true]));

        // Cliente de prueba (contraseña: password)
        $client = User::factory()->create([
            'name' => 'Cliente Prueba',
            'email' => 'cliente@barberia.test',
        ]);

        // Algunos clientes extra con citas para hoy (para ver la agenda)
        $clients = User::factory(5)->create()->push($client);
        foreach (['10:00', '11:00', '15:00', '17:00'] as $i => $time) {
            Appointment::create([
                'user_id' => $clients[$i]->id,
                'barber_id' => $barbers[$i % 3]->id,
                'service_id' => $services[$i]->id,
                'date' => now()->format('Y-m-d'),
                'time' => $time,
                'status' => 'scheduled',
            ]);
        }

        // Una cita futura y una pasada para el cliente de prueba
        Appointment::create([
            'user_id' => $client->id,
            'barber_id' => $barbers[0]->id,
            'service_id' => $services[2]->id,
            'date' => now()->addDays(2)->format('Y-m-d'),
            'time' => '12:00',
            'status' => 'scheduled',
        ]);
        Appointment::create([
            'user_id' => $client->id,
            'barber_id' => $barbers[1]->id,
            'service_id' => $services[0]->id,
            'date' => now()->subDays(5)->format('Y-m-d'),
            'time' => '09:00',
            'status' => 'completed',
        ]);
    }
}
