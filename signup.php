<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Page</title>
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

#signup-form {
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 1.0);
    width: 300px;
    text-align: center;
    position: relative; /* Add position relative to create stacking context */
    z-index: 1; /* Set a higher z-index to place the form above the blurred background */
}

/* Rest of your existing styles */


        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            box-sizing: border-box;
            border-radius:20px;
        }

        button {
            background-color: #4caf508f;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size:18px;
        }

        button:hover {
            background-color: #45a049;
        }
        a{
            color:yellow;
            text-decoration:none;
        }
    </style>
</head>
<body>
  <div id="signup-form">
    <h1>Signup</h1>
    <form action="step1.php">
        <input type="text" id="placeName" name="placeName" placeholder="Place Name" required>
        <input type="tel" id="phoneNo" name="phoneNo" placeholder="Phone Number" required>
        <input type="email" id="email" name="email" placeholder="Email" required>
        <input type="password" id="password" name="password" placeholder="Password" required>
        <h4>Nearest Emergency Details</h4>
        <input type="email" id="hospitalEmail" name="hospitalEmail" placeholder="Hospital Email" required>
        <input type="email" id="fireStationEmail" name="fireStationEmail" placeholder="Fire Station Email" required>
        <input type="email" id="policeStationEmail" name="policeStationEmail" placeholder="Police Station Email" required>
            
        <button type="submit">Next</button>
    </form>
    <p class="text-center mt-3" style="color:white;">Already have an account? <a href="signin.php">Signin</a></p>
</div>

</body>
</html>
