@csrf
<div class="form-group row">
    <div class="col-lg-4">
        <label>Tipo :</label>
        <select class="form-control" id="tipo" name="tipo">
            <option value="1" {{ old('tipo', $publicidades->tipo) == '1' ? 'Selected' : '' }}>Inicio
            </option>
            <option value="2" {{ old('tipo', $publicidades->tipo) == '2' ? 'Selected' : '' }}>Admin
            </option>
            <option value="3" {{ old('tipo', $publicidades->tipo) == '3' ? 'Selected' : '' }}>Registro
            </option>
        </select>
        @if ($errors->has('tipo'))
        <span class="text-danger">{{ $errors->first('tipo') }}</span>
        @endif
    </div>
    <div class="col-lg-4">
        <label>Fecha Inicio:</label>
        <input type="text" class="fecha form-control {{ $errors->has('fechainicio') ? 'is-invalid' : '' }}"
            placeholder="Ingrese Fecha Inicio" name="fechainicio" id="fechainicio" autocomplete="off"
            value="{{ old('fechainicio', $publicidades->fechainicio) }}" />
        @if ($errors->has('fechainicio'))
        <span class="text-danger">{{ $errors->first('fechainicio') }}</span>
        @endif
    </div>
    <div class="col-lg-4">
        <label>Fecha Fin:</label>
        <input type="text" class="fecha form-control {{ $errors->has('fechafin') ? 'is-invalid' : '' }}"
            placeholder="Ingrese Fecha Fin" name="fechafin" id="fechafin" autocomplete="off"
            value="{{ old('fechafin', $publicidades->fechafin) }}" />
        @if ($errors->has('fechafin'))
        <span class="text-danger">{{ $errors->first('fechafin') }}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-4">
        <div class="ml-2">
            <label> Imagen</label>
        </div>
        <div class="image-input image-input-outline" id="imagen">
            <div class="image-input-wrapper"
                style="background-image: url(data:image/jpg;base64,{{$publicidades->imagen}});background-size: contain">
            </div>
            <label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow"
                data-action="change" data-toggle="tooltip" title="" data-original-title="Cambiar Imagen">
                <i class="fa fa-pen icon-sm text-muted"></i>
                <input type="file" name="imagen" accept=".jpg" />
            </label>
            <span class="form-text text-muted">Imagen JPG 1000x900</span>
            @if ($errors->has('imagen'))
            <span class="text-danger">{{ $errors->first('imagen') }}</span>
            @endif
        </div>
    </div>
</div>

@section('script')
<script>
    $(document).ready(function() {
         //Iniciar fecha 
        $('.fecha').datepicker({
                language: "es",
                todayHighlight: true,
                orientation: "bottom left",
                templates: {
                    leftArrow: '<i class="la la-angle-left"></i>',
                    rightArrow: '<i class="la la-angle-right"></i>'
                }
        });

        if ("{{ isset($publicidades->sis_publicidadesid) }}" == false) {
            var fecha = new Date();
            let fechaInicia = ("0" + (fecha.getDate())).slice(-2) + "-" + ("0" + (fecha.getMonth() + 1)).slice(-2) + "-" + fecha.getFullYear()
            $('#fechainicio').val(fechaInicia);
            $('#fechafin').val(fechaInicia);
        }
        var publicidad = new KTImageInput('imagen');
    });
</script>
@endsection