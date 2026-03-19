fun parseAndValidatePh(input: String?): Double? {
    //handle null input
    if(input == null)  return null

    //step covrt the string to double here
    val ph  = input.toDoubleOrNull()

    //Step 3: validate the ph value range from 0 to 14
    return if (ph != null && ph in 0.0..14.0) ph
    else null
}
fun main(){
    println(parseAndValidatePh("6.5")) // valid input
    println(parseAndValidatePh("15.0")) // out of range
    println(parseAndValidatePh("abc")) // invalid format
    println(parseAndValidatePh(null)) // null input'
    //test
   val a = "6.45".toDoubleOrNull()   // returns 6.45 (Double)
val b = "abc".toDoubleOrNull()    // returns null (invalid number)
val c = "".toDoubleOrNull()       // returns null (empty string)
val d = "14".toDoubleOrNull()     // returns 14.0 (Double)
println("a: $a, b: $b, c: $c, d: $d")
}