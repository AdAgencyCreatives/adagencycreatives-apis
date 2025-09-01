<div class="row">

    <div class="col-md-12 col-xl-12">
        <form id="creative-form2" action="{{ route('creative.award.update', $user->creative?->uuid) }}" method="POST">
            @csrf()
            @method('PUT')

            @if (isset($user->awards) && count($user->awards))
            @foreach ($user->awards as $key => $award)
            <div class="card">

                <div class="card-header">
                    <h5 class="card-title mb-0">Award {{ $key + 1 }}</h5>
                </div>

                <div class="card-body">
                    <div class="row">

                        <div class="col-md-6">
                            <input type="hidden" name="award_id[]" value="{{ $award->id }}">
                            <div class="mb-3">
                                <label for="award_title" class="form-label">Award Title</label>
                                <input Id="award_title" type="text" class="form-control" name="award_title[]" placeholder="Award title"
                                    value="{{ $award->award_title }}">
                            </div>

                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="award_work" class="form-label">Award Work</label>
                                <input id="award_work" type="text" class="form-control" name="award_work[]"
                                    placeholder="Award work" value="{{ $award->award_work }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="award_year">Award Year </label>
                                    <input type="text" id="award_year" class="form-control" placeholder="Awarded At"
                                        disabled value="{{ $award->award_year }}">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
            @endforeach
            <button type="submit" class="btn btn-primary">Save changes</button>
            @else
            <p>No award found</p>
            @endif

        </form>
    </div>
</div>