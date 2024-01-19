let pageLength = 10,
    dt_info = "_TOTAL_ Total Data",
    dt_info_empty = "_TOTAL_ Total Data",
    dt_info_filter = "",
    dt_search_label = "Cari",
    dt_search_placeholder = "",
    dt_zero_data = "Tidak Ada Data",
    dt_thousands = ".",
    dt_processing = "Mohon Tunggu ...",
    minDate = "",
    addServ,wareH,masK,masKC,intervalLastShippingId,
    err;

$(".preloaderz").hide();

window.onscroll = function() {showBtnToTop()};
function showBtnToTop(){
    let btn = document.getElementById("btnToTop");
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        btn.style.display = "block";
      } else {
        btn.style.display = "none";
      }
}
$(document).on("click","#btnToTop",function(){
    window.scrollTo({top: 0, behavior: 'smooth'});
});

function makeId(length) {
    let result = '';
    const onlyAlpha = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    const onlyAlphaLength = onlyAlpha.length;
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    const charactersLength = characters.length;
    let counter = 0;
    while (counter < length) {
        if(counter==0){
            result += onlyAlpha.charAt(Math.floor(Math.random() * onlyAlphaLength));
        }else{
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        counter += 1;
    }
    return result;
}

function logout() {
    Swal.fire({
        title: 'Apakah Anda Yakin Akan Melakukan Logout?',
        icon: 'question',
        showDenyButton: true,
        showCancelButton: true,
        showConfirmButton: false,
        denyButtonText: `Logout`,
        customClass: {
            cancelButton: 'order-1',
            denyButton: 'order-2',
          },
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isDenied) {
            window.location = location.origin+'/logout';
        }
    })
};

function konfirm_hapus(idDb,name,subject,url,type) {

    var title='Apakah Anda Yakin Akan Menghapus '+ subject +' '+ name +'?';

    if(subject=="IND"||subject=="COR"){
        title='Apakah Anda Yakin Akan Menghapus Customer '+ name +'? Semua Data Yang Terkait Juga Akan Terhapus !!';
    }

    if(subject=="Warehouse"||subject=="Service"){
        title='Apakah Anda Yakin Akan Menghapus '+subject+' '+ name +'? Semua Data Yang Terkait Juga Akan Terhapus !!';
    }

    Swal.fire({
        title: title,
        icon: 'question',
        showDenyButton: true,
        showCancelButton: true,
        showConfirmButton: false,
        denyButtonText: `Hapus`,
        customClass: {
            cancelButton: 'order-1',
            denyButton: 'order-2',
          },
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isDenied) {
            // window.location = a;
            hapus(idDb,subject,url,type);
        }
    })
}

function hapus(idDb,subject,url,type){

    $.ajax({
        url: url,
        type: 'POST',
        data: {'id':idDb,'subject':subject},
        success: function(msg) {
            var json = JSON.parse(msg);
            if (json.status == "Berhasil") {

                Swal.fire(
                    json.status,
                    json.text,
                    'success'
                )

                if($(".btn-select").length>0){
                    var custTypeId = $(".btn-select").attr("id");
                }

                if(type=="shiplist"){
                    refreshTable(tableCi,location.origin+"/shiplist/table/order/"+custTypeId+"?filterTanggal=","table-ci_info");
                    refreshTable(tableCt,location.origin+"/shiplist/table/invoice/"+custTypeId+"?filterTanggalAwal=&filterTanggalAkhir=&filterWarehouse=&filterService=","table-ct_info");
                    refreshTable(tableSt,location.origin+"/shiplist/table/tracking/"+custTypeId+"?filterTanggalAwal=&filterTanggalAkhir=&filterWarehouse=&filterService=","table-status_info");
                }

                if(type=="custlist"){
                    refreshTable(tableInd,location.origin+"/custlist/table/IND","tableInd_info");
                    refreshTable(tableCor,location.origin+"/custlist/table/COR","tableCor_info");
                }

                if(type=="warehouse"){
                    refreshTable(table,location.origin+"/warehouse/table","table_info");
                }

                if(type=="service"){
                    refreshTable(table,location.origin+"/servlist/table","table_info");
                }

                // pageReload(reloadPage);

            } else {

                Swal.fire(
                    json.status,
                    json.text,
                    'error'
                )

            }
        }
    });

}

function copyText(modalId) {

    let textToCopy = document.querySelector("#"+modalId+" .totalHargaBoard div .value").innerText,
         myTemporaryInputElement = document.createElement("input");

    myTemporaryInputElement.type = "text";
    myTemporaryInputElement.value = textToCopy;
    document.body.appendChild(myTemporaryInputElement);

    myTemporaryInputElement.select();
    myTemporaryInputElement.setSelectionRange(0, 99999); 
    let ex = navigator.clipboard.writeText(myTemporaryInputElement.value);
    if(ex){
        $("#"+modalId+" .copyHandle").html("<div style='font-size:13px'>Copied!</div>");
        setTimeout(() => {
            $("#"+modalId+" .copyHandle").html("<i class='far fa-copy'></i>");
            document.body.removeChild(myTemporaryInputElement);
        }, 500);
    }
}

function checkSes(){

    $.ajax({
        type: 'GET',
        url: location.origin+'/check/session',
        success: function(msg) {
            let json = JSON.parse(msg);
            if(json.status == false){
                window.location = location.origin+"/logout";
                console.log("sesi berakhir");
            }
            console.log("sesi ada");
        }
    });

}

function pageReload(v,t){

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': t
        }
    });

    var spinner = "<div id='spinner'><div class='loader'><div></div><div></div><div></div></div></div>";

    $.ajax({
        type: 'POST',
        url: v,
        beforeSend: function() {
            $("#page-content").html(spinner);
         },
        //  complete: function(){
        //     $("#page-content").html("");
        //  },
        success: function(data) {
            $("#page-content").fadeOut(150, function() {
                $("#page-content").html(data).delay(50).fadeIn(150);
            });
        }
    });

}

//MASKING UNTUK INPUTAN
function maskingInp(str){

    var h = [];
    var t = "";

    for (i = str.length; i >= 0; i -= 3) {
        h.push(str.substring(i-3,i));
    }

    h.reverse();

    if(h[0]==""){
    h.shift();
    }

    for(i=0;i<=h.length-1;i++){

        var dot = "";
        if(i<h.length-1){
            dot = ".";
        }
        
        var t = t+h[i]+dot;

    }

    return t;

}

//MASKING UNTUK HASIL
function masking(str){

    str!=null?str=str.toString():'';

    if(str==null){
        return 0;
    }
    
    var p = str.length;
    var b = "";
    var t = "";
    var n = "";
    var c = "";
    var h = [];

    //JIKA ADA KOMA
    for(var i=0;i<=str.length-1;i++){

        if(str[i]=="."){
            p = i;
            c = ",";
        }else{
            if(i>p){
                n = n+str[i];
            }else{
                b = b+str[i];
            }	
        }   
        
    }
    //JIKA ADA KOMA

    //PENAMBAHAN TANDA BACA .
    for (i = b.length; i >= 0; i -= 3) {
        h.push(b.substring(i-3,i));
    }

    h.reverse();

    if(h[0]==""){
    h.shift();
    }

    for(i=0;i<=h.length-1;i++){

        var dot = "";
        if(i<h.length-1){
            dot = ".";
        }
        
        var t = t+h[i]+dot;

    }
    //PENAMBAHAN TANDA BACA .

    return t+c+n;

}

function refreshTable(table,url,rowCountEl){
    table.ajax.url(url).load();
}

function loading(form){
    
    $(".preloaderz").show();
    // let btnSubmit = form.querySelector("button[type='submit']");
    // btnSubmit.setAttribute("disabled",true);
    // btnSubmit.innerText = "Loading...";
    
    // let btnClose = form.querySelector("button[data-dismiss='modal']");
    // if(btnClose!=null){
    //     btnClose.setAttribute("disabled",true);
    // }   
    
}

function unLoading(form){

    let formId = form.id,    
        modalId = $("#"+formId).closest(".modal").attr("id");

    // let btnSubmit = form.querySelector("button[type='submit']");
    // btnSubmit.removeAttribute('disabled');
    // btnSubmit.innerText = "Submit";
    
    // let btnClose = form.querySelector("button[data-dismiss='modal']");
    // if(btnClose!=null){
    //     btnClose.removeAttribute('disabled');
    // } 

    $(".preloaderz").hide();

    $('#'+modalId).modal('hide');
    $('body').removeClass('modal-open');
    $('.modal-backdrop').remove();
    $('body').css('padding-right', '0');
    
}

function maskMoney(val){
    let reverseArray = [];
    let reverseArrayWithDot = "";
    let reverseArrayWithDotNum = 0;
    let fix = "";

	if(val<1000){
    	return val;
    }
    
    let array = val.toString().split("");
    let cekLength = array.length-1;
    for(var i =0;i<=cekLength;i++){
    	reverseArray[i] = array[cekLength-i];
    }
    
    for(var y =0;y<=cekLength;y++){
    	reverseArrayWithDot += reverseArray[y];
        reverseArrayWithDotNum++;
        if(reverseArrayWithDotNum%3==0){
        	reverseArrayWithDot += ".";
            reverseArrayWithDotNum = 0;
        }
    }
    
    let arrayAgain = reverseArrayWithDot.toString().split("");
    let arrayAgainLength = arrayAgain.length-1;
    for(var z = 0;z<=arrayAgainLength;z++){
    	if(z==0){
        	if(arrayAgain[arrayAgainLength-z]!="."){
    			fix += arrayAgain[arrayAgainLength-z];
        	}
        }else{
        	fix += arrayAgain[arrayAgainLength-z];
        }
    	
    }
  
  	return fix;
}

//Objek ke array
function objToArr(value) {
    return Object.keys(value).map((key) => value[key]).reverse();
}

//Memberi Label Baca
function simpleMoney(rp) {

    //aturan pemberian label
    const nominal = [
        [1000, "Rb"],
        [1000000, "Jt"],
        [1000000000, "Mil"],
    ];

    //nilai default output (jika nilai kurang dari 1000 maka tidak ada label)
    var output = rp;

    if(rp>=1){
    for (var i = 0; i <= nominal.length - 1; i++) {
        if (rp >= nominal[i][0]) {
            var calc = rp / nominal[i][0];
            if (calc % 1 != 0) {
                calc = toFixed(rp / nominal[i][0], 1).replace(".",",");
            }
            output = calc + nominal[i][1];
        }
    }
    }else{
        output=0;
    }

    return output;
}

function simpleWeight(weight) {
    const nominal = [
        [1, "Kg"],
        [100, "Kw"],
        [1000, "Ton"],
    ];
    var b,output,weightDec=0;
    
    if(weight<1||weight==null){
        return "0 Kg";
    }
    
    if(weight%1!=0){
        b=weight.toString().split(".");
        weight=parseInt(b[0]);
        weightDec=parseFloat("0."+b[1]).toFixed(2);
    }
    
    for (var i = 0; i <= nominal.length - 1; i++) {
        if (weight >= nominal[i][0]) {
            let calc = weight / nominal[i][0];
            output = calc + nominal[i][1];
            if(weightDec>0){
                result = parseFloat(parseInt(calc)+parseFloat(weightDec)).toFixed(2);
                output = result.toString().replace(".",",")+ nominal[i][1];
            }
        }
    }
    
    return output;
    
}

// function simpleWeight(weight) {

//     //aturan pemberian label
//     const nominal = [
//         [1, "Kg"],
//         [100, "Kw"],
//         [1000, "Ton"],
//     ];

//     //nilai default output (jika nilai kurang dari 1000 maka tidak ada label)
//     var output = weight;

//     if(weight>=1){
//     for (var i = 0; i <= nominal.length - 1; i++) {
//         if (weight >= nominal[i][0]) {
//             var calc = weight / nominal[i][0];
//             if (calc % 1 != 0) {
//                 calc = toFixed(weight / nominal[i][0], 1);
//             }
//             output = calc +" "+ nominal[i][1];
//         }
//     }
//     }else{
//         output += " Kg";
//     }

//     return output;

// }

function toFixed(value, precision) {
    var precision = precision || 0,
        power = Math.pow(10, precision),
        absValue = Math.abs(Math.round(value * power)),
        result = (value < 0 ? '-' : '') + String(Math.floor(absValue / power));

    if (precision > 0) {
        var fraction = String(absValue % power),
            padding = new Array(Math.max(precision - fraction.length, 0) + 1).join('0');
        result += '.' + padding + fraction;
    }
    return result;
}

function resetError(form){
    form.reset();
    $('label.error').remove();
    $('.error').removeClass('error');
    $('.fail-alert').removeClass('fail-alert');
    $('.is-invalid').removeClass('is-invalid');
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////FUNCTIONS
function initialShow(modal){
    $("#"+modal+" .row-noresi,#"+modal+" .col-namaForwarder").hide();
    $("#"+modal+" .col-namaForwarder").html("");
    $("#"+modal+" select[name='tipeForwarder[]'],#"+modal+" input[name='noResi[]']").val("");
}

function hideSomeElements(){
    $(".row-filter,.row-filterTwo,.card-table").hide();
}

function resetModalInvoice(modal){
    $("#"+modal+" .row-services").html("");
    $("#"+modal+" input,#"+modal+" select").val("");
    $("#"+modal+" .totalKiloBoard div .value,#"+modal+"  .totalItemBoard div .value,#"+modal+"  .totalDiskonBoard div .value,#"+modal+"  .totalHargaBoard div .value").html("0");
    $("#"+modal+" .row-detil-pembayaran,#"+modal+" .col-doku,.col-bank").hide();
    $("#"+modal+" input,#"+modal+" select").removeClass("error");
    $("#"+modal+" input,#"+modal+" select").removeClass("fail-alert");
    $("#"+modal+" input,#"+modal+" select").removeClass("is-invalid");
    $("#"+modal+" label.error").remove();
}

function floatOrInt(a){!Number.isInteger(a)?a=parseFloat(toFixed(a,2)):"";return a;}
function kgRound(a){
    if(a<=0.3){
        return 1;
    }
    if(a.toString().match(".")){
        let b = a.toString().split("."),
        c = parseFloat("0."+b[1]);
        return c>0.3?Math.ceil(a):Math.floor(a);
    }
    return a;
}

function partialCalc(modalId,i){
    let satuanBeratVal,subTotalKg=0,subTotalItem=0,subTotal=0,subTotalDiskon=0,
    kgVal,pricePer,
    pVal,lVal,tVal,
    medicineVal,medicinePerVal,medicineTotalVal,
    documentVal,documentPerVal,documentTotalVal,
    importVal,importPerVal,importTotalVal,
    packingVal,packingPerVal,packingTotalVal,
    insurancePercent,insurancePriceItem,insuranceTotalVal,
    feePercent,feePriceItem,feeTotalVal,
    taxPercent,taxPriceItem,taxTotalVal,
    extraCostPriceVal,
    pickUpWeightVal,pickUpChargeVal,
    subTotalArr,itemArr,kgArr,diskonArr;

    satuanBeratVal = $("#"+modalId+" #"+i+" select[name='satuanBerat']").val();
    if(satuanBeratVal=="KG"){
        kgVal = parseFloat($("#"+modalId+" #"+i+" input[name='kg[]']").val().replace(/\./g, "").replace(/\,/g, "."));
        pricePer = parseInt($("#"+modalId+" #"+i+" input[name='pricePerKg']").val().replace(/\./g, ""));
        $("#"+modalId+" #"+i+" input[name='pricePer[]']").val(masking(pricePer.toString()));
        subTotal=kgRound(kgVal)*pricePer;
        subTotalKg+=kgVal;
    }else if(satuanBeratVal=="VOL"){
        pVal = parseInt($("#"+modalId+" #"+i+" input[name='panjang[]']").val().replace(/\./g, ""));
        lVal = parseInt($("#"+modalId+" #"+i+" input[name='lebar[]']").val().replace(/\./g, ""));
        tVal = parseInt($("#"+modalId+" #"+i+" input[name='tinggi[]']").val().replace(/\./g, ""));
        kgVal = floatOrInt((pVal*lVal*tVal)/6000);
        pricePer = $("#"+modalId+" #"+i+" input[name='pricePerVol']").val();
        $("#"+modalId+" #"+i+" input[name='pricePer[]']").val(masking(pricePer.toString()));
        $("#"+modalId+" #"+i+" input[name='kg[]']").val(masking(kgVal.toString()));
        subTotal=kgRound(kgVal)*pricePer;
        subTotalKg+=kgVal;
    }else if(satuanBeratVal=="ITEM"){
        itemVal = parseInt($("#"+modalId+" #"+i+" input[name='item[]']").val().replace(/\./g, ""));
        pricePer = parseInt($("#"+modalId+" #"+i+" input[name='pricePerItem']").val().replace(/\./g, ""));
        $("#"+modalId+" #"+i+" input[name='pricePer[]']").val(masking(pricePer.toString()));
        subTotal=itemVal*pricePer;
        subTotalItem+=itemVal;
    }

    if($("#"+modalId+" #"+i+" input[name='discount[]']").length>0){
        discountVal = parseInt($("#"+modalId+" #"+i+" input[name='discount[]']").val().replace(/\./g, ""));
        $("#"+modalId+" #"+i+" input[name='discount"+i+"']").val(discountVal);
        $("#"+modalId+" #"+i+" input[name='hargaService[]']").val(masking(subTotal.toString()));
        $("#"+modalId+" #"+i+" input[name='hargaService"+i+"']").val(subTotal);
        subTotal-=discountVal; 
        $("#"+modalId+" #"+i+" input[name='hargaServiceAfter[]']").val(masking(subTotal.toString()));
        $("#"+modalId+" #"+i+" input[name='hargaServiceAfter"+i+"']").val(subTotal);
        subTotalDiskon+=discountVal;
    }

    if($("#"+modalId+" #"+i+" input[name='medicine[]']").length>0){
        medicineVal = parseInt($("#"+modalId+" #"+i+" input[name='medicine[]']").val().replace(/\./g, ""));
        medicinePerVal = parseInt($("#"+modalId+" #"+i+" input[name='medicinePer[]']").val().replace(/\./g, ""));
        medicineTotalVal = medicineVal*medicinePerVal;
        $("#"+modalId+" #"+i+" input[name='medicineTotal[]']").val(masking(medicineTotalVal.toString()));
        $("#"+modalId+" #"+i+" input[name='medicineTotal"+i+"']").val(medicineTotalVal);
        $("#"+modalId+" #"+i+" input[name='medicinePer"+i+"']").val(medicinePerVal);
        $("#"+modalId+" #"+i+" input[name='medicine"+i+"']").val(medicineVal);
        subTotal+=medicineTotalVal;
    }

    if($("#"+modalId+" #"+i+" input[name='document[]']").length>0){
        documentVal = parseInt($("#"+modalId+" #"+i+" input[name='document[]']").val().replace(/\./g, ""));
        documentPerVal = parseInt($("#"+modalId+" #"+i+" input[name='documentPer[]']").val().replace(/\./g, ""));
        documentTotalVal = documentVal*documentPerVal;
        $("#"+modalId+" #"+i+" input[name='documentTotal[]']").val(masking(documentTotalVal.toString()));
        $("#"+modalId+" #"+i+" input[name='documentTotal"+i+"']").val(documentTotalVal);
        $("#"+modalId+" #"+i+" input[name='documentPer"+i+"']").val(documentPerVal);
        $("#"+modalId+" #"+i+" input[name='document"+i+"']").val(documentVal);
        subTotal+=documentTotalVal;
    }

    if($("#"+modalId+" #"+i+" input[name='import[]']").length>0){
        importVal = parseInt($("#"+modalId+" #"+i+" input[name='import[]']").val().replace(/\./g, ""));
        importPerVal = parseInt($("#"+modalId+" #"+i+" input[name='importPer[]']").val().replace(/\./g, ""));
        importTotalVal = importVal*importPerVal;
        $("#"+modalId+" #"+i+" input[name='importTotal[]']").val(masking(importTotalVal.toString()));
        $("#"+modalId+" #"+i+" input[name='importTotal"+i+"']").val(importTotalVal);
        $("#"+modalId+" #"+i+" input[name='importPer"+i+"']").val(importPerVal);
        $("#"+modalId+" #"+i+" input[name='import"+i+"']").val(importVal);
        subTotal+=importTotalVal;
    }

    if($("#"+modalId+" #"+i+" input[name='packing[]']").length>0){
        packingVal = parseInt($("#"+modalId+" #"+i+" input[name='packing[]']").val().replace(/\./g, ""));
        packingPerVal = parseInt($("#"+modalId+" #"+i+" input[name='packingPer[]']").val().replace(/\./g, ""));
        packingTotalVal = packingVal*packingPerVal;
        $("#"+modalId+" #"+i+" input[name='packingTotal[]']").val(masking(packingTotalVal.toString()));
        $("#"+modalId+" #"+i+" input[name='packingTotal"+i+"']").val(packingTotalVal);
        $("#"+modalId+" #"+i+" input[name='packingPer"+i+"']").val(packingPerVal);
        $("#"+modalId+" #"+i+" input[name='packing"+i+"']").val(packingVal);
        subTotal+=packingTotalVal;
    }

    if($("#"+modalId+" #"+i+" input[name='insurancePriceItem[]']").length){
        insurancePriceItem = parseInt($("#"+modalId+" #"+i+" input[name='insurancePriceItem[]']").val().replace(/\./g, ""));
        insurancePercent = parseInt($("#"+modalId+" #"+i+" input[name='insurancePercent[]']").val().replace(/\./g, ""));
        insuranceTotalVal = floatOrInt(insurancePriceItem*insurancePercent/100);

        $("#"+modalId+" #"+i+" input[name='insurancePriceItem"+i+"']").val(insurancePriceItem);
        $("#"+modalId+" #"+i+" input[name='insurancePercent"+i+"']").val(insurancePercent);
        $("#"+modalId+" #"+i+" input[name='insuranceTotal"+i+"']").val(insuranceTotalVal);

        $("#"+modalId+" #"+i+" input[name='insuranceTotal[]']").val(masking(insuranceTotalVal.toString()));
        subTotal+=insuranceTotalVal;
    }

    if($("#"+modalId+" #"+i+" input[name='feePriceItem[]']").length>0){
        feePriceItem = parseInt($("#"+modalId+" #"+i+" input[name='feePriceItem[]']").val().replace(/\./g, ""));
        feePercent = parseInt($("#"+modalId+" #"+i+" input[name='feePercent[]']").val().replace(/\./g, ""));
        feeTotalVal = floatOrInt(feePriceItem*feePercent/100);

        $("#"+modalId+" #"+i+" input[name='feePriceItem"+i+"']").val(feePriceItem);
        $("#"+modalId+" #"+i+" input[name='feePercent"+i+"']").val(feePercent);
        $("#"+modalId+" #"+i+" input[name='feeTotal"+i+"']").val(feeTotalVal);

        $("#"+modalId+" #"+i+" input[name='feeTotal[]']").val(masking(feeTotalVal.toString()));
        subTotal+=feeTotalVal;
    }

    if($("#"+modalId+" #"+i+" input[name='taxPriceItem[]']").length>0){
        taxPriceItem = parseInt($("#"+modalId+" #"+i+" input[name='taxPriceItem[]']").val().replace(/\./g, ""));
        taxPercent = parseInt($("#"+modalId+" #"+i+" input[name='taxPercent[]']").val().replace(/\./g, ""));
        taxTotalVal = floatOrInt(taxPriceItem*taxPercent/100);

        $("#"+modalId+" #"+i+" input[name='taxPriceItem"+i+"']").val(taxPriceItem);
        $("#"+modalId+" #"+i+" input[name='taxPercent"+i+"']").val(taxPercent);
        $("#"+modalId+" #"+i+" input[name='taxTotal"+i+"']").val(taxTotalVal);

        $("#"+modalId+" #"+i+" input[name='taxTotal[]']").val(masking(taxTotalVal.toString()));
        subTotal+=taxTotalVal;
    }

    if($("#"+modalId+" #"+i+" input[name='extraCostPrice[]']").length>0){
        extraCostPriceVal = parseInt($("#"+modalId+" #"+i+" input[name='extraCostPrice[]']").val().replace(/\./g, ""));            

        $("#"+modalId+" #"+i+" input[name='extraCostPrice"+i+"']").val(extraCostPriceVal);

        subTotal+=extraCostPriceVal;
    }

    if($("#"+modalId+" #"+i+" input[name='pickUpWeight[]']").length>0){
        pickUpWeightVal = parseInt($("#"+modalId+" #"+i+" input[name='pickUpWeight[]']").val().replace(/\./g, ""));    
        if(pickUpWeightVal<=10){
            pickUpChargeVal=295000;
        }else if(pickUpWeightVal>10 && pickUpWeightVal<=20){
            pickUpChargeVal=178000;
        }else if(pickUpWeightVal>20 && pickUpWeightVal<=40){
            pickUpChargeVal=235000;
        }else{
            pickUpChargeVal=0;
        }

        $("#"+modalId+" #"+i+" input[name='pickUpWeight"+i+"']").val(pickUpWeightVal);
        $("#"+modalId+" #"+i+" input[name='pickUpCharge"+i+"']").val(pickUpChargeVal);
        $("#"+modalId+" #"+i+" input[name='pickUpCharge[]']").val(pickUpChargeVal==0?'FREE':masking(pickUpChargeVal.toString()));

        subTotal+=pickUpChargeVal;
    }

    $("#"+modalId+" #"+i+" input[name='subTotal[]']").val(masking(floatOrInt(subTotal).toString()));

    subTotalArr = JSON.parse(localStorage.getItem("subTotals"));
    itemArr = JSON.parse(localStorage.getItem("itemTotals"));
    kgArr = JSON.parse(localStorage.getItem("kgTotals"));
    diskonArr = JSON.parse(localStorage.getItem("diskonTotals"));
    subTotalArr[i]=subTotal;
    itemArr[i]=subTotalItem;
    diskonArr[i]=subTotalDiskon;
    kgArr[i]=subTotalKg;
    localStorage.setItem("subTotals",JSON.stringify(subTotalArr));
    localStorage.setItem("itemTotals",JSON.stringify(itemArr));
    localStorage.setItem("diskonTotals",JSON.stringify(diskonArr));
    localStorage.setItem("kgTotals",JSON.stringify(kgArr));

    allCalc(modalId);

}

function allCalc(modalId){
    let totalKg=0,totalItem=0,totalDiskon=0,totalBiaya=0,
        subTotalArr = JSON.parse(localStorage.getItem("subTotals")),
        itemArr = JSON.parse(localStorage.getItem("itemTotals")),
        diskonArr = JSON.parse(localStorage.getItem("diskonTotals")),
        kgArr = JSON.parse(localStorage.getItem("kgTotals"));

    for(let z=0;z<subTotalArr.length;z++){
        if(subTotalArr[z]==undefined||subTotalArr[z]==null){
            subTotalArr[z]=0;
        }

        if(itemArr[z]==undefined||itemArr[z]==null){
            itemArr[z]=0;
        }

        if(kgArr[z]==undefined||kgArr[z]==null){
            kgArr[z]=0;
        }
        
        if(diskonArr[z]==undefined||diskonArr[z]==null){
            diskonArr[z]=0;
        }

        totalKg+=kgArr[z];
        totalItem+=itemArr[z];
        totalDiskon+=diskonArr[z];
        totalBiaya+=subTotalArr[z];
    }

    $("#"+modalId+" .totalHargaBoard div .value").text(masking(floatOrInt(totalBiaya).toString()));
    $("#"+modalId+" .totalItemBoard div .value").text(masking(totalItem.toString()));
    $("#"+modalId+" .totalDiskonBoard div .value").text(masking(totalDiskon.toString()));
    $("#"+modalId+" .totalKiloBoard div .value").text(masking(floatOrInt(totalKg).toString()));
}

// function allCalc(modalId){

//     let satuanBeratVal,subTotalKg=0,subTotalItem=0,totalHarga=0,subTotal=0,
//         kgVal,pricePer,
//         pVal,lVal,tVal,
//         medicineVal,medicinePerVal,medicineTotalVal,
//         documentVal,documentPerVal,documentTotalVal,
//         importVal,importPerVal,importTotalVal,
//         packingVal,packingPerVal,packingTotalVal,
//         insurancePercent,insurancePriceItem,insuranceTotalVal,
//         feePercent,feePriceItem,feeTotalVal,
//         taxPercent,taxPriceItem,taxTotalVal,
//         extraCostPriceVal,
//         pickUpWeightVal,pickUpChargeVal,
//         servicesLength = $("#"+modalId+" .services").length;

//     for(var i=0;i<=servicesLength-1;i++){
//         //console.log("Id Price : "+i);
//         satuanBeratVal = $("#"+modalId+" #"+i+" select[name='satuanBerat']").val();
//         if(satuanBeratVal=="KG"){
//             kgVal = parseFloat($("#"+modalId+" #"+i+" input[name='kg[]']").val().replace(/\./g, "").replace(/\,/g, "."));
//             pricePer = parseInt($("#"+modalId+" #"+i+" input[name='pricePerKg']").val().replace(/\./g, ""));
//             $("#"+modalId+" #"+i+" input[name='pricePer[]']").val(masking(pricePer.toString()));
//             subTotal=kgRound(kgVal)*pricePer;
//             subTotalKg+=kgVal;
//             //console.log("KG => "+kgVal);
//         }else if(satuanBeratVal=="VOL"){
//             pVal = parseInt($("#"+modalId+" #"+i+" input[name='panjang[]']").val().replace(/\./g, ""));
//             lVal = parseInt($("#"+modalId+" #"+i+" input[name='lebar[]']").val().replace(/\./g, ""));
//             tVal = parseInt($("#"+modalId+" #"+i+" input[name='tinggi[]']").val().replace(/\./g, ""));
//             kgVal = floatOrInt((pVal*lVal*tVal)/6000);
//             pricePer = $("#"+modalId+" #"+i+" input[name='pricePerVol']").val();
//             $("#"+modalId+" #"+i+" input[name='pricePer[]']").val(masking(pricePer.toString()));
//             $("#"+modalId+" #"+i+" input[name='kg[]']").val(masking(kgVal.toString()));
//             subTotal=kgRound(kgVal)*pricePer;
//             subTotalKg+=kgVal;
//             //console.log("VOL => "+kgVal);
//         }else if(satuanBeratVal=="ITEM"){
//             itemVal = parseInt($("#"+modalId+" #"+i+" input[name='item[]']").val().replace(/\./g, ""));
//             pricePer = parseInt($("#"+modalId+" #"+i+" input[name='pricePerItem']").val().replace(/\./g, ""));
//             $("#"+modalId+" #"+i+" input[name='pricePer[]']").val(masking(pricePer.toString()));
//             subTotal=itemVal*pricePer;
//             subTotalItem+=itemVal;
//         }

//         if($("#"+modalId+" #"+i+" input[name='medicine[]']").length>0){
//             medicineVal = parseInt($("#"+modalId+" #"+i+" input[name='medicine[]']").val().replace(/\./g, ""));
//             medicinePerVal = parseInt($("#"+modalId+" #"+i+" input[name='medicinePer[]']").val().replace(/\./g, ""));
//             medicineTotalVal = medicineVal*medicinePerVal;
//             $("#"+modalId+" #"+i+" input[name='medicineTotal[]']").val(masking(medicineTotalVal.toString()));
//             $("#"+modalId+" #"+i+" input[name='medicineTotal"+i+"']").val(medicineTotalVal);
//             $("#"+modalId+" #"+i+" input[name='medicinePer"+i+"']").val(medicinePerVal);
//             $("#"+modalId+" #"+i+" input[name='medicine"+i+"']").val(medicineVal);
//             subTotal+=medicineTotalVal;
//         }

//         if($("#"+modalId+" #"+i+" input[name='document[]']").length>0){
//             documentVal = parseInt($("#"+modalId+" #"+i+" input[name='document[]']").val().replace(/\./g, ""));
//             documentPerVal = parseInt($("#"+modalId+" #"+i+" input[name='documentPer[]']").val().replace(/\./g, ""));
//             documentTotalVal = documentVal*documentPerVal;
//             $("#"+modalId+" #"+i+" input[name='documentTotal[]']").val(masking(documentTotalVal.toString()));
//             $("#"+modalId+" #"+i+" input[name='documentTotal"+i+"']").val(documentTotalVal);
//             $("#"+modalId+" #"+i+" input[name='documentPer"+i+"']").val(documentPerVal);
//             $("#"+modalId+" #"+i+" input[name='document"+i+"']").val(documentVal);
//             subTotal+=documentTotalVal;
//         }

//         if($("#"+modalId+" #"+i+" input[name='import[]']").length>0){
//             importVal = parseInt($("#"+modalId+" #"+i+" input[name='import[]']").val().replace(/\./g, ""));
//             importPerVal = parseInt($("#"+modalId+" #"+i+" input[name='importPer[]']").val().replace(/\./g, ""));
//             importTotalVal = importVal*importPerVal;
//             $("#"+modalId+" #"+i+" input[name='importTotal[]']").val(masking(importTotalVal.toString()));
//             $("#"+modalId+" #"+i+" input[name='importTotal"+i+"']").val(importTotalVal);
//             $("#"+modalId+" #"+i+" input[name='importPer"+i+"']").val(importPerVal);
//             $("#"+modalId+" #"+i+" input[name='import"+i+"']").val(importVal);
//             subTotal+=importTotalVal;
//         }

//         if($("#"+modalId+" #"+i+" input[name='packing[]']").length>0){
//             packingVal = parseInt($("#"+modalId+" #"+i+" input[name='packing[]']").val().replace(/\./g, ""));
//             packingPerVal = parseInt($("#"+modalId+" #"+i+" input[name='packingPer[]']").val().replace(/\./g, ""));
//             packingTotalVal = packingVal*packingPerVal;
//             $("#"+modalId+" #"+i+" input[name='packingTotal[]']").val(masking(packingTotalVal.toString()));
//             $("#"+modalId+" #"+i+" input[name='packingTotal"+i+"']").val(packingTotalVal);
//             $("#"+modalId+" #"+i+" input[name='packingPer"+i+"']").val(packingPerVal);
//             $("#"+modalId+" #"+i+" input[name='packing"+i+"']").val(packingVal);
//             subTotal+=packingTotalVal;
//         }

//         if($("#"+modalId+" #"+i+" input[name='insurancePriceItem[]']").length){
//             insurancePriceItem = parseInt($("#"+modalId+" #"+i+" input[name='insurancePriceItem[]']").val().replace(/\./g, ""));
//             insurancePercent = parseInt($("#"+modalId+" #"+i+" input[name='insurancePercent[]']").val().replace(/\./g, ""));
//             insuranceTotalVal = floatOrInt(insurancePriceItem*insurancePercent/100);

//             $("#"+modalId+" #"+i+" input[name='insurancePriceItem"+i+"']").val(insurancePriceItem);
//             $("#"+modalId+" #"+i+" input[name='insurancePercent"+i+"']").val(insurancePercent);
//             $("#"+modalId+" #"+i+" input[name='insuranceTotal"+i+"']").val(insuranceTotalVal);

//             $("#"+modalId+" #"+i+" input[name='insuranceTotal[]']").val(masking(insuranceTotalVal.toString()));
//             subTotal+=insuranceTotalVal;
//         }

//         if($("#"+modalId+" #"+i+" input[name='feePriceItem[]']").length>0){
//             feePriceItem = parseInt($("#"+modalId+" #"+i+" input[name='feePriceItem[]']").val().replace(/\./g, ""));
//             feePercent = parseInt($("#"+modalId+" #"+i+" input[name='feePercent[]']").val().replace(/\./g, ""));
//             feeTotalVal = floatOrInt(feePriceItem*feePercent/100);

//             $("#"+modalId+" #"+i+" input[name='feePriceItem"+i+"']").val(feePriceItem);
//             $("#"+modalId+" #"+i+" input[name='feePercent"+i+"']").val(feePercent);
//             $("#"+modalId+" #"+i+" input[name='feeTotal"+i+"']").val(feeTotalVal);

//             $("#"+modalId+" #"+i+" input[name='feeTotal[]']").val(masking(feeTotalVal.toString()));
//             subTotal+=feeTotalVal;
//         }

//         if($("#"+modalId+" #"+i+" input[name='taxPriceItem[]']").length>0){
//             taxPriceItem = parseInt($("#"+modalId+" #"+i+" input[name='taxPriceItem[]']").val().replace(/\./g, ""));
//             taxPercent = parseInt($("#"+modalId+" #"+i+" input[name='taxPercent[]']").val().replace(/\./g, ""));
//             taxTotalVal = floatOrInt(taxPriceItem*taxPercent/100);

//             $("#"+modalId+" #"+i+" input[name='taxPriceItem"+i+"']").val(taxPriceItem);
//             $("#"+modalId+" #"+i+" input[name='taxPercent"+i+"']").val(taxPercent);
//             $("#"+modalId+" #"+i+" input[name='taxTotal"+i+"']").val(taxTotalVal);

//             $("#"+modalId+" #"+i+" input[name='taxTotal[]']").val(masking(taxTotalVal.toString()));
//             subTotal+=taxTotalVal;
//         }

//         if($("#"+modalId+" #"+i+" input[name='extraCostPrice[]']").length>0){
//             extraCostPriceVal = parseInt($("#"+modalId+" #"+i+" input[name='extraCostPrice[]']").val().replace(/\./g, ""));            

//             $("#"+modalId+" #"+i+" input[name='extraCostPrice"+i+"']").val(extraCostPriceVal);

//             subTotal+=extraCostPriceVal;
//         }

//         if($("#"+modalId+" #"+i+" input[name='pickUpWeight[]']").length>0){
//             pickUpWeightVal = parseInt($("#"+modalId+" #"+i+" input[name='pickUpWeight[]']").val().replace(/\./g, ""));    
//             if(pickUpWeightVal<=10){
//                 pickUpChargeVal=295000;
//             }else if(pickUpWeightVal>10 && pickUpWeightVal<=20){
//                 pickUpChargeVal=178000;
//             }else if(pickUpWeightVal>20 && pickUpWeightVal<=40){
//                 pickUpChargeVal=235000;
//             }else{
//                 pickUpChargeVal=0;
//             }

//             $("#"+modalId+" #"+i+" input[name='pickUpWeight"+i+"']").val(pickUpWeightVal);
//             $("#"+modalId+" #"+i+" input[name='pickUpCharge"+i+"']").val(pickUpChargeVal);
//             $("#"+modalId+" #"+i+" input[name='pickUpCharge[]']").val(pickUpChargeVal==0?'FREE':masking(pickUpChargeVal.toString()));

//             subTotal+=pickUpChargeVal;
//         }

//         $("#"+modalId+" #"+i+" input[name='subTotal[]']").val(masking(floatOrInt(subTotal).toString()));
//         totalHarga+=subTotal;
//         subTotal=0;
//     }

//     //console.log("SubTotal => "+subTotalKg);
//     $("#"+modalId+" .totalHargaBoard div .value").text(masking(floatOrInt(totalHarga).toString()));
//     $("#"+modalId+" .totalItemBoard div .value").text(masking(subTotalItem.toString()));
//     $("#"+modalId+" .totalKiloBoard div .value").text(masking(floatOrInt(subTotalKg).toString()));
// }

function createServiceElement(modalId){

    //Menentukan ID Service
    let id = $("#"+modalId+" .services").length,
        idUrut = id+1,
        margin = $("#"+modalId+" .services").length<1?"":"mt-2",
        subTotalArr,itemArr,kgArr,diskonArr;

     //local Storage
     subTotalArr = JSON.parse(localStorage.getItem("subTotals"));
     itemArr = JSON.parse(localStorage.getItem("itemTotals"));
     kgArr = JSON.parse(localStorage.getItem("kgTotals"));
     diskonArr = JSON.parse(localStorage.getItem("diskonTotals"));
     subTotalArr[id]=0;
     itemArr[id]=0;
     kgArr[id]=0;
     diskonArr[id]=0;

    //Tambah Warehouse Option
    let b = JSON.parse(wareH.replace(/&quot;/g,'"')),
        selectWareH;
    b.forEach(element => {
        selectWareH += "<option value='"+element["id"]+"'>"+element["id"]+" - "+element["name"]+" - "+element["location"]+"</option>";
    });

    //Tambah Additional Service Option
    let a = JSON.parse(addServ.replace(/&quot;/g,'"')),
        selectAddServ;
    a.forEach(element => {
        selectAddServ += "<option value='"+element["id"]+"'>"+element["name"]+"</option>";
    });

    let senderFirstName,senderMiddleName,senderLastName,senderEmail,senderPhone,senderAddress,senderSubDistrict,senderDistrict,senderCity,senderProv,senderPostalCode, 
        consFirstName,consMiddleName,consLastName,consEmail,consPhone,consAddress,consSubDistrict,consDistrict,consCity,consProv,consPostalCode,
        dbFirstName,dbMiddleName,dbLastName,dbEmail,dbPhone,dbAddress,dbSubDistrict,dbDistrict,dbCity,dbProv,dbPostalCode,dbSecondName,dbSecondPhone,
        rowSenderStatus,rowConsStatus,dbCustTypeId;

    dbCustTypeId = $(".btn-select").attr("id");
    dbFirstName = $("#"+modalId+" input[name='dbFirstName']").val();   
    dbMiddleName = $("#"+modalId+" input[name='dbMiddleName']").val();  
    dbLastName = $("#"+modalId+" input[name='dbLastName']").val();  
    dbEmail = $("#"+modalId+" input[name='dbEmail']").val();  
    dbPhone = $("#"+modalId+" input[name='dbPhone']").val();  
    dbAddress = $("#"+modalId+" input[name='dbAddress']").val();  
    dbSubDistrict = $("#"+modalId+" input[name='dbSubDistrict']").val();  
    dbDistrict = $("#"+modalId+" input[name='dbDistrict']").val();  
    dbCity = $("#"+modalId+" input[name='dbCity']").val();  
    dbProv = $("#"+modalId+" input[name='dbProv']").val();  
    dbPostalCode = $("#"+modalId+" input[name='dbPostalCode']").val();
    dbSecondName = $("#"+modalId+" input[name='dbSecondName']").val();
    dbSecondPhone = $("#"+modalId+" input[name='dbSecondPhone']").val();

    if(dbCustTypeId == "IND"){
        rowSenderStatus="style='display:none'";
        rowConsStatus="style='display:none'";
        consFirstName=dbFirstName;
        consMiddleName=dbMiddleName;
        consLastName=dbLastName;
        consEmail=dbEmail;
        consPhone=dbPhone;
        consAddress=dbAddress;
        consSubDistrict=dbSubDistrict;
        consDistrict=dbDistrict;
        consCity=dbCity;
        consProv=dbProv;
        consPostalCode=dbPostalCode;
        senderFirstName=dbSecondName;
        senderMiddleName="";
        senderLastName="";
        senderEmail="";
        senderPhone=dbSecondPhone;
        senderAddress="";
        senderSubDistrict="";
        senderDistrict="";
        senderCity="";
        senderProv="";
        senderPostalCode="";
        label = "Service";
    }else if(dbCustTypeId == "COR"){
        rowSenderStatus="style='display:none'";
        rowConsStatus="";
        senderFirstName=dbFirstName;
        senderMiddleName=dbMiddleName;
        senderLastName=dbLastName;
        senderEmail=dbEmail;
        senderPhone=dbPhone;
        senderAddress=dbAddress;
        senderSubDistrict=dbSubDistrict;
        senderDistrict=dbDistrict;
        senderCity=dbCity;
        senderProv=dbProv;
        senderPostalCode=dbPostalCode;
        consFirstName="";
        consMiddleName="";
        consLastName="";
        consEmail="";
        consPhone="";
        consAddress="";
        consSubDistrict="";
        consDistrict="";
        consCity="";
        consProv="";
        consPostalCode="";
        label = "Customer";
    }

    //Element Services
    let servicesElement = "<div class='services "+margin+"' id='"+id+"'>"+
    "<input type='hidden' name='pricePerKg'>"+
    "<input type='hidden' name='pricePerVol'>"+
    "<input type='hidden' name='pricePerItem'>"+
    "<input type='hidden' name='insurancePriceItem"+id+"' class='form-control insurancePriceItem' value='0'>"+
    "<input type='hidden' name='insurancePercent"+id+"' class='form-control insurancePercent' value='0'>"+
    "<input type='hidden' name='insuranceTotal"+id+"' class='form-control insuranceTotal' value='0'>"+
    "<input type='hidden' name='feePriceItem"+id+"' class='form-control feePriceItem' value='0'>"+
    "<input type='hidden' name='feePercent"+id+"' class='form-control feePercent' value='0'>"+
    "<input type='hidden' name='feeTotal"+id+"' class='form-control feeTotal' value='0'>"+
    "<input type='hidden' name='taxPriceItem"+id+"' class='form-control taxPriceItem' value='0'>"+
    "<input type='hidden' name='taxPercent"+id+"' class='form-control taxPercent' value='0'>"+
    "<input type='hidden' name='taxTotal"+id+"' class='form-control taxTotal' value='0'>"+
    "<input type='hidden' name='extraCostPrice"+id+"' class='form-control extraCostPrice' value='0'>"+
    "<input type='hidden' name='extraCostDest"+id+"' class='form-control extraCostDest' value=''>"+
    "<input type='hidden' name='extraCostVendorName"+id+"' class='form-control extraCostVendorName' value=''>"+
    "<input type='hidden' name='extraCostShippingNum"+id+"' class='form-control extraCostShippingNum' value=''>"+
    "<input type='hidden' name='packing"+id+"' class='form-control packing' value='0'>"+
    "<input type='hidden' name='packingPer"+id+"' class='form-control packingPer' value='0'>"+
    "<input type='hidden' name='packingTotal"+id+"' class='form-control packingTotal' value='0'>"+
    "<input type='hidden' name='packingDesc"+id+"' class='form-control packingDesc' value=''>"+
    "<input type='hidden' name='import"+id+"' class='form-control import' value='0'>"+
    "<input type='hidden' name='importPer"+id+"' class='form-control importPer' value='0'>"+
    "<input type='hidden' name='importTotal"+id+"' class='form-control importTotal' value='0'>"+
    "<input type='hidden' name='importDesc"+id+"' class='form-control importDesc' value=''>"+
    "<input type='hidden' name='document"+id+"' class='form-control document' value='0'>"+
    "<input type='hidden' name='documentPer"+id+"' class='form-control documentPer' value='0'>"+
    "<input type='hidden' name='documentTotal"+id+"' class='form-control documentTotal' value='0'>"+
    "<input type='hidden' name='documentDesc"+id+"' class='form-control documentDesc' value=''>"+
    "<input type='hidden' name='medicine"+id+"' class='form-control medicine' value='0'>"+
    "<input type='hidden' name='medicinePer"+id+"' class='form-control medicinePer' value='0'>"+
    "<input type='hidden' name='medicineTotal"+id+"' class='form-control medicineTotal' value='0'>"+
    "<input type='hidden' name='medicineDesc"+id+"' class='form-control medicineDesc' value=''>"+
    "<input type='hidden' name='pickUpWeight"+id+"' class='form-control pickUpWeight' value='0'>"+
    "<input type='hidden' name='pickUpCharge"+id+"' class='form-control pickUpCharge' value='0'>"+
    "<input type='hidden' name='discount"+id+"' class='form-control discount' value='0'>"+
    "<input type='hidden' name='hargaService"+id+"' class='form-control hargaService' value='0'>"+
    "<input type='hidden' name='hargaServiceAfter"+id+"' class='form-control hargaServiceAfter' value='0'>"+
    "<div class='row'>"+
    "<div class='col'>"+
    "<h5>"+label+"#"+idUrut+"</h5>"+
    "</div>"+
    "<div class='col text-right'>"+
    "<button type='button' class='close deleteService'>"+
    "<span aria-hidden='true'>&times;</span>"+
    "</button>"+
    "</div>"+
    "</div>"+

    "<div class='row-sender' "+rowSenderStatus+">"+
    "<div class='row'>"+
        "<div class='col'>"+
            "<label for='senderLabel'>Sender / Pengirim</label>"+
        "</div>"+
    "</div>"+
    "<div class='row mt-2'>"+
        "<div class='col-md-4 col-12'>"+
            "<label for='senderFirstName'>Nama Depan</label>"+
            "<input type='text' class='form-control' value='"+senderFirstName+"' onkeyup='this.value = this.value.toUpperCase()' name='senderFirstName[]' id='"+makeId(8)+"' placeholder='First Name / Nama Depan'>"+
        "</div>"+
        "<div class='col-md-4 col-12'>"+
            "<label for='senderMiddleName'>Nama Tengah</label>"+
            "<input type='text' class='form-control' value='"+senderMiddleName+"' onkeyup='this.value = this.value.toUpperCase()' name='senderMiddleName[]' id='"+makeId(8)+"' placeholder='Middle Name / Nama Tengah (Optional)'>"+
        "</div>"+
        "<div class='col-md-4 col-12'>"+
            "<label for='senderLastName'>Nama Terakhir</label>"+
            "<input type='text' class='form-control' value='"+senderLastName+"' onkeyup='this.value = this.value.toUpperCase()' name='senderLastName[]' id='"+makeId(8)+"' placeholder='Last Name / Nama Terakhir'>"+
        "</div>"+
    "</div>"+
    "<div class='row mt-2'>"+
        "<div class='col-md-6 col-12'>"+
            "<label for='senderEmail'>Alamat Email</label>"+
            "<input type='email' class='form-control' value='"+senderEmail+"' name='senderEmail[]' id='"+makeId(8)+"' placeholder='Email Address'>"+
        "</div>"+
        "<div class='col-md-6 col-12'>"+
            "<label for='senderPhone'>Nomor Whatsapp</label>"+
            "<input type='text' class='form-control' value='"+senderPhone+"' name='senderPhone[]' id='"+makeId(8)+"' placeholder='Whatsapp Number'>"+
        "</div>"+
    "</div>"+
    "<div class='row mt-2'>"+
        "<div class='col-md-6 col-12'>"+
            "<label for='senderAddress'>Alamat</label>"+
            "<input type='text' class='form-control' value='"+senderAddress+"' onkeyup='this.value = this.value.toUpperCase()' name='senderAddress[]' id='"+makeId(8)+"' placeholder='Address'>"+
        "</div>"+
        "<div class='col-md-6 col-12'>"+
            "<label for='senderSubDistrict'>Kelurahan</label>"+
            "<input type='text' class='form-control' value='"+senderSubDistrict+"' onkeyup='this.value = this.value.toUpperCase()' name='senderSubDistrict[]' id='"+makeId(8)+"' placeholder='Sub-District'>"+
        "</div>"+
    "</div>"+
    "<div class='row mt-2'>"+
        "<div class='col-md-6 col-12'>"+
            "<label for='senderDistrict'>Kecamatan</label>"+
            "<input type='text' class='form-control' value='"+senderDistrict+"' onkeyup='this.value = this.value.toUpperCase()' name='senderDistrict[]' id='"+makeId(8)+"' placeholder='District'>"+
        "</div>"+
        "<div class='col-md-6 col-12'>"+
            "<label for='senderCity'>Kabupaten / Kota</label>"+
            "<input type='text' class='form-control' value='"+senderCity+"' onkeyup='this.value = this.value.toUpperCase()' name='senderCity[]' id='"+makeId(8)+"' placeholder='City'>"+
        "</div>"+
    "</div>"+
    "<div class='row mt-2'>"+
        "<div class='col-md-6 col-12'>"+
            "<label for='senderProv'>Provinsi</label>"+
            "<input type='text' class='form-control' value='"+senderProv+"' onkeyup='this.value = this.value.toUpperCase()' name='senderProv[]' id='"+makeId(8)+"' placeholder='Region'>"+
        "</div>"+
        "<div class='col-md-6 col-12'>"+
            "<label for='senderPostalCode'>Kode Pos</label>"+
            "<input type='text' class='form-control' value='"+senderPostalCode+"' name='senderPostalCode[]' id='"+makeId(8)+"' placeholder='Postal Code'>"+
        "</div>"+
    "</div>"+
    "<hr>"+
    "</div>"+

    "<div class='row-cons mt-2' "+rowConsStatus+">"+
    "<div class='row'>"+
        "<div class='col'>"+
            "<label for='consLabel'>Consignee / Penerima</label>"+
        "</div>"+
    "</div>"+
    "<div class='row mt-2'>"+
        "<div class='col-md-4 col-12'>"+
            "<label for='consFirstName'>Nama Depan</label>"+
            "<input type='text' class='form-control' value='"+consFirstName+"' onkeyup='this.value = this.value.toUpperCase()' name='consFirstName[]' id='"+makeId(8)+"' placeholder='First Name / Nama Depan' required>"+
        "</div>"+
        "<div class='col-md-4 col-12'>"+
            "<label for='consMiddleName'>Nama Tengah</label>"+
            "<input type='text' class='form-control' value='"+consMiddleName+"' onkeyup='this.value = this.value.toUpperCase()' name='consMiddleName[]' id='"+makeId(8)+"' placeholder='Middle Name / Nama Tengah (Optional)'>"+
        "</div>"+
        "<div class='col-md-4 col-12'>"+
            "<label for='consLastName'>Nama Terakhir</label>"+
            "<input type='text' class='form-control' value='"+consLastName+"' onkeyup='this.value = this.value.toUpperCase()' name='consLastName[]' id='"+makeId(8)+"' placeholder='Last Name / Nama Terakhir' required>"+
        "</div>"+
    "</div>"+
    "<div class='row mt-2'>"+
        "<div class='col-md-6 col-12'>"+
            "<label for='consEmail'>Alamat Email</label>"+
            "<input type='email' class='form-control' value='"+consEmail+"' name='consEmail[]' id='"+makeId(8)+"' placeholder='Email Address' required>"+
        "</div>"+
        "<div class='col-md-6 col-12'>"+
            "<label for='consPhone'>Nomor Whatsapp</label>"+
            "<input type='text' class='form-control' value='"+consPhone+"' name='consPhone[]' id='"+makeId(8)+"' placeholder='Whatsapp Number' required>"+
        "</div>"+
    "</div>"+
    "<div class='row mt-2'>"+
        "<div class='col-md-6 col-12'>"+
            "<label for='consAddress'>Alamat</label>"+
            "<input type='text' class='form-control' value='"+consAddress+"' onkeyup='this.value = this.value.toUpperCase()' name='consAddress[]' id='"+makeId(8)+"' placeholder='Address' required>"+
        "</div>"+
        "<div class='col-md-6 col-12'>"+
            "<label for='consSubDistrict'>Kelurahan</label>"+
            "<input type='text' class='form-control' value='"+consSubDistrict+"' onkeyup='this.value = this.value.toUpperCase()' name='consSubDistrict[]' id='"+makeId(8)+"' placeholder='Sub-District'>"+
        "</div>"+
    "</div>"+
    "<div class='row mt-2'>"+
        "<div class='col-md-6 col-12'>"+
            "<label for='consDistrict'>Kecamatan</label>"+
            "<input type='text' class='form-control' value='"+consDistrict+"' onkeyup='this.value = this.value.toUpperCase()' name='consDistrict[]' id='"+makeId(8)+"' placeholder='District' required>"+
        "</div>"+
        "<div class='col-md-6 col-12'>"+
            "<label for='consCity'>Kabupaten / Kota</label>"+
            "<input type='text' class='form-control' value='"+consCity+"' onkeyup='this.value = this.value.toUpperCase()' name='consCity[]' id='"+makeId(8)+"' placeholder='City' required>"+
        "</div>"+
    "</div>"+
    "<div class='row mt-2'>"+
        "<div class='col-md-6 col-12'>"+
            "<label for='consProv'>Provinsi</label>"+
            "<input type='text' class='form-control' value='"+consProv+"' onkeyup='this.value = this.value.toUpperCase()' name='consProv[]' id='"+makeId(8)+"' placeholder='Region' required>"+
        "</div>"+
        "<div class='col-md-6 col-12'>"+
            "<label for='consPostalCode'>Kode Pos</label>"+
            "<input type='text' class='form-control' value='"+consPostalCode+"' name='consPostalCode[]' id='"+makeId(8)+"' placeholder='Postal Code' required>"+
        "</div>"+
    "</div>"+
    "<hr>"+
    "</div>"+

    "<div class='row mt-2'>"+
    "<div class='col'>"+
    "<div class='form-group'>"+
    "<label for='warehouse'>Warehouse</label>"+
    "<select name='warehouse[]' id='"+makeId(8)+"' class='form-control' required>"+
    "<option value='' hidden>Pilih Warehouse</option>"+selectWareH+
    "</select>"+
    "</div>"+
    "</div>"+
    "<div class='col col-service'>"+
    "<div class='form-group'>"+
    "<label for='service'>Service</label>"+
    "<select name='service[]' id='"+makeId(8)+"' class='form-control' required></select>"+
    "</div>"+
    "</div>"+
    "<div class='col'>"+
    "<div class='form-group'>"+
    "<label for='satuanBerat'>Satuan Berat</label>"+
    "<select name='satuanBerat' id='"+makeId(8)+"' class='form-control'></select>"+
    "</div>"+
    "</div>"+
    "</div>"+
    "<div class='row row-detil-biaya mt-2'>"+
    "<div class='col col-volume'>"+
    "<div class='form-group'>"+
    "<label for='panjang'>Panjang (Cm)</label>"+
    "<input type='text' name='panjang[]' id='"+makeId(8)+"' class='form-control masking'>"+
    "</div>"+
    "</div>"+
    "<div class='col col-volume'>"+
    "<div class='form-group'>"+
    "<label for='lebar'>Lebar (Cm)</label>"+
    "<input type='text' name='lebar[]' id='"+makeId(8)+"' class='form-control masking'>"+
    "</div>"+
    "</div>"+
    "<div class='col col-volume'>"+
    "<div class='form-group'>"+
    "<label for='tinggi'>Tinggi (Cm)</label>"+
    "<input type='text' name='tinggi[]' id='"+makeId(8)+"' class='form-control masking'>"+
    "</div>"+
    "</div>"+
    "<div class='col col-kg'>"+
    "<div class='form-group'>"+
    "<label for='beratKg'>Berat (Kg)</label>"+
    "<input type='text' name='kg[]' id='"+makeId(8)+"' class='form-control maskingComma'>"+
    "</div>"+
    "</div>"+
    "<div class='col col-item'>"+
    "<div class='form-group'>"+
    "<label for='item'>Jumlah Item</label>"+
    "<input type='text' name='item[]' id='"+makeId(8)+"' class='form-control masking'>"+
    "</div>"+
    "</div>"+
    "<div class='col col-priceper'>"+
    "<div class='form-group'>"+
    "<label for='pricePer'>Harga/Kg</label>"+
    "<input type='text' name='pricePer[]' id='"+makeId(8)+"' value='0' class='form-control' readonly>"+
    "</div>"+
    "</div>"+
    "<div class='col col-actual'>"+
    "<div class='form-group'>"+
    "<label for='actualKg'>Aktual (Kg)</label>"+
    "<input type='text' name='actualKg[]' id='"+makeId(8)+"' class='form-control maskingComma'>"+
    "</div>"+
    "</div>"+
    "</div>"+
    "<div class='row row-additional mt-2'>"+
    "<div class='col'>"+
    "<div class='form-group'>"+
    "<label for='AdditionalService'>Add Additional Service</label>"+
    "<select name='additionalService' id='"+makeId(8)+"' class='form-control'>"+
    "<option value='' hidden>Pilih Additional Service</option>"+selectAddServ+
    "</select>"+
    "</div>"+
    "</div>"+
    "</div>"+
    "<div class='row mt-2'>"+
    "<div class='col'>"+
    "<div class='form-group'>"+
    "<label for='subTotal'>Sub Total</label>"+
    "<input type='text' name='subTotal[]' id='"+makeId(8)+"' class='form-control' readonly>"+
    "</div>"+
    "</div>"+
    "</div>"+
    "</div>";

    $("#"+modalId+" .row-services").append(servicesElement);

    $("#"+modalId+" #"+id+" .col-service").hide();
    $("#"+modalId+" #"+id+" .col-kg").hide();
    $("#"+modalId+" #"+id+" .col-volume").hide();
    $("#"+modalId+" #"+id+" .col-item").hide();
    $("#"+modalId+" #"+id+" .row-detil-biaya").hide();

    // console.log("createServiceElement");

}

function createAdditionalServiceElement(id,serviceElementId){

    if(id=="ASR"){
        return "<div class='row' data-id='ASR'>"+
        "<div class='col'>"+
        "<div class='form-group'>"+
        "<label for='insurancePriceItem'>Harga Barang (Rp)</label>"+
        "<input type='text' name='insurancePriceItem[]' id='"+makeId(8)+"' class='form-control masking' value='0' required>"+
        "</div>"+
        "</div>"+
        "<div class='col'>"+
        "<div class='form-group'>"+
        "<label for='insurancePercent'>Persentase (%)</label>"+
        "<input type='text' name='insurancePercent[]' id='"+makeId(8)+"' class='form-control masking' value='7' readonly>"+
        "</div>"+
        "</div>"+
        "<div class='col'>"+
        "<div class='form-group'>"+
        "<label for='insuranceTotal'>Asuransi (Rp)</label>"+
        "<input type='text' name='insuranceTotal[]' id='"+makeId(8)+"' class='form-control' value='0' readonly>"+
        "</div>"+
        "</div>"+
        "<div class='col-1' style='display:flex;align-items:end;padding-bottom:10px'>"+
        "<button type='button' class='btn deleteAdditional' style='font-size:25px;color:red'><i class='fas fa-trash'></i></button>"+
        "</div>"+
        "</div>";
    }

    if(id=="FEE"){
        return "<div class='row' data-id='FEE'>"+
        "<div class='col'>"+
        "<div class='form-group'>"+
        "<label for='feePriceItem'>Harga Barang (Rp)</label>"+
        "<input type='text' name='feePriceItem[]' id='"+makeId(8)+"' class='form-control masking' value='0' required>"+
        "</div>"+
        "</div>"+
        "<div class='col'>"+
        "<div class='form-group'>"+
        "<label for='feePercent'>Persentase (%)</label>"+
        "<input type='text' name='feePercent[]' id='"+makeId(8)+"' class='form-control masking' value='5' readonly>"+
        "</div>"+
        "</div>"+
        "<div class='col'>"+
        "<div class='form-group'>"+
        "<label for='feeTotal'>Fee (Rp)</label>"+
        "<input type='text' name='feeTotal[]' id='"+makeId(8)+"' class='form-control' value='0' readonly>"+
        "</div>"+
        "</div>"+
        "<div class='col-1' style='display:flex;align-items:end;padding-bottom:10px'>"+
        "<button type='button' class='btn deleteAdditional' style='font-size:25px;color:red'><i class='fas fa-trash'></i></button>"+
        "</div>"+
        "</div>";
    }

    if(id=="TAX"){
        return "<div class='row' data-id='TAX'>"+
        "<div class='col'>"+
        "<div class='form-group'>"+
        "<label for='taxPriceItem'>Harga Barang (Rp)</label>"+
        "<input type='text' name='taxPriceItem[]' id='"+makeId(8)+"' class='form-control masking' value='0' required>"+
        "</div>"+
        "</div>"+
        "<div class='col'>"+
        "<div class='form-group'>"+
        "<label for='taxPercent'>Persentase (%)</label>"+
        "<input type='text' name='taxPercent[]' id='"+makeId(8)+"' class='form-control masking' value='5' readonly>"+
        "</div>"+
        "</div>"+
        "<div class='col'>"+
        "<div class='form-group'>"+
        "<label for='taxTotal'>Tax (Rp)</label>"+
        "<input type='text' name='taxTotal[]' id='"+makeId(8)+"' class='form-control' value='0' readonly>"+
        "</div>"+
        "</div>"+
        "<div class='col-1' style='display:flex;align-items:end;padding-bottom:10px'>"+
        "<button type='button' class='btn deleteAdditional' style='font-size:25px;color:red'><i class='fas fa-trash'></i></button>"+
        "</div>"+
        "</div>";
    }

    if(id=="EON"){
        return "<div class='row' data-id='EON'>"+
        "<div class='col'>"+
        "<div class='form-group'>"+
        "<label for='extraCostPrice'>Extra Ongkir (Rp)</label>"+
        "<input type='text' name='extraCostPrice[]' id='"+makeId(8)+"' class='form-control masking' value='0' required>"+
        "</div>"+
        "</div>"+
        "<div class='col'>"+
        "<div class='form-group'>"+
        "<label for='extraCostDest'>Tujuan</label>"+
        "<input type='text' name='extraCostDest[]' id='"+makeId(8)+"' class='form-control' value='' onkeyup='this.value=this.value.toUpperCase()' required>"+
        "</div>"+
        "</div>"+
        "<div class='col'>"+
        "<div class='form-group'>"+
        "<label for='extraCostVendorName'>Vendor</label>"+
        "<input type='text' name='extraCostVendorName[]' id='"+makeId(8)+"' class='form-control' value='' onkeyup='this.value=this.value.toUpperCase()' required>"+
        "</div>"+
        "</div>"+
        "<div class='col'>"+
        "<div class='form-group'>"+
        "<label for='extraCostShippingNum'>No. Resi</label>"+
        "<input type='text' name='extraCostShippingNum[]' id='"+makeId(8)+"' class='form-control' value='' onkeyup='this.value=this.value.toUpperCase()' required>"+
        "</div>"+
        "</div>"+
        "<div class='col-1' style='display:flex;align-items:end;padding-bottom:10px'>"+
        "<button type='button' class='btn deleteAdditional' style='font-size:25px;color:red'><i class='fas fa-trash'></i></button>"+
        "</div>"+
        "</div>";
    }

    if(id=="KAY"){
        return "<div class='row' data-id='KAY'>"+
        "<div class='col'>"+
        "<div class='form-group'>"+
        "<label for='packing'>Packing Kayu (Jumlah Box)</label>"+
        "<input type='text' name='packing[]' id='"+makeId(8)+"' class='form-control masking' value='0' required>"+
        "</div>"+
        "</div>"+
        "<div class='col'>"+
        "<div class='form-group'>"+
        "<label for='packingPer'>Harga / Box (Rp)</label>"+
        "<input type='text' name='packingPer[]' id='"+makeId(8)+"' class='form-control' value='' required>"+
        "</div>"+
        "</div>"+
        "<div class='col'>"+
        "<div class='form-group'>"+
        "<label for='packingTotal'>Packing Total (Rp)</label>"+
        "<input type='text' name='packingTotal[]' id='"+makeId(8)+"' class='form-control' value='0' readonly>"+
        "</div>"+
        "</div>"+
        "<div class='col' style='display:none'>"+
        "<div class='form-group'>"+
        "<label for='packingDesc'>Keterangan</label>"+
        "<input type='text' name='packingDesc[]' id='"+makeId(8)+"' class='form-control' value='' onkeyup='this.value=this.value.toUpperCase()' placeholder='optional'>"+
        "</div>"+
        "</div>"+
        "<div class='col-1' style='display:flex;align-items:end;padding-bottom:10px'>"+
        "<button type='button' class='btn deleteAdditional' style='font-size:25px;color:red'><i class='fas fa-trash'></i></button>"+
        "</div>"+
        "</div>";
    }

    if(id=="IPM"){
        return "<div class='row' data-id='IPM'>"+
        "<div class='col'>"+
        "<div class='form-group'>"+
        "<label for='import'>Jumlah Import Permit</label>"+
        "<input type='text' name='import[]' id='"+makeId(8)+"' class='form-control masking' value='0' required>"+
        "</div>"+
        "</div>"+
        "<div class='col'>"+
        "<div class='form-group'>"+
        "<label for='importPer'>Harga / Import Permit (Rp)</label>"+
        "<input type='text' name='importPer[]' id='"+makeId(8)+"' class='form-control masking' value='500.000' readonly>"+
        "</div>"+
        "</div>"+
        "<div class='col'>"+
        "<div class='form-group'>"+
        "<label for='importTotal'>Import Permit Total (Rp)</label>"+
        "<input type='text' name='importTotal[]' id='"+makeId(8)+"' class='form-control' value='0' readonly>"+
        "</div>"+
        "</div>"+
        "<div class='col' style='display:none'>"+
        "<div class='form-group'>"+
        "<label for='importDesc'>Keterangan</label>"+
        "<input type='text' name='importDesc[]' id='"+makeId(8)+"' class='form-control' value='' onkeyup='this.value=this.value.toUpperCase()' placeholder='optional'>"+
        "</div>"+
        "</div>"+
        "<div class='col-1' style='display:flex;align-items:end;padding-bottom:10px'>"+
        "<button type='button' class='btn deleteAdditional' style='font-size:25px;color:red'><i class='fas fa-trash'></i></button>"+
        "</div>"+
        "</div>";
    }

    if(id=="DOC"){
        return "<div class='row' data-id='DOC'>"+
        "<div class='col'>"+
        "<div class='form-group'>"+
        "<label for='document'>Jumlah Document</label>"+
        "<input type='text' name='document[]' id='"+makeId(8)+"' class='form-control masking' value='0' required>"+
        "</div>"+
        "</div>"+
        "<div class='col'>"+
        "<div class='form-group'>"+
        "<label for='documentPer'>Harga / Document (Rp)</label>"+
        "<input type='text' name='documentPer[]' id='"+makeId(8)+"' class='form-control masking' value='250.000' readonly>"+
        "</div>"+
        "</div>"+
        "<div class='col'>"+
        "<div class='form-group'>"+
        "<label for='documentTotal'>Document Total (Rp)</label>"+
        "<input type='text' name='documentTotal[]' id='"+makeId(8)+"' class='form-control masking' value='0' readonly>"+
        "</div>"+
        "</div>"+
        "<div class='col' style='display:none'>"+
        "<div class='form-group'>"+
        "<label for='documentDesc'>Keterangan</label>"+
        "<input type='text' name='documentDesc[]' id='"+makeId(8)+"' class='form-control' value='' onkeyup='this.value=this.value.toUpperCase()' placeholder='optional'>"+
        "</div>"+
        "</div>"+
        "<div class='col-1' style='display:flex;align-items:end;padding-bottom:10px'>"+
        "<button type='button' class='btn deleteAdditional' style='font-size:25px;color:red'><i class='fas fa-trash'></i></button>"+
        "</div>"+
        "</div>";
    }

    if(id=="MED"){
        return "<div class='row' data-id='MED'>"+
        "<div class='col'>"+
        "<div class='form-group'>"+
        "<label for='medicine'>Jumlah DR Medicine</label>"+
        "<input type='text' name='medicine[]' id='"+makeId(8)+"'  class='form-control masking' value='0' required>"+
        "</div>"+
        "</div>"+
        "<div class='col'>"+
        "<div class='form-group'>"+
        "<label for='medicinePer'>Harga / DR Medicine (Rp)</label>"+
        "<input type='text' name='medicinePer[]' id='"+makeId(8)+"'  class='form-control masking' value='250.000' readonly>"+
        "</div>"+
        "</div>"+
        "<div class='col'>"+
        "<div class='form-group'>"+
        "<label for='medicineTotal'>DR Medicine Total (Rp)</label>"+
        "<input type='text' name='medicineTotal[]' id='"+makeId(8)+"'  class='form-control masking' value='0' readonly>"+
        "</div>"+
        "</div>"+
        "<div class='col' style='display:none'>"+
        "<div class='form-group'>"+
        "<label for='medicineDesc'>Keterangan</label>"+
        "<input type='text' name='medicineDesc[]' id='"+makeId(8)+"' class='form-control' value='' onkeyup='this.value=this.value.toUpperCase()' placeholder='optional'>"+
        "</div>"+
        "</div>"+
        "<div class='col-1' style='display:flex;align-items:end;padding-bottom:10px'>"+
        "<button type='button' class='btn deleteAdditional' style='font-size:25px;color:red'><i class='fas fa-trash'></i></button>"+
        "</div>"+
        "</div>";
    }

    if(id=="PCK"){
        return "<div class='row' data-id='PCK'>"+
        "<div class='col'>"+
        "<div class='form-group'>"+
        "<label for='pickUpWeight'>Berat Pickup (Kg)</label>"+
        "<input type='text' name='pickUpWeight[]' id='"+makeId(8)+"' class='form-control masking' value='0' required>"+
        "</div>"+
        "</div>"+
        "<div class='col'>"+
        "<div class='form-group'>"+
        "<label for='pickUpCharge'>Pickup Charge (Rp)</label>"+
        "<input type='text' name='pickUpCharge[]' id='"+makeId(8)+"' class='form-control' value='0' readonly>"+
        "</div>"+
        "</div>"+
        "<div class='col-1' style='display:flex;align-items:end;padding-bottom:10px'>"+
        "<button type='button' class='btn deleteAdditional' style='font-size:25px;color:red'><i class='fas fa-trash'></i></button>"+
        "</div>"+
        "</div>";
    }

    if(id=="DSK"){
        return "<div class='row' data-id='DSK'>"+
        "<div class='col'>"+
        "<div class='form-group'>"+
        "<label for='discount'>Diskon (Rp)</label>"+
        "<input type='text' name='discount[]' id='"+makeId(8)+"' class='form-control masking' value='0' required>"+
        "</div>"+
        "</div>"+
        "<div class='col'>"+
        "<div class='form-group'>"+
        "<label for='hargaService'>Harga Service (Rp)</label>"+
        "<input type='text' name='hargaService[]' id='"+makeId(8)+"' class='form-control' value='0' readonly>"+
        "</div>"+
        "</div>"+
        "<div class='col'>"+
        "<div class='form-group'>"+
        "<label for='hargaServiceAfter'>Setelah Diskon (Rp)</label>"+
        "<input type='text' name='hargaServiceAfter[]' id='"+makeId(8)+"' class='form-control masking' value='0' readonly>"+
        "</div>"+
        "</div>"+
        "<div class='col-1' style='display:flex;align-items:end;padding-bottom:10px'>"+
        "<button type='button' class='btn deleteAdditional' style='font-size:25px;color:red'><i class='fas fa-trash'></i></button>"+
        "</div>"+
        "</div>";
    }
}

function ruleForAdditional(id,serviceElementId,method,modalId){

    $("#"+modalId+" form").validate();
    if(id=="ASR"){
        $("#"+modalId+" #"+serviceElementId+" input[name='insurancePriceItem[]']").rules(method, "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='insurancePercent[]']").rules(method, "greaterThanZero");
    }

    if(id=="FEE"){
        $("#"+modalId+" #"+serviceElementId+" input[name='feePriceItem[]']").rules(method, "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='feePercent[]']").rules(method, "greaterThanZero");
    }

    if(id=="TAX"){
        $("#"+modalId+" #"+serviceElementId+" input[name='taxPriceItem[]']").rules(method, "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='taxPercent[]']").rules(method, "greaterThanZero");
    }

    if(id=="EON"){
        $("#"+modalId+" #"+serviceElementId+" input[name='extraCostPrice[]']").rules(method, "greaterThanZero");
    }

    if(id=="KAY"){
        $("#"+modalId+" #"+serviceElementId+" input[name='packing[]']").rules(method, "greaterThanZero");
    }

    if(id=="IPM"){
        $("#"+modalId+" #"+serviceElementId+" input[name='import[]']").rules(method, "greaterThanZero");
    }

    if(id=="DOC"){
        $("#"+modalId+" #"+serviceElementId+" input[name='document[]']").rules(method, "greaterThanZero");
    }

    if(id=="MED"){
        $("#"+modalId+" #"+serviceElementId+" input[name='medicine[]']").rules(method, "greaterThanZero");
    }

    if(id=="PCK"){
        $("#"+modalId+" #"+serviceElementId+" input[name='pickUpWeight[]']").rules(method, "greaterThanZero");
    }

    if(id=="DSK"){
        $("#"+modalId+" #"+serviceElementId+" input[name='discount[]']").rules(method, "greaterThanZero");
    }
}

function removeAdditionalResetData(serviceElementId,addElementDataId,modalId){
    if(addElementDataId=="ASR"){
        $("#"+modalId+" #"+serviceElementId+" .insurancePriceItem").val(0);
        // $("#"+modalId+" #"+serviceElementId+" .insurancePercent").val(0);
        $("#"+modalId+" #"+serviceElementId+" .insuranceTotal").val(0);
        return true;
    }

    if(addElementDataId=="FEE"){
        $("#"+modalId+" #"+serviceElementId+" .feePriceItem").val(0);
        // $("#"+modalId+" #"+serviceElementId+" .feePercent").val(0);
        $("#"+modalId+" #"+serviceElementId+" .feeTotal").val(0);
        return true;
    }

    if(addElementDataId=="TAX"){
        $("#"+modalId+" #"+serviceElementId+" .taxPriceItem").val(0);
        // $("#"+modalId+" #"+serviceElementId+" .taxPercent").val(0);
        $("#"+modalId+" #"+serviceElementId+" .taxTotal").val(0);
        return true;
    }

    if(addElementDataId=="EON"){
        $("#"+modalId+" #"+serviceElementId+" .extraCostPrice").val(0);
        $("#"+modalId+" #"+serviceElementId+" .extraCostDest").val("");
        $("#"+modalId+" #"+serviceElementId+" .extraCostVendorName").val("");
        $("#"+modalId+" #"+serviceElementId+" .extraCostShippingNum").val("");
        return true;
    }

    if(addElementDataId=="KAY"){
        $("#"+modalId+" #"+serviceElementId+" .packing").val(0);
        $("#"+modalId+" #"+serviceElementId+" .packingTotal").val(0);
        $("#"+modalId+" #"+serviceElementId+" .packingDesc").val("");
        return true;
    }

    if(addElementDataId=="IPM"){
        $("#"+modalId+" #"+serviceElementId+" .import").val(0);
        $("#"+modalId+" #"+serviceElementId+" .importTotal").val(0);
        $("#"+modalId+" #"+serviceElementId+" .importDesc").val("");
        return true;
    }

    if(addElementDataId=="DOC"){
        $("#"+modalId+" #"+serviceElementId+" .document").val(0);
        $("#"+modalId+" #"+serviceElementId+" .documentTotal").val(0);
        $("#"+modalId+" #"+serviceElementId+" .documentDesc").val("");
        return true;
    }

    if(addElementDataId=="MED"){
        $("#"+modalId+" #"+serviceElementId+" .medicine").val(0);
        $("#"+modalId+" #"+serviceElementId+" .medicineTotal").val(0);
        $("#"+modalId+" #"+serviceElementId+" .medicineDesc").val("");
        return true;
    }

    if(addElementDataId=="PCK"){
        $("#"+modalId+" #"+serviceElementId+" .pickUpWeight").val(0);
        $("#"+modalId+" #"+serviceElementId+" .pickUpCharge").val(0);
        return true;
    }

    if(addElementDataId=="DSK"){
        $("#"+modalId+" #"+serviceElementId+" .discount").val(0);
        $("#"+modalId+" #"+serviceElementId+" .hargaService").val(0);
        $("#"+modalId+" #"+serviceElementId+" .hargaServiceAfter").val(0);
        return true;
    }
}

masK = "###.###.###.###.###";
masKC = "###.###.###.###.###,00";

function keyUpFunc(){
    let modalId = $(this).closest(".modal").attr("id"),
        serviceElementId = $(this).closest("#"+modalId+" .services").attr("id");
    $(this).mask(masK, {
        reverse: true
    });
    partialCalc(modalId,serviceElementId);
}

function keyUpFuncKC(){
    let modalId = $(this).closest(".modal").attr("id"),
        serviceElementId = $(this).closest("#"+modalId+" .services").attr("id");
    $(this).mask(masKC, {
        reverse: true
    });
    partialCalc(modalId,serviceElementId);
}

jQuery.validator.addMethod("greaterThanZero", function(value, element) {
    return this.optional(element) || (parseFloat(value.replace(/\./g, "").replace(/\,/g, ".")) > 0);
}, "Tidak Boleh Nol");

