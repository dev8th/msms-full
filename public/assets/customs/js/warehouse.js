$('#tambahBtn').on('click', function() {
    $('#tambahWarehouse').modal('show');
    resetError($('#formTambahWarehouse')[0]);
});

$('table').on('click','#editBtn',function(){
    let id = $(this).attr("data-id"),
        name = $(this).attr("data-name"),
        location = $(this).attr("data-location"),
        modal = "editWarehouse";

    $("#editWarehouse .modal-title").html("Edit Warehouse "+id);    

    $("#editWarehouse #warehouseID").val(id).attr("readonly",true);
    $("#editWarehouse #warehouseIDOld").val(id);
    $("#editWarehouse #warehouseName").val(name);
    $("#editWarehouse #warehouseLoc").val(location);

    $('#' + modal).modal('show');
});

$("#formTambahWarehouse").validate({

    errorClass: "error fail-alert is-invalid",
    rules: {
        warehouseID: {
            required: true,
            minlength: 4,
            remote: {
                url: location.origin+"/warehouse/inputchecking",
                type: "GET",
                data: {
                    warehouseID: function() {
                        return $("#formTambahWarehouse #warehouseID").val();
                    },
                    warehouseIDOld: function() {
                        return $("#formTambahWarehouse #warehouseIDOld").val();
                    },
                }
            },
        },
        // warehouseName: "required",
        warehouseLoc: "required",
    },
    messages: {
        warehouseID: {
            required: "Tidak Boleh Kosong",
            minlength: "Kurang Dari 4 Karakter",
            remote: "ID sudah ada",
        },
        // warehouseName: "Tidak Boleh Kosong",
        warehouseLoc: "Tidak Boleh Kosong",
    },
    submitHandler: function(form) {

        $.ajax({
            type: "POST",
            url: location.origin+"/warehouse/tambah",
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

                    refreshTable(table,location.origin+"/warehouse/table","table_info");

                } else {

                    //NOTIF GAGAL
                    Swal.fire(json.status, json.text, 'error');

                }
            }

        });

    }

});

$("#formEditWarehouse").validate({

    errorClass: "error fail-alert is-invalid",
    rules: {
        warehouseID: {
            required: true,
            minlength: 4,
            remote: {
                url: location.origin+"/warehouse/inputchecking",
                type: "GET",
                data: {
                    warehouseID: function() {
                        return $("#formEditWarehouse #warehouseID").val();
                    },
                    warehouseIDOld: function() {
                        return $("#formEditWarehouse #warehouseIDOld").val();
                    },
                }
            },
        },
        // warehouseName: "required",
        warehouseLoc: "required",
    },
    messages: {
        warehouseID: {
            required: "Tidak Boleh Kosong",
            minlength: "Kurang Dari 4 Karakter",
            remote: "ID sudah ada",
        },
        // warehouseName: "Tidak Boleh Kosong",
        warehouseLoc: "Tidak Boleh Kosong",
    },
    submitHandler: function(form) {

        $.ajax({
            type: "POST",
            url: location.origin+"/warehouse/edit",
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

                    refreshTable(table,location.origin+"/warehouse/table","table_info");

                } else {

                    //NOTIF GAGAL
                    Swal.fire(json.status, json.text, 'error');

                }
                
            }

        });

    }

});

var table = $('#table').DataTable({
    "paging": true,
    "searching": true,
    // "responsive": true,
    "processing": true,
    "serverSide": true,
    "processing": true,
    "serverSide": true,
    "order": [],
    "ajax": {
        "url": location.origin+"/warehouse/table",
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