@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="card mx-auto" style="max-width: 500px;">
        <div class="card-header bg-primary text-white text-center">
            <h4>RFID Attendance Check</h4>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('rfid.store') }}">
                @csrf
                <div class="mb-3">
                    <label for="rfid" class="form-label">Scan Student RFID</label>
                    <input type="text" name="rfid" id="rfid" class="form-control form-control-lg text-center" autofocus autocomplete="off">
                </div>
            </form>

            <div class="text-center mt-4 text-muted">
                The input will capture the RFID automatically when scanned.
            </div>
        </div>
    </div>
</div>

<script>
    // Submit form automatically when RFID is scanned
    const input = document.getElementById('rfid');
    input.addEventListener('keypress', function(e) {
        if(e.key === 'Enter') {
            e.preventDefault();
            this.form.submit();
        }
    });
</script>

<style>
    body {
        background: #f3f4f6;
    }
    .card {
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    }
</style>
@endsection

