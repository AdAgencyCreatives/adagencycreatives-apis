<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\Message\UnreadMessage;
use App\Models\Category;
use App\Models\Job;
use App\Models\JobAlert;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class TestDataController extends Controller
{
    // public function index(Request $request)
    // {

    //     $view = $request?->view ?? '';

    //     $data = [];

    //     $date_range = date(now()->subDay());

    //     $unreadQuery = Message::whereDate('created_at', '<=', $date_range)
    //         ->whereIn('type', ['private', 'job'])
    //         ->whereNull('read_at')
    //         ->select('receiver_id', DB::raw('count(*) as message_count'))
    //         ->groupBy('receiver_id');

    //     $unreadMessages = $unreadQuery->get();

    //     foreach ($unreadMessages as $unreadMessage) {

    //         if (!$unreadMessage?->receiver) {
    //             continue;
    //         }

    //         $recipient = $unreadMessage->receiver;
    //         $unreadMessageCount = $unreadMessage->message_count;

    //         // Get the oldest contacts who sent messages to the user
    //         $oldestmessages = Message::select('sender_id', DB::raw('MAX(created_at) as max_created_at'))
    //             ->where('receiver_id', $unreadMessage->receiver_id)
    //             ->whereIn('type', ['private', 'job'])
    //             ->whereNull('read_at')
    //             ->whereDate('created_at', '<=', $date_range)
    //             ->groupBy('sender_id')
    //             ->take(5)
    //             ->orderBy('max_created_at', 'desc')
    //             ->with('sender')
    //             ->get();

    //         $recent_messages = [];

    //         foreach ($oldestmessages as $msg) {
    //             $recent_messages[] = [
    //                 'name' => $msg->sender->first_name,
    //                 'profile_url' => env('FRONTEND_URL') . '/profile/' . $msg->sender->id,
    //                 'profile_picture' => get_profile_picture($msg->sender),
    //                 'message_time' => \Carbon\Carbon::parse($msg->max_created_at)->diffForHumans(),
    //             ];
    //         }

    //         array_push($data, [
    //             'recipient_id' => $recipient->id,
    //             'recipient' => $recipient->first_name,
    //             'unread_message_count' => $unreadMessageCount,
    //             'recent_messages' => $recent_messages,

    //         ]);
    //     }

    //     if (strlen($view) > 0 && is_numeric($view)) {
    //         return new UnreadMessage($data[$view]);
    //     }

    //     return view('pages.test_data.index', ['data' => $data]);
    // }

    public function index(Request $request)
    {
        $job = json_decode(json_encode(array('category_id' => $request?->cid ?? 0)), FALSE);

        $categories = '';

        $category = Category::where('id', $job->category_id)->first();
        // return view('pages.test_data.index', ['data' => $category]);

        $group_categories = Category::where('group_name', $category->name)->get();

        if (count($group_categories) > 0) {
            for ($i = 0; $i < count($group_categories); $i++) {
                $categories .= ($i == 0 ? '' : ',') . $group_categories[$i]->id;
            }
        } else {
            $categories = $category->id;
        }

        return view('pages.test_data.index', ['data' => $categories]);

        $data = JobAlert::with('user')->where('category_id', $job->category_id)->where('status', 1)->get();
        return view('pages.test_data.index', ['data' => $data]);
    }
}