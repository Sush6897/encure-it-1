<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    //

    public function index(){
        $members = Member::all();

        return view('member', compact('members'));
    }

    public function getParent(){
        $members = Member::all();
        $options = '';
        foreach ($members as $m) {
            $options .= '<option value="' . $m["id"] . '">' . $m["name"] . '</option>';
        }

        return response()->json(['options' => $options]);

    }

    public function submitForm(Request $request){
        parse_str($_POST['formData'], $formData);
        $parentID = isset($formData['parent']) ? $formData['parent'] : 0;
        $memberName = $formData['name'];
    
            $data = [
                "name"=> $memberName,
                'parent_id'=> $parentID,
            ];

        $newMember= Member::create($data);

        return response()->json(['members' => $newMember]);

    }
}
