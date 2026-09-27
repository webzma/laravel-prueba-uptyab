<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BarberShopTest extends TestCase
{
    use RefreshDatabase;

    public function test_cliente_se_registra(): void
    {
        $this->post('/register', [
            'name' => 'Juan', 'email' => 'juan@test.com', 'phone' => '0414-1234567',
            'password' => 'secret123', 'password_confirmation' => 'secret123',
        ])->assertRedirect('/');

        $this->assertDatabaseHas('users', ['email' => 'juan@test.com', 'role' => 'client']);
    }

    public function test_no_permite_doble_reserva_mismo_barbero_fecha_hora(): void
    {
        $barber = User::factory()->barber()->create();
        $service = Service::factory()->create();
        $data = ['service_id' => $service->id, 'barber_id' => $barber->id,
            'date' => now()->addDay()->format('Y-m-d'), 'time' => '10:00'];

        $this->actingAs(User::factory()->create())->post('/reservar', $data)
            ->assertRedirect(route('appointments.index'));
        $this->actingAs(User::factory()->create())->post('/reservar', $data)
            ->assertSessionHasErrors('time');

        $this->assertEquals(1, Appointment::count());
    }

    public function test_valida_fecha_pasada_y_horario_laboral(): void
    {
        $barber = User::factory()->barber()->create();
        $service = Service::factory()->create();

        $this->actingAs(User::factory()->create())->post('/reservar', [
            'service_id' => $service->id, 'barber_id' => $barber->id,
            'date' => now()->subDay()->format('Y-m-d'), 'time' => '20:00',
        ])->assertSessionHasErrors(['date', 'time']);
    }

    public function test_cancelacion_solo_con_mas_de_2_horas(): void
    {
        $client = User::factory()->create();
        $pronto = Appointment::factory()->create([
            'user_id' => $client->id,
            'date' => now()->addHour()->format('Y-m-d'), 'time' => now()->addHour()->format('H:00'),
        ]);
        $lejos = Appointment::factory()->create(['user_id' => $client->id, 'date' => now()->addDays(3)->format('Y-m-d')]);

        $this->actingAs($client)->patch(route('appointments.cancel', $pronto))->assertSessionHasErrors('cancel');
        $this->actingAs($client)->patch(route('appointments.cancel', $lejos))->assertSessionHasNoErrors();

        $this->assertEquals('scheduled', $pronto->fresh()->status);
        $this->assertEquals('cancelled', $lejos->fresh()->status);
    }

    public function test_middleware_de_roles(): void
    {
        $this->actingAs(User::factory()->create())->get('/panel/agenda')->assertForbidden();
        $this->actingAs(User::factory()->create())->get('/panel/services')->assertForbidden();
        $this->actingAs(User::factory()->barber()->create())->get('/panel/agenda')->assertOk();
        $this->actingAs(User::factory()->admin()->create())->get('/panel/services')->assertOk();
    }

    public function test_crud_servicios(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->post('/panel/services', [
            'name' => 'Tinte', 'description' => 'x', 'price' => 20, 'duration' => 60, 'is_active' => 1,
        ])->assertRedirect();
        $service = Service::first();
        $this->actingAs($admin)->put("/panel/services/{$service->id}", [
            'name' => 'Tinte full', 'price' => 25, 'duration' => 60,
        ])->assertRedirect();
        $this->assertEquals('Tinte full', $service->fresh()->name);
        $this->assertFalse($service->fresh()->is_active);
    }

    public function test_paginas_renderizan(): void
    {
        $this->seed();
        $admin = User::where('role', 'admin')->first();
        $client = User::where('email', 'cliente@barberia.test')->first();

        $this->get('/')->assertOk()->assertSee('Corte clásico');
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
        $this->actingAs($client)->get('/reservar')->assertOk();
        $this->actingAs($client)->get('/mis-citas')->assertOk()->assertSee('Combo');
        $this->actingAs($admin)->get('/panel/agenda')->assertOk();
        $this->actingAs($admin)->get('/panel/services/create')->assertOk();
    }
}
