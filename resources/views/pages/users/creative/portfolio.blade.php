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
                            <img src="{{ getAttachmentBasePath() . $item->path }}"></video>
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
                            <a href="{{ $link->url }}" target="_blank">{{ $link->url }}</a>
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
