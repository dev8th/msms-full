<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'cust_list';
    protected $primarykey = 'id';
    public $incrementing = true;
    protected $keyType = 'string';
    public $timestamps = true;
    protected $guarded = [];

    protected $order = ['cust_list.updated_at' => 'DESC'];
    protected $column_order = [
        'id',
        'updated_at',
        'first_name',
        'address',
        'email',
        'phone',
        'city',
        'prov',
        'id',
        'reference',
    ];

    public function getDTQuery(Request $request, $katakunci = '', $custTypeId)
    {

        $column_search = [
            'cust_list.id',
            'cust_list.created_at',
            'cust_list.created_by',
            'cust_list.first_name',
            'cust_list.middle_name',
            'cust_list.last_name',
            'cust_list.address',
            'cust_list.sub_district',
            'cust_list.district',
            'cust_list.city',
            'cust_list.prov',
            'cust_list.postal_code',
            'cust_list.email',
            'cust_list.phone',
            'cust_list.reference',
        ];

        // $select = "cust_list.*,
        //             count(DISTINCT(CASE WHEN data_list.invoice_status='PAID' THEN data_list.shipping_number ELSE 0 END)) as totalResi,
        //             count(DISTINCT(CASE WHEN data_list.invoice_status='PAID' THEN order_list.invoice_id ELSE 0 END)) as totalInvoice,
        //             sum(CASE WHEN data_list.invoice_status='PAID' THEN data_list.weight ELSE 0 END) as totalWeight,
        //             sum(CASE WHEN data_list.invoice_status='PAID' THEN data_list.item ELSE 0 END) as totalItem,
        //             sum(CASE WHEN data_list.invoice_status='PAID' THEN data_list.sub_total ELSE 0 END) as totalHarga";
        $select = "cust_list.*";

        $query = $custTypeId == "IND" ? "cust_list.cust_type_id='IND'" : "cust_list.cust_type_id='COR'";
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

    public function getDT(Request $request, $katakunci, $custTypeId)
    {
        $custModel = new Customer;
        $query = $custModel->getDTQuery($request, $katakunci, $custTypeId);
        if ($request->input('length') != -1) {
            $offset = $request->input('start');
            $limit = $request->input('length');
            return Customer::selectRaw($query['select'])
                // ->join('order_list','cust_list.id','=','order_list.cust_id')
                // ->join('data_list','order_list.id','=','data_list.mismass_order_id')
                // ->groupBy('order_list.cust_id')
                // ->groupBy('cust_list.id')
                ->whereRaw($query['where'])
                ->skip($offset)
                ->take($limit)
                ->orderBy($query['orderByA'], $query['orderByB'])
                ->get();
        }
        return Customer::selectRaw($query['select'])
            // ->join('order_list','cust_list.id','=','order_list.cust_id')
            // ->join('data_list','order_list.id','=','data_list.mismass_order_id')
            // ->groupBy('order_list.cust_id')
            // ->groupBy('cust_list.id')
            ->whereRaw($query['where'])
            ->orderBy($query['orderByA'], $query['orderByB'])
            ->get();
    }

    public function countFiltered(Request $request, $katakunci, $custTypeId)
    {
        $custModel = new Customer;
        $query = $custModel->getDTQuery($request, $katakunci, $custTypeId);
        return count(Customer::selectRaw($query['select'])
            // ->join('order_list','cust_list.id','=','order_list.cust_id')
            // ->join('data_list','order_list.id','=','data_list.mismass_order_id')
            // ->groupBy('cust_list.id')
            ->whereRaw($query['where'])
            ->get());
    }

    public function countAll()
    {
        return Customer::count();
    }
}
