<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row row-middle">
            <div class="col-md-6 col-lg-6">
                <form id="formBackup">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Rekap Data</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="tipeCustomer">Tipe Customer</label>
                                        <select name="tipeCustomer" id="tipeCustomer" class="form-control" required>
                                            <option value="" hidden>Pilih Tipe Customer</option>
                                            <option value="IND">Individual</option>
                                            <option value="COR">Corporate</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row row-export">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="jenisExport">Jenis Export</label>
                                        <select name="jenisExport" id="jenisExport" class="form-control">
                                            <option value="" hidden>Pilih Jenis Export</option>
                                            <option value="BW">By Warehouse</option>
                                            <option value="BC">By Customer</option>
                                            <option value="BR">By Reference</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row row-warehouse">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="warehouse">Warehouse</label>
                                        <select name="warehouse" id="warehouse" class="form-control">
                                            <option value="" hidden>Pilih Warehouse</option>
                                            @foreach ($warehouse as $w)
                                            <option value="{{$w->id}}">{{$w->id}} - {{$w->name}} - {{$w->location}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row row-customer">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="customer">Customer</label>
                                        <select name="customer" id="customer" class="form-control" style="width:100%">
                                            <option value="" hidden>Pilih Customer</option>
                                            <option value="0000001">Yusuf</option>
                                            <option value="0000002">Pujo</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row row-reference">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="reference">Reference</label>
                                        <select name="reference" id="reference" class="form-control" style="width:100%">
                                            <option value="" hidden>Pilih Reference</option>
                                            @foreach ($marketing as $m)
                                                <option value="{{$m->id}}">{{$m->fullname}} - {{$m->username}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row row-paymentStatus">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="paymentStatus">Payment Status</label>
                                        <select name="paymentStatus" id="paymentStatus" class="form-control" style="width:100%">
                                            <option value="" hidden>Pilih Payment Status</option>
                                            <option value="ALL">All Status</option>
                                            <option value="PAID">PAID</option>
                                            <option value="UNPAID">UNPAID</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Tanggal</label>
                                        <div class="form-input">
                                            <input type="text" class="form-control" id="tanggalAwal" name="tanggalAwal" required>
                                            <span class="date-span"><i class="fas fa-arrow-right"></i></span>
                                            <input type="text" class="form-control" id="tanggalAkhir" name="tanggalAkhir" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col text-right">
                                    <!-- <a href="{{url('/backup/export/')}}" target="_blank" class="btn btn-primary">Download Excel</a> -->
                                    <button type="submit" class="btn btn-primary">Download Excel</button>
                                </div>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script src="{{ url('assets/customs/js/backup.js?v='.env('APP_VERSION')) }}"></script>