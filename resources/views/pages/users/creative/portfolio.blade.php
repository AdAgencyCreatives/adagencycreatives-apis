<div class="row">
    <div class="col-12 col-lg-12">
        <div class="card">
            <div class="card-header">
                <!-- Card header content -->
            </div>
            <div class="card-body pt-0">

                @if ($user->portfolio_items->isEmpty())
                    No portfolio items (attachments) found.
                @else
                    @foreach ($user->portfolio_items as $key => $item)
                        <div>
                            @if (pathinfo($item->path, PATHINFO_EXTENSION) == 'mp4')
                                <video controls style="max-width: 100%;">
                                    <source
                                        src="https://ad-agency-creatives.s3.amazonaws.com/portfolio_item/3807109a-28c8-429e-b2b6-75a160061460/KIWI-x-Falko-at-Sneaker-Exchange-Promo-3-1.mp4"
                                        type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            @else
                                <img src="{{ getAttachmentBasePath() . $item->path }}" style="max-width: 100%;" />
                            @endif
                        </div>
                    @endforeach

                @endif

            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <p>Portfolio Links</p>
            </div>
            <div class="card-body pt-0">
                @foreach ($user->portfolio_item_links as $link)
                    <div>
                        <p>
                            @php
                                // Check if the URL starts with "http://" or "https://"
                                $url = $link->url;
                                if (!Str::startsWith($url, ['http://', 'https://'])) {
                                    // If not, assume it's a relative URL and prepend "http://"
    $url = 'http://' . $url;
                                }
                            @endphp

                            <a href="{{ $url }}" target="_blank">{{ $link->url }}</a>


                        </p>
                        <p>{{ $user->uuid }}</p>
                    </div>
                @endforeach

                @if ($user->portfolio_website_preview)
                    <div>
                        <img style="max-width: 400px;"
                            src="{{ getAttachmentBasePath() . $user->portfolio_website_preview->path }}"></video>
                    </div>
                @endif

            </div>
        </div>


        <div class="card">
            <div class="card-header">

            </div>
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <form id="creative-form2"
                                action="{{ route('creative.website_preview.update', $user->creative?->uuid) }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf()
                                @method('PUT')
                                <div class="mb-3 error-placeholder">
                                    <label class="form-label">Add Portfolio Website Preview (manually)</label>
                                    <div>
                                        <input type="file" class="validation-file" name="file">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
