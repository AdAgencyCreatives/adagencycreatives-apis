<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Attachment\AttachmentResource;
use App\Mail\Message\UnreadMessage;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\FriendRequest;
use App\Models\Job;
use App\Models\JobAlert;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

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

    // public function index(Request $request)
    // {
    //     $job = json_decode(json_encode(array('category_id' => $request?->cid ?? 0)), FALSE);

    //     $categories = [];

    //     $category = Category::where('id', $job->category_id)->first();
    //     // return view('pages.test_data.index', ['data' => $category]);

    //     $group_categories = Category::where('group_name', $category->name)->get();

    //     if (count($group_categories) > 0) {
    //         for ($i = 0; $i < count($group_categories); $i++) {
    //             $categories[$i] = $group_categories[$i]->id;
    //         }
    //     } else {
    //         $categories[0] = $category->id;
    //     }

    //     // return view('pages.test_data.index', ['data' => $categories]);

    //     $data = JobAlert::with('user')->whereIn('category_id', $categories)->where('status', 1)->get();
    //     return view('pages.test_data.index', ['data' => $data]);
    // }

    public function index(Request $request)
    {

        $date_range = now()->subDay()->format('Y-m-d');

        $friendRequests = FriendRequest::where('status', 'pending')
            ->whereDate('updated_at', '=', $date_range)
            ->orderBy('receiver_id')->orderByDesc('updated_at')
            ->get();

        $bundle = [];
        $receivers = [];

        foreach ($friendRequests as $fr) {
            $receiver = $fr->receiver;
            $sender = $fr->sender;

            $sender->profile_picture = get_profile_picture($sender);

            if (array_key_exists($receiver->id, $bundle)) {
                $bundle[$receiver->id][count($bundle)] = $sender;
            } else {
                $bundle[$receiver->id] = array(0 => $sender);
                $receivers[count($receivers)] = $receiver;
            }
        }

        foreach ($receivers as $receiver) {
            $senders = $bundle[$receiver->id];

            $data = [
                'receiver' => $receiver,
                'data' => [
                    'recipient' => $receiver->first_name,
                    'senders' => $senders,
                    'multiple' => count($senders) > 0,
                    'APP_NAME' => env('APP_NAME'),
                    'FRONTEND_URL' => env('FRONTEND_URL'),
                ],
            ];
            return view('emails.friendship.request', ['data' => $data['data']]);
        }
    }

    public function testFr(Request $request)
    {
        $date_range = now()->subDay()->format('Y-m-d');

        $friendRequests = FriendRequest::where('status', 'pending')
            ->whereDate('updated_at', '=', $date_range)
            ->orderBy('receiver_id')->orderByDesc('updated_at')
            ->get();

        $bundle = [];
        $receivers = [];

        foreach ($friendRequests as $fr) {
            $receiver = $fr->receiver;
            $sender = $fr->sender;

            $sender->profile_picture = get_profile_picture($sender);

            if (array_key_exists($receiver->id, $bundle)) {
                $bundle[$receiver->id][count($bundle)] = $sender;
            } else {
                $bundle[$receiver->id] = array(0 => $sender);
                $receivers[count($receivers)] = $receiver;
            }
        }

        foreach ($receivers as $receiver) {
            $senders = $bundle[$receiver->id];
            dd($senders);
        }
    }

    public function testThumb(Request $request)
    {
        $user_id = $request->user_id;

        if ($user_id) {
            $user = User::where('uuid', $user_id)->first();

            // $attachment = Attachment::where(['user_id' => $user->id, 'resource_type' => 'profile_picture'])->first();

            $profile_picture  = getAttachmentBasePath() . $user->profile_picture->path;

            $info = pathinfo($profile_picture);
            // dd($info);

            $fname = $info['basename'];
            $thumbWidth = 150;
            $thumb_path = str_replace($info['filename'], $info['filename'] . "_thumb", $user->profile_picture->path);

            // dd($thumb_path);

            if (strtolower($info['extension']) == 'jpg') {

                // load image and get image size
                $img = \imagecreatefromjpeg("{$profile_picture}");
                $width = imagesx($img);
                $height = imagesy($img);

                // calculate thumbnail size
                $new_width = $thumbWidth;
                $new_height = floor($height * ($thumbWidth / $width));

                // create a new temporary image
                $tmp_img = imagecreatetruecolor($new_width, $new_height);

                // copy and resize old image into new image 
                imagecopyresized($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

                $temp = tmpfile();
                // save thumbnail into a temp file
                imagejpeg($tmp_img, $temp);

                $filePath = Storage::disk('s3')->put($thumb_path, $temp);

                fclose($temp);

                return '<img src="' . getAttachmentBasePath() . $thumb_path . '" />';
            }
        }
        return "No-UUID";
    }

    public function testThumbAtt(Request $request)
    {
        $user = User::where('id', $request->user_id)->first();

        // return new AttachmentResource($user->user_thumbnail);

        return new AttachmentResource(storeThumb($user, 'user_thumbnail'));
    }

    public function testJobClosed(Request $request)
    {
        $jobs = Job::where('status', 4)->orWhereDate('expired_at', '<', now())->get(['title', 'status', 'expired_at']);

        $today = now()->toDateString();
        $tomorrow = now()->addDay()->toDateString();
        $query = Job::where('status', 4)->orWhere(function ($q) use ($today, $tomorrow) {
            $q->whereDate('expired_at', '>=', $today)->where('expired_at', '<', $tomorrow);
        });

        return view('pages.test_data.index', ['data' => $query->toSql()]);
    }
}
