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
        $headline = Headline::orderBy('updated_at', 'desc')->get();

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
            'thumbnail' => 'required',
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
            if ($request->hasFile('thumbnail')) {

                $file = $request->file('thumbnail');
                $receipt = $request->file('thumbnail');
                $fileExtension = $receipt->getClientOriginalExtension();
                $fileName = time() . '_' . uniqid() . '.' . $fileExtension;
                $filePath = 'headline/' . $fileName;
                $upload_folder = public_path('headline/');

                // Move the uploaded file to the storage folder
                $file->move($upload_folder, $fileName);
            }

            Headline::create([
                'id' => $uniqueId,
                'title' => $request->title,
                'subtitle' => $request->content,
                'filename' => $fileName,
                'filepath' => $filePath
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
            'receipt' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust file validation rules as needed
            'content' => 'sometimes|string',
            'title' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $item = Headline::find($item_id);

        // Update title and content if they are present in the request
        if ($request->filled('title')) {
            $item->title = $request->title;
        }
        if ($request->filled('content')) {
            $item->subtitle = $request->content;
        }

        // Handle file upload if a new file is provided
        if ($request->hasFile('receipt')) {
            $file = $request->file('receipt');
            $fileExtension = $file->getClientOriginalExtension();
            $fileName = time() . '_' . uniqid() . '.' . $fileExtension;
            $filePath = 'headline/' . $fileName;
            $upload_folder = public_path('headline/');

            // Move the uploaded file to the storage folder
            $file->move($upload_folder, $fileName);

            // Delete the old file if it exists
            if ($item->filename) {
                $oldFilePath = public_path($item->filepath);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            // Update item with new file information
            $item->filename = $fileName;
            $item->filepath = $filePath;
        }

        // Save the changes
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
