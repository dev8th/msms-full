@php

$num=1;
header("Content-Disposition: attachment; filename=".$title.".xls");
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");

@endphp
    <style>
        .middle{
            vertical-align: middle;
        }
        .text-align-center{
            text-align: center;
        }
        .text-align-right{
            text-align: right;
        }
    </style>
	<table cellspacing="0" border="1">
		<tr>
			<th colspan=11 height="17" align="center" valign=middle class="middle text-align-center"><font size="5">{{$title}}</font></th>
		</tr>
		<tr>
			<th>No</th>
			<th>Order Number (Doku)</th>
			<th>No.Invoice</th>
			<th>No.Resi</th>
			<th>Admin</th>
			<th>Customer</th>
			<th>Alamat Lengkap Penerima</th>
			<th>Total Berat</th>
			<th>Total Item</th>
			<th>Total Diskon</th>
			<th>Total Biaya</th>
		</tr>
		@foreach($list as $d)
		@php
			$forwarder = $d->forwarder_id!="" ? ($d->forwarder_id=="PICK-UP"? "PICK-UP SENDIRI" : $d->forwarder_id." (".$d->forwarder_name.")") : "-";
			$shipping_number = $d->shipping_number!="" ? $d->shipping_number : "-";
			$shipping_created_at = $d->shipping_created_at!="0000-00-00 00:00:00" ? date("d-m-Y H:i:s",strtotime($d->shipping_created_at)) : "-"; 
		@endphp
		<tr>
			<td align="center" valign=middle class="middle text-align-center">{{$num}}</td>
			<td align="center" valign=middle class="middle text-align-center"><?php echo $d->doku_invoice_id!="" ? $d->doku_invoice_id : '-'?> <br> <?php echo $d->doku_invoice_id!="" ? date("d-m-Y H:i:s",strtotime($d->created_at)) : '-' ?></td>
			<td align="center" valign=middle class="middle text-align-center">{{$d->mismass_invoice_id}} <br> {{date("d-m-Y",strtotime($d->mismass_invoice_date))}}</td>
			<td align="center" valign=middle class="middle text-align-center">{{$forwarder}}<br>{{$shipping_number}}<br>{{$shipping_created_at}}</td>
			<td align="center" valign=middle class="middle text-align-center">{{$d->created_by}}</td>
			<td align="center" valign=middle class="middle text-align-center">{{$d->cons_first_name." ".$d->cons_middle_name." ".$d->cons_last_name}}<br>Reference : <?php echo $d->reference!=""?$d->reference:"-"?></td>
			<td align="center" valign=middle class="middle text-align-center">'{{$d->cons_phone}}<br>{{$d->cons_address.", ".$d->cons_sub_district.", ".$d->cons_district.", ".$d->cons_city.", ".$d->cons_prov.", ".$d->cons_postal_code}}</td>
			<td align="center" valign=middle class="middle text-align-center">{{number_format((float)App\Http\Controllers\Controller::hitungTotalBeratByInvoice($d->mismass_invoice_id), 2, '.', '')}} Kg</td>
			<td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::hitungTotalItemByInvoice($d->mismass_invoice_id)}}</td>
			<td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::rupiah(App\Http\Controllers\Controller::hitungTotalDiskonByInvoice($d->mismass_invoice_id))}}</td>
			<td align="right" valign=middle class="middle text-align-right">{{App\Http\Controllers\Controller::rupiah(App\Http\Controllers\Controller::hitungTotalSubTotalByInvoice($d->mismass_invoice_id))}}</td>
		</tr>
		<tr></tr>
		<tr></tr>
		@php
		$num++;
		@endphp
		@endforeach
	</table>