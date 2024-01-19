(()=>{

    $(document).ready(function(){

        $('#theElement').css("height","650px").html("<iframe scrolling='no' style='width:100%;height:100%;border:none' src='https://app-mismass.com/webform'></iframe>");

        if (window.addEventListener) {
            window.addEventListener("message",function(msg){
                msg.data!=""?msg.data.status!=""?showNotif(msg.data):'':'';
            });
        }

        function showNotif(data){
            data.status == "Success" ? Swal.fire(data.status, data.text, 'success') : ( data.noAdmin == null ? Swal.fire(data.status, data.text, 'error') : Swal.fire({
                icon: 'error',
                title: data.status,
                text: data.text,
                confirmButtonText: "Chat Admin",
              }).then((result) => {
                if (result.isConfirmed) {
                    window.open("https://api.whatsapp.com/send/?phone="+data.noAdmin, '_blank');
                }
              }))
        }
    });

})()