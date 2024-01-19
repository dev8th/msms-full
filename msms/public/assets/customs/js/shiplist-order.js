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

                    return true;
                }

                Swal.fire({
                    title: json.status,
                    text: json.text,
                    icon: "error"
                });
            }
        });

        refreshTable(tableCi,location.origin+"/shiplist/table/order/"+custTypeId+"?filterTanggal=","table-ci_info");
});