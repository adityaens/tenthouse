<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\GroupRequest;
use App\Models\Group;
use Illuminate\Http\Request;


class GroupController extends Controller
{
    private $debugMode;

    public function __construct()
    {
        $this->debugMode = config('constants.debug_mode');
    }

    public function index(Request $request)
    {
        $searchName = $request->get('name', NULL);        
        $searchStatus = $request->get('status', NULL);
        $searchCreatedAt = $request->get('created_at', NULL);



        $query = Group::select([
            'id',
            'name',
            'description',
            'status',
            'created_at'
        ]);

        // Apply filters conditionally
        if (!empty($searchName)) {
            $query->where('name', 'like', '%' . $searchName . '%');
        }
       

        if (isset($searchStatus)) {
            $query->where('status', $searchStatus);
        }

        if (!empty($searchCreatedAt)) {
            $query->whereDate('created_at', $searchCreatedAt);
        }

        $groups = $query->orderBy('id', 'DESC')->paginate(PER_PAGE);
        return view('admin.groups.index', [
            'groups' => $groups
        ]);
    }
    public function create()
    {
        return  view('admin.groups.create');
    }
    public function store(GroupRequest $request, Group $group)
    {
       
        $isSaved = false;

        try {
            $group->name = $request->input('name');           
            $group->description = $request->input('description') ? $request->input('description') : '';
            $group->status = $request->input('status') ? $request->input('status') : 1; 

            $isSaved = $group->save();

            if (!$isSaved) {
                return redirect()->back()->with('error', showErrorMessage($this->debugMode, 'Group not saved.'));
            }
            } catch (Exception $e) {
                return redirect()->back()->with('error', showErrorMessage($this->debugMode, $e->getMessage()));
            }
            return redirect()->route('admin.products.groups.index')->with('success', 'Group added successfully');

    }

    public function edit($id)
    {
        $group=Group::where('id',$id)->first();
        return  view('admin.groups.edit',['group' => $group]);
    }
    public function update(GroupRequest $request) {
        $group= Group::find($request->id);
        if($group){
            $group->update([
                'name' => $request->name,                
                'description' => $request->description,
                'status' => $request->status,
            ]);
            return redirect()->route('admin.products.groups.index')->with('success','Group updated successfully');
        }
        return redirect()->route('admin.products.groups.edit')->with('error','Something went wrong');
    }
    public function destroy($id) {
      $group= Group::find($id);
      if($group){
        $group->delete();
        return redirect()->route('admin.products.groups.index')->with('success','Group deleted successfully');
      }
      return redirect()->route('admin.products.groups.index')->with('error','Something went wrong');

    }
}
