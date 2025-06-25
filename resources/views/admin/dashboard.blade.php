@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5><i class="bi bi-people-fill text-success"></i> Users</h5>
                <p class="text-muted">Manage registered users</p>
                <h3>125</h3>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5><i class="bi bi-lock-fill text-warning"></i> Escrows</h5>
                <p class="text-muted">Pending transactions</p>
                <h3>38</h3>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5><i class="bi bi-bar-chart-fill text-primary"></i> Revenue</h5>
                <p class="text-muted">This Month</p>
                <h3>Ksh 72,500</h3>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4 border-0 shadow-sm">
    <div class="card-header bg-white fw-bold">
        Latest Transactions
    </div>
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#Ref</th>
                    <th>Buyer</th>
                    <th>Seller</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>TX12345</td>
                    <td>John</td>
                    <td>Mary</td>
                    <td>Ksh 5,000</td>
                    <td><span class="badge bg-success">Completed</span></td>
                    <td>Jun 24</td>
                </tr>
                <tr>
                    <td>TX12346</td>
                    <td>Anne</td>
                    <td>Peter</td>
                    <td>Ksh 2,000</td>
                    <td><span class="badge bg-warning">Pending</span></td>
                    <td>Jun 23</td>
                </tr>
                <!-- Add more rows as needed -->
            </tbody>
        </table>
    </div>
</div>
@endsection
