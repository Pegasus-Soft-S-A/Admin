<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Revendedores;
use App\Rules\IdentificacionRevendedor;
use App\Rules\ValidarCelular;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables as DataTables;

class revendedoresController extends Controller
{
    public function revendedoresDistribuidor($distribuidor, $tipo)
    {
        $revendedor = Revendedores::where('sis_revendedores.sis_distribuidoresid', $distribuidor)
            ->where('sis_revendedores.tipo', $tipo)
            ->get();
        return with(["revendedor" => $revendedor]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = Revendedores::select('sis_revendedores.sis_revendedoresid', 'sis_revendedores.identificacion', 'sis_revendedores.tipoidentificacion', 'sis_revendedores.razonsocial', 'sis_revendedores.correo', 'sis_revendedores.celular', 'sis_revendedores.direccion', 'sis_revendedores.tipo', 'sis_distribuidores.razonsocial as distribuidor')
                ->join('sis_distribuidores', 'sis_distribuidores.sis_distribuidoresid', 'sis_revendedores.sis_distribuidoresid');

            return DataTables::of($data)
                ->editColumn('identificacion', function ($revendedor) {
                    return '<a class="text-primary" href="' . route('revendedores.editar', $revendedor->sis_revendedoresid) . '">' . $revendedor->identificacion . ' </a>';
                })
                ->editColumn('action', function ($revendedor) {
                    return '<a class="btn btn-icon btn-light btn-hover-success btn-sm mr-2" href="' . route('revendedores.editar', $revendedor->sis_revendedoresid) . '"  title="Editar"> <i class="la la-edit"></i> </a>' .
                        '<a class="btn btn-icon btn-light btn-hover-danger btn-sm mr-2 confirm-delete" href="javascript:void(0)" data-href="' . route('revendedores.eliminar', $revendedor->sis_revendedoresid) . '" title="Eliminar"> <i class="la la-trash"></i> </a>';
                })
                ->editColumn('tipo', function ($revendedor) {
                    if ($revendedor->tipo == 1) {
                        return 'Contador';
                    } else {
                        return 'Vendedor';
                    }
                })
                ->rawColumns(['action', 'identificacion', 'tipo'])
                ->make(true);
        }
        return view('admin.revendedores.index');
    }

    public function crear()
    {
        $revendedor = new Revendedores();
        return view('admin.revendedores.crear', compact('revendedor'));
    }

    public function guardar(Request $request)
    {
        $request->validate(
            [
                'identificacion' => ['required', new IdentificacionRevendedor],
                'razonsocial' => 'required',
                'celular' => ['required', 'size:10', new ValidarCelular],
                'direccion' => 'required',
                'correo' => 'required',
                'sis_distribuidoresid' => 'required',
            ],
            [
                'identificacion.required' => 'Ingrese su cédula o RUC ',
                'identificacion.unique' => 'Su cédula o RUC ya se encuentra registrado',
                'razonsocial.required' => 'Ingrese una Razón Social',
                'celular.required' => 'Ingrese un Número Celular',
                'celular.size' => 'Ingrese 10 dígitos',
                'direccion.required' => 'Ingrese una Dirección',
                'correo.required' => 'Ingrese un Correo',
                'correo.email' => 'Ingrese un Correo válido',
                'sis_distribuidoresid.required' => 'Escoja un Distribuidor',


            ],
        );

        $contadorIdentificacion = strlen($request->identificacion);
        $request['tipoidentificacion'] = $contadorIdentificacion == 10 ? 'C' : 'R';
        $request['fechacreacion'] = now();
        $request['usuariocreacion'] = Auth::user()->nombres;

        $revendedor = Revendedores::create($request->all());

        LogService::crear('Revendedores', $revendedor);

        flash('Revendedor creado correctamente')->success();
        return redirect()->route('revendedores.editar', $revendedor->sis_revendedoresid);
    }

    public function editar(Revendedores $revendedor)
    {
        return view('admin.revendedores.editar', compact('revendedor'));
    }

    public function actualizar(Revendedores $revendedor, Request $request)
    {

        $request->validate(
            [
                'identificacion' => ['required', 'unique:sis_revendedores,identificacion,' . $revendedor->sis_revendedoresid . ',sis_revendedoresid'],
                'razonsocial' => 'required',
                'celular' => ['required', 'size:10', new ValidarCelular],
                'direccion' => 'required',
                'correo' => 'required',
                'sis_distribuidoresid' => 'required',
            ],
            [
                'identificacion.required' => 'Ingrese su cédula o RUC ',
                'identificacion.unique' => 'Su cédula o RUC ya se encuentra registrado',
                'razonsocial.required' => 'Ingrese una Razón Social',
                'celular.required' => 'Ingrese un Número Celular',
                'celular.size' => 'Ingrese 10 dígitos',
                'direccion.required' => 'Ingrese una Dirección',
                'correo.required' => 'Ingrese un Correo',
                'correo.email' => 'Ingrese un Correo válido',
                'sis_distribuidoresid.required' => 'Escoja un Distribuidor',

            ],
        );

        $contadorIdentificacion = strlen($request->identificacion);
        $request['tipoidentificacion'] = $contadorIdentificacion == 10 ? 'C' : 'R';
        $request['fechamodificacion'] = now();
        $request['usuariomodificacion'] = Auth::user()->nombres;
        $revendedor->update($request->all());

        LogService::modificar('Revendedores', $revendedor);

        flash('Actualizado Correctamente')->success();
        return back();
    }

    public function eliminar(Revendedores $revendedor)
    {

        $revendedor->delete();

        LogService::eliminar('Revendedores', $revendedor);

        flash("Eliminado Correctamente")->success();
        return back();
    }

    //API
    public function vendedores_consulta(Request $request)
    {
        $identificacionIngresada = substr($request->identificacion, 0, 10);
        $vendedor = Revendedores::whereIn('identificacion', [$identificacionIngresada, $request->identificacion, $request->identificacion . '001'])->first();

        return json_encode(["vendedor" => [$vendedor]]);
    }
}
