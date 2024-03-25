<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\Mf;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class CarController extends Controller
{
    public function index()
    {
        //  $cars = DB::table('cars')->join('mfs', 'cars.mf_id', '=', 'mfs.id')->get();
        // dd($cars);
        $cars = Car::all();

        return view('car-list', compact('cars'));
    }

    public function create()
    {
        $mfsList = Mf::all();

        return view('car-create', compact('mfsList'));
    }

    public function store(Request $request)
    {


        // Validator 
        if ($request->isMethod('post')) {
            // dd($request->mf_id);

            $validateData = $request->validate([
                'model' => 'required|min:5',
                'product_on' => 'required|date',
                'description' => 'required',
                'image' => 'file|image|mimes:jpeg,png,jpg,gif|max:2048',
                'mf_id' => 'required',
            ], [
                'model.required' => 'Trường ảnh là bắt buộc',
                'model.min' => 'Trường model có độ dài phải lớn hơn',
                'product_on.required' => 'Trường thời gian là bắt buộc',
                'product_on.date' => 'Trường thời gian là bắt buộc',
                'description.required' => 'Trường mô tả là bắt buộc',
                'image.required' => 'Trường ảnh là bắt buộc',
                'image.image' => 'Trường này phải là ảnh',
                'image.mimes' => 'Trường Kiểu ảnh là kiểu ảnh',
                'mf_id.required' => 'Trường hạng là bắt buộc',
            ]);

            //     dd($request->mf_id);
            $car = new Car();
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images'), $imageName);
                $car->image = $imageName;
                $car->model = $validateData['model'];
                $car->product_on = $validateData['product_on'];
                $car->description = $validateData['description'];
                $car->mf_id = $validateData['mf_id'];
                $car->save();
                $msg = "success";
                Session::flash('message', $msg);

                return redirect()->route('cars.index');
            }
            return redirect()->back()->withErrors($validateData)->withInput();
        }
    }


    public function delete(string $id)
    {
        if (!empty($id)) {
            $car = Car::find($id);


            if ($car) {
                $image_path = public_path("images/{$car->image}");

                if (File::exists($image_path)) {
                    File::delete($image_path);
                }

                $car->delete();
                return redirect()->route('cars.index')->with('success', 'Car deleted successfully');
            } else {
                return redirect()->back()->with('error', 'Car not found');
            }
        } else {
            return redirect()->back()->with('error', 'Invalid car ID');
        }
    }

    public function show(string $id)
    {
        $car = Car::find($id);
        $mfsList = Mf::all();

        return view('car-detail', compact('car', 'mfsList'));
    }
    public function getEdit(string $id)
    {
        $car = Car::find($id);
        $mfsList = Mf::all();
        return view('car-update', compact('car', 'mfsList'));
    }

    public function updateCar(Request $request, string $id)
    {
        $car = Car::find($id);
        $msg = 'failed';

        if (!empty($car)) {
            $validateData = $request->validate([
                'model' => 'required|min:5',
                'product_on' => 'required|date',
                'description' => 'required',
                'image' => 'file|image|mimes:jpeg,png,jpg,gif|max:2048',
                'mf_id' => 'required',
            ], [
                'model.required' => 'Trường model là bắt buộc',
                'model.min' => 'Trường model có độ dài phải lớn hơn',
                'product_on.required' => 'Trường thời gian là bắt buộc',
                'product_on.date' => 'Trường thời gian là bắt buộc',
                'description.required' => 'Trường mô tả là bắt buộc',
                'image.image' => 'Trường này phải là ảnh',
                'image.mimes' => 'Trường Kiểu ảnh là kiểu ảnh',
                'mf_id.required' => 'Trường hạng là bắt buộc',
            ]);

            if ($request->hasFile('image')) {
                $image_path = public_path("images/{$car->image}");

                if (File::exists($image_path)) {
                    File::delete($image_path);
                }

                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images'), $imageName);
                $car->image = $imageName;
            }

            $car->model = $validateData['model'];
            $car->product_on = $validateData['product_on'];
            $car->description = $validateData['description'];
            $car->mf_id = $validateData['mf_id'];
            $car->save();

            if ($car) {
                $msg = "success";
            }

            Session::flash('message', $msg);
            return redirect()->back();
        }

        Session::flash('message', $msg);
        return redirect()->back();
    }
}
