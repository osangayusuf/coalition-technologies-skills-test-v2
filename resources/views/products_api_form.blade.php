<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Product via API (jQuery)</title>
    <!-- CSRF Token (Crucial for AJAX requests to Laravel if using sessions/Sanctum) -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Tailwind CSS CDN for quick styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        input, textarea, button {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box; /* Ensures padding doesn't affect width */
        }
        button {
            background-color: #4CAF50; /* Green */
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #45a049;
        }
        .message {
            padding: 0.75rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            font-size: 0.9em;
            word-break: break-word; /* Prevents long error messages from overflowing */
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-2xl font-bold mb-4 text-center">Submit Product to API (jQuery)</h1>

        <!-- Area to display API response messages -->
        <div id="response-message" class="hidden message"></div>

        <form id="product-form">
            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Product Name:</label>
            <input type="text" id="name" name="name" required placeholder="Enter product name">

            <label for="price" class="block text-gray-700 text-sm font-bold mb-2">Price:</label>
            <input type="number" id="price" name="price" step="0.01" required placeholder="Enter price">

            <label for="quantity" class="block text-gray-700 text-sm font-bold mb-2">Quantity:</label>
            <input type="number" id="quantity" name="quantity"  placeholder="Enter quantity (optional)"/>

            <button type="submit">Submit Product</button>
        </form>
    </div>

    <!-- Include jQuery library BEFORE your custom script -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script>
        $(document).ready(function() { // Ensure DOM is ready before running jQuery
            $('#product-form').on('submit', function(event) {
                event.preventDefault(); // Prevent default form submission

                const form = $(this); // Get the form element as a jQuery object
                const name = form.find('#name').val();
                const price = form.find('#price').val();
                const quantity = form.find('#quantity').val();

                // Get CSRF token from the meta tag
                const csrfToken = $('meta[name="csrf-token"]').attr('content');

                const responseMessageDiv = $('#response-message');
                responseMessageDiv.addClass('hidden').text('').removeClass('success error'); // Reset message div

                $.ajax({
                    url: '/api/products', // Target the API route
                    type: 'POST', // HTTP method
                    headers: {
                        'X-CSRF-TOKEN': csrfToken // Send CSRF token in the header
                    },
                    contentType: 'application/json', // Tell server we're sending JSON
                    data: JSON.stringify({ name: name, price: price, quantity: quantity }), // Convert data to JSON string
                    dataType: 'json', // Expect JSON response from the server
                    success: function(response) {
                        console.log(response.product); // Debugging: Log the response to the console
                        responseMessageDiv.removeClass('hidden').addClass('success');
                        responseMessageDiv.text(`Success: ${response.message} (Product ID: ${response.product.id})`);
                        form[0].reset();
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        responseMessageDiv.removeClass('hidden').addClass('error');
                        let errorMessage = `Error: ${jqXHR.responseJSON ? jqXHR.responseJSON.message : 'An unknown error occurred.'}`;

                        if (jqXHR.responseJSON && jqXHR.responseJSON.errors) {
                            // Concatenate validation error messages
                            for (const field in jqXHR.responseJSON.errors) {
                                errorMessage += `\n- ${field}: ${jqXHR.responseJSON.errors[field].join(', ')}`;
                            }
                        } else if (errorThrown) {
                            errorMessage += `\nDetails: ${errorThrown}`;
                        } else {
                            errorMessage += `\nStatus: ${textStatus}`;
                        }
                        responseMessageDiv.text(errorMessage);
                        console.error('API Error:', jqXHR.responseJSON || jqXHR.responseText || jqXHR);
                    }
                });
            });
        });
    </script>
</body>
</html>
