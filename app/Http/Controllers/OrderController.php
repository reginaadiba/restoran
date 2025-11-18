<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::select(
            'id',
            'customer_name',
            'table_no',
            'order_date',
            'order_time',
            'status',
            'total',
            'waitress_id',
            'cashier_id'
            )
            ->with([
                'waitress:id,name',
                'cashier:id,name'
            ])
            ->get();

        return response(['data' => $orders]);
    }

    public function show($id)
    {
        $order = Order::findOrFail($id);
        $order->loadMissing([
            'orderDetail:order_id,price,item_id,qty',
            'orderDetail.item:name,id',
            'waitress:id,name',
            'cashier:id,name'
        ]);

        return response(['data' => $order]);
    }

    public function setAsDone($id)
    {
        $order = Order::findOrFail($id);
        if ($order->status != 'ordered') {
            return response('Order cannot set to DONE because the status is not ORDERED', 403);
        }

        $order->status = 'done';
        $order->save();
        $order->loadMissing([
            'orderDetail:order_id,price,item_id,qty',
            'orderDetail.item:name,id',
            'waitress:id,name',
            'cashier:id,name'
        ]);
        return response(['data' => $order]);
    }

    public function payment($id)
    {
        $order = Order::findOrFail($id);
        if ($order->status != 'done') {
            return response('Order cannot set to PAID because the status is not DONE', 403);
        }

        $order->status = 'paid';
        $order->save();
        $order->loadMissing([
            'orderDetail:order_id,price,item_id,qty',
            'orderDetail.item:name,id',
            'waitress:id,name',
            'cashier:id,name'
        ]);
        return response(['data' => $order]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|max:100',
            'table_no' => 'required|max:5',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->only(['customer_name', 'table_no']);
            $data['order_date'] = date('Y-m-d');
            $data['order_time'] = date('H:i:s');
            $data['status'] = 'ordered';
            $data['total'] = 0;
            $data['waitress_id'] = auth()->user()->id;

            $order = Order::create($data);

            $data['items'] = $request->items;

            collect($data['items'])->map(function($item) use($order) {
                $menu = Item::where('id', $item['id'])->first();
                OrderDetail::create([
                    'order_id' => $order->id,
                    'item_id' => $item['id'],
                    'price' => $menu->price,
                    'qty' => $item['qty']
                ]);
            });

            $order->total = $order->sumOrderPrice();
            $order->save();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response($th);
        }

        return response(['data' => $order]);
    }
}
