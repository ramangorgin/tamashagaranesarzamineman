{{-- پیام‌های خطا --}}
@if ($errors->any())
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        @foreach ($errors->all() as $error)
          if (window.showError) {
            window.showError('{{ addslashes($error) }}');
          }
        @endforeach
      });
    </script>
@endif

{{-- پیام موفقیت --}}
@if (session('success'))
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        if (window.showSuccess) {
          window.showSuccess('{{ addslashes(session('success')) }}');
        }
      });
    </script>
@endif

{{-- پیام هشدار --}}
@if (session('warning'))
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        if (window.showWarning) {
          window.showWarning('{{ addslashes(session('warning')) }}');
        }
      });
    </script>
@endif
