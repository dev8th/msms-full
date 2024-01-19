<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Order extends Model
{
    use HasFactory;

    protected $table = 'order_list';
    protected $primarykey = 'id';
    public $incrementing = true;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $order = ['updated_at' => 'DESC'];
    protected $column_order = [
        'id',
        'updated_at',
        'first_name',
        'address',
        'order_status_id',
        'id'
    ];

    public function getDTQuery(Request $request, $katakunci = '', $filter)
    {

        $column_search = [
            'order_list.id',
            'order_list.order_status_id',
            'order_list.created_at',
            'order_list.created_by',
            'order_list.first_name',
            'order_list.middle_name',
            'order_list.last_name',
            'order_list.address',
            'order_list.city',
            'order_list.prov',
            'order_list.phone',
            'order_list.email',
            'order_list.postal_code',
        ];

        $select = "order_list.*";
        $filterTanggal = $filter['filterTanggal'] != null || $filter['filterTanggal'] != "" ? "AND created_at LIKE '%" . date("Y-m-d", strtotime($filter['filterTanggal'])) . "%'" : "";
        $query = $filter['custTypeId'] == "IND" ? "cust_type_id='IND' AND invoice_id='' $filterTanggal" : "cust_type_id='COR' AND invoice_id='' $filterTanggal";
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

        $queries = [
            'select' => $select,
            'where' => $where,
            'orderByA' => $orderByA,
            'orderByB' => $orderByB
        ];

        return $queries;
    }

    public function getDT(Request $request, $katakunci, $filter)
    {
        $query = Order::getDTQuery($request, $katakunci, $filter);
        if ($request->input('length') != -1) {
            $offset = $request->input('start');
            $limit = $request->input('length');
            return Order::select($query['select'])
                ->whereRaw($query['where'])
                ->skip($offset)
                ->take($limit)
                ->orderBy($query['orderByA'], $query['orderByB'])
                ->get();
            
        }
        return Order::select($query['select'])
            ->whereRaw($query['where'])
            ->orderBy($query['orderByA'], $query['orderByB'])
            ->get();
    }

    public function countFiltered(Request $request, $katakunci, $filter)
    {
        $data = Order::getDTQuery($request, $katakunci, $filter);
        return Order::whereRaw($data['where'])->count();
    }

    public function countAll()
    {
        return Order::count();
    }
}
