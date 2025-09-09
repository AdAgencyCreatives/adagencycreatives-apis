<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Article;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Artisan;
class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.articles.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.articles.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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

    /**
     * Display the view for managing featured articles.
     *
     * @return \Illuminate\View\View
     */
    public function featuredArticles()
    {
        return view('pages.featured_articles.index');
    }

    /**
     * Update the sort order of multiple featured articles.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOrder(Request $request)
    {
        $order = $request->input('order');
        foreach ($order as $index => $itemId) {
            Article::where('id', $itemId)->update(['order' => $index + 1]);
        }

        Cache::forget('featured_articles');

        return response()->json(['message' => 'Order updated successfully']);
    }

    /**
     * Update the sort order of a single featured article.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOrderSingle(Request $request)
    {
        $order = $request->input('sort_order');
        $article_id = $request->input('article_id');

        Article::where('id', $article_id)->update(['order' => $order]);

        Cache::forget('featured_articles');

        return response()->json(['message' => 'Order updated successfully', 'status' => 200]);
    }

    /**
     * Update the count of featured articles on the homepage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update_articles_count(Request $request)
    {
        settings($request->only('article_count_homepage'));

        Cache::forget('featured_articles');
        Artisan::call('cache:clear');
        Session::flash('success', 'Homepage articles count updated successfully');
        return redirect()->back();
    }
}
