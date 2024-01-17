<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Url;
use Illuminate\Http\Response;

class UrlshortenerController extends Controller
{
    public function store(Request $request)
    {
		$request->validate([
            'url' => 'required|url',
        ]);

        $old_url = $request->input('url');
        $hash = substr(md5($old_url), 0, 6);
        $new_url = 'example.com/' . $hash;

        $existingUrl = Url::where('old_url', $old_url)->first();

        if ($existingUrl) {							// duplication check
            $new_url = $existingUrl->new_url;
        } else {									// Save the new URL
            Url::create([
                'old_url' => $old_url,
                'new_url' => $new_url,
            ]);
        }
		return $new_url;
    }

    public function redirectToOriginal($hash)
    {
        $url = Url::where('new_url', 'example.com/' . $hash)->first();

        if ($url) {
            return redirect($url->old_url);
        }
        abort(404);
    }

}
