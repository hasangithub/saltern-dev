<form action="{{ route('accounts.import') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="file" required>
    <button type="submit">Import Chart of Accounts</button>
</form>