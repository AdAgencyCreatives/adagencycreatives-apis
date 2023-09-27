@extends('layouts.app')

@section('title', __('Settings'))


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
                    <h5 class="card-title mb-0">Settings</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.store') }}" method="POST">
                        @csrf()

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="site_name">Site Name</label>
                                    <input type="text" class="form-control" name="site_name" placeholder="Site Name"
                                        value="{{ $data['site_name'] }}">
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="site_description">Site Description</label>
                                    <input type="text" class="form-control" name="site_description"
                                        placeholder="Site Description" value="{{ $data['site_description'] }}">
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="site_description">Separator</label>
                                    <input type="text" class="form-control" name="separator"
                                        placeholder="For example: | - ," value="{{ $data['separator'] }}">
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
