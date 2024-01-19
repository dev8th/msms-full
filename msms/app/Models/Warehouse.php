<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Warehouse extends Model
{
    use HasFactory;

    protected $table = 'warehouse_list';
    protected $primarykey = 'id';
    public $incrementing = false;
    public $timestamps = true;

    protected $order = ['warehouse_list.updated_at' => 'DESC'];
    protected $column_order = [
        'id',
        'updated_at',
        'id',
        'location',
        'name',
        'name',
        'name',
        'name',
        'name',
        'id',
    ];

    public function getDTQuery(Request $request, $katakunci = '')
    {

        $column_search = [
            'id',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
            'name',
            'location',
            'description',
        ];

        $select = "warehouse_list.*";

        $query = "warehouse_list.id!=''";
        $where = "";

        if (!empty($katakunci)) {
            for ($i = 0; $i <= count($column_search) - 1; $i++) {
                if ($i < count($column_search) - 1) {
                    $where .= $query . "AND $column_search[$i] LIKE '%$katakunci%' OR ";
                } else {
                    $where .= $query . "AND $column_search[$i] LIKE '%$katakunci%'";
                }
            }
        } else {
            $where = $query;
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
            'orderByB' => $orderByB
        ];

        return $data;
    }

    public function getDT(Request $request, $katakunci)
    {

        $wareModel = new Warehouse;
        $query = $wareModel->getDTQuery($request, $katakunci);
        if ($request->input('length') != -1) {
            $offset = $request->input('start');
            $limit = $request->input('length');
            // return Warehouse::whereRaw($query['where'])->skip($offset)->take($limit)->orderBy($query['orderByA'], $query['orderByB'])->get();
            return Warehouse::selectRaw($query['select'])
                // ->join('service_list','warehouse_list.id','=','service_list.warehouse_id')
                // ->join('data_list','data_list.warehouse_id','=','warehouse_list.id')
                // ->join('order_list','data_list.mismass_order_id','=','order_list.id')
                ->groupBy('warehouse_list.id')
                ->whereRaw($query['where'])
                ->skip($offset)
                ->take($limit)
                ->orderBy($query['orderByA'], $query['orderByB'])
                ->get();
        }
        // return Warehouse::whereRaw($query['where'])->orderBy($query['orderByA'], $query['orderByB'])->get();
        return Warehouse::selectRaw($query['select'])
                // ->join('service_list','warehouse_list.id','=','service_list.warehouse_id')
                // ->join('data_list','data_list.warehouse_id','=','warehouse_list.id')
                // ->join('order_list','data_list.mismass_order_id','=','order_list.id')
                ->groupBy('warehouse_list.id')
                ->whereRaw($query['where'])
                ->orderBy($query['orderByA'], $query['orderByB'])
                ->get();
    }

    public function countFiltered(Request $request, $katakunci)
    {
        $wareModel = new Warehouse;
        $query = $wareModel->getDTQuery($request, $katakunci);
        return count(Warehouse::selectRaw($query['select'])
        // ->join('service_list','warehouse_list.id','=','service_list.warehouse_id')
        // ->join('data_list','data_list.warehouse_id','=','warehouse_list.id')
        // ->join('order_list','data_list.mismass_order_id','=','order_list.id')
        ->groupBy('warehouse_list.id')
        ->whereRaw($query['where'])
        ->get());
    }

    public function countAll()
    {
        return Warehouse::count();
    }
}
