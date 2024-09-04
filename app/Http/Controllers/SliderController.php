<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Slider;
use Illuminate\Http\Request;
use App\Services\ImageService;

class SliderController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }
    public function index()
    {
        $sliders = Slider::orderBy('order', 'asc')->get();
        return view('backends.sliders.index', compact('sliders'));
    }

    public function create()
    {
        return view('backends.sliders.create_and_edit');
    }

    public function store(Request $request)
    {
        try {
            $slider = new Slider();
            $slider->title = $request->title;
            $slider->description = $request->description;
            $slider->link = $request->link;
            $slider->order = $request->order;
            $slider->status = $request->status;
            $slider->image = $this->imageService->uploadImage($request->file('image'));
            $slider->save();
            return redirect()->route('sliders.index')->with('success', 'Slider created successfully.');
        } catch (Exception $e) {
            dd($e);
            return redirect()->route('sliders.index')->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function edit(Slider $slider)
    {
        return view('backends.sliders.create_and_edit', compact('slider'));
    }
    public function update(Request $request, $id)
    {
        try {
            $slider = Slider::find($id);
            $slider->title = $request->title;
            $slider->description = $request->description;
            $slider->link = $request->link;
            $slider->order = $request->order;
            $slider->status = $request->status;
            if ($request->hasFile('image')) {
                $this->imageService->deleteImage($slider->image);
                $slider->image = $this->imageService->uploadImage($request->file('image'));
            }
            $slider->save();
            return redirect()->route('sliders.index')->with('success', 'Slider Update successfully.');
        } catch (Exception $e) {
            dd($e);
            return redirect()->route('sliders.index')->with('error', 'Something went wrong. Please try again.');
        }
    }
    public function destroy(Slider $slider)
    {
        $slider->delete();

        return redirect()->route('sliders.index')->with('success', 'Slider deleted successfully.');
    }
}
