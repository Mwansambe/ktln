fun main() {
    println("Hello, World!")
    println("Welcome to Kotlin Programming!")
    
    // Variables
    val name = "Developer"
    val age = 25
    
    // Print with string template
    println("My name is $name and I am $age years old")
    
    // Function example
    val result = add(10, 20)
    println("10 + 20 = $result")
}

fun add(a: Int, b: Int): Int {
    return a + b
}