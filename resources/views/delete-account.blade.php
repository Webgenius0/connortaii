<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Account</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card {
            max-width: 500px;
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .btn-danger {
            width: 100%;
        }

        .btn-secondary {
            width: 100%;
        }
    </style>
</head>

<body>

    <div class="card p-4">
        <div class="text-center mb-4">
            <h3 class="text-danger">Warning!</h3>
            <p class="text-muted">You are about to delete your account.</p>
        </div>

        <!-- Show user info -->
        <div class="mb-3 text-center">
            <p><strong>Name:</strong> {{ auth()->user()->name }}</p>
            <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
        </div>

        <p class="text-center text-muted">This action <strong>cannot</strong> be undone.</p>

        <!-- Delete Button triggers Modal -->
        <div class="mb-3">
            <button type="button" class="btn btn-danger mb-2" data-bs-toggle="modal"
                data-bs-target="#confirmDeleteModal">
                Delete My Account
            </button>
            <a href="/" class="btn btn-secondary">Cancel</a>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="confirmDeleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete your account? This action <strong>cannot</strong> be undone.</p>
                </div>
                <div class="modal-footer">
                    <form method="POST" action="{{ route('delete.account.post') }}">
                        @csrf
                        <button type="submit" class="btn btn-danger">Yes, Delete My Account</button>
                    </form>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
