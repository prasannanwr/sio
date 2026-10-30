<?php

namespace App\Http\Controllers;

use App\Constraints\UserTypes;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SubAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.subAdmins.index');
    }

    public function subAdminList()
    {
        
        $batch = User::where('user_type',UserTypes::SUB_ADMIN)->orderBy('id', 'DESC')->get();

        return DataTables::of($batch)
            ->addIndexColumn()
            ->addColumn('edit', function ($row) {
                $buttons = '';
                $buttons .= '<div class="d-flex justify-content-around">
                <a class="btn btn-primary btn-sm" href="' . route('subAdmin.edit', $row->id) . '"><i class="fa fa-pencil"></i>Edit</a> 
                <form style="" action="' . route('subAdmin.destroy', $row->id) . '" method="post">
                <input type="hidden" name="_method" value="DELETE">
                ' . csrf_field() . '
                <button type="submit" onclick="return confirm(\'Are you sure you wish to delete this entry?\')" class="btn btn-sm btn-danger">Delete</button>
                </form>
                </div>';
                return $buttons;
            })
            
            ->addColumn('view', function ($orderBatch) {
                $url = route('subAdmin.show', $orderBatch->id);
                return '<a href="' . $url . '" class="btn btn-info btn-sm" ><i class="fa fa-eye"></i> View</a>';
            })
            ->rawColumns(['edit', 'view'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $menus = DB::table('menu_lists')->orderBy('id','ASC')->get();
        return view('admin.subAdmins.create',compact('menus'));
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'mobile' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);

        $userId = User::insertGetId([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => bcrypt($request->password),
            'pass' => $request->password,
            'address' => $request->address,
            'user_type' => UserTypes::SUB_ADMIN,
        ]);
        

        $menus = $request->menus;
        if(count($menus)){
            foreach($menus as $menu){
                DB::table('menu_permission')->insert([
                    'user_id' => $userId,
                    'menu_id' => $menu,
                ]);
            }
        }

        return redirect()->route('subAdmin.index')->with(['message', 'Sub Admin created successfully.', 'message_type' => 'success']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::where('id', $id)->first();
        
        $menus = DB::table('menu_permission')
        ->join('menu_lists', 'menu_permission.menu_id', '=', 'menu_lists.id')
        ->where('menu_permission.user_id', $id)
        ->select('menu_permission.id as permission_id', 'menu_lists.id as menu_id', 'menu_lists.name')
        ->get();
        
        // dd($menus);
        return view('admin.subAdmins.show', ['user' => $user,'menus' => $menus]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::where('id', $id)->first();
        
        $menus = DB::table('menu_lists')
        ->leftJoin('menu_permission', function ($join) use ($id) {
            $join->on('menu_lists.id', '=', 'menu_permission.menu_id')
            ->where('menu_permission.user_id', '=', $id);
        })
        ->select(
            'menu_lists.id',
            'menu_lists.name',
            DB::raw('IF(menu_permission.menu_id IS NULL, 0, 1) as status')
        )
        ->get();
        
        // dd($menus);
        return view('admin.subAdmins.edit', ['user' => $user,'menus' => $menus]);
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
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'mobile' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);

        $userId = $id;
        User::where('id',$id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => bcrypt($request->password),
            'pass' => $request->password,
            'address' => $request->address,
        ]);
        

        DB::table('menu_permission')->where('user_id',$userId)->delete();
        $menus = $request->menus;
        if(count($menus)){
            foreach($menus as $menu){
                DB::table('menu_permission')->insert([
                    'user_id' => $userId,
                    'menu_id' => $menu,
                ]);
            }
        }

        return redirect()->route('subAdmin.index')->with(['message', 'Sub Admin Updated successfully.', 'message_type' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $menuDelete = DB::table('menu_permission')->where('user_id',$id)->delete();
        if($menuDelete){
            User::where('id',$id)->delete();
        }

        return redirect()->back()->with('success', 'Delete Successfully');
    }
}
