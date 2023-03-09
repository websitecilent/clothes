<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\DetailInfo;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index() {
        // $product = Product::all(); 
        $product = Product::orderBy('id', 'desc')->paginate(5);
        return view('admin.product.list-prod')->with('product', $product);
    }

    public function create() {
        $category = Category::all();
        $brand = Brand::all();
        return view('admin.product.add-prod')->with('category', $category)->with('brand', $brand);
    }

    public function store(Request $request) {
        // validate
        // $request->validate([
        //     'cate_id' => 'required',
        //     'brand_id' => 'required',
        //     'prod_name' => 'required',
        //     'prod_slug' => 'required',
        //     'prod_original_price' => 'required|numeric|min:0',
        //     'prod_selling_price' => 'required|numeric|min:0',
        //     'prod_small_description' => 'required',
        //     'prod_detail_description' => 'required',
        //     'prod_quantity' => 'required|numeric|min:1',
        //     'prod_size' => 'required',
        //     'prod_image' => 'required',
        // ], [
        //     'cate_id.required' => 'Không được để trống *',
        //     'brand_id.required' => 'Không được để trống *',
        //     'prod_name.required' => 'Không được để trống *',
        //     'prod_slug.required' => 'Không được để trống *',
        //     'prod_original_price.required' => 'Không được để trống *',
        //     'prod_original_price.numeric' => 'Nhập đúng định dạng số *',
        //     'prod_original_price.min' => 'Tối thiểu là 0 *',
        //     'prod_selling_price.required' => 'Không được để trống *',
        //     'prod_selling_price.numeric' => 'Nhập đúng định dạng số *',
        //     'prod_selling_price.min' => 'Tối thiểu là 0 *',
        //     'prod_small_description.required' => 'Không được để trống *',
        //     'prod_detail_description.required' => 'Không được để trống *',
        //     'prod_quantity.required' => 'Không được để trống *',
        //     'prod_quantity.numeric' => 'Nhập đúng định dạng số *',
        //     'prod_quantity.min' => 'Tối thiểu là 1 *',
        //     'prod_size.required' => 'Không được để trống *',
        //     'prod_image.required' => 'Không được để trống *',
        // ]);
        // $validation = $request->all();
        $product = new Product();
        // xử lý hình ảnh
        if ($request->hasFile('prod_image')) {
            $file = $request->file('prod_image');
            $extension = $file->getClientOriginalExtension();
            $filename = time(). '.' .$extension;
            $file->move('uploads/prodImg/',$filename);
            $product->prod_image = $filename;
        }
        // lấy thông tin từ trường name của form
        $product->cate_id = $request->input('cate_id');
        $product->brand_id = $request->input('brand_id');
        $product->prod_name = $request->input('prod_name');
        $product->prod_original_price = $request->input('prod_original_price');
        $product->prod_selling_price = $request->input('prod_selling_price');
        $product->prod_small_description = $request->input('prod_small_description');
        $product->prod_detail_description = $request->input('prod_detail_description');
        $product->prod_quantity = $request->input('prod_quantity');
        $product->prod_top_selling = $request->input('prod_top_selling') == TRUE?'1':'0';
        // thêm dữ liệu vào csdl
        $product->save();

        $prod_id = $product->id;

        if (isset($request->clo_style)) {
            $prod_detail = new DetailInfo();
            $prod_detail->prod_id = $prod_id;
            $prod_detail->clo_style = $request->clo_style;
            $prod_detail->clo_material = $request->clo_material;
            $prod_detail->clo_origin = $request->clo_origin;
            $prod_detail->clo_type = $request->clo_type;
            $prod_detail->clo_cate = $request->clo_cate_prod;
            $prod_detail->clo_model = $request->clo_model;
            $prod_detail->save();
        }


        // if (isset($request->color)) {
        //     $prod_color = new Color();
        //     $prod_color->prod_id = $id_pro;
        //     $prod_color->color = $request->color;
        //     $prod_color->image = $request->image_color;
        //     $prod_color->price = $request->price_color;
        //     if ($request->hasFile('image_color')) {
        //         $file = $request->file('image_color');
        //         $extension = $file->getClientOriginalExtension();
        //         $filename = time(). '.' .$extension;
        //         $file->move('uploads/prodImg/',$filename);
        //         $prod_color->image = $filename;
        //     }
        //     $prod_color->save();
        //     // dd($prod_color);
        // }

        // if (isset($request->size)) {
        //     $prod_size = new Size();
        //     $prod_size->prod_id = $id_pro;
        //     $prod_size->size = $request->size;
        //     $prod_size->price = $request->price_size;
        //     $prod_size->weight = $request->weight;
        //     $prod_size->save();
        //     // dd($prod_size);
        // }

        // sau khi thêm thì quay lại trang
        return redirect()->route('product.index')->with('status', 'Thêm mới sản phẩm thành công!');
    }

    public function edit($id) {
        $product = Product::findOrFail($id);
        $category = Category::all();
        $brand = Brand::all();
        $prod_detail = DetailInfo::where('prod_id', '=', $id)->first();
        return view('admin.product.edit-prod')
        ->with('product', $product)
        ->with('category', $category)
        ->with('brand', $brand)
        ->with('prod_detail', $prod_detail);
    }

    public function update(Request $request ,$id) {
        // $request->validate([
        //     'cate_id' => 'required',
        //     'brand_id' => 'required',
        //     'prod_name' => 'required',
        //     'prod_slug' => 'required',
        //     'prod_original_price' => 'required|numeric|min:0',
        //     'prod_selling_price' => 'required|numeric|min:0',
        //     'prod_small_description' => 'required',
        //     'prod_detail_description' => 'required',
        //     'prod_quantity' => 'required|numeric|min:1',
        //     'prod_size' => 'required',
        // ], [
        //     'cate_id.required' => 'Không được để trống *',
        //     'brand_id.required' => 'Không được để trống *',
        //     'prod_name.required' => 'Không được để trống *',
        //     'prod_slug.required' => 'Không được để trống *',
        //     'prod_original_price.required' => 'Không được để trống *',
        //     'prod_original_price.numeric' => 'Nhập đúng định dạng số *',
        //     'prod_original_price.min' => 'Tối thiểu là 0 *',
        //     'prod_selling_price.required' => 'Không được để trống *',
        //     'prod_selling_price.numeric' => 'Nhập đúng định dạng số *',
        //     'prod_selling_price.min' => 'Tối thiểu là 0 *',
        //     'prod_small_description.required' => 'Không được để trống *',
        //     'prod_detail_description.required' => 'Không được để trống *',
        //     'prod_quantity.required' => 'Không được để trống *',
        //     'prod_quantity.numeric' => 'Nhập đúng định dạng số *',
        //     'prod_quantity.min' => 'Tối thiểu là 1 *',
        //     'prod_size.required' => 'Không được để trống *',
        // ]);
        // $validation = $request->all();
        $product = Product::findOrFail($id);
        if ($request->hasFile('prod_image')) {
            $path = 'uploads/prodImg/'.$product->prod_image;
            if (File::exists($path)) {
                File::delete($path);
            }
            $file = $request->file('prod_image');
            $extension = $file->getClientOriginalExtension();
            $filename = time(). '.' .$extension;
            $file->move('uploads/prodImg/',$filename);
            $product->prod_image = $filename;
        }
        $product->cate_id = $request->input('cate_id');
        $product->brand_id = $request->input('brand_id');
        $product->prod_name = $request->input('prod_name');
        $product->prod_original_price = $request->input('prod_original_price');
        $product->prod_selling_price = $request->input('prod_selling_price');
        $product->prod_small_description = $request->input('prod_small_description');
        $product->prod_detail_description = $request->input('prod_detail_description');
        $product->prod_quantity = $request->input('prod_quantity');
        $product->prod_top_selling = $request->input('prod_top_selling') == TRUE?'1':'0';
        $product->save();

        $prod_id = $product->id;

        if (isset($request->clo_style)) { // nếu tồn tại rồi thì sửa và cập nhật
            $prod_detail = DetailInfo::where('prod_id', '=', $id)->first();
            if (isset($prod_detail)) {
                $prod_detail->prod_id = $prod_id;
                $prod_detail->clo_style = $request->clo_style;
                $prod_detail->clo_material = $request->clo_material;
                $prod_detail->clo_origin = $request->clo_origin;
                $prod_detail->clo_type = $request->clo_type;
                $prod_detail->clo_cate = $request->clo_cate_prod;
                $prod_detail->clo_model = $request->clo_model;
                $prod_detail->save();
            } else if($request->clo_style) { // nếu chưa thì thêm mới
                $prod_detail = new DetailInfo();
                $prod_detail->prod_id = $prod_id;
                $prod_detail->clo_style = $request->clo_style;
                $prod_detail->clo_material = $request->clo_material;
                $prod_detail->clo_origin = $request->clo_origin;
                $prod_detail->clo_type = $request->clo_type;
                $prod_detail->clo_cate = $request->clo_cate_prod;
                $prod_detail->clo_model = $request->clo_model;
                $prod_detail->save();
            }
        }
        return redirect()->route('product.index')->with('status', 'Cập nhật sản phẩm thành công!');
    }

    public function destroy($id) {
        $product = Product::findOrFail($id);
        if ($product->prod_image) {
            $path = 'uploads/prodImg/'.$product->prod_image;
            if (File::exists($path)) {
                File::delete($path);
            }
        }
        $product->delete();
        return redirect()->route('product.index')->with('status', 'Xóa sản phẩm thành công!');
    }


    public function unActive($id) {
        Product::where('id', $id)->update(['prod_status'=>1]);
        return redirect()->route('product.index')->with('status', 'Ẩn sản phẩm thành công!');;
    }

    public function active($id) {
        Product::where('id', $id)->update(['prod_status'=>0]);
        return redirect()->route('product.index')->with('status', 'Hiển thị sản phẩm thành công!');;
    }

    public function getColorProduct($id) {
        $product = Product::findOrFail($id);
        $prod_color = Color::where('prod_id', $id)->orderBy('id', 'desc')->get();
        return view('admin.color.color', compact('product', 'prod_color'));
    }

    public function createColorProduct(Request $request) {
        $prod_color = new Color();
        $prod_color->prod_id = $request->prod_id_hidden;
        $prod_color->color = $request->color;
        $prod_color->save();
        return back()->with('status', 'Thêm mới màu sắc thành công!');
    }

    public function destroyColorProduct($id) {
        $prod_color = Color::findOrFail($id);
        $prod_color->delete();
        return back()->with('status', 'Xóa màu sắc thành công!');
    }

    public function getSizeProduct($id) {
        $product = Product::findOrFail($id);
        $prod_size = Size::where('prod_id', $id)->get();
        return view('admin.size.size', compact('product', 'prod_size'));
    }

    public function createSizeProduct(Request $request) {
        $prod_size = new Size();
        $prod_size->prod_id = $request->prod_id_hidden;
        $prod_size->size = $request->size;
        $prod_size->save();
        return back()->with('status', 'Thêm mới kích cỡ thành công!');
    }

    public function destroySizeProduct($id) {
        $prod_size = Size::findOrFail($id);
        $prod_size->delete();
        return back()->with('status', 'Xóa kích cỡ thành công!');
    }
}
