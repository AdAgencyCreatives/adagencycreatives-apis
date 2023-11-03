<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GeneratePortfolioVisuals extends Command
{
    protected $signature = 'portfolio:generate';

    protected $description = 'Generate portfolio visuals';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $role = 'your_role'; // Replace 'your_role' with your specific role name
        $user_ids = \App\User::where('role', $role)->pluck('id');

        foreach ($user_ids as $id) {
            $candidate_id = \App\User::find($id)->candidate_id;
            $post = \App\Post::find($candidate_id);
            $video_url = $post->portfoliolink;

            $video_url = ($video_url && filter_var($video_url, FILTER_VALIDATE_URL)) ? $video_url : 'http://' . $video_url;

            if (
                $video_url
                && !preg_match('/(?:aparat\.com|youtube\.com|vimeo\.com|dailymotion\.com)/i', $video_url)
            ) {
                $filename = public_path('images/portfolio/' . $candidate_id . '.png');

                if (!file_exists($filename)) {
                    $client = new Client();
                    $googlePagespeedResponse = $client->get("https://www.googleapis.com/pagespeedonline/v5/runPagespeed", [
                        'query' => [
                            'screenshot' => 'true',
                            'url' => $video_url
                        ]
                    ]);

                    $googlePagespeedObject = json_decode($googlePagespeedResponse->getBody(), true);

                    if (isset($googlePagespeedObject['lighthouseResult']['audits']['final-screenshot']['details']['data'])) {
                        $screenshot = $googlePagespeedObject['lighthouseResult']['audits']['final-screenshot']['details']['data'];
                        $screenshot = str_replace(['_', '-'], ['/', '+'], $screenshot);
                        $img = $client->get($screenshot)->getBody();

                        file_put_contents($filename, $img);
                    }
                }
            }
        }

        $this->info('Portfolio visuals generated successfully.');
    }
}