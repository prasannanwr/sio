<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Carbon as SupportCarbon;

class AdminController extends Controller
{
    public static function canAdmin()
    {
        if (Auth::user()) {
            if (Auth::user()->user_type != 'admin') {
                abort(402, 'You do not have access to this functionality');
            }
        }
    }
    public static function canDispatch()
    {
        // dd(request()->user());
        if (Auth::user()) {
            if (Auth::user()->user_type != 'admin' && Auth::user()->user_type != 'dispatcher') {
                return abort(402, 'You do not have access to this functionality');
            }
        }
    }

    public function allUsers()
    {
        return view('admin.users.index');
    }

    public function deleteUser($id)
    {
        self::canAdmin();

        $user = User::find($id);

        if (! $user) {
            return redirect()->route('allUsers')->with(['message' => 'User not found.', 'message_type' => 'danger']);
        }

        if ((int) $user->id === (int) Auth::id()) {
            return redirect()->route('allUsers')->with(['message' => 'You cannot delete your own account.', 'message_type' => 'danger']);
        }

        if ($user->user_type === 'admin') {
            return redirect()->route('allUsers')->with(['message' => 'Admin accounts cannot be deleted.', 'message_type' => 'danger']);
        }

        try {
            $user->delete();
        } catch (\Throwable $e) {
            return redirect()->route('allUsers')->with([
                'message' => 'Could not delete this user (they may have related records). ' . $e->getMessage(),
                'message_type' => 'danger',
            ]);
        }

        return redirect()->route('allUsers')->with(['message' => 'User deleted successfully.', 'message_type' => 'success']);
    }

    /**
     * datatable for all orders
     */
    public function allUsersList()
    {
        $users = User::where('user_type', '!=', 'admin')->orderBy('created_at', 'DESC')->get();

        return Datatables::of($users)
            ->addIndexColumn()
            ->editColumn('user_type', function ($user) {
                $mar = "<span class= 'badge bg-secondary'>" . ucfirst($user->user_type) . "</span><br>";

                // if ($user->user_type == 'admin') {
                //     $url = route('makeUser');
                //     $mar .= '<br><form method="POST" action="' . $url . '">
                //             <input type="hidden" name = "_token" value = ' . csrf_token() . '>
                //             <input type="hidden" name = "user_id" value ="' . $user->id . '">
                //             <button type="submit" onclick="return confirm(\'Are you sure?\')" class="btn btn-sm btn-primary">Make User</button>
                //         </form>';

                //     $url = route('makeDispatcher');
                //     $mar .= '<br><form method="POST" action="' . $url . '">
                //                 <input type="hidden" name = "_token" value = ' . csrf_token() . '>
                //                 <input type="hidden" name = "user_id" value ="' . $user->id . '">
                //                 <button type="submit" onclick="return confirm(\'Are you sure?\')" class="btn btn-sm btn-primary">Make Dispatcher</button>
                //             </form>';
                // } else if ($user->user_type == 'user') {
                //     $url = route('makeAdmin');
                //     $mar .= '<br><form method="POST" action="' . $url . '">
                //             <input type="hidden" name = "_token" value = ' . csrf_token() . '>
                //             <input type="hidden" name = "user_id" value ="' . $user->id . '">
                //             <button type="submit" onclick="return confirm(\'Are you sure?\')" class="btn btn-sm btn-primary">Make Admin</button>
                //         </form>';

                //     $url = route('makeDispatcher');
                //     $mar .= '<br><form method="POST" action="' . $url . '">
                //                 <input type="hidden" name = "_token" value = ' . csrf_token() . '>
                //                 <input type="hidden" name = "user_id" value ="' . $user->id . '">
                //                 <button type="submit" onclick="return confirm(\'Are you sure?\')" class="btn btn-sm btn-primary">Make Dispatcher</button>
                //             </form>';
                // } else {
                //     $url = route('makeAdmin');
                //     $mar .= '<br><form method="POST" action="' . $url . '">
                //             <input type="hidden" name = "_token" value = ' . csrf_token() . '>
                //             <input type="hidden" name = "user_id" value ="' . $user->id . '">
                //             <button type="submit" onclick="return confirm(\'Are you sure?\')" class="btn btn-sm btn-primary">Make Admin</button>
                //         </form>';

                //     $url = route('makeUser');
                //     $mar .= '<br><form method="POST" action="' . $url . '">
                //                 <input type="hidden" name = "_token" value = ' . csrf_token() . '>
                //                 <input type="hidden" name = "user_id" value ="' . $user->id . '">
                //                 <button type="submit" onclick="return confirm(\'Are you sure?\')" class="btn btn-sm btn-primary">Make User</button>
                //             </form>';
                // }
                return $mar;
            })
            ->editColumn('email', function ($user) {
                $mar = "Name : " . $user->name . "<br>";
                $mar .= "Email:" . $user->email;
                return $mar;
            })
            ->addColumn('date', function ($user) {
                $mar = "Created at : " . Carbon::parse($user->created_at)->format("F j, Y") . "<br>";
                $mar .= "Last updated:" .  Carbon::parse($user->updated_at)->format("F j, Y");
                return $mar;
            })
            ->editColumn('blocked', function ($user) {
                $mar = "<span class= 'badge bg-secondary'>" . (($user->blocked == 1) ? 'Blocked' : 'Active') . "</span><br>";
                return $mar;
            })
            ->addColumn('actions', function ($user) {
                $mar = '';
                if ($user->user_type != 'agent') {
                    if ($user->blocked == 1) {
                        $url = route('unblockUser');
                        $mar .= '<form method="POST" action="' . $url . '" style="display:inline-block;">
                                <input type="hidden" name = "_token" value = ' . csrf_token() . '>
                                <input type="hidden" name = "user_id" value ="' . $user->id . '">
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Update User Status">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z"/>
                                    <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466"/>
                                    </svg>
                                </button>
                            </form>';
                    } else {
                        $url = route('blockUser');
                        $mar .= '<form method="POST" action="' . $url . '" style="display:inline-block;">
                                <input type="hidden" name = "_token" value = ' .     csrf_token() . '>
                                <input type="hidden" name = "user_id" value ="' . $user->id . '">
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="btn btn-sm btn-danger" data-toggle="tooltip" title="Update User Status">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z"/>
                                    <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466"/>
                                    </svg>
                                </button>
                            </form>';
                    }
                }

                if ((int) $user->id !== (int) Auth::id() && $user->user_type !== 'admin') {
                    $deleteUrl = route('deleteUser', $user->id);
                    $mar .= '<form method="POST" action="' . $deleteUrl . '" style="display:inline-block; margin-left:4px;">
                            <input type="hidden" name="_token" value="' . csrf_token() . '">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" onclick="return confirm(\'Delete this user permanently? This cannot be undone.\')" class="btn btn-sm btn-danger" data-toggle="tooltip" title="Delete User">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>';
                }

                return $mar;
            })
            ->rawColumns(['user_type', 'email', 'date', 'blocked', 'actions', 'payment'])
            ->make(true);
    }

    public function unblockUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        try {
            User::where('id', $request->user_id)->update([
                'blocked' => false
            ]);
            return back()->with(['message' => 'User Unblocked', 'message_type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['exception' => $e]);
            return back()->with('message', "An error occured " . $e->getMessage())->withInput();
        }
    }

    public function blockUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        try {
            User::where('id', $request->user_id)->update([
                'blocked' => true
            ]);
            return back()->with(['message' => 'User Blocked', 'message_type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['exception' => $e]);
            return back()->with('message', "An error occured " . $e->getMessage())->withInput();
        }
    }

    public function makeAdmin(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        try {
            User::where('id', $request->user_id)->update([
                'user_type' => 'admin'
            ]);
            return back()->with(['message' => 'User Made Admin', 'message_type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['exception' => $e]);
            return back()->with('message', "An error occured " . $e->getMessage())->withInput();
        }
    }

    public function makeDispatcher(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        try {
            User::where('id', $request->user_id)->update([
                'user_type' => 'dispatcher'
            ]);
            return back()->with(['message' => 'User Made Dispatcher', 'message_type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['exception' => $e]);
            return back()->with('message', "An error occured " . $e->getMessage())->withInput();
        }
    }

    public function makeUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        try {
            User::where('id', $request->user_id)->update([
                'user_type' => 'user'
            ]);
            return back()->with(['message' => 'User Made User', 'message_type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['exception' => $e]);
            return back()->with('message', "An error occured " . $e->getMessage())->withInput();
        }
    }

    public function adminReports()
    {
        $rep = [];
        $rep['total_users'] = User::count();
        $rep['total_blocked_users'] = User::where('blocked', true)->count();

        $rep['total_orders'] = Order::count();
        $rep['total_unpaid_orders'] = Order::where('status', 'unpaid')->count();
        $rep['totsl_paid_orders'] = Order::where('status', '!=', 'unpaid')->count();
        $rep['total_in_transit_orders'] = Order::where('status', 'in_transit')->count();
        $rep['total_delivered_orders'] = Order::where('status', 'delivered')->count();
        $rep['total_cancelled_orders'] = Order::where('status', 'cancelled')->count();

        $rep['total_payments'] = Payment::where('status', 'done')->count();
        $rep['total_payments_pending'] = Payment::where('status', 'pending')->count();
        $rep['total_payments_failed'] = Payment::where('status', 'failed')->count();
        $rep['total_payments_value'] = Payment::where('status', 'done')->sum('amt_paid');

        return $rep;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * hompage for the various admin settings
     */

    public function admin_settings()
    {
        return view('admin.settings.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function show(Admin $admin)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function edit(Admin $admin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Admin $admin)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function destroy(Admin $admin)
    {
        //
    }
}
