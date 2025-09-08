@extends('layout')
@section('content')


<!DOCTYPE html>
<html>
<head>
    <title>Add New Receipt</title>
</head>
<body>
    <h2>Subject Entry Form</h2>
    <form method="post" action="{{ route('receipts.store') }}" >
        Receipt ID: <input type="text" name="receiptID" required><br><br>
        Total: <input type="double" name="subTotal" required><br><br>
        Date: <input type="date" name="receiptDate" required><br><br>
       
        <input type="submit" value="Add Receipt">
    </form>
</body>
</html>

@endsection
