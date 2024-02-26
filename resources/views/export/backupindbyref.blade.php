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
        <td colspan=10 height="17" valign=middle class="middle"><font size="4"><b>Rekap Data Reference CS/Marketing</b></font></td>
    </tr>
    <tr>
        <th colspan=10 height="17" valign=middle class="middle"></th>
    </tr>
    <tr>
        <td colspan=10 height="17" valign=middle class="middle">User : <b>{{$username}}</b></td>
    </tr>
    <tr>
        <td colspan=10 height="17" valign=middle class="middle">Nama Lengkap : <b>{{$fullname}}</b></td>
    </tr>
    <tr>
        <td colspan=10 height="17" valign=middle class="middle">Tipe Customer : <b>Individual</b></td>
    </tr>
    <tr>
        <th colspan=10 height="17" valign=middle class="middle"></th>
    </tr>
    <tr>
        <td colspan=10 height="17" valign=middle class="middle"><font size="3"><b>Data Shipment Periode {{$tanggalTitle}}</b></font></td>
    </tr>
</table>
<table cellspacing="0" border="1">
    <tr>
        <th>Tanggal</th>
        <th>Data Customer</th>
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