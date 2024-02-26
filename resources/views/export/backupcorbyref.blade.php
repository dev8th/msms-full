@php

header("Content-Disposition: attachment; filename=Rekap Data Reference {{$username}} | Individual | {{$tanggalTitle}}.xls");
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
$invoiceOld="";

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
        <td colspan=9 height="17" valign=middle class="middle"><font size="4"><b>Rekap Data Reference CS/Marketing</b></font></td>
    </tr>
    <tr>
        <th colspan=9 height="17" valign=middle class="middle"></th>
    </tr>
    <tr>
        <td colspan=9 height="17" valign=middle class="middle">User : <b>{{$username}}</b></td>
    </tr>
    <tr>
        <td colspan=9 height="17" valign=middle class="middle">Nama Lengkap : <b>{{$fullname}}</b></td>
    </tr>
    <tr>
        <td colspan=9 height="17" valign=middle class="middle">Tipe Customer : <b>Corporate</b></td>
    </tr>
    <tr>
        <th colspan=9 height="17" valign=middle class="middle"></th>
    </tr>
    <tr>
        <td colspan=9 height="17" valign=middle class="middle"><font size="3"><b>Data Shipment Periode {{$tanggalTitle}}</b></font></td>
    </tr>
</table>
<table cellspacing="0" border="1">
    <tr>
        <th>Tanggal</th>
        <th>Data Client</th>
        <th>No.Invoice</th>
        <th>No.Doku</th>
        <th>Status</th>
        <th>Total Berat (Kg)</th>
        <th>Total Item</th>
        <th>Total CBM</th>
        <th>Total Berat CBM (Kgs)</th>
    </tr>

    @foreach($list as $l)
    @if($invoiceOld!=$l->mismass_invoice_id)
    <tr>
        <td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::dateFormatIndo($l->created_at,3)}}</td>
        <td valign=middle class="middle"><b>{{$l->sender_first_name." ".$l->sender_middle_name." ".$l->sender_last_name}}</b><br>'{{$l->sender_phone}}<br>{{$l->sender_address.", ".$l->sender_sub_district.", ".$l->sender_district.", ".$l->sender_city.", ".$l->sender_prov.", ".$l->sender_postal_code}}</td>
        <td align="center" valign=middle class="middle text-align-center"><b>{{$l->mismass_invoice_id}}</b></td>
        <td align="center" valign=middle class="middle text-align-center">{{$l->doku_invoice_id}}</td>
        <td align="center" valign=middle class="middle text-align-center"><?php echo $l->invoice_status=="PAID" ? "<font color=green class='green'>PAID</font>" : "<font color=red class='red'>UNPAID</font>" ?></td>
        <td align="center" valign=middle class="middle text-align-center">{{round(App\Http\Controllers\Controller::hitungTotalBeratByInvoice($l->mismass_invoice_id),2)}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::hitungTotalItemByInvoice($l->mismass_invoice_id)}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{round(App\Http\Controllers\Controller::hitungTotalCbmByInvoice($l->mismass_invoice_id),2)}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{round(App\Http\Controllers\Controller::hitungTotalCbmByInvoice($l->mismass_invoice_id),2)*100}}</td>
    </tr>
    <tr>
        <td colspan=9 height="17" valign=middle class="middle"><font size="2"><b>Rincian Detil Penerima | Tanggal Resi : {{App\Http\Controllers\Controller::dateFormatIndo($l->shipping_created_at,1)}}</b></font></td>
    </tr>
    <tr>
        <th>No</th>
        <th>Data Customer</th>
        <th colspan=3>No.Resi</th>
        <th>Berat (Kg)</th>
        <th>Item</th>
        <th>CBM</th>
        <th>Berat CBM (Kgs)</th>
    </tr>
    @php
        $num=1;
    @endphp
    @foreach($list as $m)
        @if($l->mismass_invoice_id==$m->mismass_invoice_id)
            <tr>
                <td align="center" valign=middle class="middle text-align-center">{{$num}}</td>
                <td valign=middle class="middle"><b>{{$m->cons_first_name." ".$m->cons_middle_name." ".$m->cons_last_name}}</b><br>'{{$m->cons_phone}}<br>{{$m->cons_address.", ".$m->cons_sub_district.", ".$m->cons_district.", ".$m->cons_city.", ".$m->cons_prov.", ".$m->cons_postal_code}}</td>
                <td colspan=3 align="center" valign=middle class="middle text-align-center"><?php echo $l->forwarder_id!="VENDOR"?$l->forwarder_id:$l->forwarder_name ?><br>{{$l->shipping_number}}</td>
                <td align="center" valign=middle class="middle text-align-center">{{round($m->weight,2)}}</td>
                <td align="center" valign=middle class="middle text-align-center">{{$m->item}}</td>
                <td align="center" valign=middle class="middle text-align-center">{{round($m->cbm,2)}}</td>
                <td align="center" valign=middle class="middle text-align-center"><?php echo round($m->cbm,2)*100 ?></td>
            </tr>
        @php
            $num++;
        @endphp
        @endif
    @endforeach
    <tr>
        <td colspan=9 height=17 bgcolor="black"></td>
    </tr>
    @php
        $invoiceOld=$l->mismass_invoice_id;
    @endphp
    @endif
    @endforeach

</table>    