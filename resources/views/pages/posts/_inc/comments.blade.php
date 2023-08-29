<div class="row">
    <div class="col-md-4 col-xl-6">
        <div class="card">
            <div class="card-header">

                <h5 class="card-title mb-0">Comments ({{ $post->comments->count() }})</h5>
            </div>
            <div class="card-body">
                @foreach ($post->comments as $comment)
                <div class="d-flex align-items-start">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($comment->user->first_name . ' ' . $comment->user->second_name) }}&size=10"
                        width="30" height="30" class="rounded-circle me-3">
                    <div class="flex-grow-1">
                        <small class="float-right">{{ $comment->created_at }}</small>
                        <p class="mb-2" style="margin-left: 5px;"><strong>{{ $comment->user->first_name }}</strong></p>
                        <p> {{ $comment->content }} </p>

                        @foreach ($comment->replies as $reply)
                        <div class="d-flex align-items-start mt-3">
                            <a class="pe-2" href="#">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($reply->user->first_name . ' ' . $reply->user->second_name) }}&size=10"
                                    width="36" height="36" class="rounded-circle me-2" alt="Stacie Hall">
                            </a>
                            <div class="flex-grow-1">
                                <p class="text-muted">
                                    <strong>{{ $reply->user->first_name }}</strong>: {{ $reply->content }}
                                </p>
                            </div>
                        </div>
                        @endforeach

                    </div>
                </div>
                <hr>
                @endforeach

            </div>
        </div>
    </div>
</div>