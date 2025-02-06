<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use Illuminate\Support\Str;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function index(Request $request)
{
    // Fetch all active groups (id => name)
    $groups = Group::where('status', 1)->pluck('name', 'id');
    
    // Query users with their associated groups
    $query = User::with('groups')->where(['roleId' => 2]);

    // Apply search filters
    if ($request->filled('name')) {
        $query->where('name', 'like', '%' . $request->name . '%');
    }
    
    if ($request->filled('group')) {
        $query->whereHas('groups', function ($query) use ($request) {
            $query->whereIn('group_id', (array)$request->group);
        });
    }
    
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }
    
    if ($request->filled('created_on')) {
        $query->whereDate('created_at', '=', $request->created_on);
    }

    // Retrieve users and paginate
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
            'email' => $request->email,
            'password' => $hashedPassword,
            'name' => $request->name,
            'mobile' => $request->mobile,
            'address' => $request->address,
            'status' => $request->status ? $request->status : 1
        ]);
            if ($user) {                
                if ($request->has('group')) {
                    $user->groups()->attach($request->group);
                }
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
         $user->groups()->detach();
          $user->delete();
          return redirect()->route('admin.user.index')->with('success','Customer deleted successfully');
        }
        return redirect()->route('admin.user.index')->with('error','Something went wrong');
  
      }
}
