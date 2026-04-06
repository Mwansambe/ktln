fun main()
{
    println("Welcome to the word of programing with koltlin")
    val a: Int = 3;
    val b: Int = 5;
    println("the sum of the two numbers is: ${a + b}")
println("${a == b}")

println("Entet the number of your choice")
val input = readln()

var toNumber = input.toIntOrNull()
var isEven = toNumber % 2 == 0

println("is the number $toNumber even: $isEven}")



}