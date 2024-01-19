$(".row-reg,.row-detail,.row-check,.row-detil-second,.row-label-second").hide();

$("#IND").addClass("btn-select");
$('#custTypeId').val("IND");

$("select[name='regCust']").select2();

$(".btn-daftar button").on("click",function(){
    $("select,input").attr("readonly",false);
    $("select,input").val("");

    let id = $(this).attr('id');
    $('#custTypeId').val(id);

    $(".btn-daftar button").removeClass("btn-select");
    $(this).addClass("btn-select");

    $(".card-customer").show();
    $(".row-reg,.row-detail").hide();
    
    $("label[for='customer']").text("Client");
    $("#statusCust option[value='']").text("Pilih Client");
    if(id=="IND"){
        $("label[for='customer']").text("Customer");
        $("#statusCust option[value='']").text("Pilih Customer");
    }
});

$("#statusCust").on("change",function(){
    let value = $(this).val(),
        custTypeId = $('.btn-select').attr('id');
    if(value=="REG"){
        $.ajax({
            type: "GET",
            url: location.origin+"/check/getcustlist",
            data: {
                custTypeId: custTypeId,
            },
            success: function(msg) {
                var json = JSON.parse(msg);
                $(".row-reg").show();
                $(".row-detail").hide();
                $("select[name='regCust']").attr("required", true);
                $('select[name="regCust"]').html(json.data);

                $("select,input").attr("readonly",false);
                $("select:not(#statusCust),input:not(#custTypeId)").val("");
                
                $("label[for='Reg']").text("Client");
                if(custTypeId=="IND"){
                    $("label[for='Reg']").text("Customer");
                }
            }
        });
    }else{
        $(".row-reg").hide();
        $(".row-detail").show();

        if(custTypeId=="IND"){
            $('.row-check').show();
            $(".row-label-second,.row-detil-second").show();
            $('#secondName,#secondPhone').attr("required",true).attr("readonly",false).val("");
            $('label[for="consLabel"]').text('Consignee / Penerima');
        }else{
            $('.row-check').hide();
            $(".row-label-second,.row-detil-second").hide();
            $('#secondName,#secondPhone').attr("required",false).attr("readonly",false).val("");
            $('label[for="consLabel"]').text('Sender / Pengirim');
        }

        $("select[name='regCust']").attr("required", false);
        $("select,input").attr("readonly",false);
        $("select:not(#statusCust),input:not(#custTypeId)").val("");
    }    
});

$('#regCust').on('change',function(){
    let id = $(this).val();
    $.ajax({
        type: "GET",
        url: location.origin+"/check/getcustdata",
        data: {
            id: id,
        },
        success: function(msg) {
            var json = JSON.parse(msg);
            $(".row-detail").show();

            if(json.custTypeId=="IND"){
                $('.row-check').show();
                $(".row-label-second,.row-detil-second").show();
                $('#secondName,#secondPhone').attr("required",true).attr("readonly",false).val("");
                $('label[for="consLabel"]').text('Consignee / Penerima');
            }else{
                $('.row-check').hide();
                $(".row-label-second,.row-detil-second").hide();
                $('#secondName,#secondPhone').attr("required",false).attr("readonly",false).val("");
                $('label[for="consLabel"]').text('Sender / Pengirim');
            }

            $("#firstName").val(json.firstName).attr("readonly",true);
            $("#middleName").val(json.middleName).attr("readonly",true);
            $("#lastName").val(json.lastName).attr("readonly",true);
            $("#phone").val(json.phone).attr("readonly",true);
            $("#email").val(json.email).attr("readonly",true);
            $("#address").val(json.address).attr("readonly",true);
            $("#subDistrict").val(json.subDistrict).attr("readonly",true);
            $("#district").val(json.district).attr("readonly",true);
            $("#city").val(json.city).attr("readonly",true);
            $("#prov").val(json.prov).attr("readonly",true);
            $("#postalCode").val(json.postalCode).attr("readonly",true);
        }
    });
});

$("input[name='sameSender']").on("click",function(){
    if($(this).is(":checked")){
        let fullName = $("#middleName").val()==""?$("#firstName").val()+" "+$("#lastName").val():$("#firstName").val()+" "+$("#middleName").val()+" "+$("#lastName").val();
        $(this).val("S");
        $('#secondName').attr("required",false).attr("readonly",true).val(fullName);
        $('#secondPhone').attr("required",false).attr("readonly",true).val($("#phone").val());
    }else{
        $(this).val("NS");
        $('#secondName,#secondPhone').attr("required",true).attr("readonly",false).val("");
    }
    $(".row-label-second,.row-detil-second").show();
});

$("#formTambahOrder").validate({
    errorClass: "error fail-alert is-invalid",
    rules:{
        email:{
            required:true,
            email:true,
            remote: {
                url: location.origin+"/check/checkphoneandemail",
                type: "GET",
                data: {
                    email: function() {
                        return $("#formTambahOrder #email").val();
                    },
                    statusCust:function(){
                        return $("#formTambahOrder #statusCust").val();
                    }
                } 
            },
        },
        phone:{
            required:true,
            remote: {
                url: location.origin+"/check/checkphoneandemail",
                type: "GET",
                data: {
                    phone: function() {
                        return $("#formTambahOrder #phone").val();
                    },
                    statusCust:function(){
                        return $("#formTambahOrder #statusCust").val();
                    }
                } 
            },
        },
    },
    messages: {
        firstName: "Tidak Boleh Kosong",
        // lastName: "Tidak Boleh Kosong",
        email: {
            required: "Tidak Boleh Kosong",
            email: "Format Email Salah",
            remote: "Email Sudah Terdaftar"
        },
        phone: {
            required:"Tidak Boleh Kosong",
            remote: "Telpon Sudah Terdaftar"
        },
        address: "Tidak Boleh Kosong",
        // subDistrict: "Tidak Boleh Kosong",
        district: "Tidak Boleh Kosong",
        city: "Tidak Boleh Kosong",
        prov: "Tidak Boleh Kosong",
        postalCode: "Tidak Boleh Kosong",
        secondName: "Tidak Boleh Kosong",
        secondPhone: "Tidak Boleh Kosong",
    },
    submitHandler: function(form) {

        $.ajax({
            type: "POST",
            url: location.origin+"/newship/buat",
            data: $(form).serialize(),
            beforeSend: function() {
                loading(form);
            },
            success: function(msg) {
                var json = JSON.parse(msg);

                unLoading(form);

                if (json.status == "Berhasil") {

                    Swal.fire(json.status, json.text, 'success');
                    pageReload(location.origin+"/newship");

                } else {

                    Swal.fire(json.status, json.text, 'error');

                }
            }

        });

    }

});