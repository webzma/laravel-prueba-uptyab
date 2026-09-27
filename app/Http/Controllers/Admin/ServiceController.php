<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest;
use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        return view('admin.services.index', ['services' => Service::orderBy('name')->get()]);
    }

    public function create()
    {
        return view('admin.services.form', ['service' => new Service(['is_active' => true])]);
    }

    public function store(ServiceRequest $request)
    {
        Service::create($request->validated());

        return redirect()->route('admin.services.index')->with('ok', 'Servicio creado.');
    }

    public function edit(Service $service)
    {
        return view('admin.services.form', compact('service'));
    }

    public function update(ServiceRequest $request, Service $service)
    {
        $service->update($request->validated());

        return redirect()->route('admin.services.index')->with('ok', 'Servicio actualizado.');
    }

    // "Desactivar" en lugar de borrar, para no romper las citas existentes
    public function destroy(Service $service)
    {
        $service->update(['is_active' => ! $service->is_active]);

        return back()->with('ok', $service->is_active ? 'Servicio activado.' : 'Servicio desactivado.');
    }
}
