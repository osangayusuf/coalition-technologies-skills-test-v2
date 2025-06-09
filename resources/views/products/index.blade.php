<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coalition Technologies Test V2</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
</head>
<body>
<div class="container">
  <h1>Products Manager</h1>
  <form id="product-form">
    <div class="mb-3">
        <label for="name" class="form-label">Product Name</label>
        <input type="text" class="form-control" id="name" placeholder="Product X">
        <div class="text-danger" id="name-error"></div>
    </div>
    <div class="mb-3">
        <label for="price" class="form-label">Price per Item</label>
        <input type="number" class="form-control" id="price" placeholder="100">
        <div class="text-danger" id="price-error"></div>
    </div>
    <div class="mb-3">
        <label for="quantity" class="form-label">Quantity in Stock</label>
        <input type="number" class="form-control" id="quantity" placeholder="7">
        <div class="text-danger" id="quantity-error"></div>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
  </form>
  <h2>Product List</h2>
  <table class="table table-bordered" id="product-table">
    <thead>
      <tr>
          <th>Name</th>
          <th>Price</th>
          <th>Quantity</th>
          <th>Time Submitted</th>
          <th>Total Value</th>
          <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    </tbody>
    <tfoot>
      <tr>
          <td colspan="4" class="text-end"><strong>Total:</strong></td>
          <td id="total-value-sum">0</td>
          <td></td>
      </tr>
    </tfoot>
</table>
</div>
<div class="toast-container position-fixed bottom-0 end-0 p-3">
<div class="toast align-items-center text-bg-primary" id="liveToast" role="success" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
      </div>
      <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script>
    const toastLiveExample = document.getElementById('liveToast');
    const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastLiveExample);

    function fetchProducts() {
        $.ajax({
            url: '/api/products',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                const productTableBody = $('#product-table tbody');
                productTableBody.empty();

                let totalValueSum = 0;

                const sortedProducts = response.products.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

                sortedProducts.forEach(product => {
                    totalValueSum += product.total_value;

                    const row = `<tr>
                        <td>${product.name}</td>
                        <td>${product.price}</td>
                        <td>${product.quantity}</td>
                        <td>${product.created_at}</td>
                        <td>${product.total_value}</td>
                        <td>
                            <a href="/edit/${product.id}" class="btn btn-warning btn-sm">Edit</a>
                            <button class="btn btn-danger btn-sm" type="button" onclick="deleteProduct('${product.id}')">Delete</button>
                        </td>
                    </tr>`;
                    productTableBody.append(row);
                });

                $('#total-value-sum').text(totalValueSum);
            },
            error: function(jqXHR) {
                toastBootstrap.show();
                toastLiveExample.querySelector('.toast-body').textContent = jqXHR.responseJSON?.message || 'Failed to fetch products.';
            }
        });
    }

    function deleteProduct(productId) {
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            url: `/api/products/${productId}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(response) {
                toastBootstrap.show();
                toastLiveExample.querySelector('.toast-body').textContent = response.message;
                fetchProducts();
            },
            error: function(jqXHR) {
                const errorMessage = jqXHR.responseJSON?.message || 'Failed to delete product.';
                toastBootstrap.show();
                toastLiveExample.querySelector('.toast-body').textContent = errorMessage;
                console.error('Delete Error:', errorMessage);
            }
        });
    }

    $(document).ready(function() {
        fetchProducts();

        $('#product-form').on('submit', function(event) {
            event.preventDefault();

            const form = $(this);
            const name = form.find('#name').val();
            const price = form.find('#price').val();
            const quantity = form.find('#quantity').val();

            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            $('#name-error, #price-error, #quantity-error').text('');

            $.ajax({
                url: '/api/products',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                contentType: 'application/json',
                data: JSON.stringify({ name: name, price: price, quantity: quantity }),
                dataType: 'json',
                success: function(response) {
                    toastBootstrap.show();
                    toastLiveExample.querySelector('.toast-body').textContent = response.message;

                    const productTableBody = $('#product-table tbody');
                    const newRow = `<tr>
                        <td>${response.product.name}</td>
                        <td>${response.product.price}</td>
                        <td>${response.product.quantity}</td>
                        <td>${response.product.created_at}</td>
                        <td>${response.product.total_value}</td>
                        <td>
                            <a href="/products/edit/${response.product.id}" class="btn btn-warning btn-sm">Edit</a>
                            <button class="btn btn-danger btn-sm" onclick="deleteProduct(${response.product.id})">Delete</button>
                        </td>
                    </tr>`;
                    productTableBody.prepend(newRow);

                    form[0].reset();
                    fetchProducts();
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
