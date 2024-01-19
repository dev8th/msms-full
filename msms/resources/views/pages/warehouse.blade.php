<section class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6 mb-3">
                <h1>Warehouse List</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    @if(Auth::user()->warelist_export)
                    <a href="{{url('warehouse\export')}}" target="_blank" class="btn bg-gradient-info p-2 mr-1"><i class="fas fa-file-excel"></i> Export Excel</a>
                    @endif
                    <!-- <button type="button" id="kelola-btn" class="btn bg-gradient-info p-2 mr-1"><i class="fas fa-file-excel"></i> Export Excel</button> -->
                    @if(Auth::user()->warelist_buat)
                    <button type="button" id="tambahBtn" class="btn bg-gradient-success"><i class="fas fa-user-plus"></i> Add Warehouse</button>
                    @endif
                </ol>
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
                        <table class="table table-striped" id="table">
                            <thead>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>User</th>
                                    <th>ID & Warehouse</th>
                                    <th>Lokasi</th>
                                    <th>Customer</th>
                                    <th>Service</th>
                                    <th>Invoice</th>
                                    <th>Berat/Item</th>
                                    @if(Auth::user()->warelist_nom)
                                    <th>Total Pendapatan</th>
                                    @endif
                                    @if(Auth::user()->warelist_edit||Auth::user()->warelist_hapus)
                                    <th>Action</th>
                                    @endif
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" data-backdrop="static" id="tambahWarehouse" tabindex="-1" role="dialog" aria-labelledby="addWarehouseLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <form id="formTambahWarehouse">
                        @csrf
                        <input type="hidden" name="warehouseIDOld" id="warehouseIDOld">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Warehouse</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <label for="warehouseID">ID Warehouse</label>
                                    <input type="text" name="warehouseID" id="warehouseID" onkeyup="this.value = this.value.toUpperCase()" class="form-control" required>
                                </div>
                                <div class="col-md-6 col-12">
                                    <label for="warehouseName">Nama Warehouse</label>
                                    <input type="text" name="warehouseName" id="warehouseName" onkeyup="this.value = this.value.toUpperCase()" class="form-control">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col">
                                    <label for="warehouseLoc">Lokasi Warehouse</label>
                                    <input type="text" name="warehouseLoc" id="warehouseLoc" onkeyup="this.value = this.value.toUpperCase()" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Tambahkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" data-backdrop="static" id="editWarehouse" tabindex="-1" role="dialog" aria-labelledby="addWarehouseLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <form id="formEditWarehouse">
                        @csrf
                        <input type="hidden" name="warehouseIDOld" id="warehouseIDOld">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Warehouse</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <label for="warehouseID">ID Warehouse</label>
                                    <input type="text" name="warehouseID" id="warehouseID" onkeyup="this.value = this.value.toUpperCase()" class="form-control" required>
                                </div>
                                <div class="col-md-6 col-12">
                                    <label for="warehouseName">Nama Warehouse</label>
                                    <input type="text" name="warehouseName" id="warehouseName" onkeyup="this.value = this.value.toUpperCase()" class="form-control">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col">
                                    <label for="warehouseLoc">Lokasi Warehouse</label>
                                    <input type="text" name="warehouseLoc" id="warehouseLoc" onkeyup="this.value = this.value.toUpperCase()" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<div id="btnToTop"><i class='fas fa-arrow-up'></i></div>

<script src="{{url('assets/customs/js/warehouse.js?v='.env('APP_VERSION'))}}"></script>