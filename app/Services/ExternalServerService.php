<?php

namespace App\Services;

use App\Models\Servidores;
use App\Models\Licenciasweb;
use App\Models\Clientes;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExternalServerService
{
    private bool $localMode;

    public function __construct()
    {
        $this->localMode = config('sistema.local_mode', false);
    }

    // Configuración de timeouts por tipo de operación
    private const TIMEOUTS = [
        'availability' => 6,
        'read' => 10,
        'write' => 30,
        'delete' => 15
    ];

    // Endpoints comunes
    private const ENDPOINTS = [
        'clientes' => [
            'create' => '/registros/crear_clientes',
            'update' => '/registros/editar_clientes',
            'delete' => '/registros/eliminar_cliente'
        ],
        'licencias' => [
            'create' => '/registros/crear_licencias',
            'update' => '/registros/editar_licencia',
            'query' => '/registros/consulta_licencia',
            'delete' => '/registros/eliminar_licencia',
            'generate' => '/registros/generador_licencia',
            'activity' => '/registros/consulta_actividades',
            'reset_password' => '/registros/restaurar_clave_usuario'
        ],
        'usuarios' => [
            'query' => '/registros/consulta_usuario'
        ]
    ];

    /**
     * Verifica la disponibilidad de un servidor específico
     */
    public function checkServerAvailability(Servidores $servidor): bool
    {
        if ($this->localMode) {
            return true; // En modo local, siempre disponible
        }

        try {
            $response = Http::timeout(self::TIMEOUTS['availability'])
                ->withOptions(['verify' => false])
                ->head($servidor->dominio);

            return $response->status() < 500;
        } catch (\Exception $e) {
            Log::warning("Servidor no disponible: {$servidor->dominio}", [
                'error' => $e->getMessage(),
                'servidor_id' => $servidor->sis_servidoresid
            ]);
            return false;
        }
    }

    /**
     * Verifica la disponibilidad de múltiples servidores
     */
    public function checkMultipleServersAvailability($servidores): array
    {
        if ($this->localMode) {
            return []; // En modo local, todos disponibles
        }

        $unavailableServers = [];
        foreach ($servidores as $servidor) {
            if (!$this->checkServerAvailability($servidor)) {
                $unavailableServers[] = $servidor->descripcion;
            }
        }
        return $unavailableServers;
    }

    /**
     * Operaciones específicas para CLIENTES
     */
    public function createClient(Servidores $servidor, array $clientData): array
    {
        if ($this->localMode) {
            return ['success' => true, 'sis_clientesid' => $clientData['sis_clientesid']];
        }

        $result = $this->makeRequest($servidor, self::ENDPOINTS['clientes']['create'], $clientData);
        if ($result['success'] && isset($result['data']['sis_clientes']) && !empty($result['data']['sis_clientes'])) {
            return ['success' => true, 'sis_clientesid' => $result['data']['sis_clientes'][0]['sis_clientesid']];
        }
        return ['success' => false, 'error' => $result['error'] ?? 'Respuesta inválida del servidor'];
    }

    public function updateClient(Servidores $servidor, array $clientData): array
    {
        if ($this->localMode) {
            return ['success' => true];
        }

        $result = $this->makeRequest($servidor, self::ENDPOINTS['clientes']['update'], $clientData);
        return $result['success'] && isset($result['data']['sis_clientes']) ? ['success' => true] :
            ['success' => false, 'error' => $result['error'] ?? 'Error al actualizar cliente'];
    }

    public function deleteClient(Servidores $servidor, int $clientId): array
    {
        if ($this->localMode) {
            return ['success' => true];
        }

        $result = $this->makeRequest($servidor, self::ENDPOINTS['clientes']['delete'], ['sis_clientesid' => $clientId], 'POST', 'delete');
        return $result['success'] && isset($result['data']['respuesta']) ? ['success' => true] :
            ['success' => false, 'error' => $result['error'] ?? 'Error al eliminar cliente'];
    }

    /**
     * Operaciones específicas para LICENCIAS
     */
    public function createLicense(Servidores $servidor, array $licenseData): array
    {
        if ($this->localMode) {
            // En modo local, generar un ID simulado pero no crear aún
            $licenseId = Licenciasweb::max('sis_licenciasid') + 1;
            return ['success' => true, 'license_id' => $licenseId, 'data' => array_merge($licenseData, ['sis_licenciasid' => $licenseId])];
        }

        $result = $this->makeRequest($servidor, self::ENDPOINTS['licencias']['create'], $licenseData);
        if ($result['success'] && isset($result['data']['licencias']) && !empty($result['data']['licencias'])) {
            return ['success' => true, 'license_id' => $result['data']['licencias'][0]['sis_licenciasid'], 'data' => $result['data']['licencias'][0]];
        }
        return ['success' => false, 'error' => $result['error'] ?? 'Respuesta inválida del servidor de licencias'];
    }

    public function updateLicense(Servidores $servidor, array $licenseData): array
    {
        if ($this->localMode) {
            $licencia = Licenciasweb::where('sis_licenciasid', $licenseData['sis_licenciasid'])
                ->where('sis_servidoresid', $licenseData['sis_servidoresid'])
                ->first();
            if ($licencia) {
                $licencia->update($licenseData);
                return ['success' => true, 'data' => $licencia->toArray()];
            }
            return ['success' => false, 'error' => 'Licencia no encontrada'];
        }

        $result = $this->makeRequest($servidor, self::ENDPOINTS['licencias']['update'], $licenseData);
        return $result['success'] && isset($result['data']['licencias']) ? ['success' => true, 'data' => $result['data']] :
            ['success' => false, 'error' => $result['error'] ?? 'Error al actualizar licencia'];
    }

    public function queryLicense(Servidores $servidor, array $params): array
    {
        if ($this->localMode) {
            $query = Licenciasweb::query();
            if (isset($params['sis_clientesid'])) {
                $query->where('sis_clientesid', $params['sis_clientesid']);
            }
            if (isset($params['sis_licenciasid'])) {
                $query->where('sis_licenciasid', $params['sis_licenciasid']);
            }

            $licencias = $query->get()->map(function ($licencia) {
                $array = $licencia->toArray();
                $array['sis_servidoresid'] = $licencia->sis_servidoresid;
                return $array;
            })->toArray();

            return ['success' => true, 'licenses' => $licencias];
        }

        $result = $this->makeRequest($servidor, self::ENDPOINTS['licencias']['query'], $params, 'POST', 'read');
        return $result['success'] && isset($result['data']['licencias']) ? ['success' => true, 'licenses' => $result['data']['licencias']] :
            ['success' => false, 'error' => $result['error'] ?? 'Error al consultar licencia'];
    }

    public function deleteLicense(Servidores $servidor, int $licenseId): array
    {
        if ($this->localMode) {
            $licencia = Licenciasweb::where('sis_licenciasid', $licenseId)->first();
            if ($licencia) {
                $licencia->delete();
                return ['success' => true];
            }
            return ['success' => false, 'error' => 'Licencia no encontrada'];
        }

        $result = $this->makeRequest($servidor, self::ENDPOINTS['licencias']['delete'], ['sis_licenciasid' => $licenseId], 'POST', 'delete');
        return $result['success'] && isset($result['data']['respuesta']) ? ['success' => true] :
            ['success' => false, 'error' => $result['error'] ?? 'Error al eliminar licencia'];
    }

    public function generateLicense(Servidores $servidor, array $licenseData): array
    {
        $result = $this->makeRequest($servidor, self::ENDPOINTS['licencias']['generate'], $licenseData);
        return $result['success'] && isset($result['data']['licencia']) ? ['success' => true, 'license_key' => $result['data']['licencia']] :
            ['success' => false, 'error' => $result['error'] ?? 'Error al generar licencia'];
    }

    public function getLicenseActivity(Servidores $servidor, int $licenseId): array
    {
        $result = $this->makeRequest($servidor, self::ENDPOINTS['licencias']['activity'], ['sis_licenciasid' => $licenseId], 'POST', 'read');
        return $result['success'] ? ['success' => true, 'activities' => $result['data']['actividades'] ?? []] :
            ['success' => false, 'error' => $result['error'] ?? 'Error al obtener actividades'];
    }

    public function resetUserPassword(Servidores $servidor, int $licenseId, string $identification): array
    {
        if ($this->localMode) {
            return ['success' => true, 'message' => 'Clave reseteada correctamente (modo local)'];
        }

        $result = $this->makeRequest($servidor, self::ENDPOINTS['licencias']['reset_password'],
            ['sis_licenciasid' => $licenseId, 'identificacion' => substr($identification, 0, 10)], 'POST', 'write');

        return $result['success'] && isset($result['data']['respuesta']) ? ['success' => true, 'message' => 'Clave reseteada correctamente'] :
            ['success' => false, 'error' => $result['data']['fault']['detail'] ?? $result['error'] ?? 'Error al resetear clave'];
    }

    /**
     * Operación batch para múltiples servidores
     */
    public function batchOperation($servidores, string $operation, array $data, callable $successCallback = null): array
    {
        if ($this->localMode) {
            return ['success' => true, 'successful_operations' => count($servidores)];
        }

        $successfulOperations = [];
        $unavailableServers = $this->checkMultipleServersAvailability($servidores);

        if (!empty($unavailableServers)) {
            return ['success' => false, 'error' => 'Servidores no disponibles: ' . implode(', ', $unavailableServers), 'unavailable_servers' => $unavailableServers];
        }

        try {
            foreach ($servidores as $servidor) {
                $result = match ($operation) {
                    'create_client' => $this->createClient($servidor, $data),
                    'update_client' => $this->updateClient($servidor, $data),
                    'delete_client' => $this->deleteClient($servidor, $data['sis_clientesid']),
                    'create_license' => $this->createLicense($servidor, $data),
                    'update_license' => $this->updateLicense($servidor, $data),
                    'delete_license' => $this->deleteLicense($servidor, $data['sis_licenciasid']),
                    default => throw new \InvalidArgumentException("Operación no soportada: {$operation}")
                };

                if ($result['success']) {
                    $successfulOperations[] = ['servidor' => $servidor, 'result' => $result];
                    if ($successCallback) $successCallback($servidor, $result);
                } else {
                    $this->rollbackOperations($successfulOperations, $operation);
                    return ['success' => false, 'error' => "Error en servidor {$servidor->descripcion}: {$result['error']}", 'failed_server' => $servidor->descripcion];
                }
            }
            return ['success' => true, 'successful_operations' => count($successfulOperations)];
        } catch (\Exception $e) {
            $this->rollbackOperations($successfulOperations, $operation);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Operaciones específicas para USUARIOS
     */
    public function queryUser(Servidores $servidor, array $params): array
    {
        $result = $this->makeRequest(
            $servidor,
            self::ENDPOINTS['usuarios']['query'],
            $params,
            'POST',
            'read'
        );

        if ($result['success'] && isset($result['data']['usuario']) && !empty($result['data']['usuario'])) {
            return [
                'success' => true,
                'user' => $result['data']['usuario'][0]
            ];
        }

        return [
            'success' => false,
            'error' => $result['error'] ?? 'Usuario no encontrado'
        ];
    }
    
    /**
     * Buscar cliente en múltiples servidores disponibles
     */
    public function findClientInServers(string $identificacion): array
    {
        $servidores = Servidores::where('estado', 1)->get();
        $resultados = [];

        foreach ($servidores as $servidor) {
            try {
                // Verificar disponibilidad antes de consultar
                if (!$this->checkServerAvailability($servidor)) {
                    Log::info("Servidor no disponible, saltando: {$servidor->descripcion}");
                    continue;
                }

                // Consultar usuario
                $usuario = $this->queryUser($servidor, ['identificacion' => $identificacion]);

                if (!$usuario['success']) {
                    continue;
                }

                // Consultar licencias del usuario
                $licencias = $this->queryLicense($servidor, [
                    'sis_clientesid' => $usuario['user']['sis_clientesid']
                ]);

                if ($licencias['success'] && !empty($licencias['licenses'])) {
                    $resultados[] = [
                        'servidor' => $servidor,
                        'usuario' => $usuario['user'],
                        'licencias_count' => count($licencias['licenses'])
                    ];

                    Log::info("Cliente encontrado en servidor: {$servidor->descripcion}", [
                        'identificacion' => $identificacion,
                        'licencias' => count($licencias['licenses'])
                    ]);
                }

            } catch (\Exception $e) {
                Log::warning("Error buscando cliente en servidor {$servidor->descripcion}", [
                    'error' => $e->getMessage(),
                    'identificacion' => $identificacion
                ]);
                continue;
            }
        }

        return $resultados;
    }

    public function registrarEnBitrix(array $queryParams): void
    {
        if ($this->localMode) {
            return; // No registrar en Bitrix en modo local
        }

        try {
            $response = Http::withOptions(["verify" => false])->timeout(10)
                ->get('https://perseo-soft.bitrix24.es/rest/5507/9mgnss30ssjdu1ay/crm.deal.add.json', $queryParams);

            $resultado = $response->json();
            if (isset($resultado['error'])) {
                \Log::warning('Error en Bitrix: ' . json_encode($resultado['error']));
            }
        } catch (\Exception $e) {
            \Log::warning('Error conectando con Bitrix: ' . $e->getMessage());
        }
    }

    // Métodos privados sin cambios
    private function makeRequest(Servidores $servidor, string $endpoint, array $data = [], string $method = 'POST', string $operationType = 'write'): array
    {
        try {
            $url = $servidor->dominio . $endpoint;
            $timeout = self::TIMEOUTS[$operationType];

            $request = Http::timeout($timeout)
                ->withHeaders(['Content-Type' => 'application/json', 'Accept' => 'application/json'])
                ->withOptions(['verify' => false]);

            $response = match ($method) {
                'GET' => $request->get($url, $data),
                'POST' => $request->post($url, $data),
                'PUT' => $request->put($url, $data),
                'DELETE' => $request->delete($url, $data),
                default => throw new \InvalidArgumentException("Método HTTP no soportado: {$method}")
            };

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json(), 'status' => $response->status()];
            }

            Log::warning("Petición fallida a servidor externo", [
                'servidor' => $servidor->descripcion,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return ['success' => false, 'error' => "HTTP {$response->status()}: {$response->body()}", 'status' => $response->status()];
        } catch (\Exception $e) {
            Log::error("Error en petición a servidor externo", [
                'servidor' => $servidor->descripcion,
                'error' => $e->getMessage(),
                'endpoint' => $endpoint
            ]);
            return ['success' => false, 'error' => $e->getMessage(), 'status' => 0];
        }
    }

    private function rollbackOperations(array $successfulOperations, string $originalOperation): void
    {
        if ($this->localMode) {
            return; // No rollback necesario en modo local
        }

        $rollbackOperation = match ($originalOperation) {
            'create_client' => 'delete_client',
            'create_license' => 'delete_license',
            default => null
        };

        if (!$rollbackOperation) return;

        foreach ($successfulOperations as $operation) {
            try {
                $servidor = $operation['servidor'];
                $result = $operation['result'];
                match ($rollbackOperation) {
                    'delete_client' => $this->deleteClient($servidor, $result['sis_clientesid']),
                    'delete_license' => $this->deleteLicense($servidor, $result['license_id']),
                };
                Log::info("Rollback exitoso para servidor: {$servidor->descripcion}");
            } catch (\Exception $e) {
                Log::error("Error en rollback para servidor {$servidor->descripcion}: {$e->getMessage()}");
            }
        }
    }
}
