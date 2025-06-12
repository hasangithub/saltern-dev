@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Edit owner')
@section('content_header_subtitle', 'Welcome')

{{-- Content body: main page content --}}

@section('content_body')
<div class="card card-default">
    <div class="card-header">
        <h3 class="card-title">Edit owner</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="remove">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <div class="card-body">
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        <form action="{{ route('owners.update', $owner->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" name="full_name" id="full_name" class="form-control"
                            value="{{ old('full_name', $owner->full_name) }}">
                    </div>

                    <div class="form-group">
                        <label for="name_with_initial">Name with Initial</label>
                        <input type="text" name="name_with_initial" id="name_with_initial" class="form-control"
                            value="{{ old('name_with_initial', $owner->name_with_initial) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="dob">Date of Birth</label>
                        <input type="date" name="date_of_birth" id="dob" class="form-control"
                            value="{{ old('date_of_birth', $owner->date_of_birth) }}">
                    </div>

                    <div class="form-group">
                        <label for="nic">NIC</label>
                        <input type="text" name="nic" id="nic" class="form-control"
                            value="{{ old('nic', $owner->nic) }}">
                    </div>

                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select name="gender" id="gender" class="form-control">
                            <option></option>
                            @foreach(\App\Enums\Gender::cases() as $case)
                            <option value="{{ $case->value }}"
                                {{ $owner->gender?->value === $case->value ? 'selected' : '' }}>
                                {{ $case->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <!-- Phone Number -->
                    <div class="form-group">
                        <label for="phone_number">Phone Number</label>
                        <input type="text" name="phone_number" id="phone_number" class="form-control"
                            value="{{ old('phone_number', $owner->phone_number) }}" required>
                    </div>

                    <!-- Secondary Phone Number -->
                    <div class="form-group">
                        <label for="whatsapp_number">Whatsapp Number</label>
                        <input type="text" name="whatsapp_number" id="whatsapp_number"
                            class="form-control"
                            value="{{ old('whatsapp_number', $owner->whatsapp_number) }}">
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control"
                            value="{{ old('email', $owner->email) }}">
                    </div>

                    <!-- Address Line 1 -->
                    <div class="form-group">
                        <label for="address_line_1">Address Line 1</label>
                        <textarea type="text" name="address_line_1" id="address_line_1" class="form-control"
                             required> {{ old('address_line_1', $owner->address_line_1) }}
                        </textarea>
                    </div>

                    <div class="form-group">
                        <label for="profile_picture">Profile Picture</label>
                        <input type="file" class="form-control" name="profile_picture" id="profile_picture"
                            accept="image/*" onchange="previewImage(event, 'profilePicturePreview')">
                        <img id="profilePicturePreview" src="#" alt="Owner Profile Preview"
                            style="display:none; width:200px; margin-top:10px;">
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
</div>

@stop

{{-- Push extra CSS --}}

@push('css')
{{-- Add here extra stylesheets --}}
{{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@endpush

{{-- Push extra scripts --}}

@push('js')
<script>
function previewImage(event, previewElementId) {
    const reader = new FileReader();
    reader.onload = function() {
        const output = document.getElementById(previewElementId);
        output.src = reader.result;
        output.style.display = 'block'; // Show the image
    }
    reader.readAsDataURL(event.target.files[0]); // Read the selected file
}
</script>
@endpush