<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Agency\AgencyCollection;
use App\Http\Resources\Attachment\AttachmentResource;
use App\Http\Resources\Creative\LoggedinCreativeCollection;
use App\Http\Resources\Job\JobResource;
use App\Http\Resources\Post\TrendingPostCollection;
use App\Jobs\SendEmailJob;
use App\Mail\Account\ProfileCompletionCreativeReminder;
use App\Mail\Account\ProfileCompletionAgencyReminder;
use App\Mail\Application\JobClosed;
use App\Mail\Application\NewApplication;
use App\Mail\Job\NoJobPostedAgencyReminder;
use App\Mail\Message\UnreadMessage;
use App\Models\Agency;
use App\Models\Application;
use App\Models\ApplicationEmailLog;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\Creative;
use App\Models\FriendRequest;
use App\Models\Job;
use App\Models\JobAlert;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

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

    public function testThumbResampled(Request $request)
    {
        $user_id = $request->user_id;
        $thumbWidth = 250;

        if ($user_id) {
            $user = User::where('uuid', $user_id)->first();

            $original_image  = getAttachmentBasePath() . $user?->portfolio_website_preview?->path;
            $extension =  $user?->portfolio_website_preview?->extension;

            if (strtolower($extension) == 'png') {
                $img = \imagecreatefrompng("{$original_image}");
            } else if (strtolower($extension) == 'bmp') {
                $img = \imagecreatefrombmp("{$original_image}");
            } else if (strtolower($extension) == 'gif') {
                $img = \imagecreatefromgif("{$original_image}");
            } else {
                $img = \imagecreatefromjpeg("{$original_image}");
            }

            // get image size
            $width = imagesx($img);
            $height = imagesy($img);

            // calculate thumbnail size
            if ($width >= $height) {
                $new_width = $thumbWidth;
                $new_height = floor($height * ($thumbWidth / $width));
            } else {
                $new_height = $thumbWidth;
                $new_width = floor($width * ($thumbWidth / $height));
            }

            // create a new temporary image
            $tmp_img = imagecreatetruecolor($new_width, $new_height);

            if (strtolower($extension) == 'png') {
                imagefill($tmp_img, 0, 0, imagecolorallocate($tmp_img, 255, 255, 255));
                imagealphablending($tmp_img, TRUE);
            }

            // copy and resize old image into new image 
            imagecopyresampled($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

            imagefilter($tmp_img, IMG_FILTER_CONTRAST, -5);

            ob_start();
            // save thumbnail into a temp file
            imagejpeg($tmp_img, null, 100);

            $imageData = ob_get_contents();
            ob_end_clean();

            imagedestroy($tmp_img);
            imagedestroy($img);

            return '<img src="' . 'data:image/jpeg;charset=utf-8;base64,' . (strlen($original_image) > 0 ? base64_encode($imageData) : '') . '" />';
        }
        return "No-UUID";
    }

    public function testJobClosed(Request $request)
    {
        $apply_type = $request->apply_type ?? "Internal";

        if (env('APP_ENV') != 'production') {
            Job::whereIn('id', [171])->update(['apply_type' => $apply_type, 'expired_at' => now()->subDay(), 'updated_at' => now()->subDay(), 'status' => 3]);
        }

        $yesterday = now()->subDay()->toDateString();
        $today = now()->toDateString();

        $jobs = Job::where(function ($query) use ($yesterday, $today) {
            $query->where(function ($q) use ($yesterday, $today) {
                $q->where('status', 4)->whereDate('updated_at', '>=', $yesterday)->where('updated_at', '<', $today);
            })->orWhere(function ($q) use ($yesterday, $today) {
                $q->whereDate('expired_at', '>=', $yesterday)->where('expired_at', '<', $today);
            });
        })->with('applications', function ($query) {
            $query->where('status', 0);
        });

        if ($apply_type != '') {
            $jobs = $jobs->where('apply_type', $apply_type);
        }

        $jobs = $jobs->get();

        $data = [];
        for ($i = 0; $i < count($jobs); $i++) {
            $job = $jobs[$i];

            $author = User::find($job->user_id);
            $agency = $author->agency;

            $agency_name = $job?->agency_name ?? ($agency?->name ?? '');
            $agency_profile = $job?->agency_website ?? (in_array($author->role, ['agency']) ? sprintf("%s/agency/%s", env('FRONTEND_URL'), $agency?->slug) : '');

            $job_url = sprintf('%s/job/%s', env('FRONTEND_URL'), $job->slug);

            for ($j = 0; $j < count($job->applications); $j++) {
                $application = $job->applications[$j];

                $data[] = array(
                    'receiver' =>  $application->user->email,
                    'recipient_name' => $application->user->first_name,
                    'job_title' => $job->title,
                    'job_url' => $job_url,
                    'agency_name' => $agency_name,
                    'agency_profile' => $agency_profile,
                    'apply_type' => $job->apply_type,
                    'show_test_links' => 'yes'
                );
            }
        }

        for ($k = 0; $k < count($data); $k++) {
            $item = $data[$k];

            // SendEmailJob::dispatch([
            //     'receiver' => $item['receiver'],
            //     'data' => [
            //         'recipient' => $item['recipient_name'],
            //         'job_title' => $item['job_title'],
            //         'agency_name' => $item['agency_name'],
            //     ],
            // ], 'job_closed_email');
            return new JobClosed([
                'recipient_name' => $item['recipient_name'],
                'job_title' => $item['job_title'],
                'job_url' => $item['job_url'],
                'agency_name' => $item['agency_name'],
                'agency_profile' => $item['agency_profile'],
                'apply_type' => $item['apply_type'],
                'show_test_links' => $item['show_test_links'],
            ]);
        }

        // return view('pages.test_data.index', ['data' => $data]);
    }

    public function testNewApplication(Request $request)
    {
        $json_data = File::get(resource_path('ignore/new-application.json'));
        $data = json_decode($json_data, true);

        return new NewApplication($data['data']['data']);
        // return "<pre>" . print_r($data['data']['data'], true) . "</pre>";
    }

    public function testCreativeSearch(Request $request) //Agency with No package
    {
        $search = $request->search;

        $agency_user_id = $request->user_id;
        $agency_user_applicants = [];
        if (isset($agency_user_id)) {
            $agency_user_applicants = array_unique(Application::whereHas('job', function ($query) use ($agency_user_id) {
                $query->where('user_id', $agency_user_id);
            })->pluck('user_id')->toArray());
        }

        $exact_search_ids = $this->getSearch1CreativeIDs($search, 'exact-match');
        $contains_search_ids = $this->getSearch1CreativeIDs($search, 'contains');

        $combinedCreativeIds = array_merge($exact_search_ids, $contains_search_ids);
        $combinedCreativeIds = array_values(array_unique($combinedCreativeIds, SORT_NUMERIC));
        $rawOrder = 'FIELD(id, ' . implode(',', $combinedCreativeIds) . ')';

        $creatives = Creative::whereIn('id', $combinedCreativeIds)
            ->whereHas('user', function ($query) use ($agency_user_applicants) {
                $query->where('status', 1)
                    ->where(function ($q) use ($agency_user_applicants) {
                        $q->where('is_visible', 1)
                            ->orWhere(function ($q1) use ($agency_user_applicants) {
                                $q1->where('is_visible', 0)
                                    ->whereIn('user_id', $agency_user_applicants);
                            });
                    });
            })
            ->orderByRaw($rawOrder)
            ->orderByDesc('is_featured')
            ->orderBy('created_at')
            ->paginate($request->per_page ?? config('global.request.pagination_limit'))
            ->withQueryString();

        return new LoggedinCreativeCollection($creatives);
    }

    public function getSearch1CreativeIDs($search, $match_type = 'contains')
    {
        if (!isset($match_type) || strlen($match_type) == 0) {
            $match_type = 'contains';
        }

        $wildCardStart = '%';
        $wildCardEnd = '%';

        if ($match_type == 'starts-with') {
            $wildCardStart = '';
        } elseif ($match_type == 'exact-match') {
            $wildCardStart = '';
            $wildCardEnd = '';
        }


        $terms = explode(',', $search);

        // Search via First or Last Name
        $sql = 'SELECT cr.id FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id' . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = trim($terms[$i]);
            // Check if the term contains a space or underscore (full name or both names)
            if (strpos($term, ' ') !== false || strpos($term, '_') !== false) {
                $separator = strpos($term, ' ') !== false ? ' ' : '_';
                $names = explode($separator, $term);
                $firstName = trim($names[0]);
                $lastName = trim($names[1]);

                $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "CONCAT(ur.first_name, ' ', ur.last_name) LIKE '" . $wildCardStart . "$firstName% $lastName" . $wildCardEnd . "'" . "\n";
                $sql .= " OR CONCAT(ur.last_name, ' ', ur.first_name) LIKE '" . $wildCardStart . "$lastName% $firstName" . $wildCardEnd . "'" . "\n";

                // Additional check for reverse order
                $sql .= " OR CONCAT(ur.first_name, ' ', ur.last_name) LIKE '" . $wildCardStart . "$lastName% $firstName" . $wildCardEnd . "'" . "\n";
                $sql .= " OR CONCAT(ur.last_name, ' ', ur.first_name) LIKE '" . $wildCardStart . "$firstName% $lastName" . $wildCardEnd . "'" . "\n";
            } else {
                // Search by individual terms
                $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "ur.first_name LIKE '" . $wildCardStart . "$term" . $wildCardEnd . "'" . "\n";
                $sql .= " OR ur.last_name LIKE '" . $wildCardStart . "$term" . $wildCardEnd . "'" . "\n";
            }

            break; //Because we only allow single term search
        }

        $sql .= 'UNION DISTINCT' . "\n";

        // Search via City Name
        $sql .= 'SELECT cr.id FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id INNER JOIN addresses ad ON ur.id = ad.user_id INNER JOIN locations lc ON lc.id = ad.city_id' . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(lc.parent_id IS NOT NULL AND lc.name LIKE '" . $wildCardStart . '' . trim($term) . '' . $wildCardEnd . "')" . "\n";
            break; //Because we only allow single term search
        }

        $sql .= 'UNION DISTINCT' . "\n";

        // Search via State Name
        $sql .= 'SELECT cr.id FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id INNER JOIN addresses ad ON ur.id = ad.user_id INNER JOIN locations lc ON lc.id = ad.state_id' . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(lc.parent_id IS NULL AND lc.name LIKE '" . $wildCardStart . '' . trim($term) . '' . $wildCardEnd . "')" . "\n";
            break; //Because we only allow single term search
        }

        $res = DB::select($sql);
        $creativeIds = collect($res)
            ->pluck('id')
            ->toArray();
        return $creativeIds;
    }

    public function testSkipJobAlertsForRepostJobs(Request $request)
    {

        $applications = array();
        $notifications = array();

        $current_job = Job::where('id', $request?->job_id)->first();
        $apps = Application::where('job_id', $current_job?->id)->get()->pluck('user_id')->toArray();
        $notifs = Notification::where('type', 'job_alert')->where('body', json_encode(array('job_id' => $current_job?->id)))->get()->pluck('user_id')->toArray();
        $applications = array_merge($applications, $apps ? $apps : []);
        $notifications = array_merge($notifications, $notifs ? $notifs : []);

        $original_job = Job::where('id', $request?->repost_job_id)->first();

        while ($original_job?->id) {
            $apps = Application::where('job_id', $original_job?->id)->get()->pluck('user_id')->toArray();
            $notifs = Notification::where('type', 'job_alert')->where('body', json_encode(array('job_id' => $original_job?->id)))->get()->pluck('user_id')->toArray();
            $applications = array_merge($applications, $apps ? $apps : []);
            $notifications = array_merge($notifications, $notifs ? $notifs : []);
            $original_job = Job::where('id', $original_job?->repost_job_id)->first();
        }

        $users = array_values(array_unique(array_merge($applications, $notifications)));

        $category = Category::where('id', $current_job->category_id)->first();

        $group_categories = Category::where('group_name', $category->name)->get();

        if (count($group_categories) > 0) {
            for ($i = 0; $i < count($group_categories); $i++) {
                $categories[$i] = $group_categories[$i]->id;
            }
        } else {
            $categories[0] = $category->id;
        }
        $categorySubscribers = JobAlert::with('user')->whereNotIn('user_id', $users)->whereIn('category_id', $categories)->where('status', 1)->get()->pluck('user_id')->toArray();

        return $categorySubscribers;
    }

    function validate_url($url)
    {
        $tries = "";
        $valid_url = false;
        try {
            $find = ['https://wwww.', 'https://', 'http://www.', 'http://', 'www.'];
            $replace   = ['', '', '', '', ''];

            $trimmed_url = str_replace($find, $replace, $url);

            $formats = ["https://", "https://www.", "http://", "http://www."];

            foreach ($formats as $format) {
                $formatted_url = $format . $trimmed_url;
                try {
                    if (url_exists($formatted_url)) {
                        $valid_url = $formatted_url;
                        break;
                    } else {
                        throw new Exception($formatted_url);
                    }
                } catch (Exception $e) {
                    $tries .= "Failed for => " . $e->getMessage()  . "<br>\n";
                }
            }
        } catch (Exception $ex) {
            $tries .= "Something else => " . $ex->getMessage() . "<br>\n";
        }

        return array(
            'status' => $valid_url != false ? "success" : "failure",
            'url' => $url,
            'valid_url' => $valid_url,
            'tries' => $tries,
        );
    }

    public function testDataUrl(Request $request)
    {
        return formate_url($request->url);
    }

    public function testCrop(Request $request)
    {
        $user_id = $request->user_id;
        $crop_x = $request->x;
        $crop_y = $request->y;
        $crop_width = $request->width;
        $crop_height = $request->height;

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

                $tmp_img = imagecrop($img, ['x' => $crop_x, 'y' => $crop_y, 'width' => $crop_width, 'height' => $crop_height]);

                $temp = tmpfile();
                // save thumbnail into a temp file
                imagejpeg($tmp_img, $temp);

                $filePath = Storage::disk('s3')->put($thumb_path, $temp);

                fclose($temp);

                $html = 'Crop Params:<br>';
                $html .= 'x:' . $crop_x . '<br>';
                $html .= 'y:' . $crop_y . '<br>';
                $html .= 'width:' . $crop_width . '<br>';
                $html .= 'height:' . $crop_height . '<br>';
                $html .= '<img src="' . getAttachmentBasePath() . $thumb_path . '" />';
                return $html;
            }
        }
        return "No-UUID";
    }

    private function get_location($user)
    {
        $address = $user->addresses ? collect($user->addresses)->firstWhere('label', 'personal') : null;

        if ($address) {
            return [
                'state_id' => $address->state ? $address->state->uuid : null,
                'state' => $address->state ? $address->state->name : null,
                'city_id' => $address->city ? $address->city->uuid : null,
                'city' => $address->city ? $address->city->name : null,
            ];
        } else {
            return [
                'state_id' => null,
                'state' => null,
                'city_id' => null,
                'city' => null,
            ];
        }
    }

    // public function testWelcome(Request $request)
    // {
    //     $creative = Creative::where('id', '=', $request->creative_id)->first();

    //     $user = $creative->user;
    //     $creative_category = isset($creative->category) ? $creative->category->name : null;
    //     $creative_location = $this->get_location($user);

    //     return '<div class="welcome-lounge">' .
    //         '  <img src="' . env('APP_URL') . '/assets/img/welcome-blank.jpeg" alt="Welcome Creative" />' .
    //         '  <img class="user_image" src="' . (isset($user->profile_picture) ? getAttachmentBasePath() . $user->profile_picture->path : asset('assets/img/placeholder.png')) . '" alt="Profile Image" />' .
    //         '  <div class="user_info">' .
    //         '    <div class="name">' . ($user->first_name . ' ' . $user->last_name) . '</div>' .
    //         ($creative_category != null ? ('    <div class="category">' . $creative_category . '</div>') : '') .
    //         ($creative_location['state'] || $creative_location['city'] ? ('    <div class="location">' . ($creative_location['state'] . (($creative_location['state'] && $creative_location['city']) ? ', ' : '') . $creative_location['city']) . '</div>') : '') .
    //         '  </div>' .
    //         '</div>';
    // }

    private function getWelcomePost($creative)
    {
        $user = $creative->user;
        $creative_category = isset($creative->category) ? $creative->category->name : null;
        $creative_location = $this->get_location($user);

        return '<a href="' . env('FRONTEND_URL') . '/creative/' . ($user?->creative?->slug ?? $user->username) . '">@' . $user->full_name . '</a><br />' .
            '<div class="welcome-lounge">' .
            '  <img src="' . env('APP_URL') . '/assets/img/welcome-blank.gif" alt="Welcome Creative" />' .
            '  <img class="user_image" src="' . (isset($user->profile_picture) ? getAttachmentBasePath() . $user->profile_picture->path : asset('assets/img/placeholder.png')) . '" alt="Profile Image" />' .
            '  <div class="user_info">' .
            '    <div class="name">' . ($user->first_name . ' ' . $user->last_name) . '</div>' .
            ($creative_category != null ? ('    <div class="category">' . $creative_category . '</div>') : '') .
            ($creative_location['state'] || $creative_location['city'] ? ('    <div class="location">' . ($creative_location['state'] . (($creative_location['state'] && $creative_location['city']) ? ', ' : '') . $creative_location['city']) . '</div>') : '') .
            '  </div>' .
            '</div>';
    }

    public function sendLoungeMentionNotifications($post, $recipient_ids, $send_email = 'yes')
    {
        try {
            $author = $post->user;
            foreach ($recipient_ids as $recipient_id) {

                $receiver = User::where('uuid', $recipient_id)->first();

                $data = array();
                $data['uuid'] = Str::uuid();

                $data['user_id'] = $receiver->id;
                $data['type'] = 'lounge_mention';
                $data['message'] = $author->full_name . ' commented on you in a post';

                $data['body'] = array('post_id' => $post->id);

                $notification = Notification::create($data);

                $group = $post->group;

                $group_url = $group ? ($group->slug == 'feed' ? env('FRONTEND_URL') . '/community' : env('FRONTEND_URL') . '/groups/' . $group->uuid) : '';

                $data = [
                    'data' => [
                        'recipient' => $receiver->first_name,
                        'name' => $author->full_name,
                        'inviter' => $author->full_name,
                        'inviter_profile_url' => sprintf('%s/creative/%s', env('FRONTEND_URL'), ($author?->creative?->slug ?? $author->username)),
                        'profile_picture' => get_profile_picture($author),
                        'user' => $author,
                        'group_url' => $group_url,
                        'group' => $group->name,
                        'post_time' => \Carbon\Carbon::parse($post->created_at)->diffForHumans(),
                        'notification_uuid' => $notification->uuid,
                    ],
                    'receiver' => $receiver
                ];

                if ($send_email == 'yes') {
                    SendEmailJob::dispatch($data, 'user_mentioned_in_post');
                }
            }
        } catch (\Exception $e) {
            throw new ApiException($e, 'NS-01');
        }
    }

    public function testWelcome(Request $request)
    {
        // $creative_id = $request->has('creative_id') ? $request->creative_id : null;

        // if($creative_id) {
        //     $creative = Creative::where('id','=',$creative_id)->first();
        //     $post = Post::create( [
        //         'uuid' => Str::uuid(),
        //         'user_id' => 202, // admin/erika
        //         'group_id' => 4, // The Lounge Feed
        //         'content' => $this->getWelcomePost( $creative ),
        //         'status' => 1,
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ] );

        //     if ( $post ) {
        //         $creative->is_welcomed = true;
        //         $creative->welcomed_at = now();
        //         $creative->save();

        //         $this->sendLoungeMentionNotifications( $post, [ $creative->user->uuid ], 'yes' );
        //     }
        // } 

        $today_welcomed_at_creatives_count = Creative::where('is_welcomed', '=', 1)->whereDate('welcomed_at', '=', today()->toDateString())->count('welcomed_at');
        $previous_welcome_queued_at_creatives_count = Creative::where('is_welcomed', '=', 0)->whereNotNull('welcome_queued_at')->count('welcome_queued_at');
        $next_welcome_creative = null;

        if ($today_welcomed_at_creatives_count < 3) {
            $next_welcome_creative = Creative::where('is_welcomed', '=', 0)->whereNotNull('welcome_queued_at')->orderBy('welcome_queued_at')->first();
        }

        return array(
            'Today' => today()->toDateString(),
            'today_welcomed_at_creatives_count' => $today_welcomed_at_creatives_count,
            'previous_welcome_queued_at_creatives_count' => $previous_welcome_queued_at_creatives_count,
            'next_welcome_creative' => $next_welcome_creative?->id ?? "",
        );
    }

    private function getCreativeProfileProgress($creative)
    {
        $progress = 0;
        $required_fields = 17;
        $completed_fields = 0;

        $completed_fields +=  strlen($creative?->user?->profile_picture?->path ?? '') > 0 ? 1 : 0;
        $completed_fields +=  strlen($creative?->user?->first_name ?? '') > 0 ? 1 : 0;
        $completed_fields +=  strlen($creative?->user?->last_name ?? '') > 0 ? 1 : 0;
        $completed_fields +=  strlen($creative?->user?->portfolio_website_link?->url ?? '') > 0 ? 1 : 0;
        $completed_fields +=  strlen($creative?->user?->creative_linkedin_link?->url ?? '') > 0 ? 1 : 0;
        $completed_fields +=  strlen($creative?->user?->email ?? '') > 0 ? 1 : 0;
        $completed_fields +=  strlen($creative?->user?->personal_phone?->phone_number ?? '') > 0 ? 1 : 0;
        $completed_fields += (strlen($creative?->title ?? "") > 0) ? 1 : 0;
        $completed_fields += (strlen($creative?->category?->name ?? "") > 0) ? 1 : 0;
        $completed_fields += (strlen($creative?->years_of_experience ?? "") > 0) ? 1 : 0;
        $completed_fields += (strlen($creative?->industry_experience ?? "") > 0) ? 1 : 0;
        $completed_fields += (strlen($creative?->media_experience ?? "") > 0) ? 1 : 0;

        $address = $creative?->user?->addresses ? collect($creative?->user->addresses)->firstWhere('label', 'personal') : null;

        if ($address) {
            $completed_fields += (strlen($address?->state?->name  ?? "") > 0) ? 1 : 0;
            $completed_fields += (strlen($address?->city?->name ?? "") > 0) ? 1 : 0;
        }

        $completed_fields += (strlen($creative?->strengths ?? "") > 0) ? 1 : 0;
        $completed_fields += (strlen($creative?->employment_type ?? "") > 0) ? 1 : 0;
        $completed_fields += (strlen($creative?->about ?? "") > 0) ? 1 : 0;

        $progress = intval(100 * $completed_fields / $required_fields);

        return $progress;
    }

    private function getAgencyProfileProgress($agency): int
    {
        $progress = 0;
        $required_fields = 16;
        $completed_fields = 0;

        $completed_fields +=  strlen($agency?->user?->agency_logo?->path ?? '') > 0 ? 1 : 0;
        $completed_fields += (strlen($agency?->name ?? "") > 0) ? 1 : 0;
        $completed_fields +=  strlen($agency?->user?->agency_website_link?->url ?? '') > 0 ? 1 : 0;

        $address = $agency?->user?->addresses ? collect($agency?->user->addresses)->firstWhere('label', 'business') : null;
        if ($address) {
            $completed_fields += (strlen($address?->state?->name  ?? "") > 0) ? 1 : 0;
            $completed_fields += (strlen($address?->city?->name ?? "") > 0) ? 1 : 0;
        }

        $completed_fields +=  strlen($agency?->user?->agency_linkedin_link?->url ?? '') > 0 ? 1 : 0;
        $completed_fields +=  strlen($agency?->user?->email ?? '') > 0 ? 1 : 0;
        $completed_fields +=  strlen($agency?->user?->first_name ?? '') > 0 ? 1 : 0;
        $completed_fields +=  strlen($agency?->user?->last_name ?? '') > 0 ? 1 : 0;
        $completed_fields +=  strlen($agency?->user?->business_phone?->phone_number ?? '') > 0 ? 1 : 0;
        $completed_fields += (strlen($agency?->about ?? "") > 0) ? 1 : 0;
        $completed_fields += (strlen($agency?->industry_experience ?? "") > 0) ? 1 : 0;
        $completed_fields += (strlen($agency?->media_experience ?? "") > 0) ? 1 : 0;
        $completed_fields += ($agency?->is_remote || $agency?->is_hybrid || $agency?->is_onsite) ? 1 : 0;
        $completed_fields += (strlen($agency?->size ?? "") > 0) ? 1 : 0;
        $completed_fields += (strlen($agency?->slug ?? "") > 0) ? 1 : 0;

        $progress = intval(100 * $completed_fields / $required_fields);

        return $progress;
    }

    public function calculateProfileCompletionCreative(Request $request)
    {

        $date_before = today()->subDays(2);

        $users = User::where('role', '=', 4)
            ->whereNull('profile_completed_at')
            ->whereNull('profile_completion_reminded_at')
            ->where('status', 1)
            ->whereDate('created_at', '<=', $date_before)
            ->orderBy('created_at')
            ->get();

        $output = [];

        $output[] = "Total Active Creatives: " . count($users) . ", Registered On/Before: " . $date_before->toDateString();

        foreach ($users as $user) {

            $creative = $user->creative;

            $progress = $this->getCreativeProfileProgress($creative);
            $output[] = sprintf("Progress: %d%%", $progress) . ", Registered: " .  $user?->created_at?->format(config('global.datetime_format')) . ", Full Name: " . $user?->full_name . ", Email: " . $user?->email;

            $user->profile_complete_progress = $progress;
            $user->profile_completed_at = $progress == 100 ? today() : null;
            $user->save();
        }

        return implode("\n<br />", $output);
    }

    public function calculateProfileCompletionAgency(Request $request)
    {
        $date_before = today()->subDays(2);

        $users = User::where('role', '=', 3)
            ->whereNull('profile_completed_at')
            ->whereNull('profile_completion_reminded_at')
            ->where('status', 1)
            ->whereDate('created_at', '<=', $date_before)
            ->orderBy('created_at')
            ->get();

        $output = [];

        $output[] = "Total Active Agencies: " . count($users) . ", Registered On/Before: " . $date_before->toDateString();

        foreach ($users as $user) {

            $agency = $user->agency;

            $progress = $this->getAgencyProfileProgress($agency);
            $output[] = sprintf("Progress: %d%%", $progress) . ", Registered: " .  $user?->created_at?->format(config('global.datetime_format')) . ", Full Name: " . $user?->full_name . ", Company: " . $agency?->name . ", Email: " . $user?->email;

            $user->profile_complete_progress = $progress;
            $user->profile_completed_at = $progress == 100 ? today() : null;
            $user->save();
        }

        return implode("\n<br />", $output);
    }

    public function profileCompletionCreative(Request $request)
    {
        if ($request->has('user_id')) {
            $creative = Creative::whereHas('user', function ($q) use ($request) {
                $q->where('id', '=', $request->user_id);
            })->first();
        } else {
            $creative = Creative::whereHas('user', function ($q) {
                $q->whereNull('profile_completed_at')->orderBy('created_at');
            })->take(1)->first();
        }

        if (!$creative) {
            return response()->json([
                'message' => "Creative not found",
            ], 500);
        }

        $data = [
            'data' => [
                'first_name' => $creative?->user?->first_name ?? '',
                'category_name' => $creative?->category?->name ?? '',
            ],
            'receiver' => $creative?->user,
        ];
        if ($request?->has('email') && $request?->email == "yes") {
            SendEmailJob::dispatch($data, 'profile_completion_creative');
            $user = User::where('uuid', '=', $creative->user->uuid)->first();
            $user->profile_completion_reminded_at = today();
            $user->save();
        }

        return new ProfileCompletionCreativeReminder($data['data']);
    }

    public function profileCompletionAgency(Request $request)
    {
        if ($request->has('user_id')) {
            $agency = Agency::whereHas('user', function ($q) use ($request) {
                $q->where('id', '=', $request->user_id);
            })->first();
        } else {
            $agency = Agency::whereHas('user', function ($q) {
                $q->whereNull('profile_completed_at')->orderBy('created_at');
            })->take(1)->first();
        }

        if (!$agency) {
            return response()->json([
                'message' => "Agency not found",
            ], 500);
        }

        $data = [
            'data' => [
                'first_name' => $agency?->user?->first_name ?? '',
                'profile_url' => sprintf('%s/agency/%s', env('FRONTEND_URL'), $agency->slug),
            ],
            'receiver' => $agency?->user,
        ];
        if ($request?->has('email') && $request?->email == "yes") {
            SendEmailJob::dispatch($data, 'profile_completion_agency');
            $user = User::where('uuid', '=', $agency->user->uuid)->first();
            $user->profile_completion_reminded_at = today();
            $user->save();
        }

        return new ProfileCompletionAgencyReminder($data['data']);
    }

    public function dateTimeCheck(Request $request)
    {

        $date = today();
        $targetDate = Carbon::parse("2024-10-23");

        if ($request->has('date') && strlen($request->date) > 0) {
            $date = Carbon::parse($request->date);
        }

        $messages = [];

        $messages[] = "Today: " . $date;

        // if ($date->dayOfWeek >= Carbon::MONDAY && $date->dayOfWeek <= Carbon::FRIDAY) {
        //     $messages[] = "The date is between Monday and Friday.";
        // } else {
        //     $messages[] = "The date is not between Monday and Friday.";
        // }

        if ($date->greaterThanOrEqualTo($targetDate)) {
            $messages[] = "The date is >= " . $targetDate->toDateString();
        } else {
            $messages[] = "The date is < " . $targetDate->toDateString();
        }

        $messages[] = "Difference in days to remind: ";

        $diff = 2;
        switch ($date->dayOfWeek) {
            case Carbon::MONDAY:
            case Carbon::TUESDAY:
                $diff = 4;
                break;
        }

        $new_date = $date->subDays($diff);
        $messages[] = "Remind: " . $new_date;

        return implode("\n<br />", $messages);
    }

    public function agenciesWithoutJobPosts(Request $request)
    {
        $date_before = today()->subDays(5);

        $agency_user_ids = Agency::whereHas('user', function ($q) use ($date_before) {
            $q->where('status', '=', 1)
                ->where('role', '=', 3)
                ->whereDate('created_at', '<=', $date_before);
        })->pluck('user_id')->toArray();

        $job_user_ids = Job::whereHas('user', function ($q) use ($date_before) {
            $q->where('status', '=', 1)
                ->where('role', '=', 3)
                ->whereDate('created_at', '<=', $date_before);
        })->pluck('user_id')->toArray();

        $agency_user_ids = array_values(array_unique($agency_user_ids));
        $job_user_ids = array_values(array_unique($job_user_ids));

        Agency::whereIn('user_id', $job_user_ids)->update(['is_job_posted' => 1]);

        $agency_users_without_job_posts = array_values(array_unique(array_diff($agency_user_ids, $job_user_ids)));

        $agencies_without_job_posts = User::whereHas('agency', function ($q) use ($agency_users_without_job_posts) {
            $q->whereIn('user_id', $agency_users_without_job_posts)
                ->where('is_job_posted', '=', 0)
                ->whereNull('job_posting_reminded_at');
        })->orderBy("created_at")
            ->get();

        $output = [];

        $output[] = "Total Agencies Not Posting Any Job: " . count($agencies_without_job_posts) . ", Registered On/Before: " . $date_before->toDateString();

        foreach ($agencies_without_job_posts as $user) {

            $agency = $user->agency;

            $output[] = "Registered: " .  $user?->created_at?->format(config('global.datetime_format')) . ", Full Name: " . $user?->full_name . ", Company: " . $agency?->name . ", Email: " . $user?->email;
        }

        return implode("\n<br />", $output);
    }

    public function noJobPostedAgencyReminder(Request $request)
    {
        $date_before = today()->subDays(5);

        $agency_user_ids = Agency::whereHas('user', function ($q) use ($date_before) {
            $q->where('status', '=', 1)
                ->where('role', '=', 3)
                ->whereDate('created_at', '<=', $date_before);
        })->pluck('user_id')->toArray();

        $job_user_ids = Job::whereHas('user', function ($q) use ($date_before) {
            $q->where('status', '=', 1)
                ->where('role', '=', 3)
                ->whereDate('created_at', '<=', $date_before);
        })->pluck('user_id')->toArray();

        $agency_user_ids = array_values(array_unique($agency_user_ids));
        $job_user_ids = array_values(array_unique($job_user_ids));

        Agency::whereIn('user_id', $job_user_ids)->update(['is_job_posted' => 1]);

        $agency_users_without_job_posts = array_values(array_unique(array_diff($agency_user_ids, $job_user_ids)));

        $agencies_without_job_posts = User::whereHas('agency', function ($q) use ($agency_users_without_job_posts) {
            $q->whereIn('user_id', $agency_users_without_job_posts)
                ->where('is_job_posted', '=', 0)
                ->whereNull('job_posting_reminded_at');
        })->orderBy("created_at")
            ->take(1)
            ->get();

        $user = $agencies_without_job_posts[0];
        $agency = $user?->agency;

        if (!$agency) {
            return response()->json([
                'message' => "Agency not found",
            ], 500);
        }

        $data = [
            'data' => [
                'first_name' => $user?->first_name ? $user->first_name : $user->username,
                'profile_url' => sprintf('%s/agency/%s', env('FRONTEND_URL'), $agency->slug),
            ],
            'receiver' => $user,
        ];

        if ($request?->has('email') && $request?->email == "yes") {
            SendEmailJob::dispatch($data, 'no_job_posted_agency_reminder');
            $agency = Agency::where('uuid', '=', $agency->uuid)->first();
            $agency->job_posting_reminded_at = today();
            $agency->save();
        }

        return new NoJobPostedAgencyReminder($data['data']);
    }


    public function test_trending_posts(Request $request)
    {
        $cacheKey = 'trending_posts';
        // Attempt to retrieve the data from the cache
        $trendingPosts = QueryBuilder::for(Post::class)
            ->allowedFilters([
                AllowedFilter::scope('user_id'),
                AllowedFilter::scope('group_id'),
            ])
            ->whereHas('user')
            ->whereHas('group', function ($query) {
                $query->where('status', '=', 0);
            })
            ->withCount('reactions')
            ->withCount('comments')
            ->orderBy('reactions_count', 'desc')
            ->orderBy('comments_count', 'desc')
            ->orderBy('created_at', 'desc')
            ->with('comments')
            ->withCount('reactions')
            ->paginate($request->per_page ?? config('global.request.pagination_limit'))
            ->withQueryString();

        $authenticatedUserId = auth()->id();

        $trendingPosts->getCollection()->transform(function ($post) use ($authenticatedUserId) {
            $post->user_has_liked = $post->likes->contains('user_id', $authenticatedUserId);

            return $post;
        });

        return new TrendingPostCollection($trendingPosts);
    }
}