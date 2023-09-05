@section('alert')
<script>
Swal.fire({
    title: '{{ $type }}',
    text: 'Successfully Created',
    icon: 'success'
})
</script>
@endsection