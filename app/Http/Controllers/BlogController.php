<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\OrderBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('blogs.index');
    }
    public function blogs()
    {
        $products = Blog::orderBy('id', 'DESC')->paginate(15);
        return view('blogs_page', compact('products'));
    }
    public function blogList()
    {


        $user = Auth::user();
        // where('user_id', $user->id)->
        $batch = Blog::orderBy('id', 'DESC')->get();

        return Datatables::of($batch)
            ->addIndexColumn()
            ->addColumn('edit', function ($row) {
                $buttons = '';
                $buttons .= '<div class="d-flex justify-content-around">
                <a class="btn btn-primary btn-sm" href="' . route('blogEntry.edit', $row->id) . '"><i class="fa fa-pencil"></i>Edit</a> 
                <form style="" action="' . route('blogEntry.destroy', $row->id) . '" method="post">
                <input type="hidden" name="_method" value="DELETE">
                ' . csrf_field() . '
                <button type="submit" onclick="return confirm(\'Are you sure you wish to delete this entry?\')" class="btn btn-sm btn-danger">Delete</button>
                </form>
                </div>';
                return $buttons;
            })
            ->addColumn('view', function ($orderBatch) {
                $url = route('blogEntry.show', $orderBatch->id);
                return '<a href="' . $url . '" class="btn btn-info btn-sm" ><i class="fa fa-eye"></i> View</a>';
            })
            ->addColumn('image', function ($orderBatch) {
                $url = $orderBatch->image;
                return '<img src="' . asset('images/' . $url) . '" class="img" style="width:100px;" >';
            })


            ->rawColumns(['edit', 'view', 'image'])
            ->make(true);
    }
    public function blogDetails($id)
    {
        $product = Blog::where('slug', $id)->firstOrFail();
        return view('blog_details_page', compact('product'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('blogs.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'slug' => 'required|string|unique:blogs',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // dd($request->file('image'));

        $product = new Blog($request->except('_token'));

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $product->image = $imageName;
        }

        $product->save();

        return redirect()->route('blogEntry.index')->with(['message', 'Blog created successfully.', 'message_type' => 'success']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $batch = Blog::where('id', $id)->first();

        // return view('blog.show', ['batch' => $batch, 'loactions' => $loactions]);
        return view('blogs.show', ['batch' => $batch]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $blog = Blog::find($id);
        return view('blogs.edit', compact('blog'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $blog = Blog::find($id);
        $request->validate([
            'title' => 'required|string',
            'slug' => 'required|string|unique:blogs,slug,' . $id,
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $blog->fill($request->except('_token'));

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $blog->image = $imageName;
        }

        $blog->save();

        return redirect()->route('blogEntry.index')->with(['message', 'Blog updated successfully.', 'message_type' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $blog = Blog::find($id);
        $blog->delete();
        return redirect()->back()->with('success', 'Delete Successfully');
    }
}
