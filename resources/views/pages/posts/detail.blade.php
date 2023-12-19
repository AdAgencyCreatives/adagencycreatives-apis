@extends('layouts.app')

@section('title', 'Group Details')

@section('styles')
    <style>
        .image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
        }

        .image-grid img {
            max-width: 100%;
            height: auto;
        }

        .image-container {
            display: flex;
            flex-wrap: wrap;
        }

        .image-item {
            flex: 0 0 33.33%;
            /* Distribute images in 3 columns */
            padding: 10px;
            box-sizing: border-box;
            max-width: 100%;
            height: 200px;
            /* Set a fixed height for the image container */
            overflow: hidden;
            position: relative;
        }

        .image-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* Maintain aspect ratio and cover container */
        }
    </style>
@endsection

@section('scripts')

    <script>
        function fetchPostAttachments(post_id) {
            $.ajax({
                url: '/api/v1/attachments?filter[post_id]=' + post_id,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    var imageContainer = $('#imageContainer');

                    var imageGrid = $('<div>', {
                        class: 'image-grid'
                    });

                    $.each(response.data, function(index, attachment) {
                        var imageItem = $('<div>', {
                            class: 'image-item'
                        }).append(
                            $('<img>', {
                                src: attachment.url,
                                alt: 'Image ' + (index + 1)
                            })
                        );

                        imageGrid.append(imageItem);
                    });

                    imageContainer.append(imageGrid);
                },
                error: function() {
                    alert('Failed to fetch attachments from the API.');
                }
            });
        }

        $(document).ready(function() {

            var post_id = '{{ $post->uuid }}';
            fetchPostAttachments(post_id);

            $(".daterange").daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                locale: {
                    format: "Y-MM-DD"
                }
            });

        });
    </script>
@endsection

@section('content')
    <h1 class="h3 mb-3">Post Details</h1>

    <div id="error-messages" class="alert alert-danger alert-dismissible" style="display: none;" role="alert">
        <div class="alert-message">
            <strong>Error!</strong> Please fix the following issues:
            <ul></ul>
        </div>
    </div>

    @if (session('success'))
        <x-alert type="success"></x-alert>
    @endif
    <div class="row">

        <div class="col-md-4 col-xl-6">
            <form action="{{ route('posts.update', $post->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card">
                    <div class="card-body px-4 pt-2">
                        <h5>Content</h5>
                        <p> {{ $post->content }} </p>
                        <div class="badge bg-warning my-2">{{ ucfirst($post->status) }}</div>
                        <p class="mb-2 fw-bold">Author: <span style="float:right">Created At:</span></p>
                        <p class="mb-2 fw-bold">

                            <a href="{{ url('/users/' . $post->user?->id . '/details') }}" target="_blank">
                                {{ $post->user?->username }}
                            </a>

                            <span style="float:right">
                                <input class="form-control daterange" name="created_at" type="text"
                                    value="{{ $post->created_at }}" />
                            </span>

                        </p>

                    </div>

                    <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>

    </div>

    @include('pages.posts._inc.attachments')

    @include('pages.posts._inc.likes')
    @include('pages.posts._inc.comments')
@endsection
