<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreBlogRequest;
use App\Http\Requests\UpdateBlogRequest;
use App\Http\Requests\ChangeBlogStatusRequest;


class BlogController extends Controller
{
    /**
     * Get all blogs (with optional search)
     */
    public function index(Request $request)
    {
        $query = Blog::with('user');

        // Apply search if title param exists
        if ($request->has('title') && $request->title !== '') {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        $blogs = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json($blogs);
    }

    /**
     * Store a new blog
     */
    public function store(StoreBlogRequest $request)
    {
        $blog = Blog::create([
            'title' => $request->title,
            'content' => $request->content,
            'status' => $request->status,
            'created_by' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Blog created successfully',
            'data' => $blog
        ], 201);
    }


    /**
     * Get a specific blog
     */
    public function show($id)
    {
        $blog = Blog::with('user')->find($id);

        if (!$blog) {
            return response()->json([
                'success' => false,
                'message' => 'Blog not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $blog
        ]);
    }

    /**
     * Update a blog
     */
    public function update(UpdateBlogRequest $request, $id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return response()->json([
                'success' => false,
                'message' => 'Blog not found'
            ], 404);
        }

        $blog->update($request->only(['title', 'content', 'status']));

        return response()->json([
            'success' => true,
            'message' => 'Blog updated successfully',
            'data' => $blog
        ]);
    }


    /**
     * Change blog status
     */
    public function changeStatus(ChangeBlogStatusRequest $request, $id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return response()->json([
                'success' => false,
                'message' => 'Blog not found'
            ], 404);
        }

        $blog->status = $request->status;
        $blog->save();

        return response()->json([
            'success' => true,
            'message' => 'Blog status updated successfully',
            'data' => $blog
        ]);
    }


    /**
     * Archive a blog (soft delete)
     */
    public function archive($id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return response()->json([
                'success' => false,
                'message' => 'Blog not found'
            ], 404);
        }

        $blog->delete();

        return response()->json([
            'success' => true,
            'message' => 'Blog archived successfully'
        ]);
    }
}
