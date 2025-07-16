@php
    $rol = Auth::user()->tipo;
@endphp
@csrf
<style>
    .disabled {
        pointer-events: none;
        opacity: 1;
        background-color: #F3F6F9;
    }
    
    .tab-content {
        overflow-x: hidden;
    }
</style>

{{-- Campos ocultos --}}
<input type="hidden" value="{{ $cliente->sis_clientesid }}" name="sis_clientesid">

{{-- Navegación principal --}}
<ul class="nav nav-tabs nav-tabs-line mb-5">
    <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#datos_licencia">
            <span class="nav-icon"><i class="fas fa-server"></i></span>
            <span class="nav-text">Datos Licencia</span>
        </a>
    </li>
</ul>

<div class="tab-content">
    {{-- TAB: Datos Licencia --}}
    <div class="tab-pane fade show active" id="datos_licencia" role="tabpanel" aria-labelledby="datos_licencia">

        {{-- Información básica --}}
        <p class="font-size-lg font-weight-bold mb-1"><i class="fas fa-file-contract"></i> Información Básica</p>
        <div class="separator separator-dashed mb-2"></div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Número Contrato</label>
                    <input type="text" class="form-control @error('numerocontrato') is-invalid @enderror"
                           name="numerocontrato" id="numerocontrato"
                           value="{{ old('numerocontrato', $licencia->numerocontrato) }}" readonly>
                    @error('numerocontrato')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Dirección IP</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-globe"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control @error('ip') is-invalid @enderror"
                               name="ip" id="ip" placeholder="Ej: 192.168.1.1"
                               value="{{ old('ip', $licencia->ip) }}">
                    </div>
                    @error('ip')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Credenciales de acceso --}}
        <p class="font-size-lg font-weight-bold mb-1"><i class="fas fa-key"></i> Credenciales de Acceso</p>
        <div class="separator separator-dashed mb-2"></div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Usuario</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-user"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control @error('usuario') is-invalid @enderror"
                               name="usuario" id="usuario" placeholder="Nombre de usuario"
                               value="{{ old('usuario', $licencia->usuario) }}">
                    </div>
                    @error('usuario')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Clave</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control @error('clave') is-invalid @enderror"
                               name="clave" id="clave" placeholder="Contraseña"
                               value="{{ old('clave', $licencia->clave) }}">
                    </div>
                    @error('clave')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Fechas de corte --}}
        <p class="font-size-lg font-weight-bold mb-1"><i class="fas fa-calendar-alt"></i> Fechas de Corte</p>
        <div class="separator separator-dashed mb-2"></div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Fecha Corte Proveedor</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-calendar"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control @error('fecha_corte_proveedor') is-invalid @enderror"
                               name="fecha_corte_proveedor" id="fecha_corte_proveedor"
                               value="{{ old('fecha_corte_proveedor', $licencia->fecha_corte_proveedor) }}">
                    </div>
                    @error('fecha_corte_proveedor')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Fecha Corte Cliente</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-calendar"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control @error('fecha_corte_cliente') is-invalid @enderror"
                               name="fecha_corte_cliente" id="fecha_corte_cliente"
                               value="{{ old('fecha_corte_cliente', $licencia->fecha_corte_cliente) }}">
                    </div>
                    @error('fecha_corte_cliente')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Información financiera --}}
        <p class="font-size-lg font-weight-bold mb-1"><i class="fas fa-dollar-sign"></i> Información Financiera</p>
        <div class="separator separator-dashed mb-2"></div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Costo Proveedor</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-warning text-white">
                                <i class="fas fa-dollar-sign text-white"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control precio text-warning font-weight-bold @error('costo_proveedor') is-invalid @enderror"
                               id="costo_proveedor" name="costo_proveedor"
                               value="{{ old('costo_proveedor', $licencia->costo_proveedor) }}">
                    </div>
                    @error('costo_proveedor')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Precio Cliente</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-success text-white">
                                <i class="fas fa-dollar-sign text-white"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control precio text-success font-weight-bold @error('precio_cliente') is-invalid @enderror"
                               id="precio_cliente" name="precio_cliente"
                               value="{{ old('precio_cliente', $licencia->precio_cliente) }}">
                    </div>
                    @error('precio_cliente')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>
@section('script')
    <script>
        $(document).ready(function () {
            $('.deshabilitar').click(function () {
                return false;
            });

            //Iniciar fecha
            $('#fecha_corte_proveedor').datepicker({
                language: "es",
                todayHighlight: true,
                orientation: "bottom left",
                templates: {
                    leftArrow: '<i class="la la-angle-left"></i>',
                    rightArrow: '<i class="la la-angle-right"></i>'
                }
            });

            $('#fecha_corte_cliente').datepicker({
                language: "es",
                todayHighlight: true,
                orientation: "bottom left",
                templates: {
                    leftArrow: '<i class="la la-angle-left"></i>',
                    rightArrow: '<i class="la la-angle-right"></i>'
                }
            });

            var estado = '{{ $rol }}';
            if (estado != 1) {
                estado = 'disabled';
            }

            if ("{{ isset($licencia->sis_licenciasid) }}" == false) {
                var fecha = new Date();
                let fecha_corte = ("0" + (fecha.getDate())).slice(-2) + "-" + ("0" + (fecha.getMonth() + 1)).slice(-2) + "-" + fecha.getFullYear()
                $('#fecha_corte_proveedor').val(fecha_corte);
                $('#fecha_corte_cliente').val(fecha_corte);
            }
        });
    </script>
@endsection
