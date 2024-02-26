// setTimeout(function(){
//     tdShowHide("IND");
// }, 500);

$('#tambahBtn').on('click', function() {
    let choosenCust = $('#navChooseCust .active').attr('id');

    $('#addCustomer .modal-title').html('Add Client Corporate');
    $('#addCustomer #choosenCustID').val('COR');
    if(choosenCust=="I"){
        $('#addCustomer .modal-title').html('Add Customer Individual');
        $('#addCustomer #choosenCustID').val('IND');
    }
    $('#addCustomer').modal('show');
    resetError($('#formTambahCustomer')[0]);
});

$('table').on('click','#editBtn', function() {
    let id = $(this).attr("data-id"),
        firstName= $(this).attr("data-firstName"),
        middleName= $(this).attr("data-middleName"),
        lastName= $(this).attr("data-lastName"),
        phone = $(this).attr("data-phone"),
        email = $(this).attr("data-email"),
        address = $(this).attr("data-address"),
        subDistrict = $(this).attr("data-subDistrict"),
        district = $(this).attr("data-district"),
        city = $(this).attr("data-city"),
        prov = $(this).attr("data-prov"),
        postalCode = $(this).attr("data-postalCode"),
        reference = $(this).attr("data-reference"),
        
        modal = "editCustomer";

    $("#"+modal+" .modal-title").html("Edit Customer "+firstName);    

    $("#"+modal+" #custId").val(id);
    $("#"+modal+" #firstName").val(firstName);
    $("#"+modal+" #middleName").val(middleName);
    $("#"+modal+" #lastName").val(lastName);
    $("#"+modal+" #phone").val(phone);
    $("#"+modal+" #phoneOld").val(phone);
    $("#"+modal+" #email").val(email);
    $("#"+modal+" #emailOld").val(email);
    $("#"+modal+" #address").val(address);
    $("#"+modal+" #subDistrict").val(subDistrict);
    $("#"+modal+" #district").val(district);
    $("#"+modal+" #city").val(city);
    $("#"+modal+" #prov").val(prov);
    $("#"+modal+" #postalCode").val(postalCode);
    $("#"+modal+" #reference").val(reference);

    $('#' + modal).modal('show');
});

$('#navChooseCust .nav-link').on("click",function(){
//     if($(this).hasClass('active')){
//         return false;
//     }    
    let id = $(this).attr('id'),
        url = location.origin+"/custlist/export/";

//     $('#navChooseCust .nav-link').removeClass('active');
//     $(this).addClass('active');
    
    if(id=="C"){
        $("h1").html("Client List");
        $("#exportBtn").attr('href',url+"corporate");
        $("#tambahBtn").html("<i class='fas fa-user-plus'></i> Add Client");
        // custTypeId="COR";
        // $("#table tr th:nth-child(6)").show();
        // tableCor.ajax.url(location.origin+"/custlist/table/"+custTypeId).load();
    }else{
        $("h1").html("Customer List");
        $("#exportBtn").attr('href',url+"individual");
        $("#tambahBtn").html("<i class='fas fa-user-plus'></i> Add Customer");
        // custTypeId = "IND";
        // $("#table tr th:nth-child(6)").hide();
        // tableInd.ajax.url(location.origin+"/custlist/table/"+custTypeId).load();
    }

//     setTimeout(function(){
//         tdShowHide(custTypeId);
//     }, 500);
});

$('table').on('click','.applyBtn',function(){
    let custTypeId = $("#navChooseCust .active").attr('id'),
        id = $(this).attr("data-id"),
        name = $(this).attr("data-name");

    Swal.fire({
        icon: "question",
        title: "Apakah anda yakin akan menerapkan '"+name+"' sebagai customer anda?",
        showCancelButton: true,
        confirmButtonText: "Ya Terapkan",
        reverseButtons: true,
      }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: location.origin+"/custlist/submitref",
                data: {
                    "id" : id,
                },
                success:function(msg){
                    let json = JSON.parse(msg);

                    if(json.status=="Berhasil"){
                        Swal.fire(json.status, json.text, 'success');

                        refreshTable(tableInd,location.origin+"/custlist/table/IND","tableInd_info");
                        refreshTable(tableCor,location.origin+"/custlist/table/COR","tableCor_info");
                    }else{
                        Swal.fire(json.status, json.text, 'error');
                    }
                }
            });
        } 
      });
});

$("#formTambahCustomer").validate({
    errorClass: "error fail-alert is-invalid",
    rules: {
        email: {
            // required:true,
            email:true,
            // remote: {
            //     url: location.origin+"/check/checkphoneandemail",
            //     type: "GET",
            //     data: {
            //         email: function() {
            //             return $("#formTambahCustomer #email").val();
            //         },
            //         statusCust:function(){
            //             return $("#formTambahCustomer #statusCust").val();
            //         }
            //     } 
            // },
        },
        phone: {
            required:true,
            remote: {
                url: location.origin+"/check/checkphoneandemail",
                type: "GET",
                data: {
                    phone: function() {
                        return $("#formTambahCustomer #phone").val();
                    },
                    statusCust:function(){
                        return $("#formTambahCustomer #statusCust").val();
                    }
                } 
            },
        },
    },
    messages: {
        firstName: "Tidak Boleh Kosong",
        // lastName: "Tidak Boleh Kosong",
        email: {
            required:"Tidak Boleh Kosong",
            email:"Format Email Salah",
            remote:"Email Sudah Terdaftar"
        },
        phone: {
            required:"Tidak Boleh Kosong",
            remote:"Telpon Sudah Terdaftar"
        },
        address: "Tidak Boleh Kosong",
        // subDistrict: "Tidak Boleh Kosong",
        district: "Tidak Boleh Kosong",
        city: "Tidak Boleh Kosong",
        prov: "Tidak Boleh Kosong",
        postalCode: "Tidak Boleh Kosong",
    },
    submitHandler: function(form) {

        $.ajax({
            type: "POST",
            url: location.origin+"/custlist/tambah",
            data: $(form).serialize(),
            beforeSend: function() {
                loading(form);
            },
            success: function(msg) {
                var json = JSON.parse(msg);

                unLoading(form);

                if (json.status == "Berhasil") {

                    //NOTIF SUKSES
                    Swal.fire(json.status, json.text, 'success');

                    refreshTable(tableInd,location.origin+"/custlist/table/IND","tableInd_info");
                    refreshTable(tableCor,location.origin+"/custlist/table/COR","tableCor_info");

                    //RELOAD PAGE
                    // pageReload(location.origin+"/custlist");

                } else {

                    //NOTIF GAGAL
                    Swal.fire(json.status, json.text, 'error');

                }

                $("body").css("padding-right", "0");
            }

        });

    }

});

$("#formEditCustomer").validate({
    errorClass: "error fail-alert is-invalid",
    rules: {
        firstName: "required",
        // lastName: "required",
        email: {
            // required:true,
            email:true,
            // remote: {
            //     url: location.origin+"/check/checkphoneandemail",
            //     type: "GET",
            //     data: {
            //         email: function() {
            //             return $("#formEditCustomer #email").val();
            //         },
            //         statusCust:function(){
            //             return $("#formEditCustomer #statusCust").val();
            //         },
            //         emailOld: function() {
            //             return $("#formEditCustomer #emailOld").val();
            //         },
            //     } 
            // },
        },
        phone: {
            required:true,
            remote: {
                url: location.origin+"/check/checkphoneandemail",
                type: "GET",
                data: {
                    phone: function() {
                        return $("#formEditCustomer #phone").val();
                    },
                    statusCust:function(){
                        return $("#formEditCustomer #statusCust").val();
                    },
                    phoneOld: function() {
                        return $("#formEditCustomer #phoneOld").val();
                    },
                } 
            },
        },
        address: "required",
        // subDistrict: "required",
        district: "required",
        city: "required",
        prov: "required",
        postalCode: "required",
    },
    messages: {
        firstName: "Tidak Boleh Kosong",
        // lastName: "Tidak Boleh Kosong",
        email: {
            required:"Tidak Boleh Kosong",
            email:"Format Email Salah",
            remote:"Email Sudah Terdaftar"
        },
        phone: {
            required:"Tidak Boleh Kosong",
            remote:"Telpon Sudah Terdaftar"
        },
        address: "Tidak Boleh Kosong",
        // subDistrict: "Tidak Boleh Kosong",
        district: "Tidak Boleh Kosong",
        city: "Tidak Boleh Kosong",
        prov: "Tidak Boleh Kosong",
        postalCode: "Tidak Boleh Kosong",
    },
    submitHandler: function(form) {

        $.ajax({
            type: "POST",
            url: location.origin+"/custlist/edit",
            data: $(form).serialize(),
            beforeSend: function() {
                loading(form);
            },
            success: function(msg) {
                var json = JSON.parse(msg);

                unLoading(form);

                if (json.status == "Berhasil") {

                    //NOTIF SUKSES
                    Swal.fire(json.status, json.text, 'success');

                    refreshTable(tableInd,location.origin+"/custlist/table/IND","tableInd_info");
                    refreshTable(tableCor,location.origin+"/custlist/table/COR","tableCor_info");

                    //RELOAD PAGE
                    // pageReload(location.origin+"/custlist");

                } else {

                    //NOTIF GAGAL
                    Swal.fire(json.status, json.text, 'error');

                }
            }

        });

    }

});

var tableInd = $('#tableInd').DataTable({
    "paging": true,
    "searching": true,
    // "responsive": true,
    "processing": true,
    "serverSide": true,
    "processing": true,
    "serverSide": true,
    "order": [],
    "ajax": {
        "url": location.origin+"/custlist/table/IND",
        "type": "GET",
        "dataSrc": function(json){
            $("table").parent().css("overflow-x","auto");
            return json.data;
        }
    },
    "columnDefs": [{
        "targets": [],
        "orderable": true,
    }],
    "fixedHeader": false,
    "ordering": true,
    "info": true,
    "autoWidth": true,
    "lengthChange": true,
    "pageLength": pageLength,
    "language": {
        "info": dt_info,
        "infoEmpty": dt_info_empty,
        "infoFiltered": dt_info_filter,
        "search": dt_search_label,
        "searchPlaceholder": dt_search_placeholder,
        "zeroRecords": dt_zero_data,
        "thousands": dt_thousands,
        "processing": dt_processing,
    }
});

var tableCor = $('#tableCor').DataTable({
    "paging": true,
    "searching": true,
    // "responsive": true,
    "processing": true,
    "serverSide": true,
    "processing": true,
    "serverSide": true,
    "order": [],
    "ajax": {
        "url": location.origin+"/custlist/table/COR",
        "type": "GET",
        "dataSrc": function(json){
            $("table").parent().css("overflow-x","auto");
            return json.data;
        }
    },
    "columnDefs": [{
        "targets": [],
        "orderable": true,
    }],
    "fixedHeader": false,
    "ordering": true,
    "info": true,
    "autoWidth": true,
    "lengthChange": true,
    "pageLength": pageLength,
    "language": {
        "info": dt_info,
        "infoEmpty": dt_info_empty,
        "infoFiltered": dt_info_filter,
        "search": dt_search_label,
        "searchPlaceholder": dt_search_placeholder,
        "zeroRecords": dt_zero_data,
        "thousands": dt_thousands,
        "processing": dt_processing,
    }
});

// function tdShowHide(custTypeId){
//     console.log(custTypeId);
//     if(custTypeId=="COR"){
//         $("#table tr th:nth-child(6)").show();
//         $("#table tr td:nth-child(6)").show();
//     }else{
//         $("#table tr th:nth-child(6)").hide();
//         $("#table tr td:nth-child(6)").hide();
//     }
// }