$("table").on("click","#buatResiBtn",function(){
    
    let modalId = "createResi",
        totalPrice = $(this).attr("data-totalPrice"),
        totalItem = $(this).attr("data-totalItem"),
        totalWeight = $(this).attr("data-totalWeight"),
        senderAddress = $(this).attr("data-senderAddress"),
        senderPhone = $(this).attr("data-senderPhone"),
        senderName = $(this).attr("data-senderName"),
        consAddress = $(this).attr("data-consAddress"),
        consPhone = $(this).attr("data-consPhone"),
        consName = $(this).attr("data-consName"),
        custTypeName = $(this).attr("data-custTypeName"),
        mismassInvoiceDate = $(this).attr("data-mismassInvoiceDate"),
        dokuInvoiceId = $(this).attr("data-dokuInvoiceId")!=""?$(this).attr("data-dokuInvoiceId"):"-",
        mismassInvoiceId = $(this).attr("data-mismassInvoiceId");
        createdAt = $(this).attr("data-createdAt"),
        countRow = $(this).attr("data-countRow"),
        custTypeId = $(this).attr("data-custTypeId"),
        getData = $(this).attr("data-getData");
        resiEl = '', phone = '', address = '', nameCustomer = '',
        num = 0,
        b = JSON.parse(getData.replace(/&quot;/g,'"')),
        custTypeId = $(".btn-select").attr("id"),
        modalTitle = custTypeId=="IND"?"Buat Resi Individual":"Buat Resi Corporate";

    b.every(el => {
        let data = {
            "id" : el['id'],
            "namaPenerima" : el['cons_first_name']+" "+el['cons_middle_name']+" "+el['cons_last_name'],
            "alamatPenerima" : el['cons_address']+", "+el['cons_sub_district']+", "+el['cons_district']+", "+el['cons_city']+", "+el['cons_prov']+", "+el['cons_postal_code'],
            "telponPenerima" : el['cons_phone'],
            "berat" : el['weight'],
            "item" : el['item'],
            "panjang" : el['length'],
            "lebar" : el['width'],
            "tinggi" : el['height'],
            "total" : el['sub_total'],
            "custTypeId" : custTypeId
        };
        resiEl += createResiElement(num,data);
        num++;
        if(custTypeId=="IND"){
            return false;
        }
        return true;
    });
    
    $("#"+modalId+" .resi-element").html(resiEl);

    phone = senderPhone;
    address = senderAddress;
    nameCustomer = senderName;
    $(".row-alamat-penerima").hide();
    $(".detil-penerima").show();
    $("#"+modalId+" .alert-success").show();
    $("#"+modalId+" .searchElem").show();
    $("#"+modalId+" .modal-footer").css({
        justifyContent:"space-between"
    });
    if(custTypeId=="IND"){
        phone = consPhone;
        address = consAddress;
        nameCustomer = consName;
        $(".detil-penerima").hide();
        $("#"+modalId+" .alert-success").hide();
        $("#"+modalId+" .searchElem").hide();
        $("#"+modalId+" .modal-footer").css({
            justifyContent:"right"
        });
    }
    
    initialShow(modalId);
    let invOnlyId = mismassInvoiceId.replace("INV/AJV/", "");
    $("#"+modalId+" .alert-success label a").attr("href",location.origin+"/import/example/shipping/"+invOnlyId);
    
    $("#"+modalId+" input[name='mismassInvoiceId']").val(mismassInvoiceId);
    $("#"+modalId+" .modal-title").text(modalTitle);
    $("#"+modalId+" input[name='custTypeId']").val(custTypeId);
    $("#"+modalId+" label[for='orderNumberDoku']").text(dokuInvoiceId+" | "+createdAt);
    $("#"+modalId+" label[for='noInvoiceTanggal']").text(mismassInvoiceId+" | "+mismassInvoiceDate);
    $("#"+modalId+" label[for='tipeCustomer']").text(custTypeName);
    $("#"+modalId+" label[for='namaCustomer']").text(nameCustomer);
    $("#"+modalId+" label[for='telponWhatsapp']").text(phone);
    $("#"+modalId+" label[for='alamatCustomer']").text(address);
    $("#"+modalId+" label[for='totalBerat']").text(floatOrInt(totalWeight)+" Kg");
    $("#"+modalId+" label[for='totalPcs']").text(totalItem+" Item");
    $("#"+modalId+" label[for='totalBayar']").text(totalPrice);

    $("#"+modalId).modal("show");
});

$("table").on("click","#editResiBtn",function(){

    let modalId = "editResi",
        totalPrice = $(this).attr("data-totalPrice"),
        totalItem = $(this).attr("data-totalItem"),
        totalWeight = $(this).attr("data-totalWeight"),
        senderAddress = $(this).attr("data-senderAddress"),
        senderPhone = $(this).attr("data-senderPhone"),
        senderName = $(this).attr("data-senderName"),
        consAddress = $(this).attr("data-consAddress"),
        consPhone = $(this).attr("data-consPhone"),
        consName = $(this).attr("data-consName"),
        custTypeName = $(this).attr("data-custTypeName"),
        mismassInvoiceDate = $(this).attr("data-mismassInvoiceDate"),
        dokuInvoiceId = $(this).attr("data-dokuInvoiceId")!=""?$(this).attr("data-dokuInvoiceId"):"-",
        mismassInvoiceId = $(this).attr("data-mismassInvoiceId");
        createdAt = $(this).attr("data-createdAt"),
        countRow = $(this).attr("data-countRow"),
        custTypeId = $(this).attr("data-custTypeId"),
        getData = $(this).attr("data-getData");
        resiEl = '', phone = '', address = '', nameCustomer = '',
        num = 0,
        b = JSON.parse(getData.replace(/&quot;/g,'"')),
        custTypeId = $(".btn-select").attr("id"),
        modalTitle = custTypeId=="IND"?"Edit Resi Individual":"Edit Resi Corporate";

    b.every(el => {
        let data = {
            "id" : el['id'],
            "consFirstName" : el['cons_first_name'],
            "consMiddleName" : el['cons_middle_name'],
            "consLastName" : el['cons_last_name'],
            "consAddress" : el['cons_address'],
            "consSubDistrict" : el['cons_sub_district'],
            "consDistrict" : el['cons_district'],
            "consCity" : el['cons_city'],
            "consProv" : el['cons_prov'],
            "consPhone" : el['cons_phone'],
            "consPostalCode" : el['cons_postal_code'],
            "forwarderId" : el['forwarder_id'],
            "forwarderName" : el['forwarder_name'],
            "shippingNumber" : el['shipping_number'],
            "weight" : el['weight'],
            "item" : el['item'],
            "length" : el['length'],
            "width" : el['width'],
            "height" : el['height'],
            "total" : el['sub_total'],
            "custTypeId" : custTypeId
        };
        resiEl += editResiElement(num,data);
        num++;
        if(custTypeId=="IND"){
            return false;
        }
        return true;
    });
    
    $("#"+modalId+" .resi-element").html(resiEl);

    phone = senderPhone;
    address = senderAddress;
    nameCustomer = senderName;
    $(".row-alamat-penerima").hide();
    $(".detil-penerima").show();
    $("#"+modalId+" .alert-success").show();
    $("#"+modalId+" .searchElem").show();
    $("#"+modalId+" .modal-footer").css({
        justifyContent:"space-between"
    });
    if(custTypeId=="IND"){
        phone = consPhone;
        address = consAddress;
        nameCustomer = consName;
        $(".detil-penerima").hide();
        $("#"+modalId+" .alert-success").hide();
        $("#"+modalId+" .searchElem").hide();
        $("#"+modalId+" .modal-footer").css({
            justifyContent:"right"
        });
    }
    
    initialShow(modalId);
    let invOnlyId = mismassInvoiceId.replace("INV/AJV/", "");
    $("#"+modalId+" .alert-success label a").attr("href",location.origin+"/import/example/shipping/"+invOnlyId);
    
    $("#"+modalId+" input[name='mismassInvoiceId']").val(mismassInvoiceId);
    $("#"+modalId+" .modal-title").text(modalTitle);
    $("#"+modalId+" input[name='custTypeId']").val(custTypeId);
    $("#"+modalId+" label[for='orderNumberDoku']").text(dokuInvoiceId+" | "+createdAt);
    $("#"+modalId+" label[for='noInvoiceTanggal']").text(mismassInvoiceId+" | "+mismassInvoiceDate);
    $("#"+modalId+" label[for='tipeCustomer']").text(custTypeName);
    $("#"+modalId+" label[for='namaCustomer']").text(nameCustomer);
    $("#"+modalId+" label[for='telponWhatsapp']").text(phone);
    $("#"+modalId+" label[for='alamatCustomer']").text(address);
    $("#"+modalId+" label[for='totalBerat']").text(floatOrInt(totalWeight)+" Kg");
    $("#"+modalId+" label[for='totalPcs']").text(totalItem+" Item");
    $("#"+modalId+" label[for='totalBayar']").text(totalPrice);

    let custLength = $("#"+modalId+" select[name='tipeForwarder[]']").length;
    for(let i=0;i<=custLength-1;i++){
        onChangeTipeForwarder($("#"+modalId+" #tf"+i).val($("#"+modalId+" #"+i+" input[name='forwarderIdOld']").val()));
        $("#"+modalId+" #nf"+i).val($("#"+modalId+" #"+i+" input[name='forwarderNameOld']").val());
        $("#"+modalId+" #nr"+i).val($("#"+modalId+" #"+i+" input[name='shippingNumberOld']").val());
    };

    $("#"+modalId).modal("show");
});

$("table").on("click",".printResiAll",function(){
    let type = $(".btn-select").attr("id");
    if(type=="IND"){
        let total=0,
            b="";
        $("input[name='checkResi']").each(function(){
            if($(this).is(":checked")){
                total++;
                b += $(this).attr("data-resi").replace(/TR\/ALY\//g, "")+"=";
            }
        });

        if(total==0){
            Swal.fire("Resi Belum Diseleksi", "Belum Ada Resi Yang Diseleksi", 'error');
            return false;
        }

        window.open(
            location.origin+"/printout/resi/"+b,
            "_blank"
        );

    }else if(type=="COR"){
        let id = $(this).attr("data-mismassInvoiceId");
        $.ajax({
            type: "GET",
            data:{
                "id":id
            },
            url: location.origin+"/check/getshipnumfrominvoice",
            success: function(a) {
                let z = JSON.parse(a),
                    b = "";
                for(let i=0;i<=z.data.length-1;i++){
                    b += z.data[i]['shipping_number'].replace(/TR\/ALY\//g, "")+"=";
                }
                
                window.open(
                    location.origin+"/printout/resi/"+b,
                    "_blank"
                );
            }
        });
    }

});

$("input[name='checkAll']").on("click",function(){
    let status = false;

    if($(this).is(":checked")){
        status = true;
    }
    
    $("input[name='checkResi']").each(function(){
        $(this).attr("checked",status);
    });
});

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
jQuery.validator.addMethod(
    "noSameShippingNumber", 
    function(value, element) {
        // return this.optional(element) || (parseFloat(value) > 0);
        let num=0;
        $("#formBuatResi input[name='noResi[]']").each(function(index, value) {
        	let a = $(this).val();
         		b = `${index}`;
            $("#formBuatResi input[name='noResi[]']").each(function(index, value) {
            	if(b!=`${index}`){
            	    if($(this).val()!=""){
                    	if(a==$(this).val()){
                        	num++;
                        }
            	    }
                }	
            });
        });
        // console.log(num);
        return this.optional(element) || (parseInt(num) < 1);
    }, 
    "Shipping Number Tidak Boleh Sama"
);
$("#formBuatResi").validate({
    errorClass: "error fail-alert is-invalid",
    rules: {
        "noResi[]":{
            remote:{
                url: location.origin+"/check/getshipnum",
                type: "GET",
                data: {
                    noResi : function() {
                        return $("#formBuatResi input[name='noResi[]']").val();
                    },
                } 
            },
            noSameShippingNumber:true
        }
    },
    messages: {
        "tipeForwarder[]": "Pilih Salah Satu",
        "namaForwarder[]":function(){
            if($("select[name='tipeForwarder[]']").val()=="MISMASS"){
                return "Tidak Boleh Kosong";
            }
            return "Pilih Salah Satu";
        },
        "noResi[]":{
            required : "Tidak Boleh Kosong",
            remote : "Shipping Number Telah Terdaftar",
            noSameShippingNumber: "Shipping Number Tidak Boleh Sama"
        },
    },
    submitHandler: function(form) {

        $.ajax({
            type: "POST",
            url: location.origin+"/shiplist/buat/resi",
            data: $(form).serialize(),
            beforeSend: function() {
                loading(form);
            },
            success: function(msg) {
                var json = JSON.parse(msg);

                unLoading(form);

                if (json.status == "Berhasil") {

                    //NOTIF SUKSES
                    Swal.fire({
                        icon: 'success',
                        title: json.status,
                        text: json.text,
                        showCancelButton: true,
                        reverseButtons:true,
                        cancelButtonColor:"#dc3545",
                        confirmButtonText: "Print Resi",
                        cancelButtonText: "Close",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.open(json.url, '_blank');
                        }
                    });

                    let custTypeId = $('.btn-select').attr('id');
                    refreshTable(tableCi,location.origin+"/shiplist/table/order/"+custTypeId+"?filterTanggal=","table-ci_info");
                    refreshTable(tableCt,location.origin+"/shiplist/table/invoice/"+custTypeId+"?filterTanggalAwal=&filterTanggalAkhir=&filterWarehouse=&filterService=","table-ct_info");
                    refreshTable(tableSt,location.origin+"/shiplist/table/tracking/"+custTypeId+"?mismassOrderId=&filterTanggalAwal=&filterTanggalAkhir=&filterWarehouse=&filterService=","table-status_info");

                    clearInterval(intervalLastShippingId);
                    //RELOAD PAGE
                    // pageReload(location.origin+"/shiplist");

                } else {

                    //NOTIF GAGAL
                    Swal.fire(json.status, json.text, 'error');

                }
            }

        });

    }

});

$("#formEditResi").validate({
    errorClass: "error fail-alert is-invalid",
    rules: {
        "noResi[]":{
            remote:{
                url: location.origin+"/check/getshipnum",
                type: "GET",
                data: {
                    id: function() {
                        return $("#formEditResi input[name='id[]']").val();
                    },
                    noResi: function(){
                        return $("#formEditResi input[name='noResi[]']").val();
                    }
                } 
            },
            noSameShippingNumber:true
        }
    },
    messages: {
        "consFirstName" : "Tidak Boleh Kosong",
        "consPhone" : "Tidak Boleh Kosong",
        "consAddress" : "Tidak Boleh Kosong",
        "consDistrict" : "Tidak Boleh Kosong",
        "consCity" : "Tidak Boleh Kosong",
        "consProv" : "Tidak Boleh Kosong",
        "consPostalCode" : "Tidak Boleh Kosong",
        "tipeForwarder[]": "Pilih Salah Satu",
        "namaForwarder[]":function(){
            if($("select[name='tipeForwarder[]']").val()=="MISMASS"){
                return "Tidak Boleh Kosong";
            }
            return "Pilih Salah Satu";
        },
        "noResi[]":{
            required : "Tidak Boleh Kosong",
            remote : "Shipping Number Telah Terdaftar",
            noSameShippingNumber: "Shipping Number Tidak Boleh Sama"
        },
    },
    submitHandler: function(form) {

        $.ajax({
            type: "POST",
            url: location.origin+"/shiplist/edit/resi",
            data: $(form).serialize(),
            beforeSend: function() {
                loading(form);
            },
            success: function(msg) {
                var json = JSON.parse(msg);

                unLoading(form);

                if (json.status == "Berhasil") {

                    //NOTIF SUKSES
                    Swal.fire({
                        icon: 'success',
                        title: json.status,
                        text: json.text,
                        showCancelButton: true,
                        reverseButtons:true,
                        cancelButtonColor:"#dc3545",
                        confirmButtonText: "Print Resi",
                        cancelButtonText: "Close",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.open(json.url, '_blank');
                        }
                    });

                    let custTypeId = $('.btn-select').attr('id');
                    refreshTable(tableCi,location.origin+"/shiplist/table/order/"+custTypeId+"?filterTanggal=","table-ci_info");
                    refreshTable(tableCt,location.origin+"/shiplist/table/invoice/"+custTypeId+"?filterTanggalAwal=&filterTanggalAkhir=&filterWarehouse=&filterService=","table-ct_info");
                    refreshTable(tableSt,location.origin+"/shiplist/table/tracking/"+custTypeId+"?mismassOrderId=&filterTanggalAwal=&filterTanggalAkhir=&filterWarehouse=&filterService=","table-status_info");

                    clearInterval(intervalLastShippingId);
                    //RELOAD PAGE
                    // pageReload(location.origin+"/shiplist");

                } else {

                    //NOTIF GAGAL
                    Swal.fire(json.status, json.text, 'error');

                }
            }

        });

    }

});

////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$(document).on("change","select[name='tipeForwarder[]']",function(){
    onChangeTipeForwarder(this);
});

function onChangeTipeForwarder(a){
    let id = $(a).val(),
        resiId = $(a).closest(".one-resi-element").attr("id"),
        modalId = $(a).closest(".modal").attr("id"),
        labelNamaForwarder="Nama Ekspedisi",
        element="<select class='form-control' name='namaForwarder[]' id='nf"+resiId+"' required>"+
                    "<option value='' hidden>Pilih Nama Ekspedisi</option>"+
                    "<option value='JNE'>JNE</option>"+
                    "<option value='J&T'>J&T</option>"+
                    "<option value='TIKI'>TIKI</option>"+
                    "<option value='LALAMOVE'>LALAMOVE</option>"+
                    "<option value='SICEPAT'>SICEPAT</option>"+
                    "<option value='SENTRAL CARGO'>SENTRAL CARGO</option>"+
                    "<option value='GO BOX'>GO BOX</option>"+
                    "<option value='ANTERAJA'>ANTERAJA</option>"+
                "</select>";
        
    $("#"+modalId+" #"+resiId+" input[name='noResi[]']").attr("readonly",false).attr("required",true);
    $("#"+modalId+" #"+resiId+" .col-namaForwarder").html("");
    $("#"+modalId+" #"+resiId+" input[name='noResi[]']").val("");
    
    if(id!="VENDOR"){
        labelNamaForwarder = "Nama Kurir";

        element = "<input type='text' name='namaForwarder[]' id='nf"+resiId+"' class='form-control' placeholder='Input Nama Kurir' onkeyup='this.value=this.value.toUpperCase()' required>";
        if(id=="PICK-UP"){
            element = "<input type='hidden' name='namaForwarder[]' id='nf"+resiId+"' class='form-control'>";
        }

        $("#"+modalId+" #"+resiId+" input[name='noResi[]']").attr("required",false).attr("readonly",true);

        if(modalId=="editResi"){
            if($("#"+modalId+" #"+resiId+" input[name='forwarderIdOld']").val()=="VENDOR"){
                lastShippingId(modalId,resiId);
            }else{
                $("#"+modalId+" #"+resiId+" input[name='noResi[]']").val($("#"+modalId+" #"+resiId+" input[name='shippingNumberOld']").val());
            }
        }else{
            lastShippingId(modalId,resiId);
        }
        
    }

    $("#"+modalId+" #"+resiId+" .col-namaForwarder").hide();
    if(id!="PICK-UP"){
        $("#"+modalId+" #"+resiId+"  .col-namaForwarder").append("<div class='form-group'><label for='namaForwarder'>"+labelNamaForwarder+"</label>"+element+"</div>");
        $("#"+modalId+" #"+resiId+" .col-namaForwarder").show();
    }

    $("#"+modalId+" #"+resiId+" .row-noresi").show();

    if(modalId=="editResi"){
        if($("#"+modalId+" #"+resiId+" input[name='forwarderIdOld']").val()=="VENDOR"){
            intervalLastShippingId = setInterval(() => {
                lastShippingId(modalId,resiId);
            }, 1000*60*3);
        }
    }else{
        intervalLastShippingId = setInterval(() => {
            lastShippingId(modalId,resiId);
        }, 1000*60*3);
    }
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function createResiElement(resiId,data){

    let a = resiId > 0 ? 'mt-2' : '',
        volumecheck = data['panjang'] > 0 ? "( "+data['panjang']+" x "+data['lebar']+" x "+data['tinggi']+" )" : '',
        border = data['custTypeId']=="COR" ? "border:1px solid black" : "";

    return "<div class='one-resi-element "+a+"' id='"+resiId+"' style='padding:10px;"+border+"'>"+
    "<input type='hidden' name='id[]' value='"+data['id']+"'>"+
    "<div class='detil-penerima'>"+
        "<div class='row'>"+
            "<div class='col'>"+
                "<label for='namaPenerima'>Nama Penerima</label>"+
            "</div>"+
            "<div class='col'>"+
                "<div class='namaPenerima searchAble' id='"+makeId(8)+"'>"+data['namaPenerima']+"</div>"+
            "</div>"+
        "</div>"+
        "<div class='row'>"+
            "<div class='col'>"+
                "<label for='telponPenerima'>Telpon Penerima</label>"+
            "</div>"+
            "<div class='col'>"+
                "<div class='telponPenerima searchAble' id='"+makeId(8)+"'>"+data['telponPenerima']+"</div>"+
            "</div>"+
        "</div>"+
        "<div class='row'>"+
            "<div class='col'>"+
                "<label for='alamatPenerima'>Alamat Penerima</label>"+
            "</div>"+
            "<div class='col'>"+
                "<div class='alamatPenerima searchAble' id='"+makeId(8)+"'>"+data['alamatPenerima']+"</div>"+
            "</div>"+
        "</div>"+    
        "<div class='row'>"+
            "<div class='col'>"+
                "<label for='beratBarang'>Berat</label>"+
            "</div>"+
            "<div class='col'>"+
                "<div class='beratBarang searchAble' id='"+makeId(8)+"'>"+data['berat']+" Kg "+volumecheck+"</div>"+
            "</div>"+
        "</div>"+    
        "<div class='row'>"+
            "<div class='col'>"+
                "<label for='itemBarang'>Item</label>"+
            "</div>"+
            "<div class='col'>"+
                "<div class='itemBarang searchAble' id='"+makeId(8)+"'>"+data['item']+"</div>"+
            "</div>"+
        "</div>"+     
        "<div class='row'>"+
            "<div class='col'>"+
                "<label for='totalBarang'>Total</label>"+
            "</div>"+
            "<div class='col'>"+
                "<div class='totalBarang searchAble' id='"+makeId(8)+"'>Rp."+masking(data['total'])+"</div>"+
            "</div>"+
        "</div>"+    
        "<hr>"+
    "</div>"+    
    "<div class='row'>"+
        "<div class='col'>"+
            "<div class='form-group'>"+
                "<label for='tipeForwarder'>Jasa Pengiriman</label>"+
                "<select name='tipeForwarder[]' id='tf"+resiId+"' class='form-control' required>"+
                    "<option value='' hidden>PILIH JASA PENGIRIMAN</option>"+
                    "<option value='VENDOR'>VENDOR EKSPEDISI LAIN</option>"+
                    "<option value='MISMASS'>KURIR MISMASS</option>"+
                    "<option value='PICK-UP'>PICK-UP SENDIRI</option>"+
                "</select>"+
            "</div>"+
        "</div>"+
        "<div class='col col-namaForwarder'></div>"+
    "</div>"+
    "<div class='row row-noresi'>"+
        "<div class='col'>"+
            "<div class='form-group'>"+
                "<label for='noResi'>Nomor Resi</label>"+
                "<input type='text' name='noResi[]' id='nr"+resiId+"' onkeyup='this.value=this.value.toUpperCase()'' class='form-control'>"+
            "</div>"+
        "</div>"+
    "</div>"+
"</div>";

}

function editResiElement(resiId,data){

    let a = resiId > 0 ? 'mt-2' : '',
        volumecheck = data['panjang'] > 0 ? "( "+data['panjang']+" x "+data['lebar']+" x "+data['tinggi']+" )" : '',
        border = data['custTypeId']=="COR" ? "border:1px solid black" : "";

    return "<div class='one-resi-element "+a+"' id='"+resiId+"' style='padding:10px;"+border+"'>"+
    "<input type='hidden' name='id[]' value='"+data['id']+"'>"+
    "<input type='hidden' name='forwarderIdOld' value='"+data['forwarderId']+"'>"+
    "<input type='hidden' name='forwarderNameOld' value='"+data['forwarderName']+"'>"+
    "<input type='hidden' name='shippingNumberOld' value='"+data['shippingNumber']+"'>"+
    "<div class='detil-penerima'>"+
        "<div class='row'>"+
            "<div class='col'>"+
                "<label for=''>Nama Depan</label>"+
                "<input type='text' name='consFirstName[]' id='"+makeId(8)+"' value='"+data['consFirstName']+"' onkeyup='this.value=this.value.toUpperCase()' class='form-control searchAble' required>"+
            "</div>"+
            "<div class='col'>"+
                "<label for=''>Nama Tengah</label>"+
                "<input type='text' name='consMiddleName[]' id='"+makeId(8)+"' value='"+data['consMiddleName']+"' onkeyup='this.value=this.value.toUpperCase()' class='form-control searchAble'>"+
            "</div>"+
            "<div class='col'>"+
                "<label for=''>Nama Akhir</label>"+
                "<input type='text' name='consLastName[]' id='"+makeId(8)+"' value='"+data['consLastName']+"' onkeyup='this.value=this.value.toUpperCase()' class='form-control searchAble'>"+
            "</div>"+
        "</div>"+
        "<div class='row'>"+
            "<div class='col'>"+
                "<label for=''>Telpon</label>"+
                "<input type='text' name='consPhone[]' id='"+makeId(8)+"' value='"+data['consPhone']+"' onkeyup='this.value=this.value.toUpperCase()' class='form-control searchAble' required>"+
            "</div>"+
            "<div class='col'>"+
                "<label for=''>Alamat</label>"+
                "<input type='text' name='consAddress[]' id='"+makeId(8)+"' value='"+data['consAddress']+"' onkeyup='this.value=this.value.toUpperCase()' class='form-control searchAble' required>"+
            "</div>"+
        "</div>"+
        "<div class='row'>"+
            "<div class='col'>"+
                "<label for=''>Kelurahan</label>"+
                "<input type='text' name='consSubDistrict[]' id='"+makeId(8)+"' value='"+data['consSubDistrict']+"' onkeyup='this.value=this.value.toUpperCase()' class='form-control searchAble'>"+
            "</div>"+
            "<div class='col'>"+
                "<label for=''>Kecamatan</label>"+
                "<input type='text' name='consDistrict[]' id='"+makeId(8)+"' value='"+data['consDistrict']+"' onkeyup='this.value=this.value.toUpperCase()' class='form-control searchAble' required>"+
            "</div>"+
            "<div class='col'>"+
                "<label for=''>Kabupaten/Kota</label>"+
                "<input type='text' name='consCity[]' id='"+makeId(8)+"' value='"+data['consCity']+"' onkeyup='this.value=this.value.toUpperCase()' class='form-control searchAble' required>"+
            "</div>"+
        "</div>"+
        "<div class='row'>"+
            "<div class='col'>"+
                "<label for=''>Provinsi</label>"+
                "<input type='text' name='consProv[]' id='"+makeId(8)+"' value='"+data['consProv']+"' onkeyup='this.value=this.value.toUpperCase()' class='form-control searchAble' required>"+
            "</div>"+
            "<div class='col'>"+
                "<label for=''>Postal Code</label>"+
                "<input type='text' name='consPostalCode[]' id='"+makeId(8)+"' value='"+data['consPostalCode']+"' onkeyup='this.value=this.value.toUpperCase()' class='form-control searchAble' required>"+
            "</div>"+
        "</div>"+
        "<hr>"+
        "<div class='row'>"+
            "<div class='col'>"+
                "<label for='beratBarang'>Berat</label>"+
            "</div>"+
            "<div class='col'>"+
                "<div class='beratBarang searchAble' id='"+makeId(8)+"'>"+data['weight']+" Kg "+volumecheck+"</div>"+
            "</div>"+
        "</div>"+    
        "<div class='row'>"+
            "<div class='col'>"+
                "<label for='itemBarang'>Item</label>"+
            "</div>"+
            "<div class='col'>"+
                "<div class='itemBarang searchAble' id='"+makeId(8)+"'>"+data['item']+"</div>"+
            "</div>"+
        "</div>"+     
        "<div class='row'>"+
            "<div class='col'>"+
                "<label for='totalBarang'>Total</label>"+
            "</div>"+
            "<div class='col'>"+
                "<div class='totalBarang searchAble' id='"+makeId(8)+"'>Rp."+masking(data['total'])+"</div>"+
            "</div>"+
        "</div>"+    
        "<hr>"+
    "</div>"+    
    "<div class='row'>"+
        "<div class='col'>"+
            "<div class='form-group'>"+
                "<label for='tipeForwarder'>Jasa Pengiriman</label>"+
                "<select name='tipeForwarder[]' id='tf"+resiId+"' class='form-control' required>"+
                    "<option value='' hidden>PILIH JASA PENGIRIMAN</option>"+
                    "<option value='VENDOR'>VENDOR EKSPEDISI LAIN</option>"+
                    "<option value='MISMASS'>KURIR MISMASS</option>"+
                    "<option value='PICK-UP'>PICK-UP SENDIRI</option>"+
                "</select>"+
            "</div>"+
        "</div>"+
    "<div class='col col-namaForwarder'></div>"+
    "</div>"+
    "<div class='row row-noresi'>"+
        "<div class='col'>"+
            "<div class='form-group'>"+
                "<label for='noResi'>Nomor Resi</label>"+
                "<input type='text' name='noResi[]' id='nr"+resiId+"' onkeyup='this.value=this.value.toUpperCase()'' class='form-control'>"+
            "</div>"+
        "</div>"+
    "</div>"+
"</div>";

}

function lastShippingId(modalId,resiId){
    $.ajax({
        type: "POST",
        url: location.origin+"/check/lastshippingid",
        success: function(m) {
            let j = JSON.parse(m),
                l = $("[name='noResi[]']").length,
                a = j.shippingIdAv.replace("TR/ALY/", ""),
                n = 0;

                for(let i = 0;i<=l-1;i++){
                    if($("#"+modalId+" #"+i+" select[name='tipeForwarder[]']"!="VENDOR")){
                        if($("#"+modalId+" #"+i+" input[name='forwarderIdOld']"=="VENDOR")){
                            if($("#"+modalId+" #nr"+i).val()!=""){
                                if(resiId!=i){
                                    n++;
                                }
                            }
                        }
                    }
                }

            let b = parseInt(a)+n,
                c = "TR/ALY/"+b,
                d = l > 1 ? c : j.shippingIdAv;

            $("#"+modalId+" #"+resiId+" input[name='noResi[]']").val(d);
        }
    });
}

$("table").on("click",".detail-control",function(){
    let id = $(this).attr("id"),
        createdAt = $(this).attr("data-createdAt"),
        tr = $(this).closest("tr"),
        row = tableSt.row(tr),
        printResi = auth==1?"<th>Print</th>":""
        div = "<div style='border: 1px solid rgb(52 58 64 / 34%);padding: 10px;border-radius: 5px;'>"+
        "<table class='table table-striped' id='"+id+"-child'>"+
            "<thead>"+
                "<tr>"+
                    "<th>No</th>"+
                    "<th>No. Resi</th>"+
                    "<th>Nama Penerima</th>"+
                    "<th>Kontak & Alamat</th>"+
                    "<th>Jumlah</th>"+
                    printResi+
                "</tr>"+
            "</thead>"+
        "</table>"+
        "</div>";

        if($(this).hasClass("hidden-child")){
            $(this).removeClass("hidden-child");
            $(this).addClass("shown-child");
            row.child(div).show();

            $("#"+id+"-child").DataTable({
                "paging": true,
                "searching": true,
                // "responsive": true,
                "processing": true,
                "serverSide": true,
                "processing": true,
                "serverSide": true,
                "order": [],
                "ajax": {
                    "url": location.origin+"/shiplist/table/tracking/IND?mismassOrderId="+id+"&filterTanggalAwal=&filterTanggalAkhir=&filterWarehouse=&filterService=",
                    "type": "GET",
                    "dataSrc": function(json){
                        $("#"+id+"-child").parent().css("overflow-x","auto");
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
                    "lengthMenu" : "Rincian Detil Penerima | Tanggal Resi : <div style='color:green;display:inline'>"+createdAt+"</div>"
                }
            });
            
        }else{
            $(this).removeClass("shown-child");
            $(this).addClass("hidden-child");
            row.child.hide();
        }
});