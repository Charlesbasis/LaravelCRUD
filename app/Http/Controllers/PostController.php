<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // Searching Post   
    public function search(Request $request){
        // dd("ajit");
        $search = $request->search;
        $posts = Post::where(function($query) use($search){
            $query->where('name','like',"%$search%")
            ->orWhere('description','like',"%$search%")
            ->orWhere('hobbies','like',"%$search%");            
        })
        ->paginate(3);
        
        return view('welcome',compact('posts','search'));
    }
    
    // Creating Post
    public function create(){
        return view('create');
    }

    // Storing Post
    public function ourfilestore(Request $request){

        $validated = $request->validate([
            'name' => 'required',
            'description' => 'required',
            'image' => 'nullable|mimes:jpg,bmp,png',
            'hobbies' => 'required'
        ]);

        // Uploading Image
        $imageName = null;
        if(isset($request->image)){
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('images'), $imageName);
        }        

        // Adding New Post
        $post = new Post;

        $post->name = $request->name;
        $post->description = $request->description;
        $post->image = $imageName;
        $post->hobbies = json_encode($request->hobbies);

        $post->save();

        return redirect()->route('home')->with('success', 'Your Post has been Created!');
    }

    public function editData($id){
        $post = Post::findOrFail($id);
        return view('edit', ['ourPost' => $post]);
    }

    public function updateData($id, Request $request){
        $post = Post::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required',
            'description' => 'required',
            'image' => 'nullable|mimes:jpg,bmp,png',
            'hobbies' => 'required'
        ]);
        
        // Updating Post

        $post->name = $request->name;
        $post->description = $request->description;        
        $post->hobbies = json_encode($request->hobbies);
        
        // Updating Image
        if(isset($request->image)){
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $post->image = $imageName;
        }   

        $post->save();

        return redirect()->route('home')->with('success', 'Your Post has been Updated!');
    }

    public function deleteData($id){
        $post = Post::findOrFail($id);
        $post->delete();

        return redirect()->route('home')->with('success', 'Your Post has been Deleted!');
    }

}
