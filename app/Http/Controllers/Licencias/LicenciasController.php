<?php

namespace App\Http\Controllers\Licencias;

use App\Models\Clientes;
use App\Models\Licencias;
use App\Models\Licenciasvps;
use App\Models\Servidores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class LicenciasController extends LicenciasBaseController
{
    //Vista principal de licencias de un cliente (DataTables)
    public function index(Request $request, Clientes $cliente)
    {
        if ($request->ajax()) {
            $servidores = Servidores::where('estado', 1)->get();
            $web = [];

            // ✅ OPTIMIZACIÓN PARA MODO LOCAL
            if (config('sistema.local_mode', false)) {
                // En modo local, consultar directamente el modelo (como PC y VPS)
                $web = \App\Models\Licenciasweb::select('sis_licenciasid', 'numerocontrato', 'tipo_licencia', 'fechacaduca', 'sis_clientesid', 'sis_servidoresid')
                    ->where('sis_clientesid', $cliente->sis_clientesid)
                    ->get()
                    ->toArray();
            } else {
                // En modo producción, consultar cada servidor (lógica original)
                foreach ($servidores as $servidor) {
                    try {
                        $resultado = $this->externalServerService->queryLicense($servidor, [
                            'sis_clientesid' => $cliente->sis_clientesid
                        ]);

                        if ($resultado['success'] && isset($resultado['licenses'])) {
                            $web = array_merge($web, $resultado['licenses']);
                        }
                    } catch (\Exception $e) {
                        continue; // Continúa con el siguiente servidor
                    }
                }
            }

            // Consultar licencias PC
            $data = Licencias::select('sis_licenciasid', 'numerocontrato', 'tipo_licencia', 'fechacaduca', 'sis_clientesid')
                ->selectRaw('NULL as sis_servidoresid') // PC no usa servidor específico
                ->where('sis_clientesid', $cliente->sis_clientesid)
                ->get();

            // Consultar licencias VPS
            $data2 = Licenciasvps::select('sis_licenciasid', 'numerocontrato', 'tipo_licencia', 'fecha_corte_cliente as fechacaduca', 'sis_clientesid')
                ->selectRaw('NULL as sis_servidoresid') // VPS no usa servidor específico
                ->where('sis_clientesid', $cliente->sis_clientesid)
                ->get();

            // Unir todos los datos
            $unir = $web ?
                array_merge($web, $data->toArray(), $data2->toArray()) :
                array_merge($data->toArray(), $data2->toArray());

            return DataTables::of($unir)
                ->editColumn('numerocontrato', function ($data) {
                    return $this->generarEnlaceContrato($data);
                })
                ->editColumn('action', function ($data) {
                    return $this->generarBotonesAccion($data);
                })
                ->editColumn('tipo_licencia', function ($data) {
                    return $this->obtenerNombreTipoLicencia($data);
                })
                ->editColumn('fechacaduca', function ($data) {
                    return date('d-m-Y', strtotime($data['fechacaduca']));
                })
                ->rawColumns(['action', 'numerocontrato', 'tipo_licencia'])
                ->make(true);
        }
    }

    // =====================================
    // MÉTODOS PRIVADOS PARA INDEX
    // =====================================

    private function generarEnlaceContrato(array $data): string
    {
        switch ($data['tipo_licencia']) {
            case '1': // Web
                if (isset($data['sis_servidoresid']) && isset($data['sis_licenciasid'])) {
                    $ruta = route('licencias.Web.editar', [$data['sis_clientesid'], $data['sis_servidoresid'], $data['sis_licenciasid']]);
                } else {
                    $ruta = '#';
                }
                break;
            case '2': // PC
                if (isset($data['sis_licenciasid'])) {
                    $ruta = route('licencias.Pc.editar', [$data['sis_clientesid'], $data['sis_licenciasid']]);
                } else {
                    $ruta = '#';
                }
                break;
            case '3': // VPS
                if (isset($data['sis_licenciasid'])) {
                    $ruta = route('licencias.Vps.editar', [$data['sis_clientesid'], $data['sis_licenciasid']]);
                } else {
                    $ruta = '#';
                }
                break;
            default:
                $ruta = '#';
        }

        return '<a class="text-primary" href="' . $ruta . '">' . $data['numerocontrato'] . '</a>';
    }

    private function generarBotonesAccion(array $data): string
    {
        $esAdmin = Auth::user()->tipo == 1;
        $tipoLicencia = $this->obtenerNombreTipoLicencia($data);
        $botones = [];

        switch ($data['tipo_licencia']) {
            case '1': // Web/Facturito
                $botones[] = sprintf(
                    '<a class="btn btn-icon btn-light btn-hover-primary btn-sm mr-1 actividad"
                       href="javascript:void(0)"
                       data-href="%s"
                       title="Ver Actividad"
                       data-toggle="tooltip">
                       <i class="fas fa-eye"></i>
                    </a>',
                    route('licencias.Web.actividad', [$data['sis_servidoresid'], $data['sis_licenciasid']])
                );

                $botones[] = sprintf(
                    '<a class="btn btn-icon btn-light btn-hover-success btn-sm mr-1"
                       href="%s"
                       title="Editar Licencia"
                       data-toggle="tooltip">
                       <i class="fas fa-edit"></i>
                    </a>',
                    route('licencias.Web.editar', [$data['sis_clientesid'], $data['sis_servidoresid'], $data['sis_licenciasid']])
                );

                if ($esAdmin) {
                    $botones[] = sprintf(
                        '<a class="btn btn-icon btn-light btn-hover-danger btn-sm mr-1 btn-eliminar-licencia"
                           href="javascript:void(0)"
                           data-href="%s"
                           data-licencia-contrato="%s"
                           data-licencia-tipo="%s"
                           title="Eliminar Licencia"
                           data-toggle="tooltip">
                           <i class="fas fa-trash"></i>
                        </a>',
                        route('licencias.Web.eliminar', [$data['sis_servidoresid'], $data['sis_licenciasid']]),
                        htmlspecialchars($data['numerocontrato']),
                        htmlspecialchars($tipoLicencia)
                    );
                }
                break;

            case '2': // PC
                $botones[] = sprintf(
                    '<a class="btn btn-icon btn-light btn-hover-success btn-sm mr-1"
                       href="%s"
                       title="Editar Licencia"
                       data-toggle="tooltip">
                       <i class="fas fa-edit"></i>
                    </a>',
                    route('licencias.Pc.editar', [$data['sis_clientesid'], $data['sis_licenciasid']])
                );

                if ($esAdmin) {
                    $botones[] = sprintf(
                        '<a class="btn btn-icon btn-light btn-hover-danger btn-sm mr-1 btn-eliminar-licencia"
                           href="javascript:void(0)"
                           data-href="%s"
                           data-licencia-contrato="%s"
                           data-licencia-tipo="%s"
                           title="Eliminar Licencia"
                           data-toggle="tooltip">
                           <i class="fas fa-trash"></i>
                        </a>',
                        route('licencias.Pc.eliminar', $data['sis_licenciasid']),
                        htmlspecialchars($data['numerocontrato']),
                        htmlspecialchars($tipoLicencia)
                    );
                }
                break;

            case '3': // VPS
                $botones[] = sprintf(
                    '<a class="btn btn-icon btn-light btn-hover-success btn-sm mr-1"
                       href="%s"
                       title="Editar Licencia"
                       data-toggle="tooltip">
                       <i class="fas fa-edit"></i>
                    </a>',
                    route('licencias.Vps.editar', [$data['sis_clientesid'], $data['sis_licenciasid']])
                );

                if ($esAdmin) {
                    $botones[] = sprintf(
                        '<a class="btn btn-icon btn-light btn-hover-danger btn-sm mr-1 btn-eliminar-licencia"
                           href="javascript:void(0)"
                           data-href="%s"
                           data-licencia-contrato="%s"
                           data-licencia-tipo="%s"
                           title="Eliminar Licencia"
                           data-toggle="tooltip">
                           <i class="fas fa-trash"></i>
                        </a>',
                        route('licencias.Vps.eliminar', $data['sis_licenciasid']),
                        htmlspecialchars($data['numerocontrato']),
                        htmlspecialchars($tipoLicencia)
                    );
                }
                break;

            default:
                return '<span class="text-muted font-size-sm">Sin acciones</span>';
        }

        return '<div class="btn-group" role="group" aria-label="Acciones de licencia">' . implode('', $botones) . '</div>';
    }

    private function obtenerNombreTipoLicencia(array $data): string
    {
        switch ($data['tipo_licencia']) {
            case '1':
                return (isset($data['producto']) && $data['producto'] == 12) ? 'Facturito' : 'Perseo Web';
            case '2':
                return 'Perseo PC';
            case '3':
                return 'Perseo VPS';
            default:
                return 'Tipo Desconocido';
        }
    }
}
