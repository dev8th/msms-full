<section class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6 mb-3">
                <h1>Rp.26.000.000</h1>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <ul class="nav nav-pills" id="navChooseCust" style="width: fit-content;border: 1px solid #ffc107;border-radius:5px">
                    <li class="nav-item">
                        <a class="nav-link active" id="I" data-toggle="tab" href="#nav-individual" role="tab">Individual</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="C" data-toggle="tab" href="#nav-corporate" role="tab">Corporate</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="nav-individual" role="tabpanel">
                                <table class="table table-striped" id="tableInd">
                                    <thead>
                                        <tr>
                                            <th style="width: 10px">#</th>
                                            <th>No. Invoice</th>
                                            <th>No. Order (Doku)</th>
                                            <th>No. Resi</th>
                                            <th>User</th>
                                            <th>Customer</th>
                                            <th>Kontak & Alamat</th>
                                            <th>Jumlah</th>
                                            <th>Rincian Biaya</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div class="tab-pane fade" id="nav-corporate" role="tabpanel">
                                <table class="table table-striped" id="tableCor">
                                    <thead>
                                        <tr>
                                            <th style="width: 10px">#</th>
                                            <th>No. Invoice</th>
                                            <th>No. Order (Doku)</th>
                                            <th>User</th>
                                            <th>Client</th>
                                            <th>Kontak & Alamat</th>
                                            <th>Jumlah</th>
                                            <th>Rincian Biaya</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div id="btnToTop"><i class='fas fa-arrow-up'></i></div>

<script src="{{url('assets/customs/js/diskonlist.js?v='.env('APP_VERSION'))}}"></script>