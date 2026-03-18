fun main(){
    //this is immutaable list
 val readings = listOf(5.1, 6.5, 4.8, 7.2, 6.3)   
 //h

 val history = mutableListOf<Double>()
 history.add(6.2)
 history.add(5.9)
 val acidicFields = readings.filter {it < 5.5}
 println("acidic field: $acidicFields")
 
 //map -transform each iteam into specific field let we do it

 val labels = readings.map {describePh(it)}
 println("Labels: $labels")

 //forEach - perfome an action for each item
 readings.forEach {
    ph ->
    if (ph < 5.5) println("the fiels needs lime: pH $ph")
 }

 //perfoming statistical operation
 println("Min: ${readings.min()}")
 println("Max: ${readings.max()}")
 println("Average: ${"%.2f".format(readings.average())}")
}

fun describePh(ph: Double): String{
return when {
    ph < 5.5 -> "Highly Acidic"
    ph in 5.5..6.19 -> "Slightly Acidic"
    ph in 6.2..6.8 -> "Ideal for Beans"
    ph in 6.81..7.5 -> "Neutral /Alkaline"
    else -> "Higly acidic"
}
}

data class SoilReading(
    val ph: Double,
val location: String,
val timestamp: Long
)