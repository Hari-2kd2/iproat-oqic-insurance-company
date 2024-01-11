<?php

namespace App\Http\Controllers\Api;

use App\Model\Claim;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ClaimController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'status' => true,
            'data' => Claim::with('employee')->get()
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required',
            'remarks' => 'required',
            'attachments' => 'required|mimes:jpeg,jpg,png,csv,txt,pdf|max:512',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->getMessageBag()->first()
            ], 200);
        }

        $attachmentName = NULL;

        if ($request->attachments) {
            $extension = $request->attachments->getClientOriginalExtension();
            $attachmentName = Str::random(10) . date('YmdHis') . "." . $extension;
            $request->attachments->move(public_path('storage/attachments/'), $attachmentName);
        }

        $data =  Claim::create([
            'remarks' => $request->remarks,
            'attachments' => $attachmentName,
            'employee_id' => $request->employee_id
        ]);

        return response()->json([
            'status' => true,
            'message' => 'success',
            'data' => $data
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
