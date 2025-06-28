<h2>Reset Password ({{ ucfirst($type) }})</h2>

<form method="POST" action="{{ route('password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <input type="hidden" name="type" value="{{ $type }}">
    <input type="email" name="email" value="{{ old('email', $email) }}" required>
    <input type="password" name="password" placeholder="New Password" required>
    <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
    <button type="submit">Reset Password</button>
</form>
