<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Service extends Model
{
    use HasFactory;

    protected $table = 'service_list';
    protected $primarykey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    protected $order = ['updated_at' => 'DESC'];
    protected $column_order = [
        'id',
        'updated_at',
        'name',
        'id',
        'location',
        'pricekg',
        'priceitem',
        'pricevol',
        'description',
        'id'
    ];

    public function getDTQuery(Request $request, $katakunci = '')
    {

        $column_search = [
            'service_list.id',
            'service_list.created_at',
            'service_list.created_by',
            'service_list.name',
            'service_list.warehouse_id',
            'warehouse_list.name',
            'warehouse_list.location',
            'service_list.pricekg',
            'service_list.priceitem',
            'service_list.pricevol',
            'service_list.description',
        ];

        $select = [
            "service_list.*",
            "warehouse_list.name as warename",
            "warehouse_list.location"
        ];

        $query = "service_list.id!=''";
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
        $servModel = new Service;
        $query = $servModel->getDTQuery($request, $katakunci);
        if ($request->input('length') != -1) {
            $offset = $request->input('start');
            $limit = $request->input('length');
            return Service::select($query['select'])
                ->join("warehouse_list", "warehouse_list.id", "=", "service_list.warehouse_id")
                ->whereRaw($query['where'])
                ->skip($offset)
                ->take($limit)
                ->orderBy($query['orderByA'], $query['orderByB'])
                ->get();
        }
        return Service::select($query['select'])
            ->join("warehouse_list", "warehouse_list.id", "=", "service_list.warehouse_id")
            ->whereRaw($query['where'])
            ->orderBy($query['orderByA'], $query['orderByB'])
            ->get();
    }

    public function countFiltered(Request $request, $katakunci)
    {
        $servModel = new Service;
        $query = $servModel->getDTQuery($request, $katakunci);
        return count(Service::select($query['select'])->join("warehouse_list", "warehouse_list.id", "=", "service_list.warehouse_id")->whereRaw($query['where'])->get());
    }

    public function countAll()
    {
        return Service::count();
    }
}
