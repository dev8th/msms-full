@php

$num=1;
header("Content-Disposition: attachment; filename=".$title.".xls");
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
	<table cellspacing="0" border="1">
		<tr>
			<th colspan=10 height="17" align="center" valign=middle class="middle text-align-center"><font size="5">{{$title}}</font></th>
		</tr>
		<tr>
			<th>No</th>
			<th>Order Number (Doku)</th>
			<th>No.Invoice</th>
			<th>Admin</th>
			<th>Klien</th>
			<th>Alamat Pengirim</th>
			<th>Total Item</th>
			<th>Total Berat (KG)</th>
			<th>Total Diskon</th>
			<th>Total Pembayaran</th>
		</tr>
        @foreach($list as $l)
		<tr>
			<td align="center" valign=middle class="middle text-align-center">{{$num}}</td>
			<td align="center" valign=middle class="middle text-align-center">{{$l->doku_invoice_id}}<br>{{date("d-m-Y H:i:s",strtotime($l->created_at))}}</td>
			<td align="center" valign=middle class="middle text-align-center">{{$l->mismass_invoice_id}}<br>{{date("d-m-Y",strtotime($l->mismass_invoice_date))}}</td>
			<td align="center" valign=middle class="middle text-align-center">{{$l->created_by}}</td>
			<td align="center" valign=middle class="middle text-align-center">{{$l->sender_first_name." ".$l->sender_middle_name." ".$l->sender_last_name}}<br>Reference : <?php echo $l->reference!=""?$l->reference:"-"?></td>
			<td align="center" valign=middle class="middle text-align-center">'{{$l->sender_phone}}<br>{{$l->sender_address}}</td>
			<td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::hitungItemWare($l->warehouse_id,2,$l->mismass_invoice_id)}}</td>
			<td align="center" valign=middle class="middle text-align-center">{{round(App\Http\Controllers\Controller::hitungBeratWare($l->warehouse_id,2,$l->mismass_invoice_id),2)}}</td>
            <td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::rupiah(App\Http\Controllers\Controller::hitungDiskonWare($l->warehouse_id,2,$l->mismass_invoice_id))}}</td>
			<td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::rupiah(App\Http\Controllers\Controller::hitungLabaWare($l->warehouse_id,2,$l->mismass_invoice_id))}}</td>
		</tr>
        <tr>
            <th bgcolor="#efefef" class="bg-grey" colspan="10" >Rincian Detil Penerima | Tanggal Resi : {{App\Http\Controllers\Controller::dateFormatIndo($l->shipping_created_at,1)}}</th>
        </tr>
        <tr>
            <th>No</th>
			<th>No.Resi</th>
			<th>Penerima</th>
			<th colspan="5">Alamat Penerima</th>
			<th>Item</th>
			<th>Berat</th>
        </tr>
        @php
            $num2=1;
        @endphp
        @foreach($list2 as $dl)
            @if($dl->mismass_invoice_id==$dl->mismass_invoice_id)
            @php
                $forwarder = $dl->forwarder_id!="" ? ($dl->forwarder_id=="PICK-UP"?"PICK-UP SENDIRI":$dl->forwarder_name) : "-" ;
            @endphp
            <tr>
                <td align="center" valign=middle class="middle text-align-center">{{$num2}}</td>
                <td align="center" valign=middle class="middle text-align-center">{{$forwarder}}<br>{{$dl->shipping_number}}<br>{{date("d-m-Y H:i:s",strtotime($dl->shipping_created_at))}}</td>
                <td align="center" valign=middle class="middle text-align-center">{{$dl->cons_first_name." ".$dl->cons_middle_name." ".$dl->cons_last_name}}</td>
                <td colspan="5" align="center" valign=middle class="middle text-align-center">'{{$dl->cons_phone}}<br>{{$dl->cons_address.", ".$dl->cons_sub_district.", ".$dl->cons_district.", ".$dl->cons_city.", ".$dl->cons_prov.", ".$dl->cons_postal_code}}</td>
                <td align="center" valign=middle class="middle text-align-center">{{$dl->item}}</td>
                <td align="center" valign=middle class="middle text-align-center">{{round($dl->weight,2)}} Kg</td>
            </tr>
            @php
                $num2++;
            @endphp
            @endif
        @endforeach

        <tr>
            <td bgcolor="#ffff00" class="bg-yellow" colspan="9"></td>
        </tr>
        @php
            $num++;
        @endphp
        @endforeach
	</table>