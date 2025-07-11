<?php

namespace App\Http\Controllers\Licencias;

use App\Mail\enviarlicencia;
use App\Models\Agrupados;
use App\Models\Clientes;
use App\Models\Licenciasweb;
use App\Models\Servidores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class LicenciasWebController extends LicenciasBaseController
{
    public function crear(Clientes $cliente)
    {
        $licencia = new Licenciasweb();
        $licencia->numerocontrato = $this->generarContrato();
        $licencia->numerosucursales = 0;
        $licencia->empresas = 1;
        $licencia->sis_distribuidoresid = Auth::user()->sis_distribuidoresid;

        $modulos = [
            'nomina' => false, 'activos' => false, 'produccion' => false,
            'restaurantes' => false, 'talleres' => false, 'garantias' => false, 'ecommerce' => false,
        ];

        $agrupados = Agrupados::select('sis_agrupados.sis_agrupadosid', 'sis_clientes.nombres', 'sis_agrupados.codigo')
            ->join('sis_clientes', 'sis_clientes.sis_clientesid', 'sis_agrupados.sis_clientesid')
            ->get();

        $servidores = Servidores::where('estado', 1)->get();

        return view('admin.licencias.Web.crear', compact('cliente', 'licencia', 'modulos', 'servidores', 'agrupados'));
    }

    public function guardar(Request $request)
    {
        $this->prepararDatosGuardar($request);

        try {
            $servidor = Servidores::where('sis_servidoresid', $request->sis_servidoresid)->firstOrFail();

            $resultado = $this->externalServerService->createLicense($servidor, $request->all());
            if (!$resultado['success']) {
                throw new \Exception($resultado['error']);
            }

            $request['sis_licenciasid'] = $resultado['license_id'];

            $licencia = $this->ejecutarCreacionConTransaccion(
                fn() => Licenciasweb::create($request->all()),
                'Licencia Web',
                $request->all()
            );

            $this->enviarEmailWeb($licencia, $request, 'Nuevo Registro Licencia Web');

            flash('Guardado Correctamente')->success();
            return redirect()->route('licencias.Web.editar', [$request['sis_clientesid'], $request->sis_servidoresid, $resultado['license_id']]);

        } catch (\Exception $e) {
            flash('Error al crear la licencia: ' . $e->getMessage())->error();
            return redirect()->back()->withInput();
        }
    }

    public function editar(Clientes $cliente, $servidorid, $licenciaid)
    {
        try {
            $servidor = Servidores::where('sis_servidoresid', $servidorid)->firstOrFail();

            if (!$this->externalServerService->checkServerAvailability($servidor)) {
                flash('El servidor no está disponible en este momento. Intente más tarde.')->warning();
                return back();
            }

            $resultado = $this->externalServerService->queryLicense($servidor, ['sis_licenciasid' => $licenciaid]);

            if (!$resultado['success'] || !isset($resultado['licenses'][0])) {
                flash('No se encontró la licencia en el servidor.')->error();
                return back();
            }

            $licenciaData = $resultado['licenses'][0];
            $licenciaData['fechainicia'] = date("d-m-Y", strtotime($licenciaData['fechainicia']));
            $licenciaData['fechacaduca'] = date("d-m-Y", strtotime($licenciaData['fechacaduca']));
            $licenciaData['fechacreacion'] = date("Y-m-d H:i:s", strtotime($licenciaData['fechacreacion']));

            if ($licenciaData['fechamodificacion'] != "0000-00-00T00:00:00.000") {
                $licenciaData['fechamodificacion'] = date("Y-m-d H:i:s", strtotime($licenciaData['fechamodificacion']));
            } else {
                $licenciaData['fechamodificacion'] = "";
            }

            $modulos = simplexml_load_string($licenciaData['modulos']);
            $licencia = json_decode(json_encode($licenciaData));

            $agrupados = Agrupados::select('sis_agrupados.sis_agrupadosid', 'sis_clientes.nombres', 'sis_agrupados.codigo')
                ->join('sis_clientes', 'sis_clientes.sis_clientesid', 'sis_agrupados.sis_clientesid')
                ->get();

            $servidores = Servidores::all();

            return view('admin.licencias.Web.editar', compact('cliente', 'licencia', 'modulos', 'servidores', 'agrupados'));

        } catch (\Exception $e) {
            flash('Error al consultar la licencia: ' . $e->getMessage())->error();
            return back();
        }
    }

    public function actualizar(Request $request, $servidorid, $licenciaid)
    {
        try {
            $servidor = Servidores::where('sis_servidoresid', $servidorid)->firstOrFail();

            $consultaResult = $this->externalServerService->queryLicense($servidor, ['sis_licenciasid' => $licenciaid]);
            if (!$consultaResult['success'] || empty($consultaResult['licenses'])) {
                throw new \Exception('Licencia no encontrada en el servidor externo');
            }

            $licenciaActual = json_decode(json_encode($consultaResult['licenses'][0]));
            $datosOperacion = $this->procesarTipoOperacion($request, $licenciaActual);

            $this->prepararDatosActualizar($request, $licenciaid, $datosOperacion['parametros']);

            $updateResult = $this->externalServerService->updateLicense($servidor, $request->all());
            if (!$updateResult['success']) {
                throw new \Exception($updateResult['error']);
            }

            $licenciaweb = $this->ejecutarActualizacionConTransaccion(function () use ($request, $licenciaid, $servidorid) {
                $licencia = Licenciasweb::where('sis_licenciasid', $licenciaid)
                    ->where('sis_servidoresid', $servidorid)
                    ->where('sis_clientesid', $request['sis_clientesid'])
                    ->firstOrFail();
                $licencia->update($request->all());
                return $licencia->fresh();
            }, 'Licencia Web');

            $this->enviarEmailWeb($licenciaweb, $request, $datosOperacion['asunto']);

            flash('Actualizado Correctamente')->success();

        } catch (\Exception $e) {
            flash('Error al actualizar la licencia: ' . $e->getMessage())->error();
        }

        return back();
    }

    public function eliminar($servidorid, $licenciaid)
    {
        $esAjax = request()->ajax() || request()->wantsJson();

        try {
            $servidor = Servidores::where('sis_servidoresid', $servidorid)->firstOrFail();

            $consultaResult = $this->externalServerService->queryLicense($servidor, ['sis_licenciasid' => $licenciaid]);
            if (!$consultaResult['success'] || empty($consultaResult['licenses'])) {
                $mensaje = 'Licencia no encontrada.';
                return $this->manejarRespuesta($esAjax, false, $mensaje);
            }

            $licenciaData = $consultaResult['licenses'][0];

            $adicionales = $this->verificarDependencias($licenciaData['numerocontrato']);
            if ($adicionales > 0) {
                $mensaje = "No se puede eliminar. Tiene {$adicionales} recurso(s) adicional(es) asociado(s).";
                return $this->manejarRespuesta($esAjax, false, $mensaje);
            }

            $deleteResult = $this->externalServerService->deleteLicense($servidor, $licenciaid);
            if (!$deleteResult['success']) {
                throw new \Exception($deleteResult['error']);
            }

            $this->ejecutarEliminacionConTransaccion(function () use ($licenciaid, $servidorid, $licenciaData) {
                $licenciaweb = Licenciasweb::where('sis_licenciasid', $licenciaid)
                    ->where('sis_servidoresid', $servidorid)
                    ->where('sis_clientesid', $licenciaData['sis_clientesid'])
                    ->first();

                if ($licenciaweb) {
                    $licenciaweb->delete();
                }

                $this->limpiarDependencias($licenciaData['numerocontrato']);
                return true;
            }, 'Licencia Web', $licenciaData);

            return $this->manejarRespuesta($esAjax, true, 'Licencia eliminada correctamente');

        } catch (\Exception $e) {
            $mensaje = 'Error al eliminar la licencia: ' . $e->getMessage();
            return $this->manejarRespuesta($esAjax, false, $mensaje);
        }
    }

    // =====================================
    // MÉTODOS PRIVADOS
    // =====================================

    private function prepararDatosGuardar(Request $request): void
    {
        $fechaActual = now();
        $usuarioActual = Auth::user();

        $request->merge([
            'fechacreacion' => $fechaActual,
            'usuariocreacion' => $usuarioActual->nombres,
            'fechainicia' => date('Ymd', strtotime($request->fechainicia)),
            'fechacaduca' => date('Ymd', strtotime($request->fechacaduca)),
            'fechaultimopago' => date('Ymd', strtotime($request->fechainicia)),
            'tipo_licencia' => 1,
            'Identificador' => $request->numerocontrato,
            'parametros_json' => json_encode($this->obtenerParametrosProducto($request->producto, $request->periodo ?? null)),
            'modulos' => $this->generarModulosXml($request),
        ]);

        // Eliminar campos temporales
        $camposAEliminar = ['nomina', 'activos', 'produccion', 'restaurantes', 'talleres', 'garantias', 'ecommerce', 'tipo'];
        foreach ($camposAEliminar as $campo) {
            unset($request[$campo]);
        }
    }

    private function prepararDatosActualizar(Request $request, int $licenciaid, $parametros): void
    {
        $fechaActual = now();
        $usuarioActual = Auth::user();

        $request->merge([
            'fechamodificacion' => $fechaActual->format('YmdHis'),
            'usuariomodificacion' => $usuarioActual->nombres,
            'fechainicia' => date('Ymd', strtotime($request->fechainicia)),
            'fechacaduca' => date('Ymd', strtotime($request->fechacaduca)),
            'parametros_json' => json_encode($parametros),
            'sis_licenciasid' => $licenciaid,
            'modulos' => $this->generarModulosXml($request),
        ]);

        // Eliminar campos temporales
        $camposAEliminar = ['nomina', 'activos', 'produccion', 'restaurantes', 'talleres', 'garantias', 'ecommerce', 'tipo'];
        foreach ($camposAEliminar as $campo) {
            unset($request[$campo]);
        }
    }

    private function procesarTipoOperacion(Request $request, $licenciaActual): array
    {
        $parametrosJson = json_decode($licenciaActual->parametros_json);
        $asunto = 'Modificar Licencia Web';

        switch ($request->tipo) {
            case 'mes':
                $request['fechacaduca'] = date("Ymd", strtotime($request->fechacaduca . "+ 1 month"));
                $request['fecha_renovacion'] = date('YmdHis', strtotime(now()));
                $request['periodo'] = 1;
                $asunto = 'Renovacion Mensual Licencia Web';
                break;

            case 'anual':
                $request['fecha_renovacion'] = date('YmdHis', strtotime(now()));
                $request['fechacaduca'] = date("Ymd", strtotime($request->fechacaduca . "+ 1 year"));
                $asunto = 'Renovacion Anual Licencia Web';
                if ($request->producto != 12) {
                    $request['periodo'] = 2;
                } else {
                    $parametrosJson = $this->procesarDocumentosFacturito($parametrosJson, $request->periodo);
                }
                break;

            case 'recargar':
                $parametrosJson->Documentos = $parametrosJson->Documentos + 120;
                $request['fechacaduca'] = date('Ymd', strtotime($request->fechacaduca));
                $asunto = $licenciaActual->producto == 9 ? 'Recarga 120 Documentos Perseo Web Lite' : 'Recarga 120 Documentos Perseo Web Emprendedor';
                break;

            case 'recargar240':
                $parametrosJson->Documentos = $parametrosJson->Documentos + 240;
                $request['fechacaduca'] = date('Ymd', strtotime($request->fechacaduca));
                $asunto = 'Recarga 240 Documentos Perseo Web Emprendedor';
                break;

            default:
                $request['fechacaduca'] = date('Ymd', strtotime($request->fechacaduca));
                break;
        }

        // Procesamiento para Facturito
        if ($request->producto == 12 && $licenciaActual->periodo != $request->periodo) {
            $parametrosJson = $this->procesarDocumentosFacturito($parametrosJson, $request->periodo);
        }

        return ['asunto' => $asunto, 'parametros' => $parametrosJson];
    }

    private function procesarDocumentosFacturito($parametrosJson, $periodo)
    {
        $documentos = ['1' => 60, '2' => 150, '3' => 100000, '4' => 30];
        $parametrosJson->Documentos = $parametrosJson->Documentos + ($documentos[$periodo] ?? 0);
        return $parametrosJson;
    }

    private function obtenerParametrosProducto($producto, $periodo = null): array
    {
        $parametros = ['Documentos' => "0", 'Productos' => "0", 'Almacenes' => "0", 'Nomina' => "0", 'Produccion' => "0", 'Activos' => "0", 'Talleres' => "0", 'Garantias' => "0"];

        switch ($producto) {
            case '6':
                $parametros = ['Documentos' => "120", 'Productos' => "500", 'Almacenes' => "1", 'Nomina' => "3", 'Produccion' => "3", 'Activos' => "3", 'Talleres' => "3", 'Garantias' => "3"];
                break;
            case '9':
                $parametros = ['Documentos' => "100000", 'Productos' => "100000", 'Almacenes' => "1", 'Nomina' => "3", 'Produccion' => "3", 'Activos' => "3", 'Talleres' => "3", 'Garantias' => "3"];
                break;
            case '10':
                $parametros['Documentos'] = "120";
                break;
            case '11':
                $parametros['Documentos'] = "5";
                break;
            case '12':
                $parametros['Documentos'] = ['1' => "60", '2' => "150", '3' => "100000", '4' => "30"][$periodo] ?? "0";
                break;
        }

        return $parametros;
    }

    private function generarModulosXml(Request $request): string
    {
        $xw = xmlwriter_open_memory();
        xmlwriter_start_document($xw, '1.0', 'UTF-8');
        xmlwriter_start_element($xw, 'modulos');

        $modulos = ['nomina', 'activos', 'produccion', 'restaurantes', 'talleres', 'garantias', 'ecommerce'];
        foreach ($modulos as $modulo) {
            xmlwriter_start_element($xw, $modulo);
            xmlwriter_text($xw, $request->$modulo === 'on' ? 1 : 0);
            xmlwriter_end_element($xw);
        }

        xmlwriter_end_element($xw);
        xmlwriter_end_document($xw);
        return xmlwriter_output_memory($xw);
    }

    private function enviarEmailWeb($licencia, Request $request, string $asunto): void
    {
        try {
            $cliente = $this->obtenerDatosClienteEmail($request['sis_clientesid']);
            if (!$cliente) return;

            $datosEmail = [
                'view' => 'emails.licenciaweb',
                'from' => env('MAIL_FROM_ADDRESS'),
                'subject' => $asunto,
                'cliente' => $cliente->nombres,
                'vendedor' => $cliente->razonsocial ?? '',
                'identificacion' => $cliente->identificacion,
                'correos' => $cliente->correos,
                'numerocontrato' => $licencia['numerocontrato'],
                'producto' => $licencia['producto'],
                'distribuidor' => $cliente->nombredistribuidor ?? '',
                'periodo' => $this->obtenerDescripcionPeriodo($request['producto'], $request['periodo'] ?? null),
                'fechainicia' => date("d-m-Y", strtotime($licencia['fechainicia'])),
                'fechacaduca' => date("d-m-Y", strtotime($licencia['fechacaduca'])),
                'empresas' => $licencia['empresas'],
                'numeromoviles' => $licencia['numeromoviles'],
                'usuarios' => $licencia['usuarios'],
                'modulos' => json_decode(json_encode(simplexml_load_string($licencia['modulos']))),
                'usuario' => Auth::user()->nombres,
                'fecha' => $licencia['fechacreacion'] ?? date("Y-m-d H:i:s", strtotime($request['fechamodificacion'] ?? now())),
                'tipo' => $request['producto'] == 12 ? (str_contains($asunto, 'Nuevo') ? 7 : 8) : (str_contains($asunto, 'Nuevo') ? 1 : 3),
            ];

            $emails = $this->prepararEmailsDestinatarios($cliente);

            if (config('app.env') !== 'local' && !empty($emails)) {
                Mail::to($emails)->queue(new enviarlicencia($datosEmail));
            }

        } catch (\Exception $e) {
            \Log::warning('Error enviando email Web: ' . $e->getMessage());
        }
    }

    private function obtenerDescripcionPeriodo($producto, $periodo): string
    {
        if ($producto == 12) {
            return ['1' => 'Inicial', '2' => 'Básico', '3' => 'Premium', '4' => 'Gratis'][$periodo] ?? 'No definido';
        }
        return $periodo == 1 ? 'Mensual' : 'Anual';
    }

    // =====================================
    // MÉTODOS ADICIONALES
    // =====================================

    public function enviarEmail($clienteId, $productoId)
    {
        try {
            $cliente = Clientes::select('sis_clientes.nombres', 'sis_clientes.identificacion', 'sis_clientes.correos', 'sis_distribuidores.correos as distribuidor')
                ->join('sis_distribuidores', 'sis_distribuidores.sis_distribuidoresid', 'sis_clientes.sis_distribuidoresid')
                ->where('sis_clientesid', $clienteId)
                ->first();

            if (!$cliente) {
                flash('Cliente no encontrado')->error();
                return back();
            }

            $datosEmail = [
                'from' => env('MAIL_FROM_ADDRESS'),
                'subject' => 'Envio Credenciales',
                'nombre' => $cliente->nombres,
                'usuario' => $cliente->identificacion,
                'view' => $productoId == 12 ? 'emails.envio_credenciales_facturito' : 'emails.envio_credenciales',
                'tipo' => $productoId == 12 ? 9 : 5,
            ];

            $emails = array_filter(array_merge(
                explode(", ", $cliente->distribuidor ?? ''),
                [$cliente->correos]
            ), fn($email) => !empty(trim($email)));

            if (config('app.env') !== 'local' && !empty($emails)) {
                Mail::to($emails)->queue(new enviarlicencia($datosEmail));
            }

            flash('Correo Enviado Correctamente')->success();

        } catch (\Exception $e) {
            flash('Error enviando email: ' . $e->getMessage())->error();
        }

        return back();
    }

    public function editarClave(Clientes $cliente, Servidores $servidor, $licenciaid)
    {
        try {
            $resultado = $this->externalServerService->resetUserPassword(
                $servidor,
                $licenciaid,
                $cliente->identificacion
            );

            return $resultado['success'] ?
                ['mensaje' => 'Clave Reseteada Correctamente', 'tipo' => 'success'] :
                ['mensaje' => $resultado['error'], 'tipo' => 'warning'];

        } catch (\Exception $e) {
            return ['mensaje' => 'Error al resetear clave: ' . $e->getMessage(), 'tipo' => 'error'];
        }
    }

    public function actividad($servidorid, $licenciaid)
    {
        try {
            $servidor = Servidores::where('sis_servidoresid', $servidorid)->firstOrFail();
            $resultado = $this->externalServerService->getLicenseActivity($servidor, $licenciaid);

            return $resultado['success'] ?
                ['actividades' => $resultado['activities']] :
                ['actividades' => []];

        } catch (\Exception $e) {
            return ['actividades' => []];
        }
    }
}
