<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;

use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //投稿一覧表示
        $posts = Auth::user()->posts()->orderBy('created_at', 'desc')->get();

        return view('posts.index',compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //作成ページ
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //投稿保存
        //バリデーション設定
        $request->validate([
            'title' =>'required',
            'content' =>'required',
         ]);

        $post = new Post();
        $post->title = $request->input('title');
        $post->content = $request->input('content');
        $post->user_id = Auth::id();
        $post->save();

        return redirect()->route('posts.index')->with('flash_message', '投稿が完了しました。');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        //投稿一覧詳細ページ表示
        return view('posts.show',compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        //編集ページ
        if($post->user_id !== Auth::id()){
            return redirect()->route('posts.index')->with('error_message', '不正なアクセスです。');
        }
        return view('posts.edit',compact('post'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        //更新機能
        if($post->user_id !== Auth::id()){
            return redirect()->route('posts.index')->with('error_message', '不正なアクセスです。');
        }

        //バリデーション設定
        $request->validate([
            'title' =>'required',
            'content' =>'required',
        ]);

        $post->title = $request->input('title');
        $post->content = $request->input('content');
        $post->save();
        
        return redirect()->route('posts.show',$post)->with('flash_message', '投稿を編集しました。');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        //削除機能
        if($post->user_id !== Auth::id()){
            return redirect()->route('posts.index')->with('error_message', '不正なアクセスです。');
        }
        $post->delete();
        return redirect()->route('posts.index')->with('flash_message', '投稿を削除しました。');
    }
}
