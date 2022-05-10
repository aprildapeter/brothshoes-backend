<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit', 6);
        $status = $request->input('status');

        if($id)
        {
            $transaction = Transaction::with(['items.product'])->find($id);

            if($transaction)
                return ResponseFormatter::success(
                    $transaction,
                    'Data transaksi berhasil diambil'
                );
            else
                return ResponseFormatter::error(
                    null,
                    'Data transaksi tidak ada',
                    404
                );
        }

        $transaction = Transaction::with(['items.product'])->where('users_id', Auth::user()->id);

        if($status)
            $transaction->where('status', $status);

        return ResponseFormatter::success(
            $transaction->paginate($limit),
            'Data list transaksi berhasil diambil'
        );
    }
    public function update(Request $request)
    {
        $id = $request->input('id');
        
        $data = $request->all();
        if($id)
        {
        $transaction = Transaction::with(['items.product'])->find($id);
        $transaction->update($data);

        return ResponseFormatter::success($transaction, 'Profile Updated');
        }
    }
    public function alltransaction(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit', 6);
        $status = $request->input('status');

        if($id)
        {
            $transaction = Transaction::with(['items.product'])->find($id);

            if($transaction)
                return ResponseFormatter::success(
                    $transaction,
                    'Data transaksi berhasil diambil'
                );
            else
                return ResponseFormatter::error(
                    null,
                    'Data transaksi tidak ada',
                    404
                );
        }

        $transaction = Transaction::query();

        if($id)
            $transaction->where('id', 'like', '%' . $id . '%');
        if($status)
            $transaction->where('status', 'like', '%' . $status . '%');

        

        return ResponseFormatter::success(
            $transaction->paginate($limit),
            'Data list kategori produk berhasil diambil'
        );
    
        // $id = $request->input('id');
        // $limit = $request->input('limit', 6);
        // $status = $request->input('status');

        // if($id)
        // {
        //     $transaction = Transaction::with(['items.product'])->find($id);

        //     if($transaction)
        //         return ResponseFormatter::success(
        //             $transaction,
        //             'Data transaksi berhasil diambil'
        //         );
        //     else
        //         return ResponseFormatter::error(
        //             null,
        //             'Data transaksi tidak ada',
        //             404
        //         );
        // }

        // $transaction = Transaction::with(['items.product'])->where('users_id', Auth::user()->id);

        // if($status)
        //     $transaction->where('status', $status);

        // return ResponseFormatter::success(
        //     $transaction->paginate($limit),
        //     'Data list transaksi berhasil diambil'
        // );
    }

    
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'exists:products,id',
            'categories_service' => 'required|in:ambilsendiri,pickupdelivery',
            'time_pickup_delivery' => 'required',
            'address' => 'required',
            'patokan' => 'required',
            'detail_lokasi' => 'required',
            'total_price' => 'required',
            'shipping_price' => 'required',
            'status' => 'required|in:pending,success,progress,canceled,failed,shipping,shipped',
        ]);

        $transaction = Transaction::create([
            'users_id' => Auth::user()->id,
            'categories_service' => $request->categories_service,
            'time_pickup_delivery' => $request->time_pickup_delivery,
            'address' => $request->address,
            'patokan' => $request->patokan,
            'detail_lokasi' => $request->detail_lokasi,
            'total_price' => $request->total_price,
            'shipping_price' => $request->shipping_price,
            'status' => $request->status
        ]);
        
        foreach ($request->items as $product) {
            TransactionItem::create([
                'users_id' => Auth::user()->id,
                'products_id' => $product['id'],
                'transactions_id' => $transaction->id,
                'quantity' => $product['quantity']
            ]);
        }

        return ResponseFormatter::success($transaction->load('items.product'), 'Transaksi berhasil');
    }
}
