<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Create Shipment</h1>
            </div>
            <div class="col-sm-6">
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <form id="formTambahOrder">
            @csrf
            <input type="hidden" name="custTypeId" id="custTypeId">
            <div class="card card-daftar">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="form-group" style="text-align:center;margin-bottom:0">
                                <label for="daftar">Pilih Tipe Customer/Client!</label>
                                <div class="btn-daftar mt-2">
                                    <button type="button" id="IND" class="btn-noselect">Individual</button>
                                    <button type="button" id="COR" class="btn-noselect ml-2">Corporate</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-customer">
                <div class="card-body">
                    <div class="row row-stat-cust">
                        <div class="col">
                            <div class="form-group">
                                <label for="customer">Customer</label>
                                <select name="statusCust" id="statusCust" class="form-control" required>
                                    <option value="" hidden>PILIH STATUS CUSTOMER</option>
                                    <option value="NOREG">BARU</option>
                                    <option value="REG">TERDAFTAR</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row row-reg">
                        <div class="col">
                            <div class="form-group">
                                <label for="Reg">Nama Customer</label>
                                <select name="regCust" id="regCust" class="form-control" style="width:100%"></select>
                            </div>
                        </div>
                    </div>
                    <div class="row-detail">
                        <div class="row">
                            <div class="col">
                                <label for="consLabel">Consignee / Penerima</label>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4 col-12">
                                <label for="firstName">Nama Depan</label>
                                <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="firstName" id="firstName" placeholder="First Name / Nama Depan" required>
                            </div>
                            <div class="col-md-4 col-12">
                                <label for="middleName">Nama Tengah</label>
                                <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="middleName" id="middleName" placeholder="Middle Name / Nama Tengah (Optional)">
                            </div>
                            <div class="col-md-4 col-12">
                                <label for="lastName">Nama Terakhir</label>
                                <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="lastName" id="lastName" placeholder="Last Name / Nama Terakhir (Optional)">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6 col-12">
                                <label for="email">Alamat Email</label>
                                <input type="email" class="form-control" name="email" id="email" placeholder="Email Address" required>
                            </div>
                            <div class="col-md-6 col-12">
                                <label for="phone">Nomor Whatsapp</label>
                                <input type="text" class="form-control" name="phone" id="phone" placeholder="Whatsapp Number" required>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6 col-12">
                                <label for="address">Alamat</label>
                                <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="address" id="address" placeholder="Address" required>
                            </div>
                            <div class="col-md-6 col-12">
                                <label for="subDistrict">Kelurahan</label>
                                <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="subDistrict" id="subDistrict" placeholder="Sub-District">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6 col-12">
                                <label for="district">Kecamatan</label>
                                <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="district" id="district" placeholder="District" required>
                            </div>
                            <div class="col-md-6 col-12">
                                <label for="city">Kabupaten / Kota</label>
                                <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="city" id="city" placeholder="City" required>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6 col-12">
                                <label for="prov">Provinsi</label>
                                <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="prov" id="prov" placeholder="Region" required>
                            </div>
                            <div class="col-md-6 col-12">
                                <label for="postalCode">Kode Pos</label>
                                <input type="text" class="form-control" name="postalCode" id="postalCode" placeholder="Postal Code" required>
                            </div>
                        </div>
                        <div class="row mt-2 row-check">
                            <div class="col">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="sameSender">
                                    <label class="form-check-label" for="sameSender">Data sender sama dengan penerima</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2 row-label-second">
                            <div class="col">
                                <label for="secondLabel">Sender / Pengirim</label>
                            </div>
                        </div>
                        <div class="row mt-2 row-detil-second">
                            <div class="col-md-6 col-12">
                                <label for="secondName">Nama Lengkap</label>
                                <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="secondName" id="secondName" placeholder="Full Name">
                            </div>
                            <div class="col-md-6 col-12">
                                <label for="secondPhone">Nomor Telepon</label>
                                <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="secondPhone" id="secondPhone" placeholder="Phone Number">
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col">
                                <button type="submit" class="btn btn-primary" style="width:100%">Create Shipment</button>
                            </div>
                        </div>
                    </div>
                </div>
        </form>
    </div>
    </form>
    </div>
</section>

<div id="btnToTop"><i class='fas fa-arrow-up'></i></div>

<script src="{{url('assets/customs/js/newship.js?v='.env('APP_VERSION'))}}"></script>