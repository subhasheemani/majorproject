<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signin Page</title>
    <style>
        body {
            background-image: url('https://ik.imgkit.net/3vlqs5axxjf/PCWW/uploadedImages/Articles/News/2023/June/LIH%20generative%20ai%20map%20interface.jpg?tr=w-600%2Ch-300%2Cfo-auto');
            background-size: cover;
            background-position: center;
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            position: relative; /* Add position relative to make ::before absolute relative to body */
        }

        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: inherit;
            filter: blur(4px); /* Adjust the blur intensity as needed */
            z-index: -1;
        }

        #signin-form {
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 1.0);
            width: 300px;
            text-align: center;
            position: relative; /* Add position relative to create stacking context */
            z-index: 1; /* Set a higher z-index to place the form above the blurred background */
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            box-sizing: border-box;
            border-radius: 20px;
        }

        button {
            background-color: #4caf508f;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 18px;
        }

        button:hover {
            background-color: #45a049;
        }
        a{
            color:#fff;
            text-decoration:none;
        }
    </style>
</head>
<body>
   <div id="signin-form">
    <h1>Sign In</h1>
    <?php
    session_start(); 
require "db.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
// Retrieve form data
$email = $_POST['email'];
$password = $_POST['password'];

// Perform a simple query to check if the user exists
$sql = "SELECT * FROM users WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // User found, check the password
    $row = $result->fetch_assoc();
    if (password_verify($password, $row['password'])) {
        // Password is correct, user is authenticated
       $_SESSION['email'] = $row['email'];
        header("Location: index.php");
    exit();
    } else {
        // Password is incorrect
        echo "Incorrect password!";
    }
} else {
    // User not found
    echo "User not found!";
}

// Close the connection
$conn->close();
}
?>
    <form action="#" method="post">
        <input type="email" id="email" name="email" placeholder="Email" required>
        <input type="password" id="password" name="password" placeholder="Password" required>
        <button type="submit">Sign In</button>
    </form>
    <br>
    <p class="text-center mt-3">Don't have an account? <a href="signup.php">Sign Up</a></p>
    <!--<p><a href="safety.php"><button style="background:red;font-size:12px;">Emergency Path</button></a></p>-->
</div>
</body>
</html>
