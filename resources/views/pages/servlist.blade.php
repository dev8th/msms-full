<section class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6 mb-3">
                <h1>Service List</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    @if(Auth::user()->servlist_export)
                    <a href="{{url('/servlist/export')}}" target="_blank" class="btn bg-gradient-info p-2 mr-1"><i class="fas fa-file-excel"></i> Export Excel</a>
                    @endif
                    <!-- <button type="button" id="kelola-btn" class="btn bg-gradient-info p-2 mr-1"><i class="fas fa-file-excel"></i> Export Excel</button> -->
                    @if(Auth::user()->servlist_buat)
                    <button type="button" id="tambahBtn" class="btn bg-gradient-success"><i class="fas fa-user-plus"></i> Add Service</button>
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
                                    <th style="width:100px" data-priority="1">User</th>
                                    <th>Service</th>
                                    <th data-priority="3">ID & Warehouse</th>
                                    <th data-priority="4">Lokasi</th>
                                    @if(Auth::user()->servlist_nom)
                                    <th>Harga/Kg</th>
                                    <th>Harga/Item</th>
                                    <th>Harga/Vol</th>
                                    <th>Harga/CBM</th>
                                    @endif
                                    <th>Deskripsi</th>
                                    @if(Auth::user()->servlist_edit||Auth::user()->servlist_hapus)
                                    <th>Action</th>
                                    @endif
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" data-backdrop="static" id="addService" tabindex="-1" role="dialog" aria-labelledby="addServiceLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <form id="formTambahService">
                        @csrf
                        <input type="hidden" name="serviceNameOld" id="serviceNameOld" value="">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Service</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col">
                                    <label for="warehouse">Warehouse</label>
                                    <select name="warehouse" id="warehouse" class="form-control">
                                        <option value="" hidden>Pilih Warehouse</option>
                                        @foreach ($warehouse as $w)
                                        <option value="{{$w->id}}">{{$w->id}} - {{$w->name}} - {{$w->location}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col">
                                    <label for="serviceName">Nama Service</label>
                                    <input type="text" name="serviceName" id="serviceName" onkeyup="this.value = this.value.toUpperCase()" class="form-control">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-3 col-12">
                                    <label for="priceKg">Harga/Kg</label>
                                    <input type="text" name="priceKg" id="priceKg" class="form-control masking" value="0">
                                </div>
                                <div class="col-md-3 col-12">
                                    <label for="priceVol">Harga/Vol</label>
                                    <input type="text" name="priceVol" id="priceVol" class="form-control masking" value="0">
                                </div>
                                <div class="col-md-3 col-12">
                                    <label for="priceItem">Harga/Item</label>
                                    <input type="text" name="priceItem" id="priceItem" class="form-control masking" value="0">
                                </div>
                                <div class="col-md-3 col-12">
                                    <label for="priceItem">Harga/CBM</label>
                                    <input type="text" name="priceCbm" id="priceCbm" class="form-control masking" value="0">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col">
                                    <label for="deskripsi">Deskripsi (Optional)</label>
                                    <textarea name="description" id="description" onkeyup="this.value=this.value.toUpperCase()" cols="10" rows="3" class="form-control"></textarea>
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
        <div class="modal fade" data-backdrop="static" id="editService" tabindex="-1" role="dialog" aria-labelledby="editServiceLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <form id="formEditService">
                        @csrf
                        <input type="hidden" name="serviceNameOld" id="serviceNameOld" value="">
                        <input type="hidden" name="serviceID" id="serviceID" value="">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Service</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col">
                                    <label for="warehouse">Warehouse</label>
                                    <select name="warehouse" id="warehouse" class="form-control">
                                        <option value="" hidden>Pilih Warehouse</option>
                                        @foreach ($warehouse as $w)
                                        <option value="{{$w->id}}">{{$w->id}} - {{$w->name}} - {{$w->location}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col">
                                    <label for="serviceName">Nama Service</label>
                                    <input type="text" name="serviceName" id="serviceName" onkeyup="this.value = this.value.toUpperCase()" class="form-control">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-3 col-12">
                                    <label for="priceKg">Harga/Kg</label>
                                    <input type="text" name="priceKg" id="priceKg" class="form-control masking">
                                </div>
                                <div class="col-md-3 col-12">
                                    <label for="priceVol">Harga/Vol</label>
                                    <input type="text" name="priceVol" id="priceVol" class="form-control masking">
                                </div>
                                <div class="col-md-3 col-12">
                                    <label for="priceItem">Harga/Item</label>
                                    <input type="text" name="priceItem" id="priceItem" class="form-control masking">
                                </div>
                                <div class="col-md-3 col-12">
                                    <label for="priceItem">Harga/CBM</label>
                                    <input type="text" name="priceCbm" id="priceCbm" class="form-control masking" value="0">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col">
                                    <label for="deskripsi">Deskripsi (Optional)</label>
                                    <textarea name="description" id="description" onkeyup="this.value=this.value.toUpperCase()" cols="10" rows="3" class="form-control"></textarea>
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

<script src="{{url('assets/customs/js/servlist.js?v='.env('APP_VERSION'))}}"></script>