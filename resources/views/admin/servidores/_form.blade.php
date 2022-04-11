
@csrf
<div class="form-group row">

    <div class="col-lg-6">
        <label>Descripción:</label>
        <div id="spinner">
            <input type="text" class="form-control {{ $errors->has('descripcion') ? 'is-invalid' : '' }}"
                placeholder="Ingrese descripcion" name="descripcion" autocomplete="off"
                value="{{ old('descripcion', $servidores->descripcion) }}" id="descripcion"
                />
        </div>
     
        @if ($errors->has('descripcion'))
        <span class="text-danger">{{ $errors->first('descripcion') }}</span>
        @endif
    </div>
    <div class="col-lg-6">
        <label>Dominio:</label>
        <input type="text" class="form-control {{ $errors->has('dominio') ? 'is-invalid' : '' }}"
            placeholder="Ingrese Dominio" name="dominio" autocomplete="off"
            value="{{ old('dominio', $servidores->dominio) }}" id="dominio" />
        @if ($errors->has('dominio'))
        <span class="text-danger">{{ $errors->first('dominio') }}</span>
        @endif
    </div>
</div>
