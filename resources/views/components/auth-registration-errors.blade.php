@props(['errors'])

@if ($errors->any())
    <div {{ $attributes }}>
        <ul id="test-ul" class="list-disc list-inside text-xs text-red-600 text-center">
            @foreach ($errors->all() as $error)
                @if (str_contains($error, 'first name'))
                    <script>
                        errorInput('first-name', '{{ $error }}');
                    </script>
                @endif
                @if (str_contains($error, 'middle name'))
                    <script>
                        errorInput('middle-name', '{{ $error }}');
                    </script>
                @endif
                @if (str_contains($error, 'last name'))
                    <script>
                        errorInput('last-name', '{{ $error }}');
                    </script>
                @endif
                @if (str_contains($error, 'birth date'))
                    <script>
                        errorInput('birth-date', '{{ $error }}');
                    </script>
                @endif
                @if (str_contains($error, 'department'))
                    <script>
                        errorInput('department', '{{ $error }}');
                    </script>
                @endif
                @if (str_contains($error, 'email'))
                    <script>
                        errorInput('email', '{{ $error }}');
                    </script>
                @endif   
                @if (str_contains($error, 'password'))
                    <script>
                        errorInput('password', '{{ $error }}');
                        errorInput('password-confirm', '');
                    </script>
                @endif   
            @endforeach
        </ul>
    </div>
@endif
