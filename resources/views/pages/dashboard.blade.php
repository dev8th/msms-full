@php
    $yearLaunch = intVal(env("APP_LAUNCH_YEAR"));
    $yearNow = intVal(date("Y"));
    $yearLength = $yearNow-$yearLaunch;
    $yearLoop = $yearLaunch;
    
    $p=0;
    Auth::user()->dashboard_pendapatan_board>0?$p++:'';
    Auth::user()->dashboard_paid_board>0?$p++:'';
    $colBoard2 = 12 / $p;

    $n=0;
    Auth::user()->dashboard_berat_board>0?$n++:'';
    Auth::user()->dashboard_cust_board>0?$n++:'';
    Auth::user()->dashboard_cbm_board>0?$n++:'';
    Auth::user()->dashboard_diskon_board>0?$n++:'';
    $colBoard = 12 / $n;

    $z=0;
    Auth::user()->dashboard_berat_chart>0?$z++:'';
    Auth::user()->dashboard_pendapatan_chart>0?$z++:'';
    $colChart = 12 / $z;
@endphp
<style>
    #beratCustChart,
    #pendapatanChart {
        min-height: 250px;
        height: 250px;
        max-height: 250px;
        max-width: 100%;
    }
    #titleTotalBeratCust,
    #titleTotalPendapatan{
        display:inline;
    }
    .small-box-footer{
        font-weight:700;
    }
</style>

<section class="content-header pb-md-2 pb-1">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-md-6 col-12">
                <h1>Dashboard</h1>
            </div>
            <div class="col-md-2 col-sm-12 mt-md-0 mt-2 pr-md-2 px-0">
                <div class="form-group">
                    <select id="filterWarehouse" class="form-control">
                        <option value="">All Warehouse</option>
                        @foreach ($warehouse as $w)
                        <option value="{{$w->id}}">{{$w->id}} - {{$w->name}} - {{$w->location}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2 col-sm-12 mt-md-0 mt-2 pl-md-2 px-0">
                <select id="filterBulan" class="form-control">
                    <option value="">All Months</option>
                    <option value="01">Januari</option>
                    <option value="02">Februari</option>
                    <option value="03">Maret</option>
                    <option value="04">April</option>
                    <option value="05">Mei</option>
                    <option value="06">Juni</option>
                    <option value="07">Juli</option>
                    <option value="08">Agustus</option>
                    <option value="09">September</option>
                    <option value="10">Oktober</option>
                    <option value="11">November</option>
                    <option value="12">Desember</option>
                </select>
            </div>
            <div class="col-md-2 col-sm-12 mt-md-0 mt-2 pl-md-2 px-0">
                <select id="filterTahun" class="form-control">
                    @for($i=0;$i<=$yearLength;$i++)
                        <option value="{{$yearLoop}}" <?php echo $yearLoop==$yearNow ? "selected" : "" ?>>{{$yearLoop}}</option>
                        <?php $yearLoop++; ?>
                    @endfor
                </select>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            @if(Auth::user()->dashboard_pendapatan_board)
            <div class="col-lg-{{$colBoard2}} col-12">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3 id="totalPendapatan">-</h3>
                        <p>Total Pendapatan</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-money-bill-wave" style="color: rgb(255,255,255,0.2);"></i>
                    </div>
                    <div class="small-box-footer">
                        <div id="totalPendapatanInd">Individual : Rp.-</div>
                        <div id="totalPendapatanCor">Corporate : Rp.-</div>
                    </div>
                </div>
            </div>
            @endif
            @if(Auth::user()->dashboard_paid_board)
            <div class="col-lg-{{$colBoard2}} col-12">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3 id="totalPaid">-</h3>
                        <p>Total PAID</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-cash-register" style="color: rgb(0,0,0,0.2);"></i>
                    </div>
                    <div class="small-box-footer" style="padding:15px 10px;color:black">
                        <div id="totalUnpaid">UNPAID : Rp.-</div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        <div class="row">
            @if(Auth::user()->dashboard_berat_board)
            <div class="col-lg-{{$colBoard}} col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3 id="totalBerat">- Kg</h3>
                        <p>Total Berat</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-weight-hanging" style="color: rgb(255,255,255,0.2);"></i>
                    </div>
                    <div class="small-box-footer">
                        <div id="totalBeratInd">Individual : - Kg</div>
                        <div id="totalBeratCor">Corporate : - Kg</div>
                    </div>
                </div>
            </div>
            @endif
            @if(Auth::user()->dashboard_cbm_board)
            <div class="col-lg-{{$colBoard}} col-6">
                <div class="small-box bg-orange" style='color:#fff!important'>
                    <div class="inner">
                        <h3 id="totalCbm">- CBM</h3>
                        <p>Total CBM</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-box-open" style="color: rgb(255,255,255,0.2);"></i>
                    </div>
                    <div class="small-box-footer">
                        <div id="totalCbmInd">Individual : - CBM</div>
                        <div id="totalCbmCor">Corporate : - CBM</div>
                    </div>
                </div>
            </div>
            @endif
            @if(Auth::user()->dashboard_cust_board)
            <div class="col-lg-{{$colBoard}} col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3 id="totalCustomer">-</h3>
                        <p>Total Customer</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-friends" style="color: rgb(255,255,255,0.2);"></i>
                    </div>
                    <div class="small-box-footer">
                        <div id="totalCustomerInd">Individual : -</div>
                        <div id="totalCustomerCor">Corporate : -</div>
                    </div>
                </div>
            </div>
            @endif
            @if(Auth::user()->dashboard_diskon_board)
            <div class="col-lg-{{$colBoard}} col-6">
                <div class="small-box bg-cyan">
                    <div class="inner">
                        <h3 id="totalDiskon">-</h3>
                        <p>Total Diskon</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-tags" style="color: rgb(0,0,0,0.2);"></i>
                    </div>
                    <a class="small-box-footer" href="#" id="diskonlist" link="{{url('/diskonlist')}}">
                        <div id="totalDiskonInd">Individual : Rp.-</div>
                        <div id="totalDiskonCor">Corporate : Rp.-</div>
                    </a>
                </div>
            </div>
            @endif
        </div>

        <div class="row">
            @if(Auth::user()->dashboard_berat_chart)
            <div class="col-md-12 col-lg-{{$colChart}}">
                <div class="card">
                    <div class="card-header border-0 bg-gradient-info">
                        <h3 class="card-title">
                            <i class="fas fa-th mr-1"></i>
                            <div id="titleTotalBeratCust">Total Berat & Customer | Tahun {{date("Y")}}</div>
                        </h3>

                        <div class="card-tools">
                            <button type="button" class="btn bg-info btn-sm" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body card-body-beratCustChart">
                        <canvas class="chart" id="beratCustChart"></canvas>
                    </div>
                </div>
            </div>
            @endif
            @if(Auth::user()->dashboard_pendapatan_chart)
            <div class="col-md-12 col-lg-{{$colChart}}">
                <div class="card">
                    <div class="card-header border-0 bg-gradient-warning">
                        <h3 class="card-title">
                            <i class="fas fa-th mr-1"></i>
                            <div id="titleTotalPendapatan">Total Pendapatan | {{date("Y")}}<div>
                        </h3>

                        <div class="card-tools">
                            <button type="button" class="btn bg-warning btn-sm" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body card-body-pendapatanChart">
                        <canvas class="chart" id="pendapatanChart"></canvas>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>

<div id="btnToTop"><i class='fas fa-arrow-up'></i></div>
<script>
    let paidStat = {{Auth::user()->dashboard_pendapatan_board}};
</script>
<script src="{{url('assets/customs/js/dashboard.js?v='.env('APP_VERSION'))}}"></script>