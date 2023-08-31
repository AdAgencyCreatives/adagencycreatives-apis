<div class="card">
    <div class="card-body">
        <h5 class="card-title">Add New State</h5>

        <form id="new_state_form">
            @csrf
            <div class="mb-3">
                <label for="new_state" class="form-label">State Name</label>
                <input type="text" class="form-control" id="new_state">
            </div>

            <button type="submit" class="btn btn-primary">Add New State</button>
        </form>

    </div>
</div>