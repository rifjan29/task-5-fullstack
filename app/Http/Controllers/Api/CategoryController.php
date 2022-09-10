<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categories;
use Exception;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Categories::paginate(10);
        if (!$data) {
            return response()->json(['message' => 'Terjadi kesalahan.'],Response::HTTP_UNPROCESSABLE_ENTITY);

        }
        return response()->json(['data' => $data],Response::HTTP_OK);
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
        $validator = Validator::make($request->all(),[
            'name' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(),Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        try {
            $add = new Categories;
            $add->name = $request->get('name');
            $add->user_id = auth()->user()->id;
            $add->save();

            return response()->json(
                                    [
                                        'message' => 'Berhasil menambahkan data.',
                                        'data' => $add
                                    ],
                                    Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data  = Categories::find($id);
        if (!$data) {
            return response()->json(['message' => 'Terjadi kesalahan.'],Response::HTTP_UNPROCESSABLE_ENTITY);

        }
        return response()->json(['data' => $data],Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
        $validator = Validator::make($request->all(),[
            'name' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Terjadi kesalahan'],Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        try {
            $update = Categories::find($id);
            $update->name = $request->get('name');
            $update->user_id = auth()->user()->id;
            $update->update();

            return response()->json(
                                    [
                                        'message' => 'Berhasil update Category',
                                        'data' => $update,

                                    ],Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $delete = Categories::findOrFail($id);
            $delete->delete();
            return response()->json(['message' => 'Data berhasil dihapus'], Response::HTTP_ACCEPTED);
        } catch (Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan.'], Response::HTTP_INTERNAL_SERVER_ERROR);

        }
    }
}
