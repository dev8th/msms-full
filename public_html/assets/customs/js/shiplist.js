hideSomeElements();

$("#IND").addClass("btn-select");
$(".row-filter,.card-table").show();

masK = "###.###.###.###.###";
masKC = "###.###.###.###.###,00";

$(".btn-daftar button").on("click",function(){  
    let custTypeId = $(this).attr("id");
    $(".btn-daftar button").removeClass("btn-select");
    $(this).addClass("btn-select");
    $(".row-filter,.card-table").show();

    refreshTable(tableCi,location.origin+"/shiplist/table/order/"+custTypeId+"?filterTanggal=","table-ci_info");
    refreshTable(tableCt,location.origin+"/shiplist/table/invoice/"+custTypeId+"?filterTanggalAwal=&filterTanggalAkhir=&filterWarehouse=&filterService=","table-ct_info");
    refreshTable(tableSt,location.origin+"/shiplist/table/tracking/"+custTypeId+"?filterTanggalAwal=&filterTanggalAkhir=&filterWarehouse=&filterService=","table-status_info");

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
        // $(".col-filterService").hide();
        $(".row-filterOne").hide();
        $("select[name='filterWarehouse']").val("");
        $("select[name='filterService']").val("");
        if(id=="nav-ct-tab"){
            refreshTable(tableCt,location.origin+"/shiplist/table/invoice/"+custTypeId+"?filterTanggalAwal=&filterTanggalAkhir=&filterWarehouse=&filterService=","table-ct_info");
        }else{
            refreshTable(tableSt,location.origin+"/shiplist/table/tracking/"+custTypeId+"?filterTanggalAwal=&filterTanggalAkhir=&filterWarehouse=&filterService=","table-status_info");
        }
    }
});

// $("#filterWarehouse").on("change",function(){
//     let id = $(this).val();
//     $.ajax({
//         type: "GET",
//         url: location.origin+"/check/getservlist",
//         data: {
//             warehouseId:id,
//         },
//         success: function(msg) {
//             let json = JSON.parse(msg);
//             $(".col-filterService").show();
//             $("select[name='filterService']").html(json.data);
//         }
//     });
// });

$("table").on("click","#btnStatusId",function(){
    let custTypeId = $(".btn-select").attr("id"),
        filter = $("#filterTanggalOrder").val(),
        orderId = $(this).attr("data-orderid"),
        status = $(this).text();

        $.ajax({
            type: "GET",
            url: location.origin+"/shiplist/edit/order/status",
            data: {
                status:status,
                orderId:orderId
            },
            success: function(msg) {
                let json=JSON.parse(msg);
                if(json.status=="Berhasil"){
                    if($("#btnStatusId").hasClass("btnStatusReady")){
                        $("btnStatusId").removeClass("btnStatusReady");
                        $("btnStatusId").addClass("btnStatusHold");
                    }else{
                        $("btnStatusId").addClass("btnStatusReady");
                        $("btnStatusId").removeClass("btnStatusHold");
                    }
                }
            }
        });

        refreshTable(tableCi,location.origin+"/shiplist/table/order/"+custTypeId+"?filterTanggal=","table-ci_info");
});

$("table").on("click","#buatBtn",function(){
    let orderStatusId = $(this).closest("tr").find("#btnStatusId").text();
    if(orderStatusId=="HOLD"){
        return Swal.fire(
            'Status Order HOLD!',
            'Silahkan ganti status terlebih dahulu',
            'error'
          )
    }
    let modalId = "createInvoice",
        mismassOrderId = $(this).attr("data-id"),
        custId = $(this).attr("data-custId"),
        custTypeId = $(this).attr("data-custTypeId"),
        firstName = $(this).attr("data-firstName"),
        middleName = $(this).attr("data-middleName"),
        lastName = $(this).attr("data-lastName"),
        email = $(this).attr("data-email"),
        phone = $(this).attr("data-phone"),
        address = $(this).attr("data-address"),
        subDistrict = $(this).attr("data-subDistrict"),
        district = $(this).attr("data-district"),
        city = $(this).attr("data-city"),
        prov = $(this).attr("data-prov"),
        postalCode = $(this).attr("data-postalCode"),
        secondName = $(this).attr("data-secondName"),
        secondPhone = $(this).attr("data-secondPhone"),
        tanggal = $(this).attr("data-tanggal");
        
    resetModalInvoice(modalId);

    $("#"+modalId+" .tanggal").text(tanggal);
    $("#"+modalId+" .customer").text(firstName+" "+middleName+" "+lastName);
    $("#"+modalId+" input[name='mismassOrderId']").val(mismassOrderId);
    $("#"+modalId+" input[name='dbCustId']").val(custId);
    $("#"+modalId+" input[name='dbCustTypeId']").val(custTypeId);
    $("#"+modalId+" input[name='dbFirstName']").val(firstName);
    $("#"+modalId+" input[name='dbMiddleName']").val(middleName);
    $("#"+modalId+" input[name='dbLastName']").val(lastName);
    $("#"+modalId+" input[name='dbEmail']").val(email);
    $("#"+modalId+" input[name='dbPhone']").val(phone);
    $("#"+modalId+" input[name='dbAddress']").val(address);
    $("#"+modalId+" input[name='dbSubDistrict']").val(subDistrict);
    $("#"+modalId+" input[name='dbDistrict']").val(district);
    $("#"+modalId+" input[name='dbCity']").val(city);
    $("#"+modalId+" input[name='dbProv']").val(prov);
    $("#"+modalId+" input[name='dbPostalCode']").val(postalCode);
    $("#"+modalId+" input[name='dbSecondName']").val(secondName);
    $("#"+modalId+" input[name='dbSecondPhone']").val(secondPhone);

    createServiceElement(modalId);

    $("#"+modalId+"").modal("show");
});

$("table").on("click","#editInvoiceBtn",function(){

    let modalId = "editInvoice",
        mismassOrderId = $(this).attr("data-id");

    resetModalInvoice(modalId);

    $.ajax({
        type: "GET",
        url: location.origin+"/shiplist/edit/invoice/getdata",
        data: {
            mismassOrderId:mismassOrderId,
        },
        success: function(msg) {
            let num=0,
                totalWeight=0,
                totalItem=0,
                totalBiaya=0,
                dokuInvoiceId,
                dokuLink,
                bankName,
                bankAccountName,
                bankAccountId,
                firstName,
                middleName,
                lastName,
                json = JSON.parse(msg);

            json.data.forEach(e => {
                $("#"+modalId+" .tanggal").text(e['orderCreatedAt']);

                firstName = e['sender_first_name'],
                middleName = e['sender_middle_name'],
                lastName = e['sender_last_name'];
                if(e['cust_type_id']=="IND"){
                    firstName = e['cons_first_name'];
                    middleName = e['cons_middle_name'];
                    lastName = e['cons_last_name'];
                }
                $("#"+modalId+" .customer").text(firstName+" "+middleName+" "+lastName);
                $("#"+modalId+" input[name='mismassOrderId']").val(e['mismass_order_id']);
                $("#"+modalId+" input[name='mismassInvoiceId']").val(e['mismass_invoice_id']);

                $("#"+modalId+" input[name='tanggalInvoice']").val(e['mismass_invoice_date']);

                createServiceElement(modalId);

                $("#"+modalId+" #"+num+" input[name='senderFirstName[]']").val(e['sender_first_name']);
                $("#"+modalId+" #"+num+" input[name='senderMiddleName[]']").val(e['sender_middle_name']);
                $("#"+modalId+" #"+num+" input[name='senderLastName[]']").val(e['sender_last_name']);
                $("#"+modalId+" #"+num+" input[name='senderEmail[]']").val(e['sender_email']);
                $("#"+modalId+" #"+num+" input[name='senderPhone[]']").val(e['sender_phone']);
                $("#"+modalId+" #"+num+" input[name='senderAddress[]']").val(e['sender_address']);
                $("#"+modalId+" #"+num+" input[name='senderSubDistrict[]']").val(e['sender_sub_district']);
                $("#"+modalId+" #"+num+" input[name='senderDistrict[]']").val(e['sender_district']);
                $("#"+modalId+" #"+num+" input[name='senderCity[]']").val(e['sender_city']);
                $("#"+modalId+" #"+num+" input[name='senderProv[]']").val(e['sender_prov']);
                $("#"+modalId+" #"+num+" input[name='senderPostalCode[]']").val(e['sender_postal_code']);

                $("#"+modalId+" #"+num+" input[name='consFirstName[]']").val(e['cons_first_name']);
                $("#"+modalId+" #"+num+" input[name='consMiddleName[]']").val(e['cons_middle_name']);
                $("#"+modalId+" #"+num+" input[name='consLastName[]']").val(e['cons_last_name']);
                $("#"+modalId+" #"+num+" input[name='consEmail[]']").val(e['cons_email']);
                $("#"+modalId+" #"+num+" input[name='consPhone[]']").val(e['cons_phone']);
                $("#"+modalId+" #"+num+" input[name='consAddress[]']").val(e['cons_address']);
                $("#"+modalId+" #"+num+" input[name='consSubDistrict[]']").val(e['cons_sub_district']);
                $("#"+modalId+" #"+num+" input[name='consDistrict[]']").val(e['cons_district']);
                $("#"+modalId+" #"+num+" input[name='consCity[]']").val(e['cons_city']);
                $("#"+modalId+" #"+num+" input[name='consProv[]']").val(e['cons_prov']);
                $("#"+modalId+" #"+num+" input[name='consPostalCode[]']").val(e['cons_postal_code']);

                $("#"+modalId+" #"+num+" select[name='warehouse[]']").val(e['warehouse_id']);
                onChangeWarehouseGetServiceList($("#"+modalId+" #"+num+" select[name='warehouse[]']"));

                $("#"+modalId+" #"+num+" select[name='service[]']").val(e['service_id']);
                onChangeServiceGetUOMList($("#"+modalId+" #"+num+" select[name='service[]']"));

                let satuanBeratVal = e['item']>0?"ITEM":(e['length']>0?"VOL":"KG");
                $("#"+modalId+" #"+num+" select[name='satuanBerat']").val(satuanBeratVal);
                onChangeSatuanBerat($("#"+modalId+" #"+num+" select[name='satuanBerat']"));

                $("#"+modalId+" #"+num+" input[name='panjang[]']").val(e['length']);
                $("#"+modalId+" #"+num+" input[name='lebar[]']").val(e['width']);
                $("#"+modalId+" #"+num+" input[name='tinggi[]']").val(e['height']);
                $("#"+modalId+" #"+num+" input[name='kg[]']").val(e['weight'].toFixed(2).replace(".",","));
                $("#"+modalId+" #"+num+" input[name='item[]']").val(e['item']);

                // let pricePer = e['sub_total']-(e['packing_price']+e['import_permit_price']+e['document_price']+e['dr_medicine_price']+e['insurance_total']+e['fee_total']+e['tax_total']+e['extra_cost_price']);
                $("#"+modalId+" #"+num+" input[name='pricePer[]']").val(masking(e['service_price_per'].toString()));

                if(e['packing_price']>0){
                    onChangeAdditionalService($("#"+modalId+" #"+num+" select[name='additionalService']").val("KAY"));
                }
                $("#"+modalId+" #"+num+" input[name='packingPrice[]']").val(masking(e['packing_price'].toString()));
                $("#"+modalId+" #"+num+" input[name='packingDesc[]']").val(e['packing_desc']);
                $("#"+modalId+" #"+num+" input[name='packingPrice"+num+"']").val(e['packing_price']);
                $("#"+modalId+" #"+num+" input[name='packingDesc"+num+"']").val(e['packing_desc']);

                if(e['insurance_item_price']>0){
                    onChangeAdditionalService($("#"+modalId+" #"+num+" select[name='additionalService']").val("ASR"));
                }
                $("#"+modalId+" #"+num+" input[name='insurancePriceItem[]']").val(masking(e['insurance_item_price'].toString()));
                $("#"+modalId+" #"+num+" input[name='insurancePercent[]']").val(masking(e['insurance_percent'].toString()));
                $("#"+modalId+" #"+num+" input[name='insuranceTotal[]']").val(masking(e['insurance_total'].toString()));
                $("#"+modalId+" #"+num+" input[name='insurancePriceItem"+num+"']").val(e['insurance_item_price']);
                $("#"+modalId+" #"+num+" input[name='insurancePercent"+num+"']").val(e['insurance_percent']);
                $("#"+modalId+" #"+num+" input[name='insuranceTotal"+num+"']").val(e['insurance_total']);

                if(e['extra_cost_price']>0){
                    onChangeAdditionalService($("#"+modalId+" #"+num+" select[name='additionalService']").val("EON"));
                }
                $("#"+modalId+" #"+num+" input[name='extraCostPrice[]']").val(masking(e['extra_cost_price'].toString()));
                $("#"+modalId+" #"+num+" input[name='extraCostDest[]']").val(e['extra_cost_dest']);
                $("#"+modalId+" #"+num+" input[name='extraCostVendorNamel[]']").val(e['extra_cost_vendor_name']);
                $("#"+modalId+" #"+num+" input[name='extraCostPrice"+num+"']").val(e['extra_cost_price']);
                $("#"+modalId+" #"+num+" input[name='extraCostDest"+num+"']").val(e['extra_cost_dest']);
                $("#"+modalId+" #"+num+" input[name='extraCostVendorNamel"+num+"']").val(e['extra_cost_vendor_name']);

                if(e['document_price']>0){
                    onChangeAdditionalService($("#"+modalId+" #"+num+" select[name='additionalService']").val("DOC"));
                }
                $("#"+modalId+" #"+num+" input[name='documentPrice[]']").val(masking(e['document_price'].toString()));
                $("#"+modalId+" #"+num+" input[name='documentDesc[]']").val(e['document_desc']);
                $("#"+modalId+" #"+num+" input[name='documentPrice"+num+"']").val(e['document_price']);
                $("#"+modalId+" #"+num+" input[name='documentDesc"+num+"']").val(e['document_desc']);

                if(e['fee_item_price']>0){
                    onChangeAdditionalService($("#"+modalId+" #"+num+" select[name='additionalService']").val("FEE"));
                }
                $("#"+modalId+" #"+num+" input[name='feePriceItem[]']").val(masking(e['fee_item_price'].toString()));
                $("#"+modalId+" #"+num+" input[name='feePercent[]']").val(masking(e['fee_percent'].toString()));
                $("#"+modalId+" #"+num+" input[name='feeTotal[]']").val(masking(e['fee_total'].toString()));
                $("#"+modalId+" #"+num+" input[name='feePriceItem"+num+"']").val(e['fee_item_price']);
                $("#"+modalId+" #"+num+" input[name='feePercent"+num+"']").val(e['fee_percent']);
                $("#"+modalId+" #"+num+" input[name='feeTotal"+num+"']").val(e['fee_total']);

                if(e['tax_item_price']>0){
                    onChangeAdditionalService($("#"+modalId+" #"+num+" select[name='additionalService']").val("TAX"));
                }
                $("#"+modalId+" #"+num+" input[name='taxPriceItem[]']").val(masking(e['tax_item_price'].toString()));
                $("#"+modalId+" #"+num+" input[name='taxPercent[]']").val(masking(e['tax_percent'].toString()));
                $("#"+modalId+" #"+num+" input[name='taxTotal[]']").val(masking(e['tax_total'].toString()));
                $("#"+modalId+" #"+num+" input[name='taxPriceItem"+num+"']").val(e['tax_item_price']);
                $("#"+modalId+" #"+num+" input[name='taxPercent"+num+"']").val(e['tax_percent']);
                $("#"+modalId+" #"+num+" input[name='taxTotal"+num+"']").val(e['tax_total']);

                if(e['import_permit_price']>0){
                    onChangeAdditionalService($("#"+modalId+" #"+num+" select[name='additionalService']").val("IPM"));
                }
                $("#"+modalId+" #"+num+" input[name='importPrice[]']").val(masking(e['import_permit_price'].toString()));
                $("#"+modalId+" #"+num+" input[name='importDesc[]']").val(e['import_permit_desc']);
                $("#"+modalId+" #"+num+" input[name='importPrice"+num+"']").val(e['import_permit_price']);
                $("#"+modalId+" #"+num+" input[name='importDesc"+num+"']").val(e['import_permit_desc']);

                if(e['dr_medicine_price']>0){
                    onChangeAdditionalService($("#"+modalId+" #"+num+" select[name='additionalService']").val("MED"));
                }
                $("#"+modalId+" #"+num+" input[name='medicinePrice[]']").val(masking(e['dr_medicine_price'].toString()));
                $("#"+modalId+" #"+num+" input[name='medicineDesc[]']").val(e['dr_medicine_desc']);
                $("#"+modalId+" #"+num+" input[name='medicinePrice"+num+"']").val(e['dr_medicine_price']);
                $("#"+modalId+" #"+num+" input[name='medicineDesc"+num+"']").val(e['dr_medicine_desc']);

                $("#"+modalId+" #"+num+" input[name='subTotal[]']").val(masking(e['sub_total'].toString()));

                totalWeight+=e['weight'];
                totalItem+=e['item'];
                totalBiaya+=e['sub_total'];
                dokuInvoiceId=e['doku_invoice_id'];
                dokuLink=e['doku_link'];
                bankName=e['bank_name'];
                bankAccountName=e['bank_account_name'];
                bankAccountId=e['bank_account_id'];
                num++;
            });

            $("#"+modalId+" .totalHargaBoard div .value").text(masking(totalBiaya.toString()));
            $("#"+modalId+" .totalItemBoard div .value").text(masking(totalItem.toString()));
            $("#"+modalId+" .totalKiloBoard div .value").text(masking(floatOrInt(totalWeight).toString()));

            if(dokuInvoiceId!=""){
                onChangePembayaran($("#"+modalId+" select[name='pembayaran']").val("DOKU"));
            }else{
                onChangePembayaran($("#"+modalId+" select[name='pembayaran']").val("BANK"));
            }
            $("#"+modalId+" input[name='invoiceDoku']").val(dokuInvoiceId);
            $("#"+modalId+" input[name='linkDoku']").val(dokuLink);
            $("#"+modalId+" input[name='namaBank']").text(bankName);
            $("#"+modalId+" input[name='namaRekening']").text(bankAccountName);
            $("#"+modalId+" input[name='noRekening']").text(bankAccountId);

            $("#"+modalId+"").modal("show");

        },
    });
});

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
        dokuInvoiceId = $(this).attr("data-dokuInvoiceId"),
        mismassInvoiceId = $(this).attr("data-mismassInvoiceId");
        createdAt = $(this).attr("data-createdAt"),
        countRow = $(this).attr("data-countRow"),
        custTypeId = $(this).attr("data-custTypeId"),
        getData = $(this).attr("data-getData");
        resiEl = '', phone = '', address = '', nameCustomer = '',
        num = 0,
        b = JSON.parse(getData.replace(/&quot;/g,'"'));

    b.every(el => {
        let data = {
            "namaPenerima" : el['cons_first_name']+" "+el['cons_middle_name']+" "+el['cons_last_name'],
            "alamatPenerima" : el['cons_address']+", "+el['cons_sub_district']+", "+el['cons_district']+", "+el['cons_city']+", "+el['cons_prov']+", "+el['cons_postal_code'],
            "telponPenerima" : el['cons_phone'],
            "berat" : el['weight'],
            "item" : el['item'],
            "panjang" : el['length'],
            "lebar" : el['width'],
            "tinggi" : el['height'],
            "total" : el['sub_total']
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
    if(custTypeId=="IND"){
        phone = consPhone;
        address = consAddress;
        nameCustomer = consName;
        $(".detil-penerima").hide();
    }
    
    initialShow(modalId);

    $("#"+modalId+" input[name='mismassInvoiceId']").val(mismassInvoiceId);
    $("#"+modalId+" .orderNumberDoku").text(dokuInvoiceId+" | "+createdAt);
    $("#"+modalId+" .noInvoiceTanggal").text(mismassInvoiceId+" | "+mismassInvoiceDate);
    $("#"+modalId+" .tipeCustomer").text(custTypeName);
    $("#"+modalId+" .namaCustomer").text(nameCustomer);
    $("#"+modalId+" .telponWhatsapp").text(phone);
    $("#"+modalId+" .alamatCustomer").text(address);
    $("#"+modalId+" .totalBerat").text(floatOrInt(totalWeight)+" Kg");
    $("#"+modalId+" .totalPcs").text(totalItem+" Item");
    $("#"+modalId+" .totalBayar").text(totalPrice);

    $("#"+modalId).modal("show");
});

////////////////////////////////////////////////////////////////////////////////////////////////////////////SUBMIT FUNCTION
$("#formBuatInvoice").validate({
    errorClass: "error fail-alert is-invalid",
    rules:{
        "invoiceDoku":{
            remote:{
                url: location.origin+"/check/invoicelinkdoku",
                type: "GET",
                data: {
                    invoiceDoku: function() {
                        return $("#formBuatInvoice #invoiceDoku").val();
                    },
                    mismassOrderId: function() {
                        return $("#formBuatInvoice input[name='mismassOrderId']").val();
                    },
                } 
            }
        },
        "linkDoku":{
            remote:{
                url: location.origin+"/check/invoicelinkdoku",
                type: "GET",
                data: {
                    linkDoku: function() {
                        return $("#formBuatInvoice #linkDoku").val();
                    },
                    mismassOrderId: function() {
                        return $("#formBuatInvoice input[name='mismassOrderId']").val();
                    },
                } 
            }
        },
    },
    messages: {
        "tanggalInvoice": "Tidak Boleh Kosong",
        "consFirstName[]": "Tidak Boleh Kosong",
        "consMiddleName[]": "Tidak Boleh Kosong",
        "consLastName[]": "Tidak Boleh Kosong",
        "consEmail[]": "Tidak Boleh Kosong",
        "consPhone[]": "Tidak Boleh Kosong",
        "consAddress[]": "Tidak Boleh Kosong",
        "consSubDistrict[]": "Tidak Boleh Kosong",
        "consDistrict[]": "Tidak Boleh Kosong",
        "consCity[]": "Tidak Boleh Kosong",
        "consProv[]": "Tidak Boleh Kosong",
        "consPostalCode[]": "Tidak Boleh Kosong",
        "warehouse[]":"Pilih Salah Satu",
        "service[]":"Pilih Salah Satu",
        "satuanBerat":"Pilih Salah Satu",
        "panjang[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "lebar[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "tinggi[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "item[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "kg[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "pricePer[]": "Tidak Boleh Kosong",
        "insurancePriceItem[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "insurancePercent[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "feePriceItem[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "feePercent[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "taxPriceItem[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "taxPercent[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "extraCostPrice[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "extraCostDest[]": "Tidak Boleh Kosong",
        "extraCostVendorName[]": "Tidak Boleh Kosong",
        "packingPrice[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "importPrice[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "documentPrice[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "medicinePrice[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "subTotal[]": "Tidak Boleh Kosong",
        "pembayaran": "Pilih Salah Satu",
        "invoiceDoku": {
            required:"Tidak Boleh Kosong",
            remote:"No. Invoice Telah Terdaftar"
        },
        "linkDoku": {
            required:"Tidak Boleh Kosong",
            remote:"Link Telah Terdaftar"
        },
        "namaBank": "Tidak Boleh Kosong",
        "namaRekening": "Tidak Boleh Kosong",
        "noRekening": "Tidak Boleh Kosong"
    },
    submitHandler: function(form) {

        $.ajax({
            type: "GET",
            url: location.origin+"/shiplist/buat/invoice",
            data: $(form).serialize(),
            beforeSend: function() {
                loading(form);
            },
            success: function(msg) {
                var json = JSON.parse(msg);

                if (json.status == "Berhasil") {

                    //NOTIF SUKSES
                    Swal.fire(json.status, json.text, 'success');
                    $('#createInvoice').modal('hide');
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                    $('body').css('padding-right', '0');

                    //RELOAD PAGE
                    pageReload(location.origin+"/shiplist");

                } else {

                    //NOTIF GAGAL
                    Swal.fire(json.status, json.text, 'error');
                    let btn = "form button[type='submit']";
                    $(btn).attr("disabled", false);
                    $(btn).text("Submit");

                }

                $("body").css("padding-right", "0");
            }

        });

    }

});

$("#formEditInvoice").validate({
    errorClass: "error fail-alert is-invalid",
    rules:{
        "invoiceDoku":{
            remote:{
                url: location.origin+"/check/invoicelinkdoku",
                type: "GET",
                data: {
                    invoiceDoku: function() {
                        return $("#formEditInvoice #invoiceDoku").val();
                    },
                    mismassOrderId: function() {
                        return $("#formEditInvoice input[name='mismassOrderId']").val();
                    },
                } 
            }
        },
        "linkDoku":{
            remote:{
                url: location.origin+"/check/invoicelinkdoku",
                type: "GET",
                data: {
                    linkDoku: function() {
                        return $("#formEditInvoice #linkDoku").val();
                    },
                    mismassOrderId: function() {
                        return $("#formEditInvoice input[name='mismassOrderId']").val();
                    },
                } 
            }
        }
    },
    messages: {
        "tanggalInvoice": "Tidak Boleh Kosong",
        "warehouse[]":"Pilih Salah Satu",
        "service[]":"Pilih Salah Satu",
        "satuanBerat":"Pilih Salah Satu",
        "panjang[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "lebar[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "tinggi[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "item[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "kg[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "pricePer[]": "Tidak Boleh Kosong",
        "insurancePriceItem[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "insurancePercent[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "feePriceItem[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "feePercent[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "taxPriceItem[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "taxPercent[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "extraCostPrice[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "extraCostDest[]": "Tidak Boleh Kosong",
        "extraCostVendorName[]": "Tidak Boleh Kosong",
        "packingPrice[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "importPrice[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "documentPrice[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "medicinePrice[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "subTotal[]": "Tidak Boleh Kosong",
        "pembayaran": "Pilih Salah Satu",
        "invoiceDoku": {
            required:"Tidak Boleh Kosong",
            remote:"No. Invoice Telah Terdaftar"
        },
        "linkDoku": {
            required:"Tidak Boleh Kosong",
            remote:"Link Telah Terdaftar"
        },
        "namaBank": "Tidak Boleh Kosong",
        "namaRekening": "Tidak Boleh Kosong",
        "noRekening": "Tidak Boleh Kosong"
    },
    submitHandler: function(form) {

        $.ajax({
            type: "GET",
            url: location.origin+"/shiplist/edit/invoice",
            data: $(form).serialize(),
            beforeSend: function() {
                loading(form);
            },
            success: function(msg) {
                var json = JSON.parse(msg);

                if (json.status == "Berhasil") {

                    //NOTIF SUKSES
                    Swal.fire(json.status, json.text, 'success');
                    $('#createInvoice').modal('hide');
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                    $('body').css('padding-right', '0');

                    //RELOAD PAGE
                    pageReload(location.origin+"/shiplist");

                } else {

                    //NOTIF GAGAL
                    Swal.fire(json.status, json.text, 'error');
                    let btn = "form button[type='submit']";
                    $(btn).attr("disabled", false);
                    $(btn).text("Submit");

                }

                $("body").css("padding-right", "0");
            }

        });

    }

});

$("#formBuatResi").validate({
    errorClass: "error fail-alert is-invalid",
    messages: {
        "tipeForwarder[]": "Pilih Salah Satu",
        "namaForwarder[]":function(){
            if($("select[name='tipeForwarder[]']").val()=="MISMASS"){
                return "Tidak Boleh Kosong";
            }
            return "Pilih Salah Satu";
        },
        "noResi[]":"Tidak Boleh Kosong",
    },
    submitHandler: function(form) {

        $.ajax({
            type: "GET",
            url: location.origin+"/shiplist/buat/resi",
            data: $(form).serialize(),
            beforeSend: function() {
                loading(form);
            },
            success: function(msg) {
                var json = JSON.parse(msg);

                if (json.status == "Berhasil") {

                    //NOTIF SUKSES
                    Swal.fire(json.status, json.text, 'success');
                    $('#createResi').modal('hide');
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                    $('body').css('padding-right', '0');

                    //RELOAD PAGE
                    pageReload(location.origin+"/shiplist");

                    clearInterval(intervalLastShippingId);

                } else {

                    //NOTIF GAGAL
                    Swal.fire(json.status, json.text, 'error');
                    let btn = "form button[type='submit']";
                    $(btn).attr("disabled", false);
                    $(btn).text("Submit");

                }

                $("body").css("padding-right", "0");
            }

        });

    }

});
////////////////////////////////////////////////////////////////////////////////////////////////////////////CREATE INVOICE FUNCTION
$(document).on("change","#pembayaran",function(){
    onChangePembayaran(this);
});
function onChangePembayaran(a){
    let val = $(a).val(),
        modalId = $(a).closest(".modal").attr("id");
    if(val=="DOKU"){
        $("#"+modalId+" .col-doku,#"+modalId+" .row-detil-pembayaran").show();
        $("#"+modalId+" .col-bank").hide();
        $("#"+modalId+" .col-doku input").attr("required",true);
        $("#"+modalId+" .col-bank input").attr("required",false);
    }else{
        $("#"+modalId+" .col-doku").hide();
        $("#"+modalId+" .col-bank,#"+modalId+" .row-detil-pembayaran").show();
        $("#"+modalId+" .col-doku input").attr("required",false);
        $("#"+modalId+" .col-bank input").attr("required",true);
    }
    $("#"+modalId+" .col-bank input").val('');
    $("#"+modalId+" .col-doku input").val('');
}

$(document).on("click","#btnAddServiceElement",function(){
    let modalId = $(this).closest(".modal").attr("id");
    createServiceElement(modalId);
});
////////////////////////////////////////////////////////////////////////////////////////////////////////////CREATE RESI FUNCTION
$(document).on("change","select[name='tipeForwarder[]']",function(){
    let id = $(this).val(),
        resiId = $(this).closest(".resi").attr("id"),
        modalId = $(this).closest(".modal").attr("id"),
        labelNamaForwarder="Nama Ekspedisi",
        element="<select class='form-control' name='namaForwarder[]' id='namaForwarder' required>"+
                    "<option value='' hidden>Pilih Nama Ekspedisi</option>"+
                    "<option value='JNE'>JNE</option>"+
                    "<option value='J&T'>J&T</option>"+
                    "<option value='TIKI'>TIKI</option>"+
                    "<option value='LALAMOVE'>LALAMOVE</option>"+
                    "<option value='SICEPAT'>SICEPAT</option>"+
                    "<option value='CENTRAL CARGO'>CENTRAL CARGO</option>"+
                    "<option value='GO BOX'>GO BOX</option>"+
                    "<option value='ANTERAJA'>ANTERAJA</option>"+
                "</select>";
        
    $("#"+modalId+" #"+resiId+" input[name='noResi']").attr("readonly",false).attr("required",true);
    $("#"+modalId+" #"+resiId+" .col-namaForwarder").html("");
    $("#"+modalId+" #"+resiId+" input[name='noResi']").val("");
    
    if(id=="MISMASS"){
        labelNamaForwarder = "Nama Kurir";
        element = "<input type='text' name='namaForwarder[]' id='namaForwarder' class='form-control' placeholder='Input Nama Kurir' onkeyup='this.value=this.value.toUpperCase()' required>";
        $("#"+modalId+" #"+resiId+" input[name='noResi[]']").attr("required",false).attr("readonly",true);
        lastShippingId(modalId,resiId);
    }

    $("#"+modalId+" #"+resiId+"  .col-namaForwarder").append("<div class='form-group'><label for='namaForwarder'>"+labelNamaForwarder+"</label>"+element+"</div>");
    $("#"+modalId+" #"+resiId+" .col-namaForwarder,#"+modalId+" .row-noresi").show();

    intervalLastShippingId = setInterval(() => {
        lastShippingId(modalId,resiId);
    }, 1000*60*3);
});
////////////////////////////////////////////////////////////////////////////////////////////////////////////SERVICES FUNCTION
$(document).on("click",".deleteService",function(){
    let modalId = $(this).closest(".modal").attr("id"),
        servicesLength = $("#"+modalId+" .services").length,
        serviceElementId = $(this).closest("#"+modalId+" .services").attr("id");        

    if(servicesLength>1){
        $("#"+serviceElementId).remove();
        servicesLength = $("#"+modalId+" .services").length;

        for(let i=0;i<=servicesLength-1;i++){
            $("#"+modalId+" .services").eq(i).attr("id",i);
            $("#"+modalId+" .services h5").html("Services#"+(i+1));
            $("#"+modalId+" .services .insurancePriceItem").eq(i).attr("name","insurancePriceItem"+i);
            $("#"+modalId+" .services .insurancePercent").eq(i).attr("name","insurancePercent"+i);
            $("#"+modalId+" .services .insuranceTotal").eq(i).attr("name","insuranceTotal"+i);
            $("#"+modalId+" .services .feePriceItem").eq(i).attr("name","feePriceItem"+i);
            $("#"+modalId+" .services .feePercent").eq(i).attr("name","feePercent"+i);
            $("#"+modalId+" .services .feeTotal").eq(i).attr("name","feeTotal"+i);
            $("#"+modalId+" .services .taxPriceItem").eq(i).attr("name","taxPriceItem"+i);
            $("#"+modalId+" .services .taxPercent").eq(i).attr("name","taxPercent"+i);
            $("#"+modalId+" .services .taxTotal").eq(i).attr("name","taxTotal"+i);
            $("#"+modalId+" .services .extraCostPrice").eq(i).attr("name","extraCostPrice"+i);
            $("#"+modalId+" .services .extraCostDest").eq(i).attr("name","extraCostDest"+i);
            $("#"+modalId+" .services .extraCostVendorName").eq(i).attr("name","extraCostVendorName"+i);
            $("#"+modalId+" .services .packingPrice").eq(i).attr("name","packingPrice"+i);
            $("#"+modalId+" .services .packingDesc").eq(i).attr("name","packingDesc"+i);
            $("#"+modalId+" .services .importPrice").eq(i).attr("name","importPrice"+i);
            $("#"+modalId+" .services .importDesc").eq(i).attr("name","importDesc"+i);
            $("#"+modalId+" .services .documentPrice").eq(i).attr("name","documentPrice"+i);
            $("#"+modalId+" .services .documentDesc").eq(i).attr("name","documentDesc"+i);
            $("#"+modalId+" .services .medicinePrice").eq(i).attr("name","medicinePrice"+i);
            $("#"+modalId+" .services .medicineDesc").eq(i).attr("name","medicineDesc"+i);
        }

        allCalc(modalId);
    }    
});

$(document).on("click",".deleteAdditional",function(){
    let modalId = $(this).closest(".modal").attr("id"),
        additionalElement = $(this).closest("#"+modalId+" .row"),
        serviceElementId = $(this).closest("#"+modalId+" .services").attr("id");
        addElementDataId = additionalElement.attr("data-id");
    additionalElement.remove();
    $("#"+modalId+" #"+serviceElementId+" option[value='"+addElementDataId+"']").show();
    removeAdditionalResetData(serviceElementId,addElementDataId,modalId);
    allCalc(modalId);
    ruleForAdditional(addElementDataId,serviceElementId,"remove",modalId);
});

$(document).on("change","select[name='warehouse[]']",function(){
    onChangeWarehouseGetServiceList(this);
});

function onChangeWarehouseGetServiceList(a){
    let id = $(a).val(),
        modalId = $(a).closest(".modal").attr("id"),
        serviceElementId = $(a).closest("#"+modalId+" .services").attr("id");
    $.ajax({
        type: "GET",
        url: location.origin+"/check/getservlist",
        data: {
            warehouseId:id,
        },
        async:false,
        success: function(msg) {
            let json = JSON.parse(msg);
            $("#"+modalId+" #"+serviceElementId+" .col-service").show();
            $("#"+modalId+" #"+serviceElementId+" select[name='service[]']").html(json.data).attr("required",true);
            $("#"+modalId+" #"+serviceElementId+" select[name='satuanBerat']").val("");
            $("#"+modalId+" #"+serviceElementId+" .col-item").hide();
            $("#"+modalId+" #"+serviceElementId+" .col-volume").hide();
            $("#"+modalId+" #"+serviceElementId+" .col-kg").hide();
            $("#"+modalId+" #"+serviceElementId+" .col-priceper").hide();
            $("#"+modalId+" #"+serviceElementId+" input[name='kg[]']").val(0);
            $("#"+modalId+" #"+serviceElementId+" input[name='panjang[]']").val(0);
            $("#"+modalId+" #"+serviceElementId+" input[name='lebar[]']").val(0);
            $("#"+modalId+" #"+serviceElementId+" input[name='tinggi[]']").val(0);
            $("#"+modalId+" #"+serviceElementId+" input[name='item[]']").val(0);
            $("#"+modalId+" #"+serviceElementId+" input[name='pricePer[]']").val(0);
            $("#"+modalId+" #"+serviceElementId+" input[name='subTotal[]']").val(0);
            if(modalId=="createInvoice"){
                allCalc(modalId);
            }
        }
    });
}

$(document).on("change","select[name='service[]']",function(){
    onChangeServiceGetUOMList(this);
});

function onChangeServiceGetUOMList(a){
    let id = $(a).val(),
        modalId = $(a).closest(".modal").attr("id"),
        serviceElementId = $(a).closest("#"+modalId+" .services").attr("id");
    $.ajax({
        type: "GET",
        url: location.origin+"/check/getservdata",
        data: {
            serviceId:id,
        },
        async:false,
        success: function(msg) {
            let json = JSON.parse(msg);
            $("#"+modalId+" #"+serviceElementId+" input[name='pricePerKg']").val(json.priceKg);
            $("#"+modalId+" #"+serviceElementId+" input[name='pricePerVol']").val(json.priceVol);
            $("#"+modalId+" #"+serviceElementId+" input[name='pricePerItem']").val(json.priceItem);

            $.ajax({
                type: "GET",
                url: location.origin+"/check/getuomlist",
                data: {
                    serviceId:id,
                },
                async:false,
                success: function(msg) {
                    let json = JSON.parse(msg);
                    $("#"+modalId+" #"+serviceElementId+" select[name='satuanBerat']").html(json.data).attr("required",true);
                    $("#"+modalId+" #"+serviceElementId+" select[name='satuanBerat']").val("");
                    $("#"+modalId+" #"+serviceElementId+" .col-item").hide();
                    $("#"+modalId+" #"+serviceElementId+" .col-volume").hide();
                    $("#"+modalId+" #"+serviceElementId+" .col-kg").hide();
                    $("#"+modalId+" #"+serviceElementId+" .col-priceper").hide();
                    $("#"+modalId+" #"+serviceElementId+" input[name='kg[]']").val(0);
                    $("#"+modalId+" #"+serviceElementId+" input[name='panjang[]']").val(0);
                    $("#"+modalId+" #"+serviceElementId+" input[name='lebar[]']").val(0);
                    $("#"+modalId+" #"+serviceElementId+" input[name='tinggi[]']").val(0);
                    $("#"+modalId+" #"+serviceElementId+" input[name='item[]']").val(0);
                    $("#"+modalId+" #"+serviceElementId+" input[name='pricePer[]']").val(0);
                    $("#"+modalId+" #"+serviceElementId+" input[name='subTotal[]']").val(0);
                    if(modalId=="createInvoice"){
                        allCalc(modalId);
                    }
                }
            });
        }
    });
}

$(document).on("change","select[name='satuanBerat']",function(){
    onChangeSatuanBerat(this);
});

function onChangeSatuanBerat(a){
    let val = $(a).val(),
        modalId = $(a).closest(".modal").attr("id"),
        formId = $(a).closest("#"+modalId+" form").attr("id");
        serviceElementId = $(a).closest("#"+modalId+" .services").attr("id");
    
    $("#"+modalId+" .row-detil-biaya").show();
    
    if(val=="KG"){
        $("#"+modalId+" #"+serviceElementId+" .col-kg").show();
        $("#"+modalId+" #"+serviceElementId+" .col-volume,#"+serviceElementId+" .col-item").hide();
        $("#"+modalId+" #"+serviceElementId+" input[name='kg[]']").attr("required",true);
        $("#"+modalId+" #"+serviceElementId+" input[name='kg[]']").attr("readonly",false);
        $("#"+modalId+" #"+serviceElementId+" input[name='panjang[]'],#"+serviceElementId+" input[name='lebar[]'],#"+serviceElementId+" input[name='tinggi[]'],#"+serviceElementId+" input[name='item[]']").attr("required",false);
        $("#"+modalId+" #"+serviceElementId+" label[for='pricePer']").html("Harga/Kg");
        $("#"+modalId+" #"+formId).validate();
        $("#"+modalId+" #"+serviceElementId+" input[name='kg[]']").rules("add", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='panjang[]']").rules("remove", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='lebar[]']").rules("remove", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='tinggi[]']").rules("remove", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='item[]']").rules("remove", "greaterThanZero");
    }else if(val=="VOL"){
        $("#"+modalId+" #"+serviceElementId+" .col-volume,#"+serviceElementId+" .col-kg").show();
        $("#"+modalId+" #"+serviceElementId+" .col-item").hide();
        $("#"+modalId+" #"+serviceElementId+" input[name='panjang[]'],#"+serviceElementId+" input[name='lebar[]'],#"+serviceElementId+" input[name='tinggi[]']").attr("required",true);
        $("#"+modalId+" #"+serviceElementId+" input[name='item[]'],#"+serviceElementId+" input[name='kg[]']").attr("required",false);
        $("#"+modalId+" #"+serviceElementId+" input[name='kg[]']").attr("readonly",true);
        $("#"+modalId+" #"+serviceElementId+" label[for='pricePer']").html("Harga/Vol");
        $("#"+modalId+" #"+formId).validate();
        $("#"+modalId+" #"+serviceElementId+" input[name='panjang[]']").rules("add", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='lebar[]']").rules("add", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='tinggi[]']").rules("add", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='item[]']").rules("remove", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='kg[]']").rules("remove", "greaterThanZero");
    }else{
        $("#"+modalId+" #"+serviceElementId+" .col-item").show();
        $("#"+modalId+" #"+serviceElementId+" .col-volume,#"+serviceElementId+" .col-kg").hide();
        $("#"+modalId+" #"+serviceElementId+" input[name='item[]']").attr("required",true);
        $("#"+modalId+" #"+serviceElementId+" input[name='panjang[]'],#"+serviceElementId+" input[name='lebar[]'],#"+serviceElementId+" input[name='tinggi[]'],#"+serviceElementId+" input[name='kg[]']").attr("required",false);
        $("#"+modalId+" #"+serviceElementId+" label[for='pricePer']").html("Harga/Item");
        $("#"+modalId+" #"+formId).validate();
        $("#"+modalId+" #"+serviceElementId+" input[name='item[]']").rules("add", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='panjang[]']").rules("remove", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='lebar[]']").rules("remove", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='tinggi[]']").rules("remove", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='kg[]']").rules("remove", "greaterThanZero");
    }

    $("#"+modalId+" #"+serviceElementId+" .col-priceper").show();
    $("#"+modalId+" #"+serviceElementId+" input[name='kg[]']").val(0);
    $("#"+modalId+" #"+serviceElementId+" input[name='panjang[]']").val(0);
    $("#"+modalId+" #"+serviceElementId+" input[name='lebar[]']").val(0);
    $("#"+modalId+" #"+serviceElementId+" input[name='tinggi[]']").val(0);
    $("#"+modalId+" #"+serviceElementId+" input[name='item[]']").val(0);
    $("#"+modalId+" #"+serviceElementId+" input[name='pricePer[]']").val(0);
    $("#"+modalId+" #"+serviceElementId+" input[name='subTotal[]']").val(0);

    if(modalId=="createInvoice"){
        allCalc(modalId);
    }
}

$(document).on("change","select[name='additionalService']",function(){
    onChangeAdditionalService(this);
});
function onChangeAdditionalService(a){
    let value = $(a).val(),
        modalId = $(a).closest(".modal").attr("id"),
        serviceElementId = $(a).closest(".services").attr("id"),
        choosedElement = createAdditionalServiceElement(value);
    $("#"+modalId+" #"+serviceElementId+" .row-additional").before(choosedElement);
    $("#"+modalId+" #"+serviceElementId+" option[value='"+value+"']").hide();
    $(a).val("");
    ruleForAdditional(value,serviceElementId,"add",modalId);
}

$(document).on("keyup","input[name='kg[]']",keyUpFuncKC);
$(document).on("keyup","input[name='panjang[]']",keyUpFunc);
$(document).on("keyup","input[name='lebar[]']",keyUpFunc);
$(document).on("keyup","input[name='tinggi[]']",keyUpFunc);
$(document).on("keyup","input[name='item[]']",keyUpFunc);
$(document).on("keyup","input[name='medicinePrice[]']",keyUpFunc);
$(document).on("keyup","input[name='medicineDesc[]']",function(){
    let val = $(this).val(),
        modalId = $(this).closest(".modal").attr("id"),
        serviceElementId = $(this).closest("#"+modalId+" .services").attr("id");
    $("#"+modalId+" input[name='medicineDesc"+serviceElementId+"']").val(val);
});
$(document).on("keyup","input[name='documentPrice[]']",keyUpFunc);
$(document).on("keyup","input[name='documentDesc[]']",function(){
    let val = $(this).val(),
        modalId = $(this).closest(".modal").attr("id"),
        serviceElementId = $(this).closest("#"+modalId+" .services").attr("id");
    $("#"+modalId+" input[name='documentDesc"+serviceElementId+"']").val(val);
});
$(document).on("keyup","input[name='importPrice[]']",keyUpFunc);
$(document).on("keyup","input[name='importDesc[]']",function(){
    let val = $(this).val(),
        modalId = $(this).closest(".modal").attr("id"),
        serviceElementId = $(this).closest("#"+modalId+" .services").attr("id");
    $("#"+modalId+" input[name='importDesc"+serviceElementId+"']").val(val);
});
$(document).on("keyup","input[name='packingPrice[]']",keyUpFunc);
$(document).on("keyup","input[name='packingDesc[]']",function(){
    let val = $(this).val(),
        modalId = $(this).closest(".modal").attr("id"),
        serviceElementId = $(this).closest("#"+modalId+" .services").attr("id");
    $("#"+modalId+" input[name='packingDesc"+serviceElementId+"']").val(val);
});
$(document).on("keyup","input[name='insurancePriceItem[]']",keyUpFunc);
$(document).on("keyup","input[name='insurancePercent[]']",keyUpFunc);
$(document).on("keyup","input[name='feePriceItem[]']",keyUpFunc);
$(document).on("keyup","input[name='feePercent[]']",keyUpFunc);
$(document).on("keyup","input[name='taxPriceItem[]']",keyUpFunc);
$(document).on("keyup","input[name='taxPercent[]']",keyUpFunc);
$(document).on("keyup","input[name='extraCostPrice[]']",keyUpFunc);
$(document).on("keyup","input[name='extraCostDest[]']",function(){
    let val = $(this).val(),
        modalId = $(this).closest(".modal").attr("id"),
        serviceElementId = $(this).closest("#"+modalId+" .services").attr("id");
    $("#"+modalId+" input[name='extraCostDest"+serviceElementId+"']").val(val);
});
$(document).on("keyup","input[name='extraCostVendorName[]']",function(){
    let val = $(this).val(),
        modalId = $(this).closest(".modal").attr("id"),
        serviceElementId = $(this).closest("#"+modalId+" .services").attr("id");
    $("#"+modalId+" input[name='extraCostVendorName"+serviceElementId+"']").val(val);
});
////////////////////////////////////////////////////////////////////////////////////////////////////////////FILTERS
$('#filterTanggalOrder,#filterTanggalAwal,#filterTanggalAkhir,#tanggalInvoice').daterangepicker({
    singleDatePicker: true,
    autoApply:true,
    showDropdowns: true,
    locale: {
        format: 'DD-MM-YYYY'
    }
});

$('#filterTanggalAwal').on('apply.daterangepicker', function() {
    minDate = $('#filterTanggalAwal').val();
    $('#filterTanggalAkhir').val(minDate);

    $('#filterTanggalAkhir').daterangepicker({
        singleDatePicker: true,
        autoApply:true,
        showDropdowns: true,
        minDate: minDate,
        locale: {
            format: 'DD-MM-YYYY'
        }
    });
});

$('#filterTanggalAwal').on('hide.daterangepicker', function() {
    minDate = $('#filterTanggalAwal').val();
    $('#filterTanggalAkhir').val(minDate);

    $('#filterTanggalAkhir').daterangepicker({
        singleDatePicker: true,
        autoApply:true,
        showDropdowns: true,
        minDate: minDate,
        locale: {
            format: 'DD-MM-YYYY'
        }
    });
});

$("#viewOne").on("click",function(){
    let filter = $("#filterTanggalOrder").val(),
        custTypeId = $(".btn-select").attr("id");
    refreshTable(tableCi,location.origin+"/shiplist/table/order/"+custTypeId+"?filterTanggal="+filter,"table-ci_info");
});

$("#resetOne").on("click",function(){
    let custTypeId = $(".btn-select").attr("id");
    refreshTable(tableCi,location.origin+"/shiplist/table/order/"+custTypeId+"?filterTanggal=","table-ci_info");
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
    refreshTable(table,location.origin+"/shiplist/table/"+tableType+"/"+custTypeId+"?filterTanggalAwal="+filterTanggalAwal+"&filterTanggalAkhir="+filterTanggalAkhir+"&filterWarehouse="+filterWarehouse+"&filterService="+filterService,tableInfo);
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
    $("#filterWarehouse").val("");
    $("#filterService").val("");
    // $(".col-filterService").hide();
    $('#filterTanggalOrder,#filterTanggalAwal,#filterTanggalAkhir,#tanggalInvoice').daterangepicker({
        singleDatePicker: true,
        autoApply:true,
        showDropdowns: true,
        locale: {
            format: 'DD-MM-YYYY'
        }
    });
    refreshTable(table,location.origin+"/shiplist/table/"+tableType+"/"+custTypeId+"?filterTanggalAwal=&filterTanggalAkhir=&filterWarehouse=&filterService=",tableInfo);
});
////////////////////////////////////////////////////////////////////////////////////////////////////////////DATATABLES
var tableCi = $('#table-ci').DataTable({
    "paging": true,
    "searching": true,
    // "responsive": true,
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
}),
tableCt = $('#table-ct').DataTable({
    "paging": true,
    "searching": true,
    // "responsive": true,
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
tableSt = $('#table-status').DataTable({
    "paging": true,
    "searching": true,
    // "responsive": true,
    "processing": true,
    "serverSide": true,
    "processing": true,
    "serverSide": true,
    "order": [],
    "ajax": {
        "url": location.origin+"/shiplist/table/tracking/IND?filterTanggalAwal=&filterTanggalAkhir=&filterWarehouse=&filterService=",
        "type": "GET",
        "dataSrc": function(json){
            $("#table-status").parent().css("overflow-x","auto");
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

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function createResiElement(resiId,data){

    let a = resiId > 0 ? 'mt-2' : '',
        volumecheck = data['panjang'] > 0 ? "( "+data['panjang']+" x "+data['lebar']+" x "+data['tinggi']+" )" : '';

    return "<div class='resi "+a+"' id='"+resiId+"' style='padding:10px;border:1px solid black'>"+
    "<div class='detil-penerima'>"+
        "<div class='row'>"+
            "<div class='col'>"+
                "<label for='namaPenerima'>Nama Penerima</label>"+
            "</div>"+
            "<div class='col'>"+
                "<div class='namaPenerima'>"+data['namaPenerima']+"</div>"+
            "</div>"+
        "</div>"+
        "<div class='row'>"+
            "<div class='col'>"+
                "<label for='telponPenerima'>Telpon Penerima</label>"+
            "</div>"+
            "<div class='col'>"+
                "<div class='telponPenerima'>"+data['telponPenerima']+"</div>"+
            "</div>"+
        "</div>"+
        "<div class='row'>"+
            "<div class='col'>"+
                "<label for='alamatPenerima'>Alamat Penerima</label>"+
            "</div>"+
            "<div class='col'>"+
                "<div class='alamatPenerima'>"+data['alamatPenerima']+"</div>"+
            "</div>"+
        "</div>"+    
        "<div class='row'>"+
            "<div class='col'>"+
                "<label for='beratBarang'>Berat</label>"+
            "</div>"+
            "<div class='col'>"+
                "<div class='beratBarang'>"+data['berat']+" Kg "+volumecheck+"</div>"+
            "</div>"+
        "</div>"+    
        "<div class='row'>"+
            "<div class='col'>"+
                "<label for='itemBarang'>Item</label>"+
            "</div>"+
            "<div class='col'>"+
                "<div class='itemBarang'>"+data['item']+"</div>"+
            "</div>"+
        "</div>"+     
        "<div class='row'>"+
            "<div class='col'>"+
                "<label for='totalBarang'>Total</label>"+
            "</div>"+
            "<div class='col'>"+
                "<div class='totalBarang'>Rp."+masking(data['total'])+"</div>"+
            "</div>"+
        "</div>"+    
        "<hr>"+
    "</div>"+    
    "<div class='row'>"+
        "<div class='col'>"+
            "<div class='form-group'>"+
                "<label for='tipeForwarder'>Jasa Pengiriman</label>"+
                "<select name='tipeForwarder[]' id='tipeForwarder' class='form-control' required>"+
                    "<option value='' hidden>PILIH JASA PENGIRIMAN</option>"+
                    "<option value='VENDOR'>VENDOR EKSPEDISI LAIN</option>"+
                    "<option value='MISMASS'>KURIR MISMASS</option>"+
                "</select>"+
            "</div>"+
        "</div>"+
    "<div class='col col-namaForwarder'></div>"+
    "</div>"+
    "<div class='row row-noresi'>"+
        "<div class='col'>"+
            "<div class='form-group'>"+
                "<label for='noResi'>Nomor Resi</label>"+
                "<input type='text' name='noResi[]' id='noResi' class='form-control'>"+
            "</div>"+
        "</div>"+
    "</div>"+
"</div>";

}