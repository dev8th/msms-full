hideSomeElements();

$("#IND").addClass("btn-select");
$(".row-filter,.card-table").show();

$("#navTabOrder li a").first().addClass("active");
$("#nav-tabContent .tab-pane").first().addClass("show active");
if($("#nav-ci-tab").length==0){
    $(".row-filterTwo").show();
}
$("#navTabOrder li a").length==1?$("#navTabOrder li a").hide():'';

$("select[name='filterWarehouse'],select[name='filterService']").select2();

$(".btn-daftar button").on("click",function(){  
    let custTypeId = $(this).attr("id");
    $(".btn-daftar button").removeClass("btn-select");
    $(this).addClass("btn-select");
    $(".row-filter,.card-table").show();
    
    $("#table-ci th:nth-child(3)").text("Client");
    $("#table-ct th:nth-child(5)").text("Client");
    $("#table-status th:nth-child(6)").text("Client");
    $("#createInvoice #btnAddServiceElement").html("<i class='fas fa-plus'></i> Add Customer");
    $("#editInvoice #btnAddServiceElement").html("<i class='fas fa-plus'></i> Add Customer");
    $("input[name='checkAll']").hide();
    if(custTypeId=="IND"){
        $("input[name='checkAll']").show();
        $("#table-ci th:nth-child(3)").text("Customer");
        $("#table-ct th:nth-child(5)").text("Customer");
        $("#table-status th:nth-child(6)").text("Customer");
        $("#createInvoice #btnAddServiceElement").html("<i class='fas fa-plus'></i> Add Service");
        $("#editInvoice #btnAddServiceElement").html("<i class='fas fa-plus'></i> Add Service");
    }

    refreshTable(tableCi,location.origin+"/shiplist/table/order/"+custTypeId+"?filterTanggal=","table-ci_info");
    refreshTable(tableCt,location.origin+"/shiplist/table/invoice/"+custTypeId+"?filterTanggalAwal=&filterTanggalAkhir=&filterWarehouse=&filterService=","table-ct_info");
    refreshTable(tableSt,location.origin+"/shiplist/table/tracking/"+custTypeId+"?mismassOrderId=&filterTanggalAwal=&filterTanggalAkhir=&filterWarehouse=&filterService=","table-status_info");
});

$("#filterWarehouse").on("change",function(){
    let id = $(this).val();
    $.ajax({
        type: "GET",
        url: location.origin+"/check/getservlist?inv=false",
        data: {
            warehouseId:id,
        },
        success: function(msg) {
            let json = JSON.parse(msg);
            $(".col-filterService").show();
            $("select[name='filterService']").html("");
            $("select[name='filterService']").html(json.data);
        }
    });
});

$("#navTabOrder .nav-item a").on("click",function(){
    let id = $(this).attr('id'),
        custTypeId = $(".btn-select").attr("id");
    if(id=="nav-ci-tab"){
        $(".row-filterOne").show();
        $(".row-filterTwo").hide();
        refreshTable(tableCi,location.origin+"/shiplist/table/order/"+custTypeId+"?filterTanggal=","table-ci_info");
    }else{
        $(".row-filterTwo").show();
        $(".row-filterOne").hide();
        $("select[name='filterWarehouse']").val("");
        $("select[name='filterService']").val("");
        if(id=="nav-ct-tab"){
            refreshTable(tableCt,location.origin+"/shiplist/table/invoice/"+custTypeId+"?filterTanggalAwal=&filterTanggalAkhir=&filterWarehouse=&filterService=","table-ct_info");
        }else{
            refreshTable(tableSt,location.origin+"/shiplist/table/tracking/"+custTypeId+"?mismassOrderId=&filterTanggalAwal=&filterTanggalAkhir=&filterWarehouse=&filterService=","table-status_info");
        }
    }
});

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$('#filterTanggalOrder,#filterTanggalAwal,#filterTanggalAkhir,#tanggalInvoice').daterangepicker({
    singleDatePicker: true,
    autoApply:true,
    locale: {
        format: 'DD-MM-YYYY'
    },
});

// $('#filterTanggalAwal').on('apply.daterangepicker', function() {
//     minDate = $('#filterTanggalAwal').val();
//     $('#filterTanggalAkhir').val(minDate);

//     $('#filterTanggalAkhir').daterangepicker({
//         singleDatePicker: true,
//         autoApply:true,
//         showDropdowns: true,
//         minDate: minDate,
//         locale: {
//             format: 'DD-MM-YYYY'
//         }
//     });
// });

// $('#filterTanggalAwal').on('hide.daterangepicker', function() {
//     minDate = $('#filterTanggalAwal').val();
//     $('#filterTanggalAkhir').val(minDate);

//     $('#filterTanggalAkhir').daterangepicker({
//         singleDatePicker: true,
//         autoApply:true,
//         showDropdowns: true,
//         minDate: minDate,
//         locale: {
//             format: 'DD-MM-YYYY'
//         }
//     });
// });

$("#viewOne").on("click",function(){
    let filter = $("#filterTanggalOrder").val(),
        custTypeId = $(".btn-select").attr("id");
    refreshTable(tableCi,location.origin+"/shiplist/table/order/"+custTypeId+"?filterTanggal="+filter,"table-ci_info");
});

$("#resetOne").on("click",function(){
    let custTypeId = $(".btn-select").attr("id");

    refreshTable(tableCi,location.origin+"/shiplist/table/order/"+custTypeId+"?filterTanggal=","table-ci_info");

    $('#filterTanggalOrder,#filterTanggalAwal,#filterTanggalAkhir,#tanggalInvoice').val(moment().format('DD-MM-YYYY'));
});

$("#viewTwo").on("click",function(){
    let custTypeId = $(".btn-select").attr("id"),
        navTab = $("#navTabOrder .nav-item .active").attr("id"),
        tableType="invoice",
        tableInfo="table-ct_info",
        table=tableCt,
        filterTanggalAwal=$("#filterTanggalAwal").val(),
        filterTanggalAkhir=$("#filterTanggalAkhir").val(),
        filterWarehouse=$("#filterWarehouse").val(),
        filterService=$("#filterService").val();

    if(navTab=="nav-status-tab"){
        tableType="tracking";
        tableInfo="table-status_info";
        table=tableSt;
    }

    refreshTable(table,location.origin+"/shiplist/table/"+tableType+"/"+custTypeId+"?mismassOrderId=&filterTanggalAwal="+filterTanggalAwal+"&filterTanggalAkhir="+filterTanggalAkhir+"&filterWarehouse="+filterWarehouse+"&filterService="+filterService,tableInfo);
});

$("#resetTwo").on("click",function(){
    let custTypeId = $(".btn-select").attr("id"),
        navTab = $("#navTabOrder .nav-item .active").attr("id"),
        tableType="invoice",
        tableInfo="table-ct_info",
        table=tableCt;

    if(navTab=="nav-status-tab"){
        tableType="tracking";
        tableInfo="table-status_info";
        table=tableSt;
    }

    $("#filterWarehouse").val("").trigger('change');
    $("#filterService").val("").trigger('change');
    $('#filterTanggalOrder,#filterTanggalAwal,#filterTanggalAkhir,#tanggalInvoice').val(moment().format('DD-MM-YYYY'));

    refreshTable(table,location.origin+"/shiplist/table/"+tableType+"/"+custTypeId+"?mismassOrderId=&filterTanggalAwal=&filterTanggalAkhir=&filterWarehouse=&filterService=",tableInfo);
});

var searchTurn = 0;
$(".modal").on("keyup","#searching",function(e){
    let num=0,
        array=[],
        modalId = $(this).closest(".modal").attr("id");

    $("#"+modalId+" #searchingScore").hide();
    if($(this).val()!==""){
        $("#"+modalId+" #searchingScore").text("0/0").show();
    }

    if(e.key==="enter" || e.keyCode===13){
        let keyword = $(this).val().toLowerCase();

        $("#"+modalId+" select:visible,#"+modalId+" input:not(#searching):visible, #"+modalId+" .searchAble").each(function(index, value) {
            // console.log($(this));
            let val="";
            
            if($(this).is("input")){
                val = $(this).val().toLowerCase();
            }
            
            if($(this).is("label")){
                val = $(this).text().toLowerCase();
            }
            
            if($(this).is("select")){
                val = $("#"+modalId+" select option[value='"+val+"']").text().toLowerCase();
            }

            if($(this).hasClass("searchAble")){
                val = $(this).text().toLowerCase();
            }

            if(val.match(keyword)){
                array[num]=$(this).attr("id");
                num++;
            }
        });

        if(num==0){
            return false;
        }

        searchTurn++;
        searchTurn>num?searchTurn=1:'';

        $("#"+modalId+" #searchingScore").text(searchTurn+"/"+num).show();

        document.querySelectorAll("#"+modalId+" #"+array[searchTurn-1])[0].scrollIntoView({block:'center',behavior:'smooth'});

        return true;
    }

    searchTurn = 0;
});

$("form").on("keypress",function(e){
    if(e.key==="enter" || e.keyCode===13){
        e.preventDefault();
    }
});

$("form").on("click","#importBtn",function(){
    let modalId = $(this).closest(".modal").attr("id");
    if($("#"+modalId+" #import").get(0).files.length === 0){
        console.log("empty");
    }else{
        let formData = new FormData();
        formData.append("_token",$("input[name='_token']").val());
        formData.append("excel",$("#"+modalId+" #import").prop("files")[0]);
        $.ajax({
            url: location.origin+"/import",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                loading(formData);
            },
            success: function(msg) {
                
            let json = JSON.parse(msg),
                a = 0;

            if(modalId!="createResi"){
                console.log("Length : "+$("#"+modalId+" .services input[name='consFirstName']").length);
                $("#"+modalId+" .services input[name='consFirstName[]']").each(function(){
                    if($(this).val()!=""){
                        a++
                    }
                });
            }

            for(let i=0;i<=json[0].length-1;i++){
                if(modalId=="createResi"){
                    if(i>2){
                        // jika cell ekspedisi kosong
                        if(json[0][i][7]==null||json[0][i][7]==""){
                            $(".preloaderz").hide();
                            Swal.fire({
                                title: "Gagal Import!",
                                text: "Nama Ekspedisi Kosong. Cell "+numToAlp(7)+(i+1)+".",
                                icon: "error"
                            });
                            return false;
                        }

                        // jika cell ekspedisi berisi selain mismass dan ekspedisi lain
                        if(json[0][i][7]!="MISMASS"&&json[0][i][7]!="JNE"&&json[0][i][7]!="J&T"){
                            $(".preloaderz").hide();
                            Swal.fire({
                                title: "Gagal Import!",
                                text: "Nama Ekspedisi Tidak Dikenal. Cell "+numToAlp(7)+(i+1)+".",
                                icon: "error"
                            });
                            return false
                        }

                        // jika cell kurir kosong
                        if(json[0][i][7]=="MISMASS"){
                            if(json[0][i][8]==""){
                            $(".preloaderz").hide();
                                Swal.fire({
                                    title: "Gagal Import!",
                                    text: "Jika Ekspedisi MISMASS, maka Cell Kurir "+numToAlp(8)+(i+1)+" Tidak Boleh Kosong",
                                    icon: "error"
                                });
                                return false
                            }
                        }

                        // jika cell no resi
                        if(json[0][i][7]!="MISMASS"){
                            if(json[0][i][9]==""){
                                $(".preloaderz").hide();
                                Swal.fire({
                                    title: "Gagal Import!",
                                    text: "Jika Ekspedisi Lain, maka Cell Resi "+numToAlp(9)+(i+1)+" Tidak Boleh Kosong",
                                    icon: "error"
                                });
                                return false;
                            }

                            // for(let a=3;a<=json[0].length-1;a++){
                            //     if(a!=i){
                            //         if(json[0][i][9]==json[0][a][9]){
                            //             Swal.fire({
                            //                 title: "Gagal Import!",
                            //                 text: "Cell Resi "+numToAlp(9)+(i+1)+" & "+numToAlp(9)+(a+1)+" Sama",
                            //                 icon: "error"
                            //             });
                            //             return false;
                            //         }
                            //     }
                            // }

                        }
                        
                        if(json[0][i][7]=="MISMASS"){
                            $("#"+modalId+" #"+(a)+" select[name='tipeForwarder[]']").val(json[0][i][7]);
                        }else{
                            $("#"+modalId+" #"+(a)+" select[name='tipeForwarder[]']").val("VENDOR");
                        }
                        $("#"+modalId+" #"+(a)+" select[name='tipeForwarder[]']").trigger("change");

                        $("#"+modalId+" #"+(a)+" select[name='namaForwarder[]']").val(json[0][i][7]);
                        if(json[0][i][7]=="MISMASS"){
                            $("#"+modalId+" #"+(a)+" input[name='namaForwarder[]']").val(json[0][i][8]);
                        }
                        
                        if(json[0][i][7]!="MISMASS"){
                            $("#"+modalId+" #"+(a)+" input[name='noResi[]']").val(json[0][i][9]);
                        }

                        a++;

                    }
                }else{
                    // console.log("Ini Adalah A:"+a);
                    if(i>1){
                        if(json[0][i][1]==null||json[0][i][1]==""){
                            $(".preloaderz").hide();
                            Swal.fire({
                                title: "Gagal Import!",
                                text: "Nama Depan Kosong. Cell "+numToAlp(1)+(i+1)+".",
                                icon: "error"
                            });
                            return false;
                        }

                        // if(json[0][i][2]==null||json[0][i][2]==""){
                        //     $(".preloaderz").hide();
                        //     Swal.fire({
                        //         title: "Gagal Import!",
                        //         text: "Nama Tengah Kosong. Cell "+numToAlp(2)+(i+1)+".",
                        //         icon: "error"
                        //     });
                        //     return false;
                        // }

                        // if(json[0][i][3]==null||json[0][i][3]==""){
                        //     $(".preloaderz").hide();
                        //     Swal.fire({
                        //         title: "Gagal Import!",
                        //         text: "Nama Terakhir Kosong. Cell "+numToAlp(3)+(i+1)+".",
                        //         icon: "error"
                        //     });
                        //     return false;
                        // }

                        // if(json[0][i][4]==null||json[0][i][4]==""){
                        //     $(".preloaderz").hide();
                        //     Swal.fire({
                        //         title: "Gagal Import!",
                        //         text: "Email Kosong. Cell "+numToAlp(4)+(i+1)+".",
                        //         icon: "error"
                        //     });
                        //     return false;
                        // }

                        if(json[0][i][5]==null||json[0][i][5]==""){
                            $(".preloaderz").hide();
                            Swal.fire({
                                title: "Gagal Import!",
                                text: "No Telp Kosong. Cell "+numToAlp(5)+(i+1)+".",
                                icon: "error"
                            });
                            return false;
                        }

                        if(json[0][i][6]==null||json[0][i][6]==""){
                            $(".preloaderz").hide();
                            Swal.fire({
                                title: "Gagal Import!",
                                text: "Alamat Kosong. Cell "+numToAlp(6)+(i+1)+".",
                                icon: "error"
                            });
                            return false;
                        }

                        // if(json[0][i][7]==null||json[0][i][7]==""){
                        //     $(".preloaderz").hide();
                        //     Swal.fire({
                        //         title: "Gagal Import!",
                        //         text: "Kelurahan Kosong. Cell "+numToAlp(7)+(i+1)+".",
                        //         icon: "error"
                        //     });
                        //     return false;
                        // }

                        // if(json[0][i][8]==null||json[0][i][8]==""){
                        //     $(".preloaderz").hide();
                        //     Swal.fire({
                        //         title: "Gagal Import!",
                        //         text: "Kecamatan Kosong. Cell "+numToAlp(8)+(i+1)+".",
                        //         icon: "error"
                        //     });
                        //     return false;
                        // }

                        if(json[0][i][9]==null||json[0][i][9]==""){
                            $(".preloaderz").hide();
                            Swal.fire({
                                title: "Gagal Import!",
                                text: "Kab/Kota Kosong. Cell "+numToAlp(9)+(i+1)+".",
                                icon: "error"
                            });
                            return false;
                        }

                        if(json[0][i][10]==null||json[0][i][10]==""){
                            $(".preloaderz").hide();
                            Swal.fire({
                                title: "Gagal Import!",
                                text: "Provinsi Kosong. Cell "+numToAlp(10)+(i+1)+".",
                                icon: "error"
                            });
                            return false;
                        }

                        if(json[0][i][11]==null||json[0][i][11]==""){
                            $(".preloaderz").hide();
                            Swal.fire({
                                title: "Gagal Import!",
                                text: "Kode Pos Kosong. Cell "+numToAlp(11)+(i+1)+".",
                                icon: "error"
                            });
                            return false;
                        }

                        if($("#"+modalId+" #"+a).length==0){
                            $("#"+modalId+" #btnAddServiceElement").trigger("click");
                        }

                        $("#"+modalId+" #"+a+" input[name='consFirstName[]']").val(json[0][i][1]);
                        $("#"+modalId+" #"+a+" input[name='consMiddleName[]']").val(json[0][i][2]);
                        $("#"+modalId+" #"+a+" input[name='consLastName[]']").val(json[0][i][3]);
                        $("#"+modalId+" #"+a+" input[name='consEmail[]']").val(json[0][i][4]);
                        $("#"+modalId+" #"+a+" input[name='consPhone[]']").val(json[0][i][5]);
                        $("#"+modalId+" #"+a+" input[name='consAddress[]']").val(json[0][i][6]);
                        $("#"+modalId+" #"+a+" input[name='consSubDistrict[]']").val(json[0][i][7]);
                        $("#"+modalId+" #"+a+" input[name='consDistrict[]']").val(json[0][i][8]);
                        $("#"+modalId+" #"+a+" input[name='consCity[]']").val(json[0][i][9]);
                        $("#"+modalId+" #"+a+" input[name='consProv[]']").val(json[0][i][10]);
                        $("#"+modalId+" #"+a+" input[name='consPostalCode[]']").val(json[0][i][11]);

                        if($("#"+modalId+" #"+a+" input[name='consFirstName[]']").val()==""){
                            $("#"+modalId+" #"+a).remove();
                        }

                        if(i<json[0].length-1){
                            $("#"+modalId+" #btnAddServiceElement").trigger("click");
                        }

                        a++;
                    }
                }
            }

            unLoading(formData);
            $("input[name='import']").val("");

            Swal.fire({
                title: "Berhasil Import!",
                text: "Data Berhasil Diimport",
                icon: "success"
            });
            }
        });
    }
});

function numToAlp(num){
    const arr = ["A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z"];
    return arr[num];
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if($('#table-ci').length>0){
var tableCi = $('#table-ci').DataTable({
    "paging": true,
    "searching": true,
    "processing": true,
    "serverSide": true,
    "processing": true,
    "serverSide": true,
    "order": [],
    "ajax": {
        "url": location.origin+"/shiplist/table/order/IND?filterTanggal=",
        "type": "GET",
        "dataSrc": function(json){
            $("#table-ci").parent().css("overflow-x","auto");
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
}

if($('#table-ct').length>0){
var tableCt = $('#table-ct').DataTable({
    "paging": true,
    "searching": true,
    "processing": true,
    "serverSide": true,
    "processing": true,
    "serverSide": true,
    "order": [],
    "ajax": {
        "url": location.origin+"/shiplist/table/invoice/IND?filterTanggalAwal=&filterTanggalAkhir=&filterWarehouse=&filterService=",
        "type": "GET",
        "dataSrc": function(json){
            $("#table-ct").parent().css("overflow-x","auto");
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
}

if($('#table-status').length>0){
var tableSt = $('#table-status').DataTable({
    "paging": true,
    "searching": true,
    "processing": true,
    "serverSide": true,
    "processing": true,
    "serverSide": true,
    "order": [],
    "ajax": {
        "url": location.origin+"/shiplist/table/tracking/IND?mismassOrderId=&filterTanggalAwal=&filterTanggalAkhir=&filterWarehouse=&filterService=",
        "type": "GET",
        "dataSrc": function(json){
            $("#table-status").parent().css("overflow-x","auto");
            return json.data;
        }
    },
    "fnDrawCallback": function( oSettings ) {
        let custTypeId = $(".btn-select").attr("id");
        custTypeId == "IND" ? $("#thResi").show() : $("#thResi").hide();
        custTypeId == "IND" ? $("#table-status tbody tr td:nth-child(4)").show() : $("#table-status tbody tr td:nth-child(4)").hide();
    },
    "columnDefs": [{
        "targets": [],
        "orderable": true,
    }],
    "fixedHeader": false,
    "ordering": false,
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
}