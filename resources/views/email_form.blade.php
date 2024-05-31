<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Form</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Send Encrypted and Signed Email</h2>
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <form action="{{ route('send-email') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="recipient">Recipient Email:</label>
            <input type="email" class="form-control" id="recipient" name="recipient" required>
        </div>
        <div class="form-group">
            <label for="subject">Subject:</label>
            <input type="text" class="form-control" id="subject" name="subject" required>
        </div>
        <div class="form-group">
            <label for="message">Message:</label>
            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
        </div>
        <div class="form-group">
            <label for="encryption">Encryption:</label>
            <select class="form-control" id="encryption" name="encryption">
                <option value="none">None</option>
                <option value="openpgp">OpenPGP</option>
            </select>
        </div>
        <div class="form-group">
            <label for="signing">Sign Message:</label>
            <select class="form-control" id="signing" name="signing">
                <option value="no">No</option>
                <option value="yes">Yes</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Send Email</button>
    </form>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
