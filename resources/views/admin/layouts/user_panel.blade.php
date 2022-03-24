<div
    class="dropdown-menu p-0 m-0 dropdown-menu-right dropdown-menu-anim-up dropdown-menu-lg offcanvas offcanvas-right p-7">
    <div class="offcanvas-content pr-0 mr-n2">

        <div class="d-flex align-items-center mt-5">
            <div class="symbol symbol-75 mr-5">

{{-- Dibujito --}}
            </div>
            <div class="d-flex flex-column">
                <div>
                    <a href="#" class="font-weight-bold font-size-5 text-dark-75 ">
                        Nombre
                    </a>
                </div>

                <div class="navi mt-4">

                  

                </div>
            </div>
        </div>

    </div>
    <div class="separator separator-dashed my-1 mb-3">

    </div>

    <div class="d-flex">
       
            <div class="col-5 ">
                <label>Menu Claro: </label>
                <span class="switch switch-sm switch-icon">
                    <label>
                        <input type="checkbox" name="menu" id="menu" onchange="cambiarMenu();"
                            @if (Session::get('menu') == 1) checked @endif />
                        <span></span>
                    </label>
                </span>
            </div>
      

        
    </div>


</div>
@section('scriptMenu')
    <script>
        function cambiarMenu() {

            var estado;
            if ($('#menu').is(':checked')) {
                estado = 1;
            } else {

                estado = 0;
            }

            $.post('{{ route('cambiarMenu') }}', {
                _token: $('meta[name="csrf-token"]').attr("content"),
                estado: estado
            }, function(data) {

                location.reload();
            });
        }
    </script>
@endsection
