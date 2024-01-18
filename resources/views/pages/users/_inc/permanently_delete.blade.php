<script>
    $(document).ready(function() {
        $('#confirmDelete').on('click', function() {
            $('#delete-user-form').submit();
        });
    });
</script>
<div class="row">
    <div class="col-md-12 col-xl-12">
        <div class="card">

            <div class="card-header">
                <h5 class="card-title mb-0">Permanently Delete</h5>
            </div>
            <div class="card-body">
                <form id="delete-user-form" method="post" action="{{ route('permanently_delete', $user->id) }}">
                    @csrf
                    @method('DELETE')
                    <p>Permanently delete the user data from the website. This action cannot be undone.</p>
                    <button type="button" class="btn btn-danger" data-toggle="modal"
                        data-target="#confirmationModal">Permanently Delete User</button>

                    <!-- Confirmation Modal -->
                    <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog"
                        aria-labelledby="confirmationModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="confirmationModalLabel">Confirmation</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to permanently delete the user data?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-danger" id="confirmDelete">Yes, Permanently
                                        Delete User</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Confirmation Modal -->
                </form>
            </div>

        </div>
    </div>
</div>
