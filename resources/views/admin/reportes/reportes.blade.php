@extends('admin.layouts.app')
@section('contenido')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="d-flex flex-column-fluid">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                        <div class="card-header ">
                            <div class="card-title">
                                <h3 class="card-label">Versiones Ejecutable</h3>
                            </div>
                            <div class="card-toolbar">
                                <a href="{{ route('reportes.export_versiones') }}"
                                    class="btn btn-success font-weight-bolder">
                                    <span class="svg-icon svg-icon-md">
                                        <i class="flaticon-download"></i>
                                    </span>Descargar
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            <div id="chart1"></div>
                        </div>

                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                        <div class="card-header ">
                            <div class="card-title">
                                <h3 class="card-label">Respaldos</h3>
                            </div>
                            <div class="card-toolbar">
                                <a href="{{ route('reportes.export_respaldos') }}"
                                    class="btn btn-success font-weight-bolder">
                                    <span class="svg-icon svg-icon-md">
                                        <i class="flaticon-download"></i>
                                    </span>Descargar
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            <div id="chart2"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    const colors = ["#FA5563", "#FB834F", "#FCCF3E", "#A0D77A", "#6BCA6C"];

        const dataChart = {
            ejecutable: {
                values: [],
                labels: []
            },
            respaldo: {
                values: [],
                labels: []
            }
        }

        var options1 = {
            series: dataChart.ejecutable.values,
            chart: {
                width: 380,
                type: 'pie',
            },
            colors: colors,
            labels: dataChart.ejecutable.labels,
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 300
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }],
            dataLabels: {
                enabled: true,
                formatter(val, opts) {
                    const tempValue = opts.w.globals.seriesTotals[opts.seriesIndex]
                    return [tempValue, val.toFixed(1) + '%']
                }
            },
            plotOptions: {
                pie: {
                    dataLabels: {
                        offset: -20
                    }
                }
            },
            title: {
                offsetY: 0,
                offsetX: 0,
                style: {
                    color: '#000'
                }
            }
        };

        var options2 = {
            series: dataChart.respaldo.values,
            chart: {
                width: 400,
                type: 'pie',
            },
            colors: colors,
            labels: dataChart.respaldo.labels,
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 300
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }],
            dataLabels: {
                enabled: true,
                formatter(val, opts) {
                    const tempValue = opts.w.globals.seriesTotals[opts.seriesIndex]
                    return [tempValue, val.toFixed(1) + '%']
                }
            },
            plotOptions: {
                pie: {
                    dataLabels: {
                        offset: -20
                    }
                }
            },
            title: {
                offsetY: 0,
                offsetX: 0,
                style: {
                    color: '#000'
                }
            }
        };

        const chartP1 = new ApexCharts(document.querySelector("#chart1"), options1);
        const chartP2 = new ApexCharts(document.querySelector("#chart2"), options2);

        $(document).ready(function() {

            const data = @json($data);

            chartP1.render();
            chartP2.render();

                chartP1.updateOptions({
                    series: data.ejecutable.values,
                    labels: data.ejecutable.labels,
                });
                chartP2.updateOptions({
                    series: data.respaldo.values,
                    labels: data.respaldo.labels,
                });

        });

</script>
@endsection