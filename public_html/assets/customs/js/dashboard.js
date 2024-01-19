loadData();

$("#filterWarehouse,#filterBulan,#filterTahun").on("change",function(){
    loadData();
});

function loadData(){

    let filterWarehouse = $("#filterWarehouse").val(),
        filterBulan = $("#filterBulan").val(),
        filterTahun = $("#filterTahun").val();

    $.ajax({
        type: "GET",
        url: location.origin+"/load/dashboard",
        data: {'filterWarehouse':filterWarehouse,'filterBulan':filterBulan,'filterTahun':filterTahun},
        success: function(msg) {
            var json = JSON.parse(msg),
                totalBeratInd = json.totalBeratInd!=null?json.totalBeratInd.toFixed(2).replace(".",",")+" Kg":'0,00 Kg',
                totalBeratCor = json.totalBeratCor!=null?json.totalBeratCor.toFixed(2).replace(".",",")+" Kg":'0,00 Kg',
                totalBerat = json.totalBerat!=null?masking(json.totalBerat.toFixed(2))+' Kg':'0,00 Kg',
                totalCustomer = json.totalCustomer!=null?masking(json.totalCustomer):'0',
                totalCustomerInd = json.totalCustomerInd!=null?masking(json.totalCustomerInd):'0',
                totalCustomerCor = json.totalCustomerCor!=null?masking(json.totalCustomerCor):'0';
                

            $("#titleTotalBeratCust").html("Total Berat & Customer | Tahun "+filterTahun);
            $("#titleTotalPendapatan").html("Total Pendapatan | Tahun "+filterTahun);

            // $("#totalBerat").html(simpleWeight(Math.round(json.totalBerat)));
            // $("#totalBeratInd").html("Individual : "+simpleWeight(Math.round(json.totalBeratInd)));
            // $("#totalBeratCor").html("Corporate : "+simpleWeight(Math.round(json.totalBeratCor)));
            
            // if($("#totalBerat").length>0){
                $("#totalBerat").html(totalBerat);
                $("#totalBeratInd").html("Individual : "+totalBeratInd);
                $("#totalBeratCor").html("Corporate : "+totalBeratCor);
            // }

            // if($("#totalCustomer").length>0){
                $("#totalCustomer").html(totalCustomer);
                $("#totalCustomerInd").html("Individual : "+totalCustomerInd);
                $("#totalCustomerCor").html("Corporate : "+totalCustomerCor);
            // }

            // if($("#totalPendapatan").length>0){
                $("#totalPendapatan").html("Rp."+masking(json.totalPendapatan));
                $("#totalPendapatanInd").html("Individual : Rp."+masking(json.totalPendapatanInd));
                $("#totalPendapatanCor").html("Corporate : Rp."+masking(json.totalPendapatanCor));
            // }
            
            // if($("#totalDiskon").length>0){
                $("#totalDiskon").html("Rp."+masking(json.totalDiskon));
                $("#totalDiskonInd").html("Individual : Rp."+masking(json.totalDiskonInd));
                $("#totalDiskonCor").html("Corporate : Rp."+masking(json.totalDiskonCor));
            // }

            // if($("#totalPaid").length>0){
                if(paidStat){
                    $("#totalPaid").html("Rp."+masking(json.totalPaid));
                }
                $("#totalUnpaid").html("UNPAID Rp."+masking(json.totalUnpaid));
            // }

            loadDataChart([[json.yearBerat,json.yearCustomer],[json.yearInd,json.yearCor]]);
        }
    });

}

function loadDataChart(data) {

    var type,chartId,node,color,type,chartCanvas,
        chart = document.getElementsByTagName('canvas');

    for (var i = 0; i <= chart.length - 1; i++) {

        chartId = chart[i].id;

        document.getElementById(chartId).remove();

        node = document.createElement("canvas");
        node.setAttribute("id", chartId);
        node.setAttribute("class", "chart");
        document.getElementsByClassName("card-body-" + chartId)[0].appendChild(node);

        color = ["#dd3645","#28a745"],
        labelText = ["Berat","Customer"];
        if (chartId == "pendapatanChart") {
            color = ["#ffc107","#0888e6"];
            labelText = ["Individual","Corporate"];
        } 

        type="bar",
        display=true;

        chartCanvas = $("#" + chartId).get(0).getContext('2d');

        var graphChartData = {
            labels: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"],
            datasets: [{
                fill: false,
                borderWidth: 2,
                lineTension: 0,
                spanGaps: false,
                borderColor: color[0],
                pointRadius: 5,
                pointHoverRadius: 7,
                pointColor: color[0],
                pointBackgroundColor: color[0],
                backgroundColor: color[0],
                hoverBackgroundColor: 'white',
                data: data[i][0],
                label: labelText[0],
            }, {
                fill: false,
                borderWidth: 2,
                lineTension: 0,
                spanGaps: false,
                borderColor: color[1],
                pointRadius: 5,
                pointHoverRadius: 7,
                pointColor: color[1],
                pointBackgroundColor: color[1],
                backgroundColor: color[1],
                hoverBackgroundColor: 'white',
                data: data[i][1],
                label: labelText[1],
            }]
        };

        var graphChartOptions = {
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        var label = data.datasets[tooltipItem.datasetIndex].label || '';

                        if (label) {
                            label += ': ';
                        }

                        if (this._chart.canvas.id == "beratCustChart") {
                            if (tooltipItem.datasetIndex == 0) {
                                label = maskMoney(Math.round(tooltipItem.yLabel));
                                label += " Kg";
                            }else{
                                label = maskMoney(tooltipItem.yLabel);
                                label += " Customer";
                            }
                        } else {
                            label = maskMoney(tooltipItem.yLabel);
                            label = "Rp." + label;
                        }

                        return label;
                    }
                }
            },
            maintainAspectRatio: false,
            responsive: true,
            legend: {
                display: display
            },
            scales: {
                xAxes: [{
                    ticks: {
                        fontColor: "#000000",
                    },
                    gridLines: {
                        display: false,
                        color: "#e7e7e7",
                        drawBorder: false
                    }
                }],
                yAxes: [{
                    ticks: {
                        fontColor: "#000000",
                        callback: function(value, index, values) {
                            return simpleMoney(value);
                        }
                    },
                    gridLines: {
                        display: true,
                        color: "#e7e7e7",
                        drawBorder: false
                    }
                }, ]
            },
        }

        var graphChart = new Chart(chartCanvas, {
            type: type,
            data: graphChartData,
            options: graphChartOptions
        });

    }

}