<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h1>Edit Product</h1>
    <form id="edit-product-form">
        <div class="mb-3">
            <label for="name" class="form-label">Product Name</label>
            <input type="text" class="form-control" id="name">
            <div class="text-danger" id="name-error"></div>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Price per Item</label>
            <input type="number" class="form-control" id="price">
            <div class="text-danger" id="price-error"></div>
        </div>
        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity in Stock</label>
            <input type="number" class="form-control" id="quantity">
            <div class="text-danger" id="quantity-error"></div>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        // Fetch product data when the page loads
        $.ajax({
            url: `/api/products/{{ $id }}`,
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            dataType: 'json',
            success: function(response) {
                $('#name').val(response.product.name);
                $('#price').val(response.product.price);
                $('#quantity').val(response.product.quantity);
            },
            error: function(jqXHR) {
                alert('Failed to fetch product data.');
            }
        });

        $('#edit-product-form').on('submit', function(event) {
            event.preventDefault();
            const form = $(this);
            const name = form.find('#name').val();
            const price = form.find('#price').val();
            const quantity = form.find('#quantity').val();
            $('#name-error, #price-error, #quantity-error').text('');
            $.ajax({
                url: `/api/products/{{ $id }}`,
                type: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                contentType: 'application/json',
                data: JSON.stringify({ name: name, price: price, quantity: quantity }),
                dataType: 'json',
                success: function(response) {
                    window.location.href = '/';
                },
                error: function(jqXHR) {
                    const errors = jqXHR.responseJSON?.errors || {};
                    if (errors.name) {
                        $('#name-error').text(errors.name.join(', '));
                    }
                    if (errors.price) {
                        $('#price-error').text(errors.price.join(', '));
                    }
                    if (errors.quantity) {
                        $('#quantity-error').text(errors.quantity.join(', '));
                    }
                }
            });
        });
    });
</script>
</body>
</html>
