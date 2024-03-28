$("table").on("click","#buatBtn",function(){
    $(".preloaderz").show();
    let orderStatusId = $(this).closest("tr").find("#btnStatusId").text();
    if(orderStatusId=="HOLD"){
        $(".preloaderz").hide();
        return Swal.fire(
            'Status Order HOLD!',
            'Silahkan ganti status terlebih dahulu',
            'error'
          )
    }
    let modalId = "createInvoice",
        typeId = $(".btn-select").attr("id");
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
        tanggal = $(this).attr("data-tanggal"),
        custTypeId = $(".btn-select").attr("id");
        
    resetModalInvoice(modalId);

    $("#"+modalId+" .alert-success").show();
    $("#"+modalId+" .searchElem").show();
    $("#"+modalId+" .modal-footer").css({
        justifyContent:"space-between"
    });
    if(custTypeId=="IND"){
        $("#"+modalId+" .alert-success").hide();
        $("#"+modalId+" .searchElem").hide();
        $("#"+modalId+" .modal-footer").css({
            justifyContent:"right"
        });
    }

    $("#"+modalId+" input[name='convertToSGD']").prop("checked",false);
    $(".row-convert").hide();

    $("#"+modalId+" .modal-title").text(typeId=="IND"?"Buat Invoice Individual":"Buat Invoice Corporate");
    $("#"+modalId+" input[name='foreignSymbol']").val("SGD");
    $("#"+modalId+" label[for='tanggal']").text(tanggal);
    $("#"+modalId+" label[for='customer']").text(firstName+" "+middleName+" "+lastName);
    $("#"+modalId+" label[for='alamat']").text(address+", "+subDistrict+", "+district+", "+city+", "+prov+", "+postalCode);
    $("#"+modalId+" label[for='telpon']").text(phone);
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

    //Set Local Storage
    let subTotals = [],
        itemTotals = [],
        cbmTotals = [],
        diskonTotals = [],
        kgTotals = [];

    localStorage.setItem("subTotals",JSON.stringify(subTotals));
    localStorage.setItem("itemTotals",JSON.stringify(itemTotals));
    localStorage.setItem("kgTotals",JSON.stringify(kgTotals));
    localStorage.setItem("cbmTotals",JSON.stringify(cbmTotals));
    localStorage.setItem("diskonTotals",JSON.stringify(diskonTotals));
    
    createServiceElement(modalId);

    $(".preloaderz").hide();

    $("#"+modalId+"").modal("show");
});

$("table").on("click","#editInvoiceBtn",function(){

    let modalId = "editInvoice",
        mismassOrderId = $(this).attr("data-id"),
        tanggalInvoice = $(this).attr("data-tglInv");

    resetModalInvoice(modalId);
    $(".preloaderz").show();

    $.ajax({
        type: "GET",
        url: location.origin+"/shiplist/edit/invoice/getdata",
        data: {
            mismassOrderId:mismassOrderId,
        },
        async: false,
        success: function(msg) {
            let num=0,
                totalCbm=0,
                totalWeight=0,
                totalItem=0,
                totalDiskon=0,
                totalBiaya=0,
                dokuInvoiceId,
                dokuLink,
                bankName,
                bankAccountName,
                bankAccountId,
                firstName,
                middleName,
                lastName,
                address,subDistrict,
                district,city,prov,
                email,postalCode,
                actualKg,
                json = JSON.parse(msg),
                typeId = $(".btn-select").attr("id"),
                subTotals = [],itemTotals = [],kgTotals = [],diskonTotals = [],cbmTotals=[],
                timeout=0;
                
                localStorage.setItem("subTotals",JSON.stringify(subTotals));
                localStorage.setItem("itemTotals",JSON.stringify(itemTotals));
                localStorage.setItem("kgTotals",JSON.stringify(kgTotals));
                localStorage.setItem("cbmTotals",JSON.stringify(cbmTotals));
                localStorage.setItem("diskonTotals",JSON.stringify(diskonTotals));
                
                json.data.forEach(e => {
                    dokuInvoiceId=e['doku_invoice_id'];
                    dokuLink=e['doku_link'];
                    bankName=e['bank_name'];
                    bankAccountName=e['bank_account_name'];
                    bankAccountId=e['bank_account_id'];
                    fcValue = e['fc_value'];
                    fcSymbol = e['fc_symbol'];

                    firstName = e['sender_first_name'],
                    middleName = e['sender_middle_name'],
                    lastName = e['sender_last_name'];
                    address = e['sender_address'];
                    subDistrict = e['sender_sub_district'];
                    district = e['sender_district'];
                    city = e['sender_city'];
                    prov = e['sender_prov'];
                    email = e['sender_email'];
                    postalCode = e['sender_postal_code'];
                    phone = e['sender_phone'];
                    secondName = "";
                    secondPhone = "";
                    if(e['cust_type_id']=="IND"){
                        firstName = e['cons_first_name'];
                        middleName = e['cons_middle_name'];
                        lastName = e['cons_last_name'];
                        address = e['cons_address'];
                        subDistrict = e['cons_sub_district'];
                        district = e['cons_district'];
                        city = e['cons_city'];
                        prov = e['cons_prov'];
                        email = e['cons_email'];
                        postalCode = e['cons_postal_code'];
                        phone = e['cons_phone'];
                        secondName = e['sender_first_name'];
                        secondPhone = e['sender_phone'];
                    }
                });

                json.data.forEach(e => {
                    setTimeout(() => {                    
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
                    
                    $("#"+modalId+" label[for='tanggal']").text(moment(e['orderCreatedAt']).format("DD MMMM YYYY"));
                    $("#"+modalId+" label[for='customer']").text(firstName+" "+middleName+" "+lastName);
                    $("#"+modalId+" label[for='alamat']").text(address+", "+subDistrict+", "+district+", "+city+", "+prov+", "+postalCode);
                    $("#"+modalId+" label[for='telpon']").text(phone);
                    $("#"+modalId+" label[for='noInvoice']").text(e['mismass_invoice_id']);
                    $("#"+modalId+" input[name='tanggalInvoice']").val(moment(e['mismass_invoice_date']).format("DD-MM-YYYY"));
    
                    $("#"+modalId+" input[name='mismassOrderId']").val(e['mismass_order_id']);
                    $("#"+modalId+" input[name='mismassInvoiceId']").val(e['mismass_invoice_id']);
    
                    $("#"+modalId+" input[name='createdAt']").val(e['created_at']);
                    $("#"+modalId+" input[name='serviceName']").val(e['service_name']);
                    $("#"+modalId+" input[name='mismassInvoiceLink']").val(e['mismass_invoice_link']);
    
                    $("#"+modalId+" input[name='custId']").val(e['cust_id']);
                    $("#"+modalId+" input[name='custTypeId']").val(e['cust_type_id']);
    
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
                    
                    console.log($("#"+modalId+" #"+num+" input[name='senderFirstName[]']").val());
    
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
    
                    let satuanBeratVal = e['item']>0?"ITEM":(e['length']>0?"VOL":(e['cbm']>0?"CBM":"KG"));
                    $("#"+modalId+" #"+num+" select[name='satuanBerat']").val(satuanBeratVal);
                    onChangeSatuanBerat($("#"+modalId+" #"+num+" select[name='satuanBerat']"));
    
                    $("#"+modalId+" #"+num+" input[name='panjang[]']").val(e['length']);
                    $("#"+modalId+" #"+num+" input[name='lebar[]']").val(e['width']);
                    $("#"+modalId+" #"+num+" input[name='tinggi[]']").val(e['height']);
                    $("#"+modalId+" #"+num+" input[name='kg[]']").val(e['weight'].toFixed(2).replace(".",","));
                    $("#"+modalId+" #"+num+" input[name='cbm[]']").val(e['cbm'].toFixed(2).replace(".",","));
                    $("#"+modalId+" #"+num+" input[name='item[]']").val(e['item']);
                    $("#"+modalId+" #"+num+" input[name='actualKg[]']").val(e['actual_weight'].toFixed(2).replace(".",","));
    
                    // let pricePer = e['sub_total']-(e['packing_price']+e['import_permit_price']+e['document_price']+e['dr_medicine_price']+e['insurance_total']+e['fee_total']+e['tax_total']+e['extra_cost_price']);
                    $("#"+modalId+" #"+num+" input[name='pricePer[]']").val(masking(e['service_price_per'].toString()));
    
                    if(e['discount']>0){
                        onChangeAdditionalService($("#"+modalId+" #"+num+" select[name='additionalService']").val("DSK"));
                    }

                    let hargaService = 0;
                    if(satuanBeratVal=="ITEM"){
                        hargaService = e['item']*e['service_price_per'];
                    }else if(satuanBeratVal=="CBM"){
                        hargaService = e['cbm']*e['service_price_per'];
                    }else{
                        hargaService = kgRound(e['weight'])*e['service_price_per'];
                    }
                    $("#"+modalId+" #"+num+" input[name='discount[]']").val(masking(e['discount'].toString()));
                    $("#"+modalId+" #"+num+" input[name='hargaService[]']").val(masking(hargaService).toString());
                    $("#"+modalId+" #"+num+" input[name='hargaServiceAfter[]']").val(masking(hargaService-e['discount']).toString());
                    $("#"+modalId+" #"+num+" input[name='discount"+num+"']").val(e['discount']);
                    $("#"+modalId+" #"+num+" input[name='hargaService"+num+"']").val(hargaService);
                    $("#"+modalId+" #"+num+" input[name='hargaServiceAfter"+num+"']").val(hargaService-e['discount']);

                    if(e['packing']>0){
                        onChangeAdditionalService($("#"+modalId+" #"+num+" select[name='additionalService']").val("KAY"));
                    }
                    $("#"+modalId+" #"+num+" input[name='packing[]']").val(masking(e['packing'].toString()));
                    $("#"+modalId+" #"+num+" input[name='packingPer[]']").val(masking(e['packing_per']).toString());
                    $("#"+modalId+" #"+num+" input[name='packingTotal[]']").val(masking(e['packing_total']).toString());
                    $("#"+modalId+" #"+num+" input[name='packingDesc[]']").val(e['packing_desc']);
                    $("#"+modalId+" #"+num+" input[name='packing"+num+"']").val(e['packing']);
                    $("#"+modalId+" #"+num+" input[name='packingPrice"+num+"']").val(e['packing_price']);
                    $("#"+modalId+" #"+num+" input[name='packingPer"+num+"']").val(e['packing_per']);
                    $("#"+modalId+" #"+num+" input[name='packingTotal"+num+"']").val(e['packing_total']);
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
                    $("#"+modalId+" #"+num+" input[name='extraCostVendorName[]']").val(e['extra_cost_vendor_name']);
                    $("#"+modalId+" #"+num+" input[name='extraCostShippingNum[]']").val(e['extra_cost_shipping_number']);
                    $("#"+modalId+" #"+num+" input[name='extraCostPrice"+num+"']").val(e['extra_cost_price']);
                    $("#"+modalId+" #"+num+" input[name='extraCostDest"+num+"']").val(e['extra_cost_dest']);
                    $("#"+modalId+" #"+num+" input[name='extraCostVendorName"+num+"']").val(e['extra_cost_vendor_name']);
                    $("#"+modalId+" #"+num+" input[name='extraCostShippingNum"+num+"']").val(e['extra_cost_shipping_number']);
    
                    if(e['document']>0){
                        onChangeAdditionalService($("#"+modalId+" #"+num+" select[name='additionalService']").val("DOC"));
                    }
                    $("#"+modalId+" #"+num+" input[name='document[]']").val(masking(e['document'].toString()));
                    $("#"+modalId+" #"+num+" input[name='documentPer[]']").val(masking(e['document_per']).toString());
                    $("#"+modalId+" #"+num+" input[name='documentTotal[]']").val(masking(e['document_total']).toString());
                    $("#"+modalId+" #"+num+" input[name='documentDesc[]']").val(e['document_desc']);
                    $("#"+modalId+" #"+num+" input[name='document"+num+"']").val(e['document']);
                    $("#"+modalId+" #"+num+" input[name='documentPer"+num+"']").val(e['document_per']);
                    $("#"+modalId+" #"+num+" input[name='documentTotal"+num+"']").val(e['document_total']);
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
    
                    if(e['import_permit']>0){
                        onChangeAdditionalService($("#"+modalId+" #"+num+" select[name='additionalService']").val("IPM"));
                    }
                    $("#"+modalId+" #"+num+" input[name='import[]']").val(masking(e['import_permit'].toString()));
                    $("#"+modalId+" #"+num+" input[name='importPer[]']").val(masking(e['import_permit_per'].toString()));
                    $("#"+modalId+" #"+num+" input[name='importTotal[]']").val(masking(e['import_permit_total'].toString()));
                    $("#"+modalId+" #"+num+" input[name='importDesc[]']").val(e['import_permit_desc']);
                    $("#"+modalId+" #"+num+" input[name='import"+num+"']").val(e['import_permit']);
                    $("#"+modalId+" #"+num+" input[name='importPer"+num+"']").val(e['import_permit_per']);
                    $("#"+modalId+" #"+num+" input[name='importTotal"+num+"']").val(e['import_permit_total']);
                    $("#"+modalId+" #"+num+" input[name='importDesc"+num+"']").val(e['import_permit_desc']);
    
                    if(e['dr_medicine']>0){
                        onChangeAdditionalService($("#"+modalId+" #"+num+" select[name='additionalService']").val("MED"));
                    }
                    $("#"+modalId+" #"+num+" input[name='medicine[]']").val(masking(e['dr_medicine'].toString()));
                    $("#"+modalId+" #"+num+" input[name='medicinePer[]']").val(masking(e['dr_medicine_per'].toString()));
                    $("#"+modalId+" #"+num+" input[name='medicineTotal[]']").val(masking(e['dr_medicine_total'].toString()));
                    $("#"+modalId+" #"+num+" input[name='medicineDesc[]']").val(e['dr_medicine_desc']);
                    $("#"+modalId+" #"+num+" input[name='medicinePrice"+num+"']").val(e['dr_medicine']);
                    $("#"+modalId+" #"+num+" input[name='medicinePer"+num+"']").val(e['dr_medicine_per']);
                    $("#"+modalId+" #"+num+" input[name='medicineTotal"+num+"']").val(e['dr_medicine_total']);
                    $("#"+modalId+" #"+num+" input[name='medicineDesc"+num+"']").val(e['dr_medicine_desc']);
    
                    if(e['pickup_weight']>0){
                        onChangeAdditionalService($("#"+modalId+" #"+num+" select[name='additionalService']").val("PCK"));
                    }
                    $("#"+modalId+" #"+num+" input[name='pickUpWeight[]']").val(masking(e['pickup_weight'].toString()));
                    $("#"+modalId+" #"+num+" input[name='pickUpCharge[]']").val(masking(e['pickup_charge'].toString()));
                    $("#"+modalId+" #"+num+" input[name='pickUpWeight"+num+"']").val(e['pickup_weight']);
                    $("#"+modalId+" #"+num+" input[name='pickUpCharge"+num+"']").val(e['pickup_charge']);

                    if(e['additional_nom']>0){
                        onChangeAdditionalService($("#"+modalId+" #"+num+" select[name='additionalService']").val("ADD"));
                    }
                    $("#"+modalId+" #"+num+" input[name='additionalDesc[]']").val(e['additional_desc']);
                    $("#"+modalId+" #"+num+" input[name='additionalNominal[]']").val(masking(e['additional_nom'].toString()));
                    $("#"+modalId+" #"+num+" input[name='additionalDesc"+num+"']").val(e['additional_desc']);
                    $("#"+modalId+" #"+num+" input[name='additionalNominal"+num+"']").val(e['additional_nom']);
    
                    $("#"+modalId+" #"+num+" input[name='subTotal[]']").val(masking(e['sub_total'].toString()));
    
                    subTotals[num]=e['sub_total'];
                    itemTotals[num]=e['item'];
                    kgTotals[num]=e['weight'];
                    diskonTotals[num]=e['discount'];
                    cbmTotals[num]=e['cbm'];
    
                    totalWeight+=e['weight'];
                    totalCbm+=e['cbm'];
                    totalItem+=e['item'];
                    totalDiskon+=e['discount'];
                    totalBiaya+=e['sub_total'];
                    num++;
                    timeout+=100;
                }, timeout);
                });  

            setTimeout(() => {
                localStorage.setItem("subTotals",JSON.stringify(subTotals));
                localStorage.setItem("itemTotals",JSON.stringify(itemTotals));
                localStorage.setItem("kgTotals",JSON.stringify(kgTotals));
                localStorage.setItem("cbmTotals",JSON.stringify(cbmTotals));
                localStorage.setItem("diskonTotals",JSON.stringify(diskonTotals));
    
                $("#"+modalId+" .modal-title").text(typeId=="IND"?"Edit Invoice Individual":"Edit Invoice Corporate");
                $("#"+modalId+" .totalHargaBoard div .value").text(masking(totalBiaya.toString()));
                $("#"+modalId+" .totalItemBoard div .value").text(masking(totalItem.toString()));
                $("#"+modalId+" .totalDiskonBoard div .value").text(masking(totalDiskon.toString()));
                $("#"+modalId+" .totalCbmBoard div .value").text(masking(totalCbm.toString()));
                $("#"+modalId+" .totalKiloBoard div .value").text(masking(floatOrInt(totalWeight).toString()));    

                $(".preloaderz").hide();
            }, timeout);

            if(dokuInvoiceId!=""){
                onChangePembayaran($("#"+modalId+" select[name='pembayaran']").val("DOKU"));
            }else{
                onChangePembayaran($("#"+modalId+" select[name='pembayaran']").val("BANK"));
            }
            $("#"+modalId+" input[name='invoiceDoku']").val(dokuInvoiceId);
            $("#"+modalId+" input[name='linkDoku']").val(dokuLink);
            $("#"+modalId+" input[name='namaBank']").val(bankName);
            $("#"+modalId+" input[name='namaRekening']").val(bankAccountName);
            $("#"+modalId+" input[name='noRekening']").val(bankAccountId);

            $("#"+modalId+" .row-convert").hide();
            $("#"+modalId+" input[name='convertToSGD']").prop("checked",false);
            $("#"+modalId+" input[name='foreignRateValue']").attr("required",false);
            if(fcSymbol!=""){
                let finalRate = fcValue,
                    totalHargaRp = parseInt($("#"+modalId+" .totalHargaBoard div .value").text().replace(/\./g, "")),
                    totalHargaConvert = totalHargaRp/finalRate;
                    finalResult = totalHargaConvert.toFixed(2);
                $("#"+modalId+" input[name='convertToSGD']").prop("checked",true);
                $("#"+modalId+" .row-convert").show();
                $("#"+modalId+" input[name='foreignRateValue']").val(masking(finalRate.toString())).attr("required",true);
                $("#"+modalId+" input[name='foreignSymbol']").val(fcSymbol);
                $("#"+modalId+" input[name='totalHargaRP']").val(masking(totalHargaRp.toString()));
                $("#"+modalId+" input[name='totalHargaConvert']").val(masking(finalResult.toString()));
            }

            $("#"+modalId+" .alert-success").show();
            $("#"+modalId+" .searchElem").show();
            $("#"+modalId+" .modal-footer").css({
                justifyContent:"space-between"
            });
            $("#"+modalId+" .row-edit-detil-penerima").hide();
            if(typeId=="IND"){
                $("#"+modalId+" .alert-success").hide();
                $("#"+modalId+" .searchElem").hide();
                $("#"+modalId+" .modal-footer").css({
                    justifyContent:"right"
                });
                $("#"+modalId+" .row-edit-detil-penerima").show();
                $("#"+modalId+" #btnEditDetilPenerima").attr("data-id",mismassOrderId);
                $("#"+modalId+" #btnEditDetilPenerima").attr("data-firstName",firstName);
                $("#"+modalId+" #btnEditDetilPenerima").attr("data-middleName",middleName);
                $("#"+modalId+" #btnEditDetilPenerima").attr("data-lastName",lastName);
                $("#"+modalId+" #btnEditDetilPenerima").attr("data-phone",phone);
                $("#"+modalId+" #btnEditDetilPenerima").attr("data-email",email);
                $("#"+modalId+" #btnEditDetilPenerima").attr("data-address",address);
                $("#"+modalId+" #btnEditDetilPenerima").attr("data-subDistrict",subDistrict);
                $("#"+modalId+" #btnEditDetilPenerima").attr("data-district",district);
                $("#"+modalId+" #btnEditDetilPenerima").attr("data-city",city);
                $("#"+modalId+" #btnEditDetilPenerima").attr("data-prov",prov);
                $("#"+modalId+" #btnEditDetilPenerima").attr("data-postalCode",postalCode);
            }
            

            $("#"+modalId).modal("show");

        },
    });
});

$("#btnEditDetilPenerima").on("click",function(){
    let id = $(this).attr("data-id"),
        firstName = $(this).attr("data-firstName"),
        middleName = $(this).attr("data-middleName"),
        lastName = $(this).attr("data-lastName"),
        phone = $(this).attr("data-phone"),
        email = $(this).attr("data-email"),
        address = $(this).attr("data-address"),
        subDistrict = $(this).attr("data-subDistrict"),
        district = $(this).attr("data-district"),
        city = $(this).attr("data-city"),
        prov = $(this).attr("data-prov"),
        postalCode = $(this).attr("data-postalCode"),
        modalId = "editDetilPenerima",
        title = "Edit Data Penerima Individual";

    $(".preloaderz").show();

    $("#"+modalId+" input[name='firstName']").val(firstName);
    $("#"+modalId+" input[name='middleName']").val(middleName);
    $("#"+modalId+" input[name='lastName']").val(lastName);
    $("#"+modalId+" input[name='phone']").val(phone);
    $("#"+modalId+" input[name='phoneOld']").val(phone);
    $("#"+modalId+" input[name='email']").val(email);
    $("#"+modalId+" input[name='emailOld']").val(email);
    $("#"+modalId+" input[name='subDistrict']").val(subDistrict);
    $("#"+modalId+" input[name='district']").val(district);
    $("#"+modalId+" input[name='city']").val(city);
    $("#"+modalId+" input[name='address']").val(address);
    $("#"+modalId+" input[name='prov']").val(prov);
    $("#"+modalId+" input[name='postalCode']").val(postalCode);
    $("#"+modalId+" input[name='orderId']").val(id);
    $("#"+modalId+" .modal-title").text(title);

    $("#"+modalId).modal("show");

    $(".preloaderz").hide();
});

$(".copyHandle").on("click",function(){
    let modalId = $(this).closest(".modal").attr("id");
    copyText(modalId);
});

$("input[name='convertToSGD']").on("click",function(){
    let modalId = $(this).closest(".modal").attr("id");
    $("#"+modalId+" .row-convert").hide();
    $("#"+modalId+" input[name='foreignRateValue']").val(0);
    $("#"+modalId+" input[name='foreignRateValue']").attr("required",false);
    $("#"+modalId+" form").validate();
    $("#"+modalId+" input[name='foreignRateValue']").rules("remove", "greaterThanZero");
    if($(this).is(":checked")){
        $.ajax({
            type: "GET",
            url: location.origin+"/foreignrate",
            beforeSend:function(){
                $("#"+modalId+" .row-convert").before("<div id='loadload'>Loading...</div>");
            },
            success:function(msg){
                let finalRate = parseInt(JSON.parse(msg)),
                    totalHargaRp = parseInt($("#"+modalId+" .totalHargaBoard div .value").text().replace(/\./g, "")),
                    totalHargaConvert = totalHargaRp/finalRate;
                    finalResult = totalHargaConvert.toFixed(2);
                $("#"+modalId+" #loadload").remove();
                $("#"+modalId+" .row-convert").show();
                $("#"+modalId+" input[name='foreignRateValue']").attr("required",true);
                $("#"+modalId+" input[name='foreignSymbol']").val("SGD");
                $("#"+modalId+" input[name='foreignRateValue']").val(masking(finalRate.toString()));
                $("#"+modalId+" input[name='totalHargaRP']").val(masking(totalHargaRp.toString()));
                $("#"+modalId+" input[name='totalHargaConvert']").val(masking(finalResult.toString()));
                $("#"+modalId+" form").validate();
                $("#"+modalId+" input[name='foreignRateValue']").rules("add", "greaterThanZero");
            },
        });
    }
});

///////////////////////////////////////////////////////////////////////////////////////////////////////

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
        // "consSubDistrict[]": "Tidak Boleh Kosong",
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
        "cbm[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "pricePer[]": "Tidak Boleh Kosong",
        "actualKg[]": "Tidak Boleh Kosong",
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
        "extraCostShippingNum[]": "Tidak Boleh Kosong",
        "packing[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "additionalDesc[]": "Tidak Boleh Kosong",
        "additionalNominal[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "discount[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "import[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "document[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "medicine[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "pickUpWeight[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "subTotal[]": "Tidak Boleh Kosong",
        "pembayaran": "Pilih Salah Satu",
        "invoiceDoku": {
            required:"Tidak Boleh Kosong",
            remote:"No. Order Number Doku Telah Terdaftar"
        },
        "linkDoku": {
            required:"Tidak Boleh Kosong",
            remote:"Link Telah Terdaftar"
        },
        "namaBank": "Tidak Boleh Kosong",
        "namaRekening": "Tidak Boleh Kosong",
        "noRekening": "Tidak Boleh Kosong",
        "foreignRateValue": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
    },
    submitHandler: function(form) {

        $.ajax({
            type: "POST",
            url: location.origin+"/shiplist/buat/invoice",
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
                        confirmButtonText: "Print Invoice",
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
        "cbm[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "pricePer[]": "Tidak Boleh Kosong",
        "actualKg[]": "Tidak Boleh Kosong",
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
        "extraCostShippingNum[]": "Tidak Boleh Kosong",
        "discount[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "additionalDesc[]": "Tidak Boleh Kosong",
        "additionalNominal[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "packing[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "import[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "document[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "medicine[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "pickUpWeight[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "subTotal[]": "Tidak Boleh Kosong",
        "pembayaran": "Pilih Salah Satu",
        "invoiceDoku": {
            required:"Tidak Boleh Kosong",
            remote:"No. Order Number Doku Telah Terdaftar"
        },
        "linkDoku": {
            required:"Tidak Boleh Kosong",
            remote:"Link Telah Terdaftar"
        },
        "namaBank": "Tidak Boleh Kosong",
        "namaRekening": "Tidak Boleh Kosong",
        "noRekening": "Tidak Boleh Kosong",
        "foreignRateValue": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
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
                        confirmButtonText: "Print Invoice",
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

$("#formEditDetilPenerima").validate({
    errorClass: "error fail-alert is-invalid",
    rules: {
        firstName: "required",
        // lastName: "required",
        email: {
            required:true,
            email:true,
            remote: {
                url: location.origin+"/check/checkphoneandemail",
                type: "GET",
                data: {
                    email: function() {
                        return $("#formEditDetilPenerima #email").val();
                    },
                    statusCust:function(){
                        return $("#formEditDetilPenerima #statusCust").val();
                    },
                    emailOld: function() {
                        return $("#formEditDetilPenerima #emailOld").val();
                    },
                } 
            },
        },
        phone: {
            required:true,
            remote: {
                url: location.origin+"/check/checkphoneandemail",
                type: "GET",
                data: {
                    phone: function() {
                        return $("#formEditDetilPenerima #phone").val();
                    },
                    statusCust:function(){
                        return $("#formEditDetilPenerima #statusCust").val();
                    },
                    phoneOld: function() {
                        return $("#formEditDetilPenerima #phoneOld").val();
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
        
        let modalId = "editDetilPenerima",
            modalId2 = "editInvoice";
            btn = "btnEditDetilPenerima",
            firstNameEdit = $("#"+modalId+" input[name='firstName']").val(),
            middleNameEdit = $("#"+modalId+" input[name='middleName']").val(),
            lastNameEdit = $("#"+modalId+" input[name='lastName']").val(),
            phoneEdit = $("#"+modalId+" input[name='phone']").val(),
            emailEdit = $("#"+modalId+" input[name='email']").val(),
            subDistrictEdit = $("#"+modalId+" input[name='subDistrict']").val(),
            districtEdit = $("#"+modalId+" input[name='district']").val(),
            cityEdit = $("#"+modalId+" input[name='city']").val(),
            addressEdit = $("#"+modalId+" input[name='address']").val(),
            provEdit = $("#"+modalId+" input[name='prov']").val(),
            postalCodeEdit = $("#"+modalId+" input[name='postalCode']").val();
            
        firstName = $("#"+btn).attr("data-firstName",firstNameEdit);
        middleName = $("#"+btn).attr("data-middleName",middleNameEdit);
        lastName = $("#"+btn).attr("data-lastName",lastNameEdit);
        phone = $("#"+btn).attr("data-phone",phoneEdit);
        email = $("#"+btn).attr("data-email",emailEdit);
        address = $("#"+btn).attr("data-address",addressEdit);
        subDistrict = $("#"+btn).attr("data-subDistrict",subDistrictEdit);
        district = $("#"+btn).attr("data-district",districtEdit);
        city = $("#"+btn).attr("data-city",cityEdit);
        prov = $("#"+btn).attr("data-prov",provEdit);
        postalCode = $("#"+btn).attr("data-postalCode",postalCodeEdit);
        
        $("#"+modalId2+" input[name='consFirstName[]']").val(firstNameEdit);
        $("#"+modalId2+" input[name='consMiddleName[]']").val(middleNameEdit);
        $("#"+modalId2+" input[name='consLastName[]']").val(lastNameEdit);
        $("#"+modalId2+" input[name='consPhone[]']").val(phoneEdit);
        $("#"+modalId2+" input[name='consEmail[]']").val(emailEdit);
        $("#"+modalId2+" input[name='consSubDistrict[]']").val(subDistrictEdit);
        $("#"+modalId2+" input[name='consDistrict[]']").val(districtEdit);
        $("#"+modalId2+" input[name='consCity[]']").val(cityEdit);
        $("#"+modalId2+" input[name='consAddress[]']").val(addressEdit);
        $("#"+modalId2+" input[name='consProv[]']").val(provEdit);
        $("#"+modalId2+" input[name='consPostalCode[]']").val(postalCodeEdit);
        
        let middleNameE = middleNameEdit==""?"":" "+middleNameEdit+" ",
            lastNameE = lastNameEdit==""?'':" "+lastNameEdit;
    
        $("#"+modalId2+" label[for='customer']").text(firstNameEdit+middleNameE+lastNameE);
        $("#"+modalId2+" label[for='alamat']").text(addressEdit+", "+subDistrictEdit+", "+districtEdit+", "+cityEdit+", "+provEdit+", "+postalCodeEdit);
        $("#"+modalId2+" label[for='telpon']").text(phoneEdit);
        
        Swal.fire("Berhasil", "Data Penerima Akan Tersimpan Jika Melakukan Submit & Blast", 'success');
        
        unLoading(form);

        // $.ajax({
        //     type: "GET",
        //     url: location.origin+"/shiplist/edit/detilcons",
        //     data: $(form).serialize(),
        //     beforeSend: function() {
        //         loading(form);
        //     },
        //     success: function(msg) {
        //         var json = JSON.parse(msg),
        //             modalId = "editDetilPenerima",
        //             modalId2 = "editInvoice";

        //         unLoading(form);

        //         if (json.status == "Berhasil") {

        //             Swal.fire(json.status, json.text, 'success');

        //             if(json.data!=""){
        //                 $("#"+modalId+" input[name='firstName']").val(json.data['firstName']);
        //                 $("#"+modalId+" input[name='middleName']").val(json.data['middleName']);
        //                 $("#"+modalId+" input[name='lastName']").val(json.data['lastName']);
        //                 $("#"+modalId+" input[name='phone']").val(json.data['phone']);
        //                 $("#"+modalId+" input[name='phoneOld']").val(json.data['phone']);
        //                 $("#"+modalId+" input[name='email']").val(json.data['email']);
        //                 $("#"+modalId+" input[name='emailOld']").val(json.data['email']);
        //                 $("#"+modalId+" input[name='subDistrict']").val(json.data['subDistrict']);
        //                 $("#"+modalId+" input[name='district']").val(json.data['district']);
        //                 $("#"+modalId+" input[name='city']").val(json.data['city']);
        //                 $("#"+modalId+" input[name='address']").val(json.data['address']);
        //                 $("#"+modalId+" input[name='prov']").val(json.data['prov']);
        //                 $("#"+modalId+" input[name='postalCode']").val(json.data['postalCode']);
                        
        //                 $("#"+modalId2+" input[name='consFirstName[]']").val(json.data['firstName']);
        //                 $("#"+modalId2+" input[name='consMiddleName[]']").val(json.data['middleName']);
        //                 $("#"+modalId2+" input[name='consLastName[]']").val(json.data['lastName']);
        //                 $("#"+modalId2+" input[name='consPhone[]']").val(json.data['phone']);
        //                 $("#"+modalId2+" input[name='consEmail[]']").val(json.data['email']);
        //                 $("#"+modalId2+" input[name='consSubDistrict[]']").val(json.data['subDistrict']);
        //                 $("#"+modalId2+" input[name='consDistrict[]']").val(json.data['district']);
        //                 $("#"+modalId2+" input[name='consCity[]']").val(json.data['city']);
        //                 $("#"+modalId2+" input[name='consAddress[]']").val(json.data['address']);
        //                 $("#"+modalId2+" input[name='consProv[]']").val(json.data['prov']);
        //                 $("#"+modalId2+" input[name='consPostalCode[]']").val(json.data['postalCode']);
    
        //                 let middleName = json.data['middleName']==null?'':json.data['middleName'],
        //                     lastName = json.data['lastName']==null?'':json.data['lastName'];
    
        //                 $("#"+modalId2+" label[for='customer']").text(json.data['firstName']+" "+middleName+" "+lastName);
        //                 $("#"+modalId2+" label[for='alamat']").text(json.data['address']+", "+json.data['subDistrict']+", "+json.data['district']+", "+json.data['city']+", "+json.data['prov']+", "+json.data['postalCode']);
        //                 $("#"+modalId2+" label[for='telpon']").text(json.data['phone']);
        //             }
                    
        //             let custTypeId = $('.btn-select').attr('id');
        //             refreshTable(tableCi,location.origin+"/shiplist/table/order/"+custTypeId+"?filterTanggal=","table-ci_info");
        //             refreshTable(tableCt,location.origin+"/shiplist/table/invoice/"+custTypeId+"?filterTanggalAwal=&filterTanggalAkhir=&filterWarehouse=&filterService=","table-ct_info");
        //             refreshTable(tableSt,location.origin+"/shiplist/table/tracking/"+custTypeId+"?mismassOrderId=&filterTanggalAwal=&filterTanggalAkhir=&filterWarehouse=&filterService=","table-status_info");

        //         } else {

        //             Swal.fire(json.status, json.text, 'error');

        //         }
        //     }

        // });

    }

});

///////////////////////////////////////////////////////////////////////////////////////////////////////

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

$(document).on("click",".deleteService",function(){
    let modalId = $(this).closest(".modal").attr("id"),
        servicesLength = $("#"+modalId+" .services").length,
        serviceElementId = $(this).closest("#"+modalId+" .services").attr("id");        

    if(servicesLength>1){
        $("#"+serviceElementId).remove();
        servicesLength = $("#"+modalId+" .services").length;

        for(let i=0;i<=servicesLength-1;i++){
            $("#"+modalId+" .services").eq(i).attr("id",i);
            $("#"+modalId+" .services h5").eq(i).html("Services#"+(i+1));
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
            $("#"+modalId+" .services .extraCostShippingNum").eq(i).attr("name","extraCostShippingNum"+i);
            $("#"+modalId+" .services .packingPrice").eq(i).attr("name","packingPrice"+i);
            $("#"+modalId+" .services .packingDesc").eq(i).attr("name","packingDesc"+i);
            $("#"+modalId+" .services .importPrice").eq(i).attr("name","importPrice"+i);
            $("#"+modalId+" .services .importDesc").eq(i).attr("name","importDesc"+i);
            $("#"+modalId+" .services .documentPrice").eq(i).attr("name","documentPrice"+i);
            $("#"+modalId+" .services .documentDesc").eq(i).attr("name","documentDesc"+i);
            $("#"+modalId+" .services .medicinePrice").eq(i).attr("name","medicinePrice"+i);
            $("#"+modalId+" .services .medicineDesc").eq(i).attr("name","medicineDesc"+i);
            $("#"+modalId+" .services .discount").eq(i).attr("name","discount"+i);
            $("#"+modalId+" .services .hargaService").eq(i).attr("name","hargaService"+i);
            $("#"+modalId+" .services .hargaServiceAfter").eq(i).attr("name","hargaServiceAfter"+i);
        }

        let subTotalArr = JSON.parse(localStorage.getItem("subTotals")),
            itemArr = JSON.parse(localStorage.getItem("itemTotals")),
            diskonArr = JSON.parse(localStorage.getItem("diskonTotals")),
            cbmArr = JSON.parse(localStorage.getItem("cbmTotals")),
            kgArr = JSON.parse(localStorage.getItem("kgTotals"));
        subTotalArr.splice(serviceElementId,1);
        itemArr.splice(serviceElementId,1);
        kgArr.splice(serviceElementId,1);
        cbmArr.splice(serviceElementId,1);
        diskonArr.splice(serviceElementId,1);
        localStorage.setItem("subTotals",JSON.stringify(subTotalArr));
        localStorage.setItem("itemTotals",JSON.stringify(itemArr));
        localStorage.setItem("kgTotals",JSON.stringify(kgArr));
        localStorage.setItem("cbmTotals",JSON.stringify(cbmArr));
        localStorage.setItem("diskonTotals",JSON.stringify(diskonArr));

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
    partialCalc(modalId,serviceElementId);;
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
        url: location.origin+"/check/getservlist?inv=true",
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
            $("#"+modalId+" #"+serviceElementId+" .col-cbm").hide();
            $("#"+modalId+" #"+serviceElementId+" .col-priceper").hide();
            $("#"+modalId+" #"+serviceElementId+" input[name='cbm[]']").val(0);
            $("#"+modalId+" #"+serviceElementId+" input[name='kg[]']").val(0);
            $("#"+modalId+" #"+serviceElementId+" input[name='panjang[]']").val(0);
            $("#"+modalId+" #"+serviceElementId+" input[name='lebar[]']").val(0);
            $("#"+modalId+" #"+serviceElementId+" input[name='tinggi[]']").val(0);
            $("#"+modalId+" #"+serviceElementId+" input[name='item[]']").val(0);
            $("#"+modalId+" #"+serviceElementId+" input[name='pricePer[]']").val(0);
            $("#"+modalId+" #"+serviceElementId+" input[name='subTotal[]']").val(0);
            // if(modalId=="createInvoice"){
            let subTotalArr = JSON.parse(localStorage.getItem("subTotals")),
                itemArr = JSON.parse(localStorage.getItem("itemTotals")),
                diskonArr = JSON.parse(localStorage.getItem("diskonTotals")),
                cbmArr = JSON.parse(localStorage.getItem("cbmTotals")),
                kgArr = JSON.parse(localStorage.getItem("kgTotals"));
            subTotalArr[serviceElementId] = 0;
            itemArr[serviceElementId] = 0;
            kgArr[serviceElementId] = 0;
            diskonArr[serviceElementId] = 0;
            cbmArr[serviceElementId] = 0;
            localStorage.setItem("subTotals",JSON.stringify(subTotalArr));
            localStorage.setItem("itemTotals",JSON.stringify(itemArr));
            localStorage.setItem("kgTotals",JSON.stringify(kgArr));
            localStorage.setItem("diskonTotals",JSON.stringify(diskonArr));
            localStorage.setItem("cbmTotals",JSON.stringify(cbmArr));

            allCalc(modalId);
            // }
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
            $("#"+modalId+" #"+serviceElementId+" input[name='pricePerCbm']").val(json.priceCbm);

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
                    $("#"+modalId+" #"+serviceElementId+" .col-cbm").hide();
                    $("#"+modalId+" #"+serviceElementId+" .col-priceper").hide();
                    $("#"+modalId+" #"+serviceElementId+" input[name='cbm[]']").val(0);
                    $("#"+modalId+" #"+serviceElementId+" input[name='kg[]']").val(0);
                    $("#"+modalId+" #"+serviceElementId+" input[name='panjang[]']").val(0);
                    $("#"+modalId+" #"+serviceElementId+" input[name='lebar[]']").val(0);
                    $("#"+modalId+" #"+serviceElementId+" input[name='tinggi[]']").val(0);
                    $("#"+modalId+" #"+serviceElementId+" input[name='item[]']").val(0);
                    $("#"+modalId+" #"+serviceElementId+" input[name='pricePer[]']").val(0);
                    $("#"+modalId+" #"+serviceElementId+" input[name='subTotal[]']").val(0);
                    // if(modalId=="createInvoice"){
                    let subTotalArr = JSON.parse(localStorage.getItem("subTotals")),
                        itemArr = JSON.parse(localStorage.getItem("itemTotals")),
                        diskonArr = JSON.parse(localStorage.getItem("diskonTotals")),
                        cbmArr = JSON.parse(localStorage.getItem("cbmTotals")),
                        kgArr = JSON.parse(localStorage.getItem("kgTotals"));
                    subTotalArr[serviceElementId] = 0;
                    itemArr[serviceElementId] = 0;
                    kgArr[serviceElementId] = 0;
                    diskonArr[serviceElementId] = 0;
                    cbmArr[serviceElementId] = 0;
                    localStorage.setItem("subTotals",JSON.stringify(subTotalArr));
                    localStorage.setItem("itemTotals",JSON.stringify(itemArr));
                    localStorage.setItem("kgTotals",JSON.stringify(kgArr));
                    localStorage.setItem("cbmTotals",JSON.stringify(cbmArr));
                    localStorage.setItem("diskonTotals",JSON.stringify(diskonArr));
                    
                    allCalc(modalId);
                    // }
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
        formId = $(a).closest("#"+modalId+" form").attr("id"),
        serviceElementId = $(a).closest("#"+modalId+" .services").attr("id"),
        pricePerVal;
    
    $("#"+modalId+" .row-detil-biaya").show();
    
    if(val=="KG"){
        $("#"+modalId+" #"+serviceElementId+" .col-kg").show();
        $("#"+modalId+" #"+serviceElementId+" .col-volume,"+
          "#"+modalId+" #"+serviceElementId+" .col-item,"+
          "#"+modalId+" #"+serviceElementId+" .col-actual,"+
          "#"+modalId+" #"+serviceElementId+" .col-cbm").hide();
        $("#"+modalId+" #"+serviceElementId+" input[name='kg[]']").attr("required",true).attr("readonly",false);
        $("#"+modalId+" #"+serviceElementId+" input[name='panjang[]'],"+
          "#"+modalId+" #"+serviceElementId+" input[name='lebar[]'],"+
          "#"+modalId+" #"+serviceElementId+" input[name='tinggi[]'],"+
          "#"+modalId+" #"+serviceElementId+" input[name='item[]'],"+
          "#"+modalId+" #"+serviceElementId+" input[name='actualKg[]'],"+
          "#"+modalId+" #"+serviceElementId+" input[name='cbm[]']").attr("required",false);
        $("#"+modalId+" #"+serviceElementId+" label[for='pricePer']").html("Harga/Kg");
        $("#"+modalId+" #"+formId).validate();
        $("#"+modalId+" #"+serviceElementId+" input[name='kg[]']").rules("add", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='cbm[]']").rules("remove", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='panjang[]']").rules("remove", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='lebar[]']").rules("remove", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='tinggi[]']").rules("remove", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='item[]']").rules("remove", "greaterThanZero");
        pricePerVal = $("#"+modalId+" #"+serviceElementId+" input[name='pricePerKg']").val();
    }else if(val=="VOL"){
        $("#"+modalId+" #"+serviceElementId+" .col-volume,#"+modalId+" #"+serviceElementId+" .col-kg,#"+modalId+" #"+serviceElementId+" .col-actual").show();
        $("#"+modalId+" #"+serviceElementId+" .col-item,#"+modalId+" #"+serviceElementId+" .col-cbm").hide();
        $("#"+modalId+" #"+serviceElementId+" input[name='panjang[]'],#"+modalId+" #"+serviceElementId+" input[name='lebar[]'],#"+modalId+" #"+serviceElementId+" input[name='tinggi[]'],#"+modalId+" #"+serviceElementId+" input[name='actualKg[]']").attr("required",true);
        $("#"+modalId+" #"+serviceElementId+" input[name='item[]'],#"+modalId+" #"+serviceElementId+" input[name='kg[]'],#"+modalId+" #"+serviceElementId+" input[name='cbm[]']").attr("required",false);
        $("#"+modalId+" #"+serviceElementId+" input[name='kg[]']").attr("readonly",true);
        $("#"+modalId+" #"+serviceElementId+" label[for='pricePer']").html("Harga/Vol");
        $("#"+modalId+" #"+formId).validate();
        $("#"+modalId+" #"+serviceElementId+" input[name='panjang[]']").rules("add", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='lebar[]']").rules("add", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='tinggi[]']").rules("add", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='item[]']").rules("remove", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='kg[]']").rules("remove", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='cbm[]']").rules("remove", "greaterThanZero");
        pricePerVal = $("#"+modalId+" #"+serviceElementId+" input[name='pricePerVol']").val();
    }else if(val=="CBM"){
        $("#"+modalId+" #"+serviceElementId+" .col-cbm").show();
        $("#"+modalId+" #"+serviceElementId+" .col-volume,"+
          "#"+modalId+" #"+serviceElementId+" .col-item,"+
          "#"+modalId+" #"+serviceElementId+" .col-kg,"+
          "#"+modalId+" #"+serviceElementId+" .col-actual").hide();
        $("#"+modalId+" #"+serviceElementId+" input[name='cbm[]']").attr("required",true).attr("readonly",false);
        $("#"+modalId+" #"+serviceElementId+" input[name='panjang[]'],"+
          "#"+modalId+" #"+serviceElementId+" input[name='lebar[]'],"+
          "#"+modalId+" #"+serviceElementId+" input[name='tinggi[]'],"+
          "#"+modalId+" #"+serviceElementId+" input[name='kg[]'],"+
          "#"+modalId+" #"+serviceElementId+" input[name='item[]'],"+
          "#"+modalId+" #"+serviceElementId+" input[name='actualKg[]']").attr("required",false);
        $("#"+modalId+" #"+serviceElementId+" label[for='pricePer']").html("Harga/CBM");
        $("#"+modalId+" #"+formId).validate();
        $("#"+modalId+" #"+serviceElementId+" input[name='cbm[]']").rules("add", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='kg[]']").rules("remove", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='panjang[]']").rules("remove", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='lebar[]']").rules("remove", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='tinggi[]']").rules("remove", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='item[]']").rules("remove", "greaterThanZero");
        pricePerVal = $("#"+modalId+" #"+serviceElementId+" input[name='pricePerCbm']").val();
    }else{
        $("#"+modalId+" #"+serviceElementId+" .col-item").show();
        $("#"+modalId+" #"+serviceElementId+" .col-volume,#"+modalId+" #"+serviceElementId+" .col-kg,#"+modalId+" #"+serviceElementId+" .col-cbm,#"+modalId+" #"+serviceElementId+" .col-actual").hide();
        $("#"+modalId+" #"+serviceElementId+" input[name='item[]']").attr("required",true);
        $("#"+modalId+" #"+serviceElementId+" input[name='panjang[]'],#"+modalId+" #"+serviceElementId+" input[name='lebar[]'],#"+modalId+" #"+serviceElementId+" input[name='tinggi[]'],#"+modalId+" #"+serviceElementId+" input[name='kg[]'],#"+modalId+" #"+serviceElementId+" input[name='actualKg[]'],#"+modalId+" #"+serviceElementId+" input[name='cbm[]']").attr("required",false);
        $("#"+modalId+" #"+serviceElementId+" label[for='pricePer']").html("Harga/Item");
        $("#"+modalId+" #"+formId).validate();
        $("#"+modalId+" #"+serviceElementId+" input[name='item[]']").rules("add", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='panjang[]']").rules("remove", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='lebar[]']").rules("remove", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='tinggi[]']").rules("remove", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='kg[]']").rules("remove", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='cbm[]']").rules("remove", "greaterThanZero");
        pricePerVal = $("#"+modalId+" #"+serviceElementId+" input[name='pricePerItem']").val();
    }

    $("#"+modalId+" #"+serviceElementId+" .col-priceper").show();
    $("#"+modalId+" #"+serviceElementId+" input[name='kg[]']").val(0);
    $("#"+modalId+" #"+serviceElementId+" input[name='panjang[]']").val(0);
    $("#"+modalId+" #"+serviceElementId+" input[name='lebar[]']").val(0);
    $("#"+modalId+" #"+serviceElementId+" input[name='tinggi[]']").val(0);
    $("#"+modalId+" #"+serviceElementId+" input[name='item[]']").val(0);
    $("#"+modalId+" #"+serviceElementId+" input[name='cbm[]']").val(0);
    $("#"+modalId+" #"+serviceElementId+" input[name='pricePer[]']").val(masking(pricePerVal).toString());
    $("#"+modalId+" #"+serviceElementId+" input[name='subTotal[]']").val(0);

    // if(modalId=="createInvoice"){
    let subTotalArr = JSON.parse(localStorage.getItem("subTotals")),
        itemArr = JSON.parse(localStorage.getItem("itemTotals")),
        diskonArr = JSON.parse(localStorage.getItem("diskonTotals")),
        kgArr = JSON.parse(localStorage.getItem("kgTotals"));
    subTotalArr[serviceElementId] = 0;
    itemArr[serviceElementId] = 0;
    kgArr[serviceElementId] = 0;
    diskonArr[serviceElementId] = 0;
    localStorage.setItem("subTotals",JSON.stringify(subTotalArr));
    localStorage.setItem("itemTotals",JSON.stringify(itemArr));
    localStorage.setItem("kgTotals",JSON.stringify(kgArr));
    localStorage.setItem("diskonTotals",JSON.stringify(diskonArr));

    allCalc(modalId);
    // }
}

$(document).on("change","select[name='additionalService']",function(){
    onChangeAdditionalService(this);
});
function onChangeAdditionalService(a){
    let value = $(a).val(),
        modalId = $(a).closest(".modal").attr("id"),
        serviceElementId = $(a).closest(".services").attr("id"),
        choosedElement = createAdditionalServiceElement(value,serviceElementId);
    $("#"+modalId+" #"+serviceElementId+" .row-additional").before(choosedElement);
    $("#"+modalId+" #"+serviceElementId+" option[value='"+value+"']").hide();
    $(a).val("");
    ruleForAdditional(value,serviceElementId,"add",modalId);
}

$(document).on("keyup","input[name='cbm[]']",keyUpFuncKC);
$(document).on("keyup","input[name='kg[]']",keyUpFuncKC);
$(document).on("keyup","input[name='actualKg[]']",keyUpFuncKC);
$(document).on("keyup","input[name='panjang[]']",keyUpFunc);
$(document).on("keyup","input[name='lebar[]']",keyUpFunc);
$(document).on("keyup","input[name='tinggi[]']",keyUpFunc);
$(document).on("keyup","input[name='item[]']",keyUpFunc);
$(document).on("keyup","input[name='discount[]']",keyUpFunc);
$(document).on("keyup","input[name='additionalNominal[]']",keyUpFunc);
$(document).on("keyup","input[name='additionalDesc[]']",function(){
    let val = $(this).val(),
        modalId = $(this).closest(".modal").attr("id"),
        serviceElementId = $(this).closest("#"+modalId+" .services").attr("id");
    $("#"+modalId+" input[name='additionalDesc"+serviceElementId+"']").val(val);
});
$(document).on("keyup","input[name='medicine[]']",keyUpFunc);
$(document).on("keyup","input[name='medicineDesc[]']",function(){
    let val = $(this).val(),
        modalId = $(this).closest(".modal").attr("id"),
        serviceElementId = $(this).closest("#"+modalId+" .services").attr("id");
    $("#"+modalId+" input[name='medicineDesc"+serviceElementId+"']").val(val);
});
$(document).on("keyup","input[name='document[]']",keyUpFunc);
$(document).on("keyup","input[name='documentDesc[]']",function(){
    let val = $(this).val(),
        modalId = $(this).closest(".modal").attr("id"),
        serviceElementId = $(this).closest("#"+modalId+" .services").attr("id");
    $("#"+modalId+" input[name='documentDesc"+serviceElementId+"']").val(val);
});
$(document).on("keyup","input[name='import[]']",keyUpFunc);
$(document).on("keyup","input[name='importDesc[]']",function(){
    let val = $(this).val(),
        modalId = $(this).closest(".modal").attr("id"),
        serviceElementId = $(this).closest("#"+modalId+" .services").attr("id");
    $("#"+modalId+" input[name='importDesc"+serviceElementId+"']").val(val);
});
$(document).on("keyup","input[name='packing[]']",keyUpFunc);
$(document).on("keyup","input[name='packingPer[]']",keyUpFunc);
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
$(document).on("keyup","input[name='extraCostShippingNum[]']",function(){
    let val = $(this).val(),
        modalId = $(this).closest(".modal").attr("id"),
        serviceElementId = $(this).closest("#"+modalId+" .services").attr("id");
    $("#"+modalId+" input[name='extraCostShippingNum"+serviceElementId+"']").val(val);
});
$(document).on("keyup","input[name='pickUpWeight[]']",keyUpFunc);
$(document).on("keyup","input[name='foreignRateValue']",function(){
    $(this).mask(masK, {
        reverse: true
    });
    let modalId = $(this).closest(".modal").attr("id"),
        value = $(this).val().replace(/\./g, ""),
        totalHargaRp = $("#"+modalId+" input[name='totalHargaRP']").val().replace(/\./g, ""),
        totalHargaConvert = totalHargaRp/value;
        finalResult = totalHargaConvert.toFixed(2);
    $("#"+modalId+" input[name='totalHargaConvert']").val(masking(finalResult.toString()));
});