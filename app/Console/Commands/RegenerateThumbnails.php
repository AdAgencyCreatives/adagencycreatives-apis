<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Attachment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class RegenerateThumbnails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'regenerate:thumbnails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate all user profile and agency logo thumbnails to the new 362x362 size with a circular radial dark mask.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting thumbnail regeneration process...');

        $users = User::with(['profile_picture', 'agency_logo'])->where(function ($query) {
            $query->whereHas('profile_picture')->orWhereHas('agency_logo');
        })->get();

        $progressBar = $this->output->createProgressBar(count($users));
        $progressBar->start();

        foreach ($users as $user) {
            try {
                $this->regenerateThumbnailForUser($user, 362);
            } catch (\Exception $e) {
                Log::error("Failed to regenerate thumbnail for user: {$user->id}. Error: " . $e->getMessage());
                $this->error("\nFailed for user: {$user->id} - {$user->email}");
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->info("\nThumbnail regeneration process completed.");
        return 0;
    }

    /**
     * Regenerate the thumbnail for a specific user with a circular radial mask.
     */
    private function regenerateThumbnailForUser(User $user, int $thumbWidth): ?Attachment
    {
        $resource_type = 'user_thumbnail';
        $original_attachment = $user->profile_picture ?: $user->agency_logo;

        if (!$original_attachment || !$original_attachment->path) {
            return null;
        }

        if (!Storage::disk('public')->exists($original_attachment->path)) {
            $this->error("\nOriginal file not found for user: {$user->id} at path: {$original_attachment->path}");
            return null;
        }

        $fileContents = Storage::disk('public')->get($original_attachment->path);
        $original_extension = strtolower($original_attachment->extension);

        // 1. Resize the base image
        $thumbnail = Image::make($fileContents)
            ->fit($thumbWidth, $thumbWidth, function ($constraint) {
                $constraint->upsize();
            });

        // 2. Create the circular radial gradient mask
        $mask = Image::canvas($thumbWidth, $thumbWidth);
        $center = $thumbWidth / 2;
        $maxDistance = sqrt(pow($center, 2) + pow($center, 2));

        for ($x = 0; $x < $thumbWidth; $x++) {
            for ($y = 0; $y < $thumbWidth; $y++) {
                $distance = sqrt(pow($x - $center, 2) + pow($y - $center, 2));
                $opacity = ($distance / $maxDistance) * 0.5; // Adjust 0.5 to make the edges darker or lighter
                $mask->pixel('rgba(0, 0, 0, ' . $opacity . ')', $x, $y);
            }
        }

        // 3. Apply the mask to the thumbnail
        $thumbnail->insert($mask);

        // 4. Save the new thumbnail
        $fileName = uniqid() . '.' . $original_extension;
        $directory = 'attachments/' . $user->id;
        $thumbnail_path = sprintf('%s/thumbnails/%s', $directory, $fileName);

        Storage::disk('public')->put($thumbnail_path, (string) $thumbnail->encode($original_extension, 90));

        // 5. Update the database
        $existing_attachment = Attachment::where('user_id', $user->id)
            ->where('resource_type', $resource_type)
            ->first();

        if ($existing_attachment) {
            Storage::disk('public')->delete($existing_attachment->path);
            $existing_attachment->delete();
        }

        return Attachment::create([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'resource_type' => $resource_type,
            'path' => $thumbnail_path,
            'name' => $original_attachment->name,
            'extension' => $original_extension,
        ]);
    }
}
