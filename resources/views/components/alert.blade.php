@section('alert')
<script>
Swal.fire({
    title: '{{ $type }}',
    text: 'Successfully updated',
    icon: 'success'
})
</script>
@endsection