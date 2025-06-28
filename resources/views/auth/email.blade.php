@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<h2>Forgot Password ({{ ucfirst($type) }})</h2>

<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <input type="hidden" name="type" value="{{ $type }}">
    <input type="email" name="email" placeholder="Email" required>
    <button type="submit">Send Reset Link</button>
</form>
