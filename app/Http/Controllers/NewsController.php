<?php

namespace App\Http\Controllers;

use App\Models\Headline;
use App\Models\News_feed;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use NewsFeed;

class NewsController extends Controller
{
    public function index()
    {
        $newsFeed = News_feed::orderBy('created_at', 'desc')->get();
        $headline = Headline::all();
        return view('news-feed.index', ['newsFeed' => $newsFeed, 'headline' => $headline]);
    }
    
    public function create()
    {
        return view('news-feed.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $uniqueId = hexdec(substr(uniqid(), 0, 8));

        while (News_feed::where('id', $uniqueId)->exists()) {
            $uniqueId = hexdec(substr(uniqid(), 0, 8));
        }

        try {
            // Store the file if it is provided
            // if ($request->hasFile('img')) {
            //     $headline = $request->file('img');
            //     $fileExtension = $headline->getClientOriginalExtension();
            //     $fileName = time() . '_' . uniqid() . '.' . $fileExtension;
            //     $filePath = 'img/' . $fileName;
            //     $upload_folder = public_path('img/');

            //     // Move the uploaded file to the storage folder
            //     $headline->move($upload_folder, $fileName);

            News_feed::create([
                'id' => $uniqueId,
                'title' => $request->title,
                'content' => $request->content,
                'date_released' => date('Y-m-d'),
                'created_by' => Auth::id()
            ]);
        } catch (Exception $e) {
            //do nothing
        }

        return redirect()->route('manage-news')->with('success', 'News feed created successfully.');
    }

    public function get_id_headline($id)
    {
        // Get the Timesheet records between the start and end dates
        $itemData = Headline::find($id);

        return response()->json($itemData);
    }

    public function updateHeadlineData(Request $request, $item_id)
    {
        $validator = Validator::make($request->all(), [
            'receipt' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $item = Headline::find($item_id);

        if ($request->hasFile('receipt')) {

            // Delete the file from the public folder if it exists
            if ($item->exists()) {
                $filePath = public_path($item->filepath);

                // Delete the file
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $file = $request->file('receipt');
            $receipt = $request->file('receipt');
            $fileExtension = $receipt->getClientOriginalExtension();
            $fileName = time() . '_' . uniqid() . '.' . $fileExtension;
            $filePath = 'img/' . $fileName;
            $upload_folder = public_path('img/');

            // Move the uploaded file to the storage folder
            $file->move($upload_folder, $fileName);

            $item->filename = $fileName;
            $item->filepath = $filePath;
        }
        $item->save();

        return response()->json(['success' => 'Item updated successfully.']);
    }

    public function edit_post($id)
    {
        $newsFeed = News_feed::find($id);
        return view('news-feed.edit_post', ['newsFeed' => $newsFeed]);
    }

    public function update_post(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            Session::flash('failed',"Error Database has Occured! Failed to create request! You need to fill all the required fields");
            return redirect()->back();
        }

        try {
            $newsFeed = News_feed::find($id);
            $newsFeed->content = $request->content;
            $newsFeed->title = $request->title;
            $newsFeed->save();
        } catch (Exception $e) {
            //do nothing
        }

        return redirect()->route('manage-news')->with('success', 'News feed updated successfully.');
    }

    public function delete_post($id)
    {
        $newsFeed = News_feed::find($id);
        $newsFeed->delete();

        return redirect()->route('manage-news')->with('success', 'News feed deleted successfully.');
    }
}
