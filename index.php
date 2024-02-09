<?php
session_start();
// Connect to database
$conn = mysqli_connect("localhost", "root", "", "bootcampemobile");
$total = 0;
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if (isset($_POST['add_customer'])) {
    if ($_POST['password'] == $_POST['confirm-password']) {
        $_SESSION['user'] = "user";
        
    }
}

if (isset($_POST['add_to_cart']) && isset($_POST['item_id'])) {
    // Add item to cart
    $item_id = $_POST['item_id'];
    $_SESSION['cart'][$item_id] = $item_id;
    header('Location: index.php');
    exit;
}
// Remove item from cart
if (isset($_GET['remove']) && isset($_SESSION['cart'][$_GET['remove']])) {
    unset($_SESSION['cart'][$_GET['remove']]);
}

// Clear cart
if (isset($_GET['clear'])) {
    unset($_SESSION['cart']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMOBILE</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

</head>

<body>

    <!-- Header -->
    <header class="bg-dark text-light text-center py-4">
        <h1>EMOBILE </h1> <a href="cart.php"></a>
        <!-- You can add background image here if needed -->
    </header>

    <!-- Main Content -->
    <?php if (!isset($_SESSION['user'])) : ?>
        <!-- Customer Registration Page -->
        <div class="row justify-content-center">
            <div class="col-6">
                <section id="customer-registration">
                    <h2>Customer Registration</h2>
                    <form method="POST">
                        <div class="form-group">
                            <label for="name">Name *</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="contact">Contact No *</label>
                            <input type="tel" class="form-control" name="contact" required>
                        </div>
                        <div class="form-group">
                            <label for="address">Postal Address</label>
                            <input type="text" class="form-control" name="address">
                        </div>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" name="username">
                        </div>
                        <div class="form-group">
                            <label for="password">Password *</label>
                            <input type="password" class="form-control" name="password" minlength="6" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm-password">Confirm Password *</label>
                            <input type="password" class="form-control" name="confirm-password" minlength="6" required>
                        </div>
                        <button name="add_customer" type="submit" class="btn btn-primary">Register</button>
                    </form>
                </section>
            </div>
        </div>

    <?php endif; ?>
    <div class="m-5">
        <?php if (isset($_SESSION['user'])) : ?>
            <div class="row">
                <div class="col-2 bg-dark">
                    <!-- Category Filter -->
                    <div class="">catogary
                        <div><button type="button" class="btn btn-primary">All</button></div>
                        <div><button type="button" class="btn btn-primary">Category 1</button></div>
                        <div><button type="button" class="btn btn-primary">Category 2</button></div>



                        <!-- Add more category buttons as needed -->
                    </div>
                </div>
                <div class="col-7"> <!-- Item Gallery -->
                    <section id="item-gallery" class="mt-5">
                        <h2>Item Gallery</h2>
                        <!-- Filter buttons can be added here if needed -->
                        <div class="row">
                            <!-- Sample Item Card -->
                            <?php
                            // Fetch items from database
                            $sql = "SELECT * FROM items";
                            $result = $conn->query($sql);

                            // Display items
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo '<div class="col-md-4 mb-3">';
                                    echo '<div class="card">';
                                    echo '<img src="' . $row["image_url"] . '" class="card-img-top" alt="' . $row["name"] . '">';
                                    echo '<div class="card-body">';
                                    echo '<h5 class="card-title">' . $row["name"] . '</h5>';
                                    echo '<p class="card-text">$' . $row["price"] . '</p>';
                                    echo '<form method="POST">';
                                    echo '<input type="hidden" name="item_id" value="' . $row["item_id"] . '">';
                                    echo '<button name="add_to_cart" class="btn btn-primary btn-block">Add to Cart</button>';
                                    echo '</form>';
                                    echo '</div>';
                                    echo '</div>';
                                    echo '</div>';
                                }
                            } else {
                                echo "0 results";
                            }
                            // // Close connection
                            // $conn->close();
                            ?>
                            <!-- Add more item cards here -->
                        </div>
                    </section>
                </div>
                <div class="col-3 bg-dark" style="height:100vh"> <!-- Cart -->
                    <section id="cart" class="mt-5">
                        <h2 class="text-light">Shopping Cart</h2>
                        <div class="row">
                            <div class="col-md-12">
                                <?php if (isset($_SESSION['cart'])) : ?>
                                    <ul class="list-group">
                                        <?php foreach ($_SESSION['cart'] as $item_id) : ?>
                                            <li class="list-group-item">
                                                <?php
                                                $cartItem = $conn->query("SELECT * FROM items WHERE item_id = $item_id")->fetch_assoc();
                                                echo $cartItem['name'];
                                                ?>
                                                <span class="float-right"> <span class="bg-success p-1"><?php echo "$" . $cartItem['price']; ?></span> <a href="index.php?remove=<?php echo $item_id; ?>" class="btn btn-danger btn-sm ml-1">Remove</a></span>
                                                
                                            </li>
                                        <?php 
                                    $total = $total+ $cartItem['price'];
                                    
                                    endforeach; ?>
                                    </ul>
                                    <a href="index.php?clear=1" class="btn btn-danger mt-3">Clear Cart</a>
                                <?php else : ?>
                                    <p>Your cart is empty.</p>
                                <?php endif; ?>
                                <h4 class="mt-3 text-light" id="total-amount">Total: $<?php echo $total; ?></h4>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <!-- Footer -->
    <footer class="mt-5 bg-dark text-light py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Contact Information</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-map-marker-alt"></i> Postal Address: </li>
                        <li><i class="fas fa-phone-alt"></i> Telephone: </li>
                        <li><i class="fas fa-envelope"></i> Email: </li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>