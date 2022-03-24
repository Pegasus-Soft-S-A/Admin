<?php

namespace App\Http\Controllers;

use App\Models\Usuarios;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables as DataTables;

class usuariosController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = Usuarios::select('sis_distribuidores_usuarios.sis_distribuidores_usuariosid', 'sis_distribuidores_usuarios.identificacion', 'sis_distribuidores_usuarios.tipoidentificacion', 'sis_distribuidores_usuarios.nombres', 'sis_distribuidores_usuarios.correo', 'sis_distribuidores_usuarios.estado', 'sis_distribuidores_usuarios.tipo', 'sis_distribuidores.razonsocial as distribuidor')->join('sis_distribuidores', 'sis_distribuidores.sis_distribuidoresid', 'sis_distribuidores_usuarios.sis_distribuidoresid');

            //Filtrar por tipo fecha

            return DataTables::of($data)

                ->editColumn('identificacion', function ($usuario) {
                    return '<a class="text-primary" href="' . route('usuarios.editar', $usuario->sis_distribuidores_usuariosid) . '">' . $usuario->identificacion . ' </a>';
                })

                ->editColumn('action', function ($usuario) {
                    return '<a class="btn btn-icon btn-light btn-hover-success btn-sm mr-2" href="' . route('usuarios.editar', $usuario->sis_distribuidores_usuariosid) . '"  title="Editar"> <i class="la la-edit"></i> </a>' .
                        '<a class="btn btn-icon btn-light btn-hover-danger btn-sm mr-2 confirm-delete" href="javascript:void(0)" data-href="' . route('usuarios.eliminar', $usuario->sis_distribuidores_usuariosid) . '" title="Eliminar"> <i class="la la-trash"></i> </a>';
                })


                ->editColumn('estado', function ($usuario) {
                    if ($usuario->estado == 1) {
                        return '<span class="text-success">Activo</span>';
                    } else {
                        return '<span class="text-danger">Inactivo</span>';
                    }
                })
                ->editColumn('tipo', function ($usuario) {
                    if ($usuario->tipo == 1) {
                        return 'Admin';
                    } elseif ($usuario->tipo == 2) {
                        return 'Soporte';
                    } elseif ($usuario->tipo == 3) {
                        return 'Ventas';
                    }
                })
                ->filterColumn('estado', function ($query, $keyword) {
                    $estado = 2;

                    $terminoBuscar = strtolower($keyword);
                    $cadenaActivo = strtolower('Activo');
                    $cadenaInactivo = strtolower('Inactivo');
                    $vefificarActivo = strpos($cadenaActivo, $terminoBuscar);
                    $vefificarInactivo = strpos($cadenaInactivo, $terminoBuscar);


                    if ($vefificarActivo === 0) {
                        $estado = 1;
                    }
                    if ($vefificarInactivo === 0) {
                        $estado = 0;
                    }


                    $query->where('estado', $estado);
                })
                ->rawColumns(['action', 'identificacion', 'estado'])
                ->make(true);
        }
        return view('admin.usuarios.index');
    }

    public function crear()
    {
        $usuarios = new Usuarios();
        return view('admin.usuarios.crear', compact('usuarios'));
    }

    public function guardar(Request $request)
    {

        $request->validate(
            [
                'identificacion' => ['required', 'unique:sis_distribuidores_usuarios'],
                'nombres' => 'required',
                'contrasena' => 'required',
                'correo' => 'required',
                'sis_distribuidoresid' => 'required',
                'tipo' => 'required',
            ],
            [
                'identificacion.required' => 'Ingrese su cédula o RUC ',
                'identificacion.unique' => 'Su cédula o RUC ya se encuentra registrado',
                'nombres.required' => 'Ingrese Nombres',
                'contrasena.required' => 'Ingrese una Dirección',
                'correo.required' => 'Ingrese un Correo',
                'correo.email' => 'Ingrese un Correo válido',
                'sis_distribuidoresid.required' => 'Escoja un Distribuidor',
                'tipo.required' => 'Escoja un Tipo',


            ],
        );

        $usuarios = $request->all();
        $contadorIdentificacion = strlen($request->identificacion);
        $usuarios['tipoidentificacion'] = $contadorIdentificacion == 10 ? 'C' : 'R';
        $usuarios['estado'] = $request->estado == null ? 0 : 1;
        $usuarios['contrasena'] = encrypt_openssl($request->contrasena, "Perseo1232*");
        $usuarios['fechacreacion'] = now();
        $usuarios['usuariocreacion'] = 'admin';

        $usuarios =   Usuarios::create($usuarios);
        flash('Usuario creado correctamente')->success();
        return view('admin.usuarios.editar', compact('usuarios'));
    }

    public function editar(Usuarios $usuarios)
    {
        return view('admin.usuarios.editar', compact('usuarios'));
    }
    public function actualizar(Usuarios $usuarios, Request $request)
    {
        $request->validate(
            [
                'identificacion' => ['required', 'unique:sis_distribuidores_usuarios,identificacion,' . $usuarios->sis_distribuidores_usuariosid . ',sis_distribuidores_usuariosid'],
                'nombres' => 'required',
                'correo' => 'required',
                'sis_distribuidoresid' => 'required',
                'tipo' => 'required',
            ],
            [
                'identificacion.required' => 'Ingrese su cédula o RUC ',
                'identificacion.unique' => 'Su cédula o RUC ya se encuentra registrado',
                'nombres.required' => 'Ingrese Nombres',
                'contrasena.required' => 'Ingrese una Dirección',
                'correo.required' => 'Ingrese un Correo',
                'correo.email' => 'Ingrese un Correo válido',
                'sis_distribuidoresid.required' => 'Escoja un Distribuidor',
                'tipo.required' => 'Escoja un Tipo',


            ],
        );

        $contadorIdentificacion = strlen($request->identificacion);
        $request['tipoidentificacion'] = $contadorIdentificacion == 10 ? 'C' : 'R';

        if ($request->contrasena != null) {
            $request['contrasena'] = encrypt_openssl($request->contrasena, "Perseo1232*");
        } else {
            $request['contrasena'] = $usuarios->contrasena;
        }

        $request['estado'] = $request->estado == null ? 0 : 1;

        $request['fechamodificacion'] = now();
        $request['usuariomodificacion'] = 'admin';

        $usuarios->update($request->all());

        flash('Actualizado Correctamente')->success();
        return back();
    }

    public function eliminar(Usuarios $usuarios)
    {
        $usuarios->delete();
        flash("Eliminado Correctamente")->success();
        return back();
    }
}
