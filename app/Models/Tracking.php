<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Tracking extends Model
{
    use HasFactory;

    protected $table = 'data_list';
    protected $primarykey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    protected $order = ['data_list.shipping_updated_at' => 'DESC'];
    protected $column_order = [
        'id',
        'mismass_invoice_id',
        'mismass_invoice_date',
        'doku_invoice_id',
        'created_by',
        'created_at',
        'weight',
        'item',
        'sub_total',
        'doku_link',
        'bank_name',
        'bank_account_name',
        'bank_account_id',
    ];

    public function getDTQuery(Request $request, $katakunci = '', $filter)
    {

        $trackingModel = new Tracking;
        $column_search = $trackingModel->columnSearch($filter[4],$filter[5]);
        $select = "data_list.*,SUM(weight) as totalWeight,SUM(cbm) as totalCbm,SUM(item) as totalItem,SUM(discount) as totalDisc,SUM(sub_total) as totalPrice,cust_type_list.name as custTypeName";
        $filterTanggal = $filter[0] != null && $filter[1] != null ? "AND data_list.mismass_invoice_date BETWEEN '" . date("Y-m-d", strtotime($filter[0])) . " 00:00:00' AND '" . date("Y-m-d", strtotime($filter[1])) . " 23:59:59'" : "";
        $filterWarehouse = $filter[2] != null && $filter[2] != "" ? "AND data_list.warehouse_id='" . $filter[2] . "'" : "";
        $filterService = $filter[3] != null && $filter[3] != "" ? "AND data_list.service_id='" . $filter[3] . "'" : "";
        $query = $filter[4] == "IND" ? "data_list.cust_type_id='IND' AND shipping_number!='' $filterWarehouse $filterService $filterTanggal" : "data_list.cust_type_id='COR' AND shipping_number!='' $filterWarehouse $filterService $filterTanggal";
        $groupBy = "data_list.mismass_invoice_id";
        if($filter[5]!=""){
            $query = "data_list.mismass_order_id='$filter[5]'";
            $groupBy = "data_list.shipping_number";
        }

        $where = $query;
        
        if (!empty($katakunci)) {
            $where = "";
            for ($i = 0; $i <= count($column_search) - 1; $i++) {
                if ($i < count($column_search) - 1) {
                    $where .= $query . "AND $column_search[$i] LIKE '%$katakunci%' OR ";
                } else {
                    $where .= $query . "AND $column_search[$i] LIKE '%$katakunci%'";
                }
            }
        }

        if ($request->input('order')) {
            $orderByA = $this->column_order[$request->input('order')['0']['column']];
            $orderByB = $request->input('order')['0']['dir'];
        } else if (isset($this->order)) {
            $orderByA = key($this->order);
            $orderByB = $this->order[key($this->order)];
        }

        $data = [
            'select' => $select,
            'where' => $where,
            'orderByA' => $orderByA,
            'orderByB' => $orderByB,
            'groupBy' => $groupBy
        ];

        return $data;
    }

    public function getDT(Request $request, $katakunci, $filter)
    {
        $query = Tracking::getDTQuery($request, $katakunci, $filter);

        if ($request->input('length') != -1) {
            $offset = $request->input('start');
            $limit = $request->input('length');
            return Tracking::selectRaw($query['select'])
                ->join("cust_type_list", "cust_type_list.id", "=", "data_list.cust_type_id")
                ->whereRaw($query['where'])
                ->groupBy($query['groupBy'])
                ->skip($offset)
                ->take($limit)
                ->orderBy($query['orderByA'], $query['orderByB'])
                ->get();
        }
        return Tracking::selectRaw($query['select'])
            ->join("cust_type_list", "cust_type_list.id", "=", "data_list.cust_type_id")
            ->whereRaw($query['where'])
            ->groupBy($query['groupBy'])
            ->orderBy($query['orderByA'], $query['orderByB'])
            ->get();

    }

    public function countFiltered(Request $request, $katakunci, $filter)
    {
        $query = Tracking::getDTQuery($request, $katakunci, $filter);
        return count(Tracking::selectRaw($query['select'])
            ->join("cust_type_list", "cust_type_list.id", "=", "data_list.cust_type_id")
            ->whereRaw($query['where'])
            ->groupBy($query['groupBy'])
            ->get());
    }

    public function countAll(Request $request, $katakunci, $filter)
    {
        $query = Tracking::getDTQuery($request, $katakunci, $filter);
        return Tracking::groupBy($query['groupBy'])->count();
    }

    public function columnSearch($typeId,$mismassOrderId){

        if($mismassOrderId==""){
            $column_search = [
                'data_list.mismass_invoice_id',
                'data_list.mismass_invoice_date',
                'data_list.doku_invoice_id',
                'data_list.doku_link',
                'data_list.created_at',
                'data_list.created_by',
                'data_list.forwarder_id',
                'data_list.forwarder_name',
                'data_list.shipping_number',
                'data_list.bank_name',
                'data_list.bank_account_id',
                'data_list.bank_account_name',
                'data_list.sender_first_name',
                'data_list.sender_middle_name',
                'data_list.sender_last_name',
                'data_list.sender_address',
                'data_list.sender_sub_district',
                'data_list.sender_district',
                'data_list.sender_city',
                'data_list.sender_prov',
                'data_list.sender_postal_code',
                'data_list.sender_phone',
                'data_list.cons_first_name',
                'data_list.cons_middle_name',
                'data_list.cons_last_name',
                'data_list.cons_address',
                'data_list.cons_sub_district',
                'data_list.cons_district',
                'data_list.cons_city',
                'data_list.cons_prov',
                'data_list.cons_postal_code',
                'data_list.cons_phone'
            ];
        }else{
            $column_search = [
                'data_list.forwarder_id',
                'data_list.forwarder_name',
                'data_list.shipping_number',
                'data_list.cons_first_name',
                'data_list.cons_middle_name',
                'data_list.cons_last_name',
                'data_list.cons_address',
                'data_list.cons_sub_district',
                'data_list.cons_district',
                'data_list.cons_city',
                'data_list.cons_prov',
                'data_list.cons_postal_code',
                'data_list.weight',
                'data_list.item'
            ];
        }

        return $column_search;
    }
}
