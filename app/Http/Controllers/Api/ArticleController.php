<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Articles;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Articles::select('articles.*','categories.id as id_category','categories.name')->join('categories','categories.id','articles.category_id')->latest()->paginate(4);
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
            'title' => ['required'],
            'content' => ['required'],
            'image' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(),Response::HTTP_UNPROCESSABLE_ENTITY);
        };
        try {
            if ($request->hasFile('image')) {
                $img = $request->file('image');
                $filename = date('His') . '.' . $img->getClientOriginalExtension();
                $img->move(public_path('/image'),$filename);
            }

            $add = new Articles;
            $add->title = $request->get('title');
            $add->content = $request->get('content');
            $add->image = $filename;
            $add->user_id = auth()->user()->id;
            $add->category_id = $request->get('category_id');
            $add->save();

            return response()->json([
                                        'message' => 'Berhasil Upload Artikel',
                                        'data' => $add,
                                    ],Response::HTTP_OK);
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
        //
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
        $update = Articles::findOrFail($id);
        if (!$update) {
            return response()->json([
                'message' => 'Tidak menemukan artikel',
            ], Response::HTTP_NOT_FOUND);
        };
        $validator = Validator::make($request->all(),[
            'title' => ['required'],
            'content' => ['required'],
            'image' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(),Response::HTTP_UNPROCESSABLE_ENTITY);
        };
        try {
            $update = Articles::findOrFail($id);
            $update->title = $request->get('title');
            $update->content = $request->get('content');
            $update->user_id = auth()->user()->id;
            $update->category_id = $request->get('category_id');
            if ($request->hasFile('image')) {
                $path_current = public_path() . '/image/';
                $file_old = $path_current.$update->image;
                if (unlink($file_old)) {
                    $img = $request->file('image');
                    $filename = date('His') . '.' . $img->getClientOriginalExtension();
                    $update->image = $filename;
                    $img->move(public_path('/image'),$filename);
                }
            }
            $update->update();
            return response()->json([
                                    'message' => 'Berhasil update Artikel',
                                    'data' => $update
                                    ],
                                    Response::HTTP_OK);
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
            $delete = Articles::find($id);
            if (!$delete) {
                return response()->json([
                    'message' => 'Tidak menemukan artikel',
                ], Response::HTTP_NOT_FOUND);
            }
            $img_path = public_path().'/image/'.$delete->image;
            if (File::delete($img_path)) {
                $delete->delete();
            };
            return response()->json(['message' => 'Data Berhasil dihapus.'],Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan.'], Response::HTTP_INTERNAL_SERVER_ERROR);

        }
    }
}
