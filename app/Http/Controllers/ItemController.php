<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::all();

        return response(['data' => $items]);
    }

    public function show($id)
    {
        $item = Item::findOrFail($id);
        return response(['data' => $item]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'category' => 'required',
            'price' => 'required|integer',
            'image_file' => 'nullable|mimes:jpg,png',
        ]);

        if ($request->file('image_file')) {
            $file = $request->file('image_file');
            $fileName = $file->getClientOriginalName();
            $newName = Carbon::now()->timestamp.'_'.$fileName;
            Storage::putFileAs('items', $file, $newName);

            $request['image'] = $newName;
        }

        $item = Item::create($request->all());

        return response(['data' => $item]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255',
            'category' => 'required',
            'price' => 'required|integer',
            'image_file' => 'nullable|mimes:jpg,png',
        ]);

        if ($request->file('image_file')) {
            $file = $request->file('image_file');
            $fileName = $file->getClientOriginalName();
            $newName = Carbon::now()->timestamp.'_'.$fileName;
            Storage::putFileAs('items', $file, $newName);

            $request['image'] = $newName;
        }

        $item = Item::findOrFail($id);
        $item->update($request->all());
        return response(['data' => $item]);
    }
}
