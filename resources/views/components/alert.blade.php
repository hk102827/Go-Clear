@if(session('success'))
    <div id="flash-message"
         class="mb-4 rounded-lg bg-green-100 border border-green-300 px-4 py-3 text-green-800">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div id="flash-message"
         class="mb-4 rounded-lg bg-red-100 border border-red-300 px-4 py-3 text-red-800">
        {{ session('error') }}
    </div>
@endif



<script>
    document.addEventListener('DOMContentLoaded', function () {
        const flashMessage = document.getElementById('flash-message');

        if (flashMessage) {
            setTimeout(() => {
                flashMessage.style.transition = 'opacity 0.5s ease';
                flashMessage.style.opacity = '0';

                setTimeout(() => {
                    flashMessage.remove();
                }, 300);
            }, 3000); // 5 seconds
        }
    });
</script>
