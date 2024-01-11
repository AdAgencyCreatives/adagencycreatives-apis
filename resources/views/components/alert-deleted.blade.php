@section('alert')
    <script>
        Swal.fire({
            title: '{{ $type }}',
            text: 'Deleted Successfully',
            icon: 'success'
        })
    </script>
@endsection
