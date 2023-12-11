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
                            <img src="{{ getAttachmentBasePath() . $item->path }}" style="max-width: 100%;" />
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
    </div>
</div>
