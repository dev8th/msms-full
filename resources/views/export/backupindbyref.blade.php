@php

$num=1;
header("Content-Disposition: attachment; filename=Rekap Data Reference ".$fullname." | Individual | ".$tanggalTitle.".xls");
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");

@endphp
<style>
    .top{
        vertical-align: top;
    }
    .middle{
        vertical-align: middle;
    }
    .text-align-center{
        text-align: center;
    }
    .text-align-right{
        text-align: right;
    }
    .bg-grey{
        background-color: #efefef;
    }
    .bg-yellow{
        background-color: #ffff00;
    }
    .bg-green{
        background-color: #34a853;
    }
    .green{
        color:green;
    }
    .red{
        color:red;
    }
</style>
<table cellspacing="0" border="0">
    <tr>
        <td colspan=12 height="17" valign=middle class="middle"><font size="4"><b>Rekap Data Reference CS/Marketing</b></font></td>
    </tr>
    <tr>
        <th colspan=12 height="17" valign=middle class="middle"></th>
    </tr>
    <tr>
        <td height="17">User</td>
        <td colspan=4 height="17">: <b>{{$username}}</b></td>
        <td height="17">Total Customer</td>
        <td colspan=6 height="17">: <b>{{$totalcustomer}}</b></td>
    </tr>
    <tr>
        <td height="17">Nama Lengkap</td>
        <td colspan=4 height="17">: <b>{{$fullname}}</b></td>
        <td height="17">Total Kilogram</td>
        <td colspan=6 height="17">: <b>{{round($totalweight,2)}}</b></td>
    </tr>
    <tr>
        <td height="17">Tipe Customer</td>
        <td colspan=4 height="17">: <b>Individual</b></td>
        <td height="17">Total CBM</td>
        <td colspan=6 height="17">: <b>{{round($totalcbm,2)}}</b></td>
    </tr>
    <tr>
        <th colspan=12 height="17" valign=middle class="middle"></th>
    </tr>
    <tr>
        <td colspan=12 height="17" valign=middle class="middle"><font size="3"><b>Data Shipment Periode {{$tanggalTitle}}</b></font></td>
    </tr>
</table>
<table cellspacing="0" border="1">
    <tr>
        <th>Tanggal</th>
        <th>Data Customer</th>
        <th>Created By</th>
        <th>Status Customer</th>
        <th>No.Invoice</th>
        <th>No.Doku</th>
        <th>No.Resi</th>
        <th>Status</th>
        <th>Berat(Kg)</th>
        <th>Item/Box</th>
        <th>CBM</th>
        <th>Berat CBM (Kgs)</th>
    </tr>

    @foreach($list as $l)
    <tr>
        <td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::dateFormatIndo($l->created_at,3)}}</td>
        <td valign=middle class="middle"><b>{{$l->cons_first_name." ".$l->cons_middle_name." ".$l->cons_last_name}}</b><br>'{{$l->cons_phone}}<br>{{$l->cons_address.", ".$l->cons_sub_district.", ".$l->cons_district.", ".$l->cons_city.", ".$l->cons_prov.", ".$l->cons_postal_code}}</td>
        <td align="center" valign=middle class="middle text-align-center"><b>{{$l->ordercreatedby}}</b><br>Created At :<br>{{App\Http\Controllers\Controller::dateFormatIndo($l->ordercreatedat,3)}}</td>
        <td align="center" valign=middle class="middle text-align-center"><?php echo $l->jumlahkirim>1 ? "<font color=red class='red'>OC</font>" : "<font color=green class='green'>NC</font>" ?></td>
        <td align="center" valign=middle class="middle text-align-center"><b>{{$l->mismass_invoice_id}}</b></td>
        <td align="center" valign=middle class="middle text-align-center">{{$l->doku_invoice_id}}</td>
        <td align="center" valign=middle class="middle text-align-center"><?php echo $l->forwarder_id!="VENDOR"?$l->forwarder_id:$l->forwarder_name ?><br>{{$l->shipping_number}}</td> 
        <td align="center" valign=middle class="middle text-align-center"><?php echo $l->invoice_status=="PAID" ? "<font color=green class='green'>PAID</font>" : "<font color=red class='red'>UNPAID</font>" ?></td>
        <td align="center" valign=middle class="middle text-align-center">{{round($l->weight, 2)}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{$l->item}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{round($l->cbm,2)}}</td>
        <td align="center" valign=middle class="middle text-align-center"><?php echo round($l->cbm,2)*100 ?></td>
    </tr>

    @php
        $num++;
    @endphp
    @endforeach

</table>    