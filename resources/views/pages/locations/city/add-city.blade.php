<div class="card">
    <div class="card-body">
        <h5 class="card-title">Add New City in {{ $location->name }} </h5>

        <form id="new_city_form">
            @csrf
            <div class="mb-3">
                <label for="new_city" class="form-label">City Name</label>
                <input type="text" class="form-control" id="new_city">
            </div>

            <button type="submit" class="btn btn-primary">Add New City</button>
        </form>

    </div>
</div>