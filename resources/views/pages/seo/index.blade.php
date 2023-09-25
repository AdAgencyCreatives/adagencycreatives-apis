@extends('layouts.app')

@section('title', __('SEO'))


@section('scripts')
    <script src="{{ asset('/assets/js/custom.js') }}"></script>
@endsection

@if (session('success'))
    <x-alert type="success"></x-alert>
@endif

@section('content')

    <div class="row">

        <div class="col-md-12 col-xl-12">
            <div class="card">

                <div class="card-header">
                    <h5 class="card-title mb-0">SEO</h5>
                </div>
                <div class="card-body">
                    <form id="creative-form2" action="{{ route('website-seo.update', $seo->id) }}" method="POST">
                        @csrf()
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="title">Title</label>
                                    <input type="text" class="form-control" name="title" placeholder="SEO Title"
                                        value="{{ $seo->title }}">
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="title">Description</label>
                                    <input type="text" class="form-control" name="description"
                                        placeholder="SEO Description" value="{{ $seo->description }}">
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-12 col-lg-12">
                                <div class="form-group">
                                    <label class="form-label" for="seo_keywords">Keywords (Select up to 5)</label>
                                    <select name="keywords[]" id="seo_keywords"
                                        class="form-control form-select custom-select select2" multiple="multiple"
                                        data-toggle="select2">
                                        <option value="-100"> Select Keywords </option>
                                        @if (!is_null($seo->keywords))
                                            @php
                                                $tagsArray = explode(',', $seo->keywords);
                                            @endphp
                                            @foreach ($tagsArray as $tag)
                                                <option value="{{ $tag }}" selected>
                                                    {{ trim($tag) }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>


                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </form>
                </div>

            </div>
        </div>
    </div>

@endsection
