<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    
    public function index()
    {
        return view('index');
    }
  
    public function store(Request $request)
    { 
        $file = $request->file('avatar');
		$fileName = time() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public/images', $fileName);
        
        $empData = [
            'first_name' => $request->fname,
            'last_name' => $request->lname,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->post,
            'profile_image' => $fileName
        ];
        
        Employee::create($empData);
        return response()->json([
            'status'=>200
        ]);
    }

    public function fetchAll()
    {
        $emps = Employee::all();
		$output = '';
		if ($emps->count() > 0) {
			$output .= '<table class="table table-striped table-sm text-center align-middle">
            <thead>
              <tr>
                <th>ID</th>
                <th>Avatar</th>
                <th>Name</th>
                <th>E-mail</th>
                <th>Post</th>
                <th>Phone</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>';
			foreach ($emps as $emp) {
				$output .= '<tr>
                <td>' . $emp->id . '</td>
                <td><img src="storage/images/' . $emp->profile_image . '" width="50" class="img-thumbnail rounded-circle"></td>
                <td>' . $emp->first_name . ' ' . $emp->last_name . '</td>
                <td>' . $emp->email . '</td>
                <td>' . $emp->role . '</td>
                <td>' . $emp->phone . '</td>
                <td>
                  <a href="#" id="' . $emp->id . '" class="text-success mx-1 editIcon" data-bs-toggle="modal" data-bs-target="#editEmployeeModal"><i class="bi-pencil-square h4"></i></a>

                  <a href="#" id="' . $emp->id . '" class="text-danger mx-1 deleteIcon"><i class="bi-trash h4"></i></a>
                </td>
              </tr>';
			}
			$output .= '</tbody></table>';
			echo $output;
		} else {
			echo '<h1 class="text-center text-secondary my-5">No record present in the database!</h1>';
		}
    }

    public function editEmployee(Request $request)
    {
        $id = $request->id;
        $emp = Employee::find($id);
        return response()->json($emp);
    }

    public function updateEmployee(Request $request)
    {
        $fileName = '';
		$emp = Employee::find($request->emp_id);
       
        //print $request->hasFile('avatar');
		if ($request->hasFile('avatar')) {
			$file = $request->file('avatar');
			$fileName = time() . '.' . $file->getClientOriginalExtension();
			$file->storeAs('public/images', $fileName);
			if ($emp->profile_image) {
				Storage::delete('public/images/' . $emp->profile_image);
			}
		} else {
			$fileName = $request->emp_avatar;
		}

		$empData = [
            'first_name' => $request->fname,
             'last_name' => $request->lname, 
             'email' => $request->email, 
             'phone' => $request->phone, 
             'role' => $request->post, 
             'profile_image' => $fileName];

		$emp->update($empData);
		return response()->json([
			'status' => 200,
		]);
    }

    public function deleteEmployee(Request $request) {
		$id = $request->id;
		$emp = Employee::find($id);
		if (Storage::delete('public/images/' . $emp->profile_image)) {
			Employee::destroy($id);
		}
	}
}
