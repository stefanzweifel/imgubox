<?php

namespace ImguBox\Http\Controllers;

use Illuminate\Http\Request;
use ImguBox\Http\Controllers\Controller;
use ImguBox\Http\Requests;
use ImguBox\Http\Requests\PurgeFavoriteRequest;
use ImguBox\Jobs\DeleteFavorites;
use ImguBox\Jobs\FetchImages;
use ImguBox\Log;

class FavoritesController extends Controller
{
    /**
     * List all associated favorites with this account
     * @param  Request $request [description]
     * @return view
     */
    public function index(Request $request)
    {
        $favorites =  $request->user()->logs()->latest()->paginate(25);

        return view('favorites', compact('favorites'));
    }

    /**
     * Purge / delete a single Favorite
     * @param  PurgeFavoriteRequest $request
     * @param  Log                  $log
     * @return redirect
     */
    public function purge(PurgeFavoriteRequest $request, Log $log)
    {
        $log->delete();

        return redirect()->back()->withSuccess("Log for Favorite deleted.");
    }

    /**
     * Purge / delete all favorites of current account
     * Method dispatches a job to the queue
     * @param  Request $request
     * @return redirect
     */
    public function purgeAll(Request $request)
    {
        $this->dispatch((new DeleteFavorites($request->user()))->onQueue('low'));
        $message = "Deleted Logs for Favorites, but we haven't found a Token.";

        if ($request->user()->canFetchFavorites()) {
            $this->dispatch((new FetchImages($request->user()))->onQueue('low'));
            $message = "Queue download of your favorites.";
        }

        return redirect()->back()->withSuccess($message);
    }
}
