<div class="row">
    <div class="col-12 col-lg-12">
        <div class="card">
            <div class="card-header">
                <!-- Card header content -->
            </div>
            <div class="card-body pt-0">

                @if ($user->portfolio_spotlights->isEmpty())

                    No portfolio items found.
                @else
                    <div class="row">
                        <div class="col-12 col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <!-- Card header content -->
                                </div>
                                <div class="card-body pt-0">

                                    @if ($user->portfolio_spotlights->isEmpty())

                                        No portfolio items found.
                                    @else
                                        @foreach ($user->portfolio_spotlights as $key => $video)
                                            <div>
                                                <video controls loop
                                                    src="{{ getAttachmentBasePath() . $video->path }}"></video>
                                            </div>
                                        @endforeach
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
