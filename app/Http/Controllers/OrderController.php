<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
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
                $menu = Item::where('id', $item)->first();
                OrderDetail::create([
                    'order_id' => $order->id,
                    'item_id' => $item,
                    'price' => $menu->price
                ]);
            });

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response($th);
        }

        return response(['data' => $order]);
    }
}
