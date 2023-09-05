<div class="card">
    <div class="card-body">
        <h5 class="card-title">Add New Category</h5>

        <form id="new_category_form">
            @csrf
            <div class="mb-3">
                <label for="new_category" class="form-label">Category Name</label>
                <input type="text" class="form-control" id="new_category">
            </div>

            <button type="submit" class="btn btn-primary">Add New Category</button>
        </form>

    </div>
</div>