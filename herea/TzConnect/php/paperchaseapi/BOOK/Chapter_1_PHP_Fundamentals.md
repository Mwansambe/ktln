# Chapter 1: Getting Started with PHP

## Introduction to PHP

PHP (Hypertext Preprocessor) is a popular server-side scripting language designed specifically for web development. It's the language that powers WordPress, Facebook, and millions of other websites. PHP is known for its simplicity, flexibility, and strong community support.

### Why Learn PHP?

Before diving into Laravel, it's essential to understand PHP because Laravel is built entirely with PHP. Here's why PHP is an excellent choice:

1. **Easy to Learn**: PHP has a gentle learning curve, making it perfect for beginners
2. **Web-Focused**: Designed specifically for creating dynamic web pages
3. **Large Community**: Extensive documentation and community support
4. **Career Opportunities**: High demand for PHP developers worldwide
5. **Framework Ecosystem**: Powers Laravel, Symfony, CodeIgniter, and more

### Your First PHP Script

Let's start by creating your very first PHP program. A PHP script can be embedded within HTML or exist as a standalone file.

#### Creating a Basic PHP File

1. Create a new file named `hello.php` in your project directory
2. Add the following code:

```php
<?php
    // This is a comment in PHP
    echo "Hello, World!";
?>
```

3. Open the file in your browser to see the output

#### Understanding the Code

- `<?php` - Opens a PHP code block
- `//` - Single-line comment
- `/* */` - Multi-line comments
- `echo` - Outputs text to the browser
- `?>` - Closes the PHP code block

### Variables in PHP

Variables in PHP start with the `$` symbol. Let's explore:

```php
<?php
    // String variable
    $name = "Africa";
    
    // Integer variable
    $year = 2024;
    
    // Float variable
    $population = 1.4; // billions
    
    // Boolean variable
    $isDeveloping = true;
    
    // Array variable
    $countries = ["Kenya", "Nigeria", "South Africa", "Egypt"];
    
    // Outputting variables
    echo "Welcome to $name!";
    echo "<br>";
    echo "Year: " . $year;
    echo "<br>";
    
    // Using print_r to view arrays
    print_r($countries);
?>
```

### Data Types in PHP

PHP supports several data types:

| Type | Description | Example |
|------|-------------|---------|
| String | Text data | `"Hello World"` |
| Integer | Whole numbers | `42`, `-17` |
| Float | Decimal numbers | `3.14`, `-0.5` |
| Boolean | True/False | `true`, `false` |
| Array | Collection of values | `[1, 2, 3]` |
| Object | Instance of a class | `$user = new User()` |
| NULL | No value | `NULL` |

### Operators in PHP

#### Arithmetic Operators

```php
<?php
    $a = 10;
    $b = 3;
    
    echo $a + $b;  // Addition: 13
    echo $a - $b;  // Subtraction: 7
    echo $a * $b;  // Multiplication: 30
    echo $a / $b;  // Division: 3.333...
    echo $a % $b;  // Modulus: 1
    echo $a ** $b; // Exponentiation: 1000
?>
```

#### Comparison Operators

```php
<?php
    $x = 10;
    $y = "10";
    
    var_dump($x == $y);  // Equal: true
    var_dump($x === $y); // Identical: false (different types)
    var_dump($x != $y);  // Not equal: false
    var_dump($x !== $y); // Not identical: true
    var_dump($x > $y);   // Greater than: false
    var_dump($x < $y);   // Less than: false
?>
```
This PHP code demonstrates how **comparison operators** work when comparing a number (`$x = 10`) and a string (`$y = "10"`):

- `var_dump($x == $y);` → **true** because `==` checks only value, and `"10"` is equal to `10` after type conversion.  
- `var_dump($x === $y);` → **false** because `===` checks both value and type. Here one is an integer, the other is a string.  
- `var_dump($x != $y);` → **false** because they are equal in value.  
- `var_dump($x !== $y);` → **true** because they are not identical (different types).  
- `var_dump($x > $y);` → **false** because both are equal in value, so `$x` is not greater.  
- `var_dump($x < $y);` → **false** for the same reason — they are equal, so `$x` is not less.  

👉 In short: `==` compares values loosely, while `===` compares values **and** types strictly. This example shows how PHP handles type juggling when comparing integers and strings. this is all about

#### Logical Operators

```php
<?php
    $age = 25;
    
    if ($age >= 18 && $age < 65) {
        echo "Adult";
    }
    
    $isStudent = true;
    $isSenior = false;
    
    if ($isStudent || $isSenior) {
        echo "Discount available";
    }
?>
```

### Control Structures

#### If-Else Statements

```php
<?php
    $score = 85;
    
    if ($score >= 90) {
        echo "Grade: A";
    } elseif ($score >= 80) {
        echo "Grade: B";
    } elseif ($score >= 70) {
        echo "Grade: C";
    } elseif ($score >= 60) {
        echo "Grade: D";
    } else {
        echo "Grade: F";
    }
?>
```

#### Switch Statements

```php
<?php
    $day = "Monday";
    
    switch ($day) {
        case "Monday":
        case "Tuesday":
        case "Wednesday":
        case "Thursday":
        case "Friday":
            echo "Weekday";
            break;
        case "Saturday":
        case "Sunday":
            echo "Weekend";
            break;
        default:
            echo "Invalid day";
    }
?>
```

#### Loops

**For Loop:**
```php
<?php
    for ($i = 1; $i <= 5; $i++) {
        echo "Count: $i <br>";
    }
?>
```

**While Loop:**
```php
<?php
    $i = 1;
    while ($i <= 5) {
        echo "Count: $i <br>";
        $i++;
    }
?>
```

**Foreach Loop (for arrays):**
```php
<?php
    $fruits = ["Apple", "Banana", "Orange", "Mango"];
    
    foreach ($fruits as $fruit) {
        echo "$fruit <br>";
    }
    
    // With key-value pairs
    $ capitals = ["Kenya" => "Nairobi", "Nigeria" => "Abuja"];
    
    foreach ($capitals as $country => $capital) {
        echo "$capital is the capital of $country <br>";
    }
?>
```

### Functions in PHP

Functions are reusable blocks of code:

```php
<?php
    // Simple function
    function greet($name) {
        return "Hello, $name!";
    }
    
    echo greet("Africa");
    
    // Function with default parameter
    function welcome($name = "Guest") {
        return "Welcome, $name!";
    }
    
    echo welcome();        // Output: Welcome, Guest!
    echo welcome("John");  // Output: Welcome, John!
    
    // Function returning multiple values
    function getDimensions() {
        return [
            'width' => 100,
            'height' => 200,
            'depth' => 50
        ];
    }
    
    $dimensions = getDimensions();
    echo $dimensions['width'];
?>
```

### Working with Arrays

Arrays are fundamental in PHP. Let's explore them in detail:

```php
<?php
    // Indexed arrays
    $colors = ["Red", "Green", "Blue"];
    echo $colors[0];  // Red
    
    // Associative arrays
    $user = [
        "name" => "John Doe",
        "email" => "john@example.com",
        "age" => 25
    ];
    echo $user["name"];  // John Doe
    
    // Multi-dimensional arrays
    $students = [
        ["name" => "Alice", "score" => 85],
        ["name" => "Bob", "score" => 92],
        ["name" => "Charlie", "score" => 78]
    ];
    
    echo $students[1]["name"];  // Bob
    
    // Useful array functions
    $numbers = [5, 2, 8, 1, 9];
    sort($numbers);           // Sort ascending
    rsort($numbers);          // Sort descending
    array_push($numbers, 10); // Add element
    $count = count($numbers); // Count elements
?>
```

### Superglobals in PHP

PHP provides several superglobal variables that are available in all scopes:

```php
<?php
    // $_GET - Data sent via URL parameters
    // $_POST - Data sent via HTTP POST
    // $_SESSION - Session variables
    // $_COOKIE - Cookie variables
    // $_SERVER - Server information
    // $_FILES - Uploaded files
    
    // Example: Getting server information
    echo $_SERVER['PHP_SELF'];
    echo $_SERVER['SERVER_NAME'];
    echo $_SERVER['HTTP_USER_AGENT'];
    
    // Example: Handling form data (POST)
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST["name"];
        $email = $_POST["email"];
        echo "Received: $name ($email)";
    }
?>
```

### Form Handling in PHP

Let's create a simple HTML form and handle it with PHP:

**HTML Form (form.html):**
```html
<!DOCTYPE html>
<html>
<head>
    <title>Contact Form</title>
</head>
<body>
    <form method="POST" action="process.php">
        <label>Name:</label>
        <input type="text" name="name" required>
        
        <label>Email:</label>
        <input type="email" name="email" required>
        
        <label>Message:</label>
        <textarea name="message"></textarea>
        
        <button type="submit">Send</button>
    </form>
</body>
</html>
```

**PHP Handler (process.php):**
```php
<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get form data
        $name = htmlspecialchars($_POST["name"]);
        $email = htmlspecialchars($_POST["email"]);
        $message = htmlspecialchars($_POST["message"]);
        
        // Validate
        if (empty($name) || empty($email)) {
            echo "Name and email are required!";
            exit;
        }
        
        // Process the data (save to database, send email, etc.)
        echo "Thank you, $name!";
        echo "We will contact you at $email";
    }
?>
```

> **Important Security Note**: Always use `htmlspecialchars()` to sanitize user input to prevent XSS (Cross-Site Scripting) attacks.

### Include and Require

These statements let you insert the content of one PHP file into another:

```php
<?php
    // include - continues if file not found
    include "header.php";
    
    // require - stops if file not found
    require "config.php";
    
    // include_once / require_once - only once
    include_once "functions.php";
?>
```

### PHP Best Practices

1. **Always use meaningful variable names**:
   ```php
   // Bad
   $x = 10;
   $y = 20;
   
   // Good
   $itemPrice = 10;
   $quantity = 20;
   ```

2. **Comment your code**:
   ```php
   // Calculate total price
   $totalPrice = $itemPrice * $quantity;
   ```

3. **Use constant values**:
   ```php
   define("TAX_RATE", 0.16);
   // Or in PHP 8+
   const MAX_UPLOAD_SIZE = 10485760; // 10MB
   ```

4. **Keep logic separate from presentation**:
   - Process data in PHP
   - Display results in HTML

### Exercise: Building a Simple Calculator

Let's put together everything we've learned to create a simple calculator:

```php
<?php
    // calculator.php
    
    $num1 = 0;
    $num2 = 0;
    $result = 0;
    $operation = "+";
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $num1 = floatval($_POST["num1"]);
        $num2 = floatval($_POST["num2"]);
        $operation = $_POST["operation"];
        
        switch ($operation) {
            case "+":
                $result = $num1 + $num2;
                break;
            case "-":
                $result = $num1 - $num2;
                break;
            case "*":
                $result = $num1 * $num2;
                break;
            case "/":
                if ($num2 != 0) {
                    $result = $num1 / $num2;
                } else {
                    $result = "Error: Division by zero!";
                }
                break;
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Simple Calculator</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        form { max-width: 400px; margin: 0 auto; }
        input, select, button { 
            width: 100%; 
            padding: 10px; 
            margin: 5px 0; 
            box-sizing: border-box;
        }
        .result { 
            background: #f0f0f0; 
            padding: 15px; 
            margin-top: 15px;
            font-size: 1.2em;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1 style="text-align: center;">Simple Calculator</h1>
    
    <form method="POST">
        <label>First Number:</label>
        <input type="number" name="num1" step="any" required 
               value="<?php echo $num1; ?>">
        
        <label>Operation:</label>
        <select name="operation">
            <option value="+" <?php echo $operation == "+" ? "selected" : ""; ?>>+ (Add)</option>
            <option value="-" <?php echo $operation == "-" ? "selected" : ""; ?>>- (Subtract)</option>
            <option value="*" <?php echo $operation == "*" ? "selected" : ""; ?>>× (Multiply)</option>
            <option value="/" <?php echo $operation == "/" ? "selected" : ""; ?>>÷ (Divide)</option>
        </select>
        
        <label>Second Number:</label>
        <input type="number" name="num2" step="any" required 
               value="<?php echo $num2; ?>">
        
        <button type="submit">Calculate</button>
    </form>
    
    <div class="result">
        Result: <?php echo $result; ?>
    </div>
</body>
</html>
```

### Summary

In this chapter, you've learned:

- ✅ What PHP is and why it's important
- ✅ Variables, data types, and operators
- ✅ Control structures (if-else, switch, loops)
- ✅ Functions in PHP
- ✅ Working with arrays
- ✅ Form handling basics
- ✅ PHP best practices

### What's Next?

In Chapter 2, we'll explore how PHP evolved into modern object-oriented programming and introduce the Laravel framework, which will make your development journey much more efficient and enjoyable.

Here’s a rewritten version of your practice exercises, now with **clear answers included** for each one:

---

## 📝 Practice Exercises with Solutions

### 1. Temperature Converter
**Exercise:** Create a PHP program that converts temperatures between Celsius and Fahrenheit.  

**Answer:**
```php
<?php
// Celsius to Fahrenheit
$celsius = 25;
$fahrenheit = ($celsius * 9/5) + 32;
echo "$celsius °C = $fahrenheit °F <br>";

// Fahrenheit to Celsius
$fahrenheit = 77;
$celsius = ($fahrenheit - 32) * 5/9;
echo "$fahrenheit °F = $celsius °C";
?>
```

---

### 2. Grade Calculator
**Exercise:** Build a program that takes multiple test scores and calculates the average, then assigns a letter grade.  

**Answer:**
```php
<?php
$scores = [80, 90, 70, 85, 95];
$average = array_sum($scores) / count($scores);

if ($average >= 90) {
    $grade = "A";
} elseif ($average >= 80) {
    $grade = "B";
} elseif ($average >= 70) {
    $grade = "C";
} elseif ($average >= 60) {
    $grade = "D";
} else {
    $grade = "F";
}

echo "Average Score: $average <br>";
echo "Letter Grade: $grade";
?>
```

---

### 3. Multiplication Table
**Exercise:** Create a program that generates a multiplication table (1–10) using nested loops.  

**Answer:**
```php
<?php
for ($i = 1; $i <= 10; $i++) {
    for ($j = 1; $j <= 10; $j++) {
        echo "$i x $j = " . ($i * $j) . "<br>";
    }
    echo "<hr>"; // separator between rows
}
?>
```

---

### 4. Contact Form
**Exercise:** Build a simple contact form that validates input and displays a confirmation message.  

**Answer (HTML + PHP):**
```html
<!-- contact.html -->
<form action="contact.php" method="post">
  Name: <input type="text" name="name" required><br>
  Email: <input type="email" name="email" required><br>
  Message: <textarea name="message" required></textarea><br>
  <button type="submit">Send</button>
</form>
```

```php
<?php
// contact.php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    if (!empty($name) && !empty($email) && !empty($message)) {
        echo "Thank you, $name! Your message has been received.";
    } else {
        echo "Please fill in all fields.";
    }
}
?>
```

---

### 5. Array Manipulation
**Exercise:** Write functions to find the maximum, minimum, and average of an array of numbers.  

**Answer:**
```php
<?php
$numbers = [12, 45, 23, 67, 34, 89, 10];

function getMax($arr) {
    return max($arr);
}

function getMin($arr) {
    return min($arr);
}

function getAverage($arr) {
    return array_sum($arr) / count($arr);
}

echo "Maximum: " . getMax($numbers) . "<br>";
echo "Minimum: " . getMin($numbers) . "<br>";
echo "Average: " . getAverage($numbers);
?>
```

---

✅ With these rewritten exercises and answers, a beginner can practice **basic PHP programming concepts** (variables, arrays, loops, conditionals, functions, and forms) while seeing practical solutions.  

W

*Continue to Chapter 2: Introduction to Laravel Framework*
