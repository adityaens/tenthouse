<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use Illuminate\Support\Str;
use App\Models\Group;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    private $debugMode;

    public function __construct()
    {
        $this->debugMode = config('constants.debug_mode');
    }
    
    public function index(Request $request)
    {
        $groups=Group::where('status',1)->pluck('name','id');
        $query = User::with('group')->where(['roleId' => 2]);

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
    
        if ($request->filled('discount')) {
            $query->where('group_id', 'like', '%' . $request->discount . '%');
        }
    
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
    
        if ($request->filled('created_on')) {
            $query->whereDate('created_at', '=', $request->created_on);
        }
    
        $users = $query->paginate(10)->appends($request->query());
    
        return view('admin.customers.index', compact('users', 'groups'));
    }
    public function create()
    {

        $groups = Group::where('status',1)->pluck('name', 'id');
        return view('admin.customers.create', ['groups' => $groups]);
    }
    public function store(CustomerRequest $request)
    {
       
        
        $plainPassword = Str::random(12);
        $hashedPassword = Hash::make($plainPassword);
        $user=User::create([
            'roleId' => 2,
            'group_id' => $request->group,
            'email' => $request->email,
            'password' => $hashedPassword,
            'name' => $request->name,
            'mobile' => $request->mobile,
            'address' => $request->address,
            'status' => $request->status ? $request->status : 1
        ]);
        if($user){
            return redirect()->route('admin.user.index')->with('success','Customer created successfully');
        }else{
            return redirect()->route('admin.user.index')->with('error','Something went wrong!!');
        }
    }
    public function edit(Request $request) {
        $groups=Group::where('status',1)->pluck('name','id');
        $user=User::where('userId',$request->id)->first();
  
        return view('admin.customers.edit', compact('groups','user'));
    }
    public function update(CustomerRequest $request) {
     
        $user=User::where('userId', $request->id)->first();
        $user->update([            
            'group_id' => $request->group,
            'email' => $request->email,            
            'name' => $request->name,
            'mobile' => $request->mobile,
            'address' => $request->address,
            'status' => $request->status
        ]);
        if($user){
          return redirect()->route('admin.user.index')->with('success','Customer updated successfully');
        }else{
          return redirect()->route('admin.user.index')->with('error','Something went wrong');
        }
        
    }
    public function destroy($id) {     
        $user= User::where('roleId',2)->find($id);
        if($user){
          $user->delete();
          return redirect()->route('admin.user.index')->with('success','Customer deleted successfully');
        }
        return redirect()->route('admin.user.index')->with('error','Something went wrong');
  
      }

      public function getUsersList(Request $request)
    {
        $customerKeyword = $request->input('customerKeyword');
        try {

            if(empty($customerKeyword)) {
                return response()->json([
                    'success' => false,
                    'error' => showErrorMessage($this->debugMode, 'Nothing to search.')
                ]);
            }

            $customers = User::select([
                'userId',
                'name'
            ])
            ->where('status', ACTIVE)
            ->where('roleId', CUSTOMER)
            ->where('name', 'like', $customerKeyword .'%')
            ->get();

            return response()->json([
                'success' => true,
                'customers' => $customers
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => showErrorMessage($this->debugMode, $e->getMessage())
            ]);
        }
    }
}
