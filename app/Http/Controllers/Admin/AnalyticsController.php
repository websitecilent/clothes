<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\OrderDetails;
use App\Models\Orders;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index() {
        // tổng doanh thu
        // $revenue = Orders::where('order_status', 3)->orWhere('payment_method', 1)->sum('order_total');
        $revenue = OrderDetails::sum(DB::raw('price * quantity'));

        $t = Product::count('id');  
        // dd($t);

        $totalProdByCate = DB::table('products')
        ->join('categories', 'products.cate_id', '=', 'categories.id')
        ->select('categories.cate_name as cName', 'categories.cate_image as cImg', DB::raw('count(cate_id) as total'))
        ->groupBy('cate_id')
        ->orderByDesc('total')
        ->get();
        // dd($totalProdByCate);

        $topProdHighest = DB::table('order_details')
        ->join('products', 'order_details.prod_id', '=', 'products.id')
        ->select('prod_id', 'prod_name as pName', 'prod_image as pImg', 'color', 'size', 'price', DB::raw('sum(quantity) as total'))
        ->groupBy('prod_id', 'color')
        ->orderBy('total', 'desc')
        ->limit(5)
        ->get();
        // dd($topProdHighest);

        $topProdLowest = DB::table('order_details')
        ->join('products', 'order_details.prod_id', '=', 'products.id')
        ->select('prod_id', 'prod_name as pName', 'prod_image as pImg', 'color', 'size', 'price', DB::raw('sum(quantity) as total'))
        ->groupBy('prod_id', 'color')
        ->orderBy('total', 'asc')
        ->limit(5)
        ->get();

        // thống kê kho hàng

        $dataOrderStatus = Orders::select(['id', 'order_status'])->get();
        // dd($dataOrderStatus);
        return view('admin.analytics.stats', compact('revenue', 'topProdHighest', 'topProdLowest', 'totalProdByCate', 't'));
    }

    public function googleChart() {
        $dataOrderStatus = Orders::select(['id, order_status'])->get();
        foreach ($dataOrderStatus as $key => $value) {
            # code...
        }
        
    }

    public function revenue() { 
        
        $totalView = Product::sum('prod_view');
        $totalProd = OrderDetails::sum('quantity');
        $revenueByWeek = Orders::select('*')->whereBetween('created_at', [Carbon::now()->subWeek(), Carbon::now()])->get();
        $revenueByMonth = Orders::select('*')->whereBetween('created_at', [Carbon::now()->subMonth(), Carbon::now()])->get();
        $revenueByDay = Orders::select('*')->whereBetween('created_at', [Carbon::now()->subDay(), Carbon::now()])->get();
        $date = Carbon::now();
        $highest_resolved = DB::select(DB::raw('SELECT prod_id, SUM(quantity) as total, price FROM order_details GROUP BY prod_id ORDER BY total DESC LIMIT 7'));
        $lowest_resolved = DB::select(DB::raw('SELECT prod_id, SUM(quantity) as total, price FROM order_details GROUP BY prod_id ORDER BY total ASC LIMIT 7'));
        $resolved = Orders::join('order_details','orders.id', '=', 'order_details.order_id')->select('order_details.prod_id as id')->pluck('id');
        $no_resolved = Product::whereNotIn('id', $resolved)->get();
        return view('admin.analytics.stats', compact('revenue', 'totalView', 'totalProd', 'revenueByWeek', 'revenueByMonth', 'revenueByDay', 'date', 'highest_resolved', 'lowest_resolved', 'resolved', 'no_resolved'));
    }

    public function revenueByWeek() {
        $type = 'month';
        $totalView = Product::select('*')->whereBetween('updated_at', [Carbon::now()->subWeek(), Carbon::now()])->sum();
        return view('admin.analytics.stats');
    }

    public function revenueByMonth() {
        return view('admin.analytics.stats');
    }


}
