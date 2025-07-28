@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Loan')
@section('content_header_title', 'Complaint')
@section('content_header_subtitle', 'Complaint')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <form action="{{ route('owner.complaints.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="saltern_id">Membership</label>
                            <select class="form-control" name="membership_id" id="membership_id" required>
                                <option value="">Select Membership</option>
                                @foreach($memberships as $membership)
                                <option value="{{ $membership->id }}">
                                    {{ $membership->saltern->yahai->name." - ".$membership->saltern->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="text-complaint">
                            <label for="complaint_text">Complaint Text</label>
                            <textarea name="complaint_text" id="complaint_text" class="form-control"
                                required></textarea>
                        </div>
                        <br>
                        <div id="voice-complaint">
                            <label for="complaint_voice">Record Voice Complaint</label>
                            <button type="button" id="start-record" class="btn btn-danger">Start Recording</button>
                            <button type="button" id="stop-record" class="btn btn-default">Stop Recording</button>
                            <audio id="audio-preview" controls style="display: none;"></audio>
                            <input type="file" name="complaint_voice" id="complaint_voice" accept="audio/*"
                                style="display: none;">
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
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
<script src="https://cdn.webrtc-experiment.com/RecordRTC.js"></script>
<script>
// Show/hide fields based on complaint type
const typeSelect = document.getElementById('type');
const textComplaint = document.getElementById('text-complaint');
const voiceComplaint = document.getElementById('voice-complaint');

// Voice recording logic
let recorder;
let audioChunks = [];

document.getElementById('start-record').addEventListener('click', async () => {
    const stream = await navigator.mediaDevices.getUserMedia({
        audio: true
    });
    recorder = new RecordRTC(stream, {
        type: 'audio',
        mimeType: 'audio/webm',
    });
    recorder.startRecording();
    document.getElementById('start-record').disabled = true;
    document.getElementById('stop-record').disabled = false;
});

document.getElementById('stop-record').addEventListener('click', () => {
    recorder.stopRecording(() => {
        const blob = recorder.getBlob();
        const audioURL = URL.createObjectURL(blob);

        // Show the audio preview
        const audioPreview = document.getElementById('audio-preview');
        audioPreview.src = audioURL;
        audioPreview.style.display = 'block';

        // Convert the blob to a file and set it in the file input
        const file = new File([blob], 'voice_complaint.webm', {
            type: 'audio/webm'
        });
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);

        // Set the file to the file input
        const fileInput = document.getElementById('complaint_voice');
        fileInput.files = dataTransfer.files;

        // Disable buttons
        document.getElementById('start-record').disabled = false;
        document.getElementById('stop-record').disabled = true;
    });
});
</script>
@endpush