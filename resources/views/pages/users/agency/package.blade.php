@php
    $subscription = $user->latest_subscription;
    $plans = \App\Models\Plan::all();
@endphp
<div class="row">

    <div class="col-md-12 col-xl-12">
        <div class="card">

            <div class="card-header">
                <h5 class="card-title mb-0">Agency Package</h5>
            </div>
            <div class="card-body">
                <form id="agency-form2" action="{{ route('agency.package.update', $user->id) }}" method="POST">
                    @csrf()
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="is_visible"> Package </label>
                                <select name="name" class="form-control form-select custom-select select2"
                                    data-toggle="select2" id="subscriptionDuration">
                                    <option value="-1">
                                        Select Package
                                    </option>

                                    @foreach ($plans as $plan)
                                        @php
                                            $selected = $subscription && $subscription->name == $plan->slug ? 'selected' : '';
                                        @endphp
                                        <option value="{{ $plan->slug }}" data-days="{{ $plan->days }}"
                                            data-quota="{{ $plan->quota }}" {{ $selected }}>
                                            {{ $plan->name }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="size" class="form-label">Quota</label>
                                <input type="number" class="form-control" name="quota_left" placeholder="Quota Left"
                                    value="{{ $subscription?->quota_left }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-12 col-lg-6">
                            <div class="form-group">
                                <label class="form-label"> Expired At </label>
                                <input class="form-control daterange" type="text" name="ends_at" />
                            </div>
                        </div>
                    </div>


                    <button type="submit" class="btn btn-primary">Save changes</button>
                </form>
            </div>

        </div>
    </div>
</div>
