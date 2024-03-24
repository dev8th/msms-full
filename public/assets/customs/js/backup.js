$("#formBackup #customer").select2();

$(".row-export,.row-warehouse,.row-customer,.row-paymentStatus,.row-reference,.row-filterCustomer").hide();

$("#formBackup #tipeCustomer").on("change",function(){
    $(".row-export").show();
    $(".row-warehouse,.row-customer").hide();
    $("#jenisExport").val();
    $("#jenisExport").attr("required",true);
});

$("#formBackup #jenisExport").on("change",function(){
    let value = $(this).val();
    if(value=="BW"){
        $(".row-warehouse").show();
        $(".row-customer,.row-reference,.row-paymentStatus,.row-filterCustomer").hide();
        $("#warehouse").attr("required",true);
        $("#customer,#reference,#paymentStatus,#filterCustomer").attr("required",false);
        $("#warehouse,#customer,#reference,#filterCustomer").val("");
    }else if(value=="BR"){
        $(".row-reference,.row-paymentStatus,.row-filterCustomer").show();
        $(".row-customer,.row-warehouse").hide();
        $("#reference,#paymentStatus,#filterCustomer").attr("required",true);
        $("#customer,#warehouse").attr("required",false);
        $("#warehouse,#customer,#reference,#filterCustomer").val("");
    }else{
        let custTypeId = $("#tipeCustomer").val();
        $.ajax({
            type: "GET",
            url: location.origin+"/check/getcustlist",
            data: {
                custTypeId: custTypeId,
            },
            success: function(msg) {
                var json = JSON.parse(msg);
                $(".row-customer").show();
                $(".row-warehouse,.row-reference,.row-paymentStatus,.row-filterCustomer").hide();

                $("#customer").html(json.data);

                $("#warehouse,#customer,#reference").val("");
                
                $("#warehouse,#reference,#paymentStatus,#filterCustomer").attr("required",false);
                $("#customer").attr("required",true);
            }
        });
    }
});

$('#formBackup #tanggalAwal,#formBackupOps #tanggalAwal').daterangepicker({
    drops:'up',
    singleDatePicker: true,
    showDropdowns: true,
    autoApply:true,
    locale: {
        format: 'DD-MM-YYYY'
    }
});

$('#formBackup #tanggalAkhir,#formBackupOps #tanggalAkhir').daterangepicker({
    drops:'up',
    singleDatePicker: true,
    showDropdowns: true,
    autoApply:true,
    locale: {
        format: 'DD-MM-YYYY'
    }
});

$('#formBackup #tanggalAwal,#formBackupOps #tanggalAwal').on('apply.daterangepicker', function() {
    minDate = $('#formBackup #tanggalAwal,#formBackupOps #tanggalAwal').val();
    $('#formBackup #tanggalAkhir,#formBackupOps #tanggalAkhir').val(minDate);

    $('#formBackup #tanggalAkhir,#formBackupOps #tanggalAkhir').daterangepicker({
        drops:'up',
        singleDatePicker: true,
        showDropdowns: true,
        autoApply:true,
        minDate: minDate,
        locale: {
            format: 'DD-MM-YYYY'
        }
    });
});

$('#formBackup #tanggalAwal,#formBackupOps #tanggalAwal').on('hide.daterangepicker', function() {
    minDate = $('#formBackup #tanggalAwal,#formBackupOps #tanggalAwal').val();
    $('#formBackup #tanggalAkhir,#formBackupOps #tanggalAkhir').val(minDate);

    $('#formBackup #tanggalAkhir,#formBackupOps #tanggalAkhir').daterangepicker({
        drops:'up',
        singleDatePicker: true,
        showDropdowns: true,
        autoApply:true,
        minDate: minDate,
        locale: {
            format: 'DD-MM-YYYY'
        }
    });
});

$("#formBackup").validate({
    errorClass: "error fail-alert is-invalid",
    messages: {
        tipeCustomer: "Pilih Salah Satu",
        jenisExport: "Pilih Salah Satu",
        warehouse: "Pilih Salah Satu",
        reference: "Pilih Salah Satu",
        paymentStatus: "Pilih Salah Satu",
        filterCustomer: "Pilih Salah Satu",
        customer: "Pilih Salah Satu"
    },
    submitHandler: function(form) {

        let tipeCustomer = $("#"+form.id+" #tipeCustomer").val(),
            jenisExport = $("#"+form.id+" #jenisExport").val(),
            idWarehouse = $("#"+form.id+" #warehouse").val(),
            idCustomer = $("#"+form.id+" #customer").val(),
            reference = $("#"+form.id+" #reference").val(),
            filterCustomer = $("#"+form.id+" #filterCustomer").val(),
            paymentStatus = $("#"+form.id+" #paymentStatus").val(),
            tanggalAwal = $("#"+form.id+" #tanggalAwal").val(),
            tanggalAkhir = $("#"+form.id+" #tanggalAkhir").val();

        window.open(
            location.origin+'/backup/export?tipecustomer='+tipeCustomer+'&reference='+reference+'&paymentStatus='+paymentStatus+'&jenisexport='+jenisExport+'&idwarehouse='+idWarehouse+'&idcustomer='+idCustomer+'&filtercustomer='+filterCustomer+'&tanggalawal='+tanggalAwal+'&tanggalakhir='+tanggalAkhir,
            '_blank'
        );    

        // $("#"+form.id).reset();
        $(".row-export,.row-warehouse,.row-customer,.row-reference,.row-paymentStatus,.row-filterCustomer").hide();
        $("#"+form.id+" #tipeCustomer,#"+form.id+" #warehouse,#"+form.id+" #customer,#"+form.id+" #reference,#"+form.id+" #paymentStatus,#"+form.id+" #filterCustomer").val("");

    }

});

$("#formBackupOps").validate({
    errorClass: "error fail-alert is-invalid",
    messages: {
        tipeCustomer: "Pilih Salah Satu",
    },
    submitHandler: function(form) {

        let tipeCustomer = $("#"+form.id+" #tipeCustomer").val(),
            tanggalAwal = $("#"+form.id+" #tanggalAwal").val(),
            tanggalAkhir = $("#"+form.id+" #tanggalAkhir").val();

        window.open(
            location.origin+'/backup/exportops?tipecustomer='+tipeCustomer+'&tanggalawal='+tanggalAwal+'&tanggalakhir='+tanggalAkhir,
            '_blank'
        );   

        $("#"+form.id+" #tipeCustomer").val("");
    }
});