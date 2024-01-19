<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ url('assets/plugins/bootstrap/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{ url('assets/plugins/sweetalert2/sweetalert2.min.css')}}">
    <link rel="stylesheet" href="{{ url('assets/plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{url('assets/customs/css/styleku.css?v='.date('dmyHis'))}}">
    <link rel="stylesheet" href="{{url('assets/customs/css/custom.css?v='.date('dmyHis'))}}">
    <title>Web Form</title>
    
    <style>
        label {
            font-weight: bold;
            margin-top: 5px;
        }
        
        input.form-check-input {
            margin-top: 9px;
        }
        
        form#formOrder {
            padding: 10px;
        }   

    </style>
</head>
<body>
    <div class="row-detail">
        <form id="formOrder">
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
                    <div class="form-check form-switch" style="margin-left:0">
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
                    <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="secondName" id="secondName" placeholder="Full Name" required>
                </div>
                <div class="col-md-6 col-12">
                    <label for="secondPhone">Nomor Telepon</label>
                    <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="secondPhone" id="secondPhone" placeholder="Phone Number" required>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col">
                    <button type="submit" class="btn btn-primary mt-2" style="width:100%">Submit</button>
                </div>
            </div>
        </form>
    </div>

    <script src="{{url('assets/plugins/jquery/jquery.min.js')}}"></script>
    <script src="{{url('assets/plugins/jquery-mask/jquery.mask.min.js')}}"></script>
    <script src="{{url('assets/plugins/jquery-validation/jquery.validate.js')}}"></script>
    <script src="{{url('assets/plugins/jquery-validation/additional-methods.min.js')}}"></script>
    <script src="{{url('assets/plugins/sweetalert2/sweetalert2.min.js')}}"></script>
    <script src="{{url('assets/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{url('assets/customs/js/script.js?v='.env('APP_VERSION'))}}"></script>
    <script src="{{url('assets/customs/js/webform.js?v='.env('APP_VERSION'))}}"></script>
</body>
</html>