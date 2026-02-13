<script src="{{ asset('assets/vendor/js/sweetalert2.min.js') }}"></script>

<script>
    var Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 4000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    })

    document.addEventListener('livewire:init', () => {
        Livewire.on('toast', (payload) => {
            Toast.fire({
                icon: (payload && payload.icon) ? payload.icon : 'success',
                title: (payload && payload.title) ? payload.title : ''
            })
        })
    })
</script>

@if (session()->has('message'))
    <script>
        Toast.fire({
            icon: 'error',
            title: '{{ session()->get('message') }}'
        })
    </script>
@endif

@if ($errors->any())
    @foreach ($errors->all() as $error)
        <script>
            Toast.fire({
                icon: 'error',
                title: '{{ $error }}'
            })
        </script>
    @endforeach
@endif

@if ($errors->login)
    @foreach ($errors->login->all() as $error)
        <script>
            Toast.fire({
                icon: 'error',
                title: '{{ $error }}'
            })
        </script>
    @endforeach
@endif

@if (session()->has('success'))
    <script>
        Toast.fire({
            icon: 'success',
            title: '{{ session()->get('success') }}'
        })
    </script>
@endif
