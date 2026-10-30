<?php

namespace App\Http\Controllers;

use App\Models\Dispatcher;
use App\Models\Kyc;
use App\Models\PaymentRequest;
use App\Models\User;
use App\Models\WalkInCustomer;
use Carbon\Carbon;
use Illuminate\Http\Request;

use CodiceFiscale\InverseCalculator;
use CodiceFiscale\Calculator;
use CodiceFiscale\Subject;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class WalkInCustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.users.customer_kyc');
    }

    public function allWalkInCusList()
    {
        $users = WalkInCustomer::orderBy('created_at', 'DESC')->get();
        return Datatables::of($users)
            ->addIndexColumn()
            ->editColumn('kyc_status', function ($user) {
                $mar = "<span class= 'badge bg-secondary'>" . $user->kyc_status . "</span><br>";
                return $mar;
            })
            ->addColumn('surname', function ($user) {
                $mar = "Name : " . $user->surname . ' ' . $user->name . "<br>";
                $mar .= "Contact:" . $user->email . ', ' . $user->phone . "<br>";
                $mar .= "Bio:" . $user->gender . ', ' . $user->birthDate . "<br>";
                $mar .= "Tax Code:" . $user->tax_code . "<br>";
                return $mar;
            })
            ->addColumn('date', function ($user) {
                $mar = "Created at : " . $user->created_at . "<br>";
                $mar .= "Last updated:" . $user->updated_at;
                return $mar;
            })
            ->addColumn('kyc_actions', function ($user) {
                // $mar = "<span class= 'badge bg-secondary'>" . ($user->kyc_status) . "</span><br>";
                $mar = "";
                $doc_front_url = asset('uploads/' . $user->doc_front);
                $doc_back_url = asset('uploads/' . $user->doc_back);
                $mar .= "Docment Front: <a href = " . $doc_front_url . " target='_blank'>" . $user->doc_front . "</a><br>";
                $mar .= "Docment Back: <a href = " . $doc_back_url . " target='_blank'>" . $user->doc_back . "</a><br>";

                if ($user->kyc_status == 'pending' || $user->kyc_status == 'rejected') {
                    $url = route('approveWalkInCusKYC');
                    $mar .= '<br><form method="POST" action="' . $url . '">
                            <input type="hidden" name = "_token" value = ' . csrf_token() . '>
                            <input type="hidden" name = "user_id" value ="' . $user->id . '">
                            <button type="submit" onclick="return confirm(\'Are you sure?\')" class="btn btn-sm btn-primary">Approve KYC</button>
                        </form>';
                } else {
                    $url = route('unapproveWalkInCusKYC');
                    $mar .= '<br><form method="POST" action="' . $url . '">
                            <input type="hidden" name = "_token" value = ' . csrf_token() . '>
                            <input type="hidden" name = "user_id" value ="' . $user->id . '">
                            <button type="submit" onclick="return confirm(\'Are you sure?\')" class="btn btn-sm btn-danger">Unapprove KYC</button>
                        </form>';
                }

                if ($user->user_type == 'dispatcher') {
                    $mar .= '<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    see More
                  </button>';
                }
                return $mar;
            })
            // ->addColumn('delete', function ($user) {
            //
            // })
            ->rawColumns(['kyc_status', 'surname', 'date', 'kyc_actions'])
            ->make(true);
    }

    public function allDispatcherList()
    {
        $users = User::where('user_type', 'dispatcher')->orderBy('created_at', 'DESC')->get();
        return Datatables::of($users)
            ->addIndexColumn()
            ->addColumn('email', function ($user) {
                $mar = "Name : " . $user->name . "<br>";
                $mar .= "Contact:" . $user->email . "<br>";
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
                $mar = '<div class="d-flex">';
                if ($user->blocked == 1 && $user->user_type !== 'agent') {
                    $url = route('unblockUser');
                    $mar .= '<form method="POST" action="' . $url . '">
                            <input type="hidden" name = "_token" value = ' . csrf_token() . '>
                            <input type="hidden" name = "user_id" value ="' . $user->id . '">
                            <button type="submit" onclick="return confirm(\'Are you sure?\')" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Update Dispatcher Status">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z"/>
                                <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466"/>
                                </svg>
                            </button>
                        </form>';
                } else {
                    if($user->user_type !== 'agent') {
                    $url = route('blockUser');
                    $mar .= '<form method="POST" action="' . $url . '">
                            <input type="hidden" name = "_token" value = ' . csrf_token() . '>
                            <input type="hidden" name = "user_id" value ="' . $user->id . '">
                            <button type="submit" onclick="return confirm(\'Are you sure?\')" class="btn btn-sm btn-danger" data-toggle="tooltip" title="Update Dispatcher Status">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z"/>
                                <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466"/>
                                </svg>
                            </button>
                        </form>';
                    }
                }
                $mar .= '<a type="button" class="btn btn-sm btn-secondary ms-2" data-bs-toggle="modal" data-bs-target="#' . $user->id . 'Modal" data-toggle="tooltip" title="View Dispatcher Information">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                            <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                            </svg>
                        </a>';

                $mar .= '
                    <div class="modal fade" id="' . $user->id . 'Modal" tabindex="-1" aria-labelledby="' . $user->id . 'ModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                <h5 class="modal-title" id="' . $user->id . 'ModalLabel">' . $user->name . '</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <table class="table table-striped">
                                        <tr>
                                            <th>Business Name</th>
                                            <td>' . $user->dispatcher->name . '<td>
                                            <th>Phone</th>
                                            <td>' . $user->dispatcher->phone . '<td>
                                        </tr>
                                        <tr>
                                            <th>Phone 2</th>
                                            <td>' . $user->dispatcher->phone_alt . '<td>
                                            <th>Address1</th>
                                            <td>' . $user->dispatcher->address1 . '<td>
                                        </tr>
                                        <tr>
                                            <th>Address 2</th>
                                            <td>' . $user->dispatcher->address2 . '<td>
                                            <th>Zip</th>
                                            <td>' . $user->dispatcher->zip . '<td>
                                        </tr>
                                        <tr>
                                            <th>City </th>
                                            <td>' . $user->dispatcher->city . '<td>
                                            <th>State</th>
                                            <td>' . $user->dispatcher->state . '<td>
                                        </tr>
                                        <tr>
                                            <th> Country</th>
                                            <td>' . $user->dispatcher->country . '<td>
                                            <th>Account type</th>
                                            <td>' . $user->dispatcher->agency_type . '<td>
                                        </tr>
                                        <tr>
                                            <th> Agency Name </th>  
                                            <td>' . $user->dispatcher->business_name . '<td>
                                            <th>Tax ID Code</th>
                                            <td>' . $user->dispatcher->tax_id_code . '<td>
                                        </tr>
                                        <tr>
                                            <th> VAT No. </th>
                                            <td>' . $user->dispatcher->vat_no . '<td>
                                            <th>PEC</th>
                                            <td>' . $user->dispatcher->pec . '<td>
                                        </tr>
                                        <tr>
                                            <th> SDI </th>
                                            <td>' . $user->dispatcher->sdi . '<td>
                                            <th></th>
                                            <td><td>
                                        </tr>
                                    </table>        
                                </div>
                                <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>';
                $mar .= '<a href="' . route('dispatchers.edit', $user->dispatcher) . '" class="btn btn-sm btn-primary ms-2" data-toggle="tooltip" title="Edit Dispatcher">
                       <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                        </svg>
                    </a></div>';
                return $mar;
            })
            ->rawColumns(['blocked', 'email', 'date', 'actions'])
            ->make(true);
    }

    public function allAgentList()
    {
        $users = User::where('user_type', 'agent')->orderBy('created_at', 'DESC')->get();
        return Datatables::of($users)
            ->addIndexColumn()
            ->addColumn('email', function ($user) {
                $mar = "Name : " . $user->name . "<br>";
                $mar .= "Contact:" . $user->email . "<br>";
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
                $mar = '<div class="d-flex">';
                if ($user->blocked == 1 && $user->user_type !== 'agent') {
                    $url = route('unblockUser');
                    $mar .= '<form method="POST" action="' . $url . '">
                            <input type="hidden" name = "_token" value = ' . csrf_token() . '>
                            <input type="hidden" name = "user_id" value ="' . $user->id . '">
                            <button type="submit" onclick="return confirm(\'Are you sure?\')" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Update Agent Status">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z"/>
                                <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466"/>
                                </svg>
                            </button>
                        </form>';
                } else {
                    if($user->user_type !== 'agent') {
                    $url = route('blockUser');
                    $mar .= '<form method="POST" action="' . $url . '">
                            <input type="hidden" name = "_token" value = ' . csrf_token() . '>
                            <input type="hidden" name = "user_id" value ="' . $user->id . '">
                            <button type="submit" onclick="return confirm(\'Are you sure?\')" class="btn btn-sm btn-danger" data-toggle="tooltip" title="Update Agent Status">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z"/>
                                <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466"/>
                                </svg>
                            </button>
                        </form>';
                    }
                }
                $mar .= '<a class="btn btn-sm btn-secondary ms-2" href="' . route('agent.editSitting', $user->id) . '">Show</a>';

                $mar .= '
                    <div class="modal fade" id="' . $user->id . 'Modal" tabindex="-1" aria-labelledby="' . $user->id . 'ModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                <h5 class="modal-title" id="' . $user->id . 'ModalLabel">' . $user->name . '</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <table class="table table-striped">
                                        <tr>
                                            <th>Business Name</th>
                                            <td>' . $user->agent->name . '<td>
                                            <th>Phone</th>
                                            <td>' . $user->agent->phone . '<td>
                                        </tr>
                                        <tr>
                                            <th>Phone 2</th>
                                            <td>' . $user->agent->phone_alt . '<td>
                                            <th>Address1</th>
                                            <td>' . $user->agent->address1 . '<td>
                                        </tr>
                                        <tr>
                                            <th>Address 2</th>
                                            <td>' . $user->agent->address2 . '<td>
                                            <th>Zip</th>
                                            <td>' . $user->agent->zip . '<td>
                                        </tr>
                                        <tr>
                                            <th>City </th>
                                            <td>' . $user->agent->city->name . '<td>
                                            <th>State</th>
                                            <td>' . $user->agent->state->name . '<td>
                                        </tr>
                                        <tr>
                                            <th> Country</th>
                                            <td>' . $user->agent->country->name . '<td>
                                            <th>Account type</th>
                                            <td>' . $user->agent->agency_type . '<td>
                                        </tr>
                                        <tr>
                                            <th> Agency Name </th>  
                                            <td>' . $user->agent->business_name . '<td>
                                            <th>Tax ID Code</th>
                                            <td>' . $user->agent->tax_id_code . '<td>
                                        </tr>
                                        <tr>
                                            <th> VAT No. </th>
                                            <td>' . $user->agent->vat_no . '<td>
                                            <th>PEC</th>
                                            <td>' . $user->agent->pec . '<td>
                                        </tr>
                                        <tr>
                                            <th> SDI </th>
                                            <td>' . $user->agent->sdi . '<td>
                                            <th></th>
                                            <td><td>
                                        </tr>
                                    </table>        
                                </div>
                                <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>';


                $mar .= '
                        <div class="modal fade" id="' . $user->id . 'Modal" tabindex="-1" aria-labelledby="' . $user->id . 'ModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl">
                                <div class="modal-content">
                                    <div class="modal-header">
                                    <h5 class="modal-title" id="' . $user->id . 'ModalLabel">' . $user->name . '</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <table class="table table-striped">
                                            <tr>
                                                <th>Business Name</th>
                                                <td>' . $user->agent->name . '<td>
                                                <th>Phone</th>
                                                <td>' . $user->agent->phone . '<td>
                                            </tr>
                                            <tr>
                                                <th>Phone 2</th>
                                                <td>' . $user->agent->phone_alt . '<td>
                                                <th>Address1</th>
                                                <td>' . $user->agent->address1 . '<td>
                                            </tr>
                                            <tr>
                                                <th>Address 2</th>
                                                <td>' . $user->agent->address2 . '<td>
                                                <th>Zip</th>
                                                <td>' . $user->agent->zip . '<td>
                                            </tr>
                                            <tr>
                                                <th>City </th>
                                                <td>' . $user->agent->city->name . '<td>
                                                <th>State</th>
                                                <td>' . $user->agent->state->name . '<td>
                                            </tr>
                                            <tr>
                                                <th> Country</th>
                                                <td>' . $user->agent->country->name . '<td>
                                                <th>Account type</th>
                                                <td>' . $user->agent->agency_type . '<td>
                                            </tr>
                                            <tr>
                                                <th> Agency Name </th>  
                                                <td>' . $user->agent->business_name . '<td>
                                                <th>Tax ID Code</th>
                                                <td>' . $user->agent->tax_id_code . '<td>
                                            </tr>
                                            <tr>
                                                <th> VAT No. </th>
                                                <td>' . $user->agent->vat_no . '<td>
                                                <th>PEC</th>
                                                <td>' . $user->agent->pec . '<td>
                                            </tr>
                                            <tr>
                                                <th> SDI </th>
                                                <td>' . $user->agent->sdi . '<td>
                                                <th></th>
                                                <td><td>
                                            </tr>
                                        </table>        
                                    </div>
                                    <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>';

            //     $mar .= '<a type="button" class="btn btn-sm btn-info ms-2" data-bs-toggle="modal" data-bs-target="#document' . $user->id . 'Modal" data-toggle="tooltip" title="View Agent Payment Request">
            // <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-code" viewBox="0 0 16 16">
            // <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5z"/>
            // <path d="M8.646 6.646a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1 0 .708l-2 2a.5.5 0 0 1-.708-.708L10.293 9 8.646 7.354a.5.5 0 0 1 0-.708m-1.292 0a.5.5 0 0 0-.708 0l-2 2a.5.5 0 0 0 0 .708l2 2a.5.5 0 0 0 .708-.708L5.707 9l1.647-1.646a.5.5 0 0 0 0-.708"/>
            // </svg>
            // </a>';
                $url_payment = route('admin-paymentRequestget', $user->id);
                $paymentRequests = PaymentRequest::where('user_id', $user->id)->where('status', 'pending')->count();
                if ($paymentRequests > 0) {
                    $mar .= '<a type="button" class="btn btn-sm btn-info ms-2" href="' . $url_payment . '">Pending Payment - <b>' . $paymentRequests . '</b></a>';
                } else {
                    $mar .= '<a type="button" class="btn btn-sm btn-info ms-2" href="' . $url_payment . '">Payment</a>';
                }


                $mar .= '
                <div class="modal fade" id="document' . $user->id . 'Modal" tabindex="-1" aria-labelledby="document' . $user->id . 'ModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                            <h5 class="modal-title" id="' . $user->id . 'ModalLabel">' . $user->name . '</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <iframe class="pdf" src="' . asset($user->agent->attachment_path) . '"  height="650"></iframe>  
                            </div>
                            <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>';
                return $mar;
            })
            ->rawColumns(['blocked', 'email', 'date', 'actions'])
            ->make(true);
    }



    public function allMobileUserList()
    {
        $users = User::where('user_type', 'mobile')->orderBy('created_at', 'DESC')->get();
        // dd($users);
        return Datatables::of($users)
            ->addIndexColumn()
            ->editColumn('kyc_status', function ($user) {
                $mar = "<span class= 'badge bg-secondary'>" . (($user->kyc) ? $user->registration_status : 'n/a') . "</span><br>";
                return $mar;
            })
            ->addColumn('surname', function ($user) {
                $mar = "Name : " . $user->name . "<br>";
                $mar .= "Contact:" . $user->email . "<br>";

                if ($user->kyc) {
                    $mar .= '<br><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#' . $user->id . 'Modal">
                        see More
                    </button>';

                    $mar .= '
                        <div class="modal fade" id="' . $user->id . 'Modal" tabindex="-1" aria-labelledby="' . $user->id . 'ModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl">
                                <div class="modal-content">
                                    <div class="modal-header">
                                    <h5 class="modal-title" id="' . $user->id . 'ModalLabel">' . $user->name . '</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <table class="table table-striped">
                                            <tr>
                                                <th>ID Document Type</th>
                                                <td>' . $user->kyc->documentType->name . '<td>
                                                <th>ID Document No.</th>
                                                <td>' . $user->kyc->document_number . '<td>
                                            </tr>
                                            <tr>
                                                <th>ID Document Issue Date</th>
                                                <td>' . $user->kyc->document_issue_date . '<td>
                                                <th>ID Document Exp. Date</th>
                                                <td>' . $user->kyc->document_expiry_date . '<td>
                                            </tr>
                                            <tr>
                                                <th>Photo</th>
                                                <td>' . $user->kyc->selfie . '<td>
                                                <th>Address Proof Type</th>
                                                <td>' . $user->kyc->proofOfAddressType->name . '<td>
                                            </tr>
                                            <tr>
                                                <th>Address Proof Document</th>
                                                <td>' . $user->kyc->proof_of_address . '<td>
                                                <th>KYC Level Reached</th>
                                                <td>' . $user->kyc->kyc_level . '<td>
                                            </tr>
                                            <tr>
                                                <th>Current KYC Status</th>
                                                <td>' . $user->registration_status . '<td>
                                                <th>KYC Rejection Reason(If any)</th>
                                                <td>' . $user->kyc->rejection_reason . '<td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                    </div>';
                } else {
                    $mar .= "<hr><i>No KYC Submitted Yet</i>";
                }
                return $mar;
            })
            ->addColumn('date', function ($user) {
                $mar = "Created at : " . $user->created_at . "<br>";
                $mar .= "Last updated:" . $user->updated_at;
                return $mar;
            })
            ->addColumn('kyc_actions', function ($user) {
                // $mar = "<span class= 'badge bg-secondary'>" . ($user->kyc_status) . "</span><br>";
                $mar = "";
                // $doc_front_url = asset('uploads/' . $user->doc_front);
                // $doc_back_url = asset('uploads/' . $user->doc_back);
                // $mar .= "Docment Front: <a href = " . $doc_front_url . " target='_blank'>" . $user->doc_front . "</a><br>";
                // $mar .= "Docment Back: <a href = " . $doc_back_url . " target='_blank'>" . $user->doc_back . "</a><br>";

                if ($user->registration_status == 'pending') {
                    $url = route('approveMobileUserKYC');
                    $mar .= '<br><form method="POST" action="' . $url . '">
                            <input type="hidden" name = "_token" value = ' . csrf_token() . '>
                            <input type="hidden" name = "user_id" value ="' . $user->id . '">
                            <button type="submit" onclick="return confirm(\'Are you sure?\')" class="btn btn-sm btn-primary">Approve KYC</button>
                        </form>';
                    $mar .= '<hr><button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#' . $user->id . 'KYCModal">
                        Reject
                    </button>';
                    $url2 = route('unapproveMobileUserKYC');
                    $mar .= '
                        <div class="modal fade" id="' . $user->id . 'KYCModal" tabindex="-1" aria-labelledby="' . $user->id . 'KYCModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                    <h5 class="modal-title" id="' . $user->id . 'KYCModalLabel"> Reject KYC For: ' . $user->name . '</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                    <form method="POST" action="' . $url2 . '">
                                        <input type="hidden" name = "_token" value = ' . csrf_token() . '>
                                        <input type="hidden" name = "user_id" value ="' . $user->id . '">
                                        <label>Reason</label>
                                        <textarea class = "form-control" name="reason" required></textarea>
                                        <br>
                                        <button type="submit" onclick="return confirm(\'Are you sure?\')" class="btn btn-danger">Reject KYC</button>
                                    </form>
                                    </div>
                                    <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                    </div>';
                } elseif ($user->registration_status == 'rejected') {
                    $url = route('approveMobileUserKYC');
                    $mar .= '<br><form method="POST" action="' . $url . '">
                            <input type="hidden" name = "_token" value = ' . csrf_token() . '>
                            <input type="hidden" name = "user_id" value ="' . $user->id . '">
                            <button type="submit" onclick="return confirm(\'Are you sure?\')" class="btn btn-sm btn-primary">Approve KYC</button>
                        </form>';
                } else {
                    $mar .= '<hr><button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#' . $user->id . 'KYCModal">
                        Reject
                    </button>';
                    $url2 = route('unapproveMobileUserKYC');
                    $mar .= '
                        <div class="modal fade" id="' . $user->id . 'KYCModal" tabindex="-1" aria-labelledby="' . $user->id . 'KYCModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                    <h5 class="modal-title" id="' . $user->id . 'KYCModalLabel"> Reject KYC For: ' . $user->name . '</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                    <form method="POST" action="' . $url2 . '">
                                        <input type="hidden" name = "_token" value = ' . csrf_token() . '>
                                        <input type="hidden" name = "user_id" value ="' . $user->id . '">
                                        <label>Reason</label>
                                        <textarea class = "form-control" name="reason" required></textarea>
                                        <br>
                                        <button type="submit" onclick="return confirm(\'Are you sure?\')" class="btn btn-danger">Reject KYC</button>
                                    </form>
                                    </div>
                                    <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>';
                }
                return $mar;
            })
            // ->addColumn('delete', function ($user) {
            //
            // })
            ->rawColumns(['kyc_status', 'surname', 'date', 'kyc_actions'])
            ->make(true);
    }
    public function approveWalkInCusKYC(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:walk_in_customers,id'
        ]);

        try {
            WalkInCustomer::where('id', $request->user_id)->update([
                'kyc_status' => 'approved'
            ]);
            return back()->with(['message' => 'User Approved', 'message_type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['exception' => $e]);
            return back()->with('message', "An error occured " . $e->getMessage())->withInput();
        }
    }

    public function unapproveWalkInCusKYC(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:walk_in_customers,id'
        ]);

        try {
            WalkInCustomer::where('id', $request->user_id)->update([
                'kyc_status' => 'rejected'
            ]);
            return back()->with(['message' => 'User Rejected', 'message_type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['exception' => $e]);
            return back()->with('message', "An error occured " . $e->getMessage())->withInput();
        }
    }

    public function approveAgentKYC(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        try {
            Dispatcher::where('user_id', $request->user_id)->update([
                'kyc_status' => 1
            ]);
            return back()->with(['message' => 'Agent Approved', 'message_type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['exception' => $e]);
            return back()->with('message', "An error occured " . $e->getMessage())->withInput();
        }
    }

    public function unapproveAgentKYC(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        try {
            Dispatcher::where('user_id', $request->user_id)->update([
                'kyc_status' => 0
            ]);
            return back()->with(['message' => 'Agent Rejected', 'message_type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['exception' => $e]);
            return back()->with('message', "An error occured " . $e->getMessage())->withInput();
        }
    }

    public function approveMobileUserKYC(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        try {
            Kyc::where('user_id', $request->user_id)->update([
                'status' => 'approved'
            ]);
            return back()->with(['message' => 'Mobile User Approved', 'message_type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['exception' => $e]);
            return back()->with('message', "An error occured " . $e->getMessage())->withInput();
        }
    }

    public function unapproveMobileUserKYC(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'reason' => 'required'
        ]);

        try {
            Kyc::where('user_id', $request->user_id)->update([
                'status' => 'rejected',
                'rejection_reason' => $request->reason
            ]);
            return back()->with(['message' => 'Mobile User Rejected', 'message_type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['exception' => $e]);
            return back()->with('message', "An error occured " . $e->getMessage())->withInput();
        }
    }

    public function tax_fetch(Request $request)
    {
        $codiceFiscale = $request->tax_code;
        $cus = WalkInCustomer::where('tax_code', $codiceFiscale)->first();
        $res = [];
        $res = [
            'isValid' => isset($cus) ? true : false,
            'dob' => isset($cus) ? $cus->birthDate : null,
            'gender' => isset($cus) ? $cus->gender : null,
        ];
        $res['surname'] = isset($cus) ? $cus->surname : '';
        $res['name'] = isset($cus) ? $cus->name : '';
        $res['doc_type'] = isset($cus) ? $cus->doc_type : '';
        $res['doc_num'] = isset($cus) ? $cus->doc_num : '';
        $res['kyc_status'] = isset($cus) ? $cus->kyc_status : '';
        $res['id'] = isset($cus) ? $cus->id : "";
        $res['phone'] = isset($cus) ? $cus->phone : "";
        $res['address'] = isset($cus) ? $cus->address : "";
        $res['email'] = isset($cus) ? $cus->email : '';
        $res['doc_back_img'] = isset($cus) && $cus->doc_back ? asset('uploads/' . $cus->doc_back) : '';
        $res['doc_front_img'] = isset($cus) && $cus->doc_front ? asset('uploads/' . $cus->doc_front) : '';

        echo json_encode($res);
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
     * @param  \App\Models\WalkInCustomer  $walkInCustomer
     * @return \Illuminate\Http\Response
     */
    public function show(WalkInCustomer $walkInCustomer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\WalkInCustomer  $walkInCustomer
     * @return \Illuminate\Http\Response
     */
    public function edit(WalkInCustomer $walkInCustomer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WalkInCustomer  $walkInCustomer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WalkInCustomer $walkInCustomer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WalkInCustomer  $walkInCustomer
     * @return \Illuminate\Http\Response
     */
    public function destroy(WalkInCustomer $walkInCustomer)
    {
        //
    }
}
