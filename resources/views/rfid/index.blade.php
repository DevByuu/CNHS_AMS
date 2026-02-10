@extends('layouts.app')

@section('content')
<div class="container">
    <h2>RFID Attendance Check</h2>
    <p>Scan student RFID here to mark attendance.</p>

    <!-- Example input for scanning RFID -->
    <input type="text" id="rfidInput" class="form-control w-50" placeholder="Scan RFID here" autofocus>
</div>

<script>
    const input = document.getElementById('rfidInput');
    input.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            alert('RFID scanned: ' + input.value);
            input.value = '';
        }
    });
</script>
@endsection
