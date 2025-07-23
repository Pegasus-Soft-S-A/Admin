<?php

namespace App\Http\Controllers\Licencias;

use App\Mail\enviarlicencia;
use App\Models\Clientes;
use App\Models\Licenciasvps;
use App\Services\EmailLicenciaService;
use App\Services\LicenciaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class LicenciasVpsController extends LicenciasBaseController
{
    //Mostrar formulario de creación de licencia VPS
    public function crear(Clientes $cliente)
    {
        $licencia = new Licenciasvps();
        $licencia->numerocontrato = $this->generarContrato();

        return view('admin.licencias.Vps.crear', compact('licencia', 'cliente'));
    }

    //Guardar nueva licencia VPS
    public function guardar(Request $request)
    {
        $this->validarDatosVps($request);
        $this->prepararDatosGuardar($request);

        try {
            $licencia = $this->ejecutarCreacionConTransaccion(
                fn() => Licenciasvps::create($request->all()),
                'Licencia Vps',
                $request->all()
            );

            $cliente = $this->obtenerDatosClienteEmail($request['sis_clientesid']);

            LicenciaService::procesar('nuevo', $licencia, $cliente, $request->all());

            flash('Guardado Correctamente')->success();
            return redirect()->route('licencias.Vps.editar', [$request['sis_clientesid'], $licencia->sis_licenciasid]);

        } catch (\Exception $e) {
            flash('Error al crear la licencia: ' . $e->getMessage())->error();
            return redirect()->back()->withInput();
        }
    }

    //Mostrar formulario de edición
    public function editar(Clientes $cliente, Licenciasvps $licencia)
    {
        $licencia->fecha_corte_proveedor = date("d-m-Y", strtotime($licencia->fecha_corte_proveedor));
        $licencia->fecha_corte_cliente = date("d-m-Y", strtotime($licencia->fecha_corte_cliente));

        return view('admin.licencias.Vps.editar', compact('cliente', 'licencia'));
    }

    //Actualizar licencia VPS existente
    public function actualizar(Request $request, Licenciasvps $licencia)
    {
        $this->validarDatosVps($request);
        $this->prepararDatosActualizar($request);

        try {
            $licenciaActualizada = $this->ejecutarActualizacionConTransaccion(function () use ($request, $licencia) {
                $licencia->update($request->all());
                return $licencia->fresh();
            }, 'Licencia Vps');

            $cliente = $this->obtenerDatosClienteEmail($request['sis_clientesid']);

            LicenciaService::procesar("modificado", $licencia, $cliente, $request->all());

            flash('Actualizado Correctamente')->success();

        } catch (\Exception $e) {
            flash('Error al actualizar la licencia: ' . $e->getMessage())->error();
        }

        return back();
    }

    //Eliminar licencia VPS
    public function eliminar(Licenciasvps $licencia)
    {
        $esAjax = request()->ajax() || request()->wantsJson();

        try {
            // Verificar dependencias
            $adicionales = $this->verificarDependencias($licencia->numerocontrato);
            if ($adicionales > 0) {
                $mensaje = "No se puede eliminar la licencia porque tiene {$adicionales} recurso(s) adicional(es) asociado(s).";
                return $this->manejarRespuesta($esAjax, false, $mensaje);
            }

            // Guardar datos para el log antes de eliminar
            $licenciaData = $licencia->toArray();

            $this->ejecutarEliminacionConTransaccion(function () use ($licencia) {
                $this->limpiarDependencias($licencia->numerocontrato);
                $licencia->delete();
                return true;
            }, 'Licencia VPS', $licenciaData);

            return $this->manejarRespuesta($esAjax, true, 'Licencia eliminada correctamente');

        } catch (\Exception $e) {
            $mensaje = 'Error al eliminar la licencia: ' . $e->getMessage();
            return $this->manejarRespuesta($esAjax, false, $mensaje);
        }
    }

    // =====================================
    // MÉTODOS PRIVADOS
    // =====================================

    //Validaciones específicas para licencias VPS
    private function validarDatosVps(Request $request): void
    {
        $request->validate([
            'usuario' => ['required'],
            'clave' => ['required'],
            'ip' => ['required'],
        ], [
            'usuario.required' => 'Ingrese un Usuario',
            'clave.required' => 'Ingrese una Clave',
            'ip.required' => 'Ingrese una IP',
        ]);
    }

    //Preparar datos para guardar
    private function prepararDatosGuardar(Request $request): void
    {
        $request->merge([
            'fechacreacion' => now(),
            'usuariocreacion' => Auth::user()->nombres,
            'fecha_corte_proveedor' => date('Ymd', strtotime($request->fecha_corte_proveedor)),
            'fecha_corte_cliente' => date('Ymd', strtotime($request->fecha_corte_cliente)),
            'tipo_licencia' => 3,
        ]);
    }

    //Preparar datos para actualizar
    private function prepararDatosActualizar(Request $request): void
    {
        $request->merge([
            'fecha_corte_proveedor' => date("Ymd", strtotime($request->fecha_corte_proveedor)),
            'fecha_corte_cliente' => date("Ymd", strtotime($request->fecha_corte_cliente)),
            'fechamodificacion' => now(),
            'usuariomodificacion' => Auth::user()->nombres,
        ]);
    }

}
