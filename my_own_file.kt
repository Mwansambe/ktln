data class SoilReading(
    val ph: Double,
    val timestamp: String,
    val recommendation: String


)

fun main(){
    val reading1 = soilReading(4.5, "2025-06-10 10:00", "Add lime to increase pH")
    val reading2 = soilReading(6.0, "2025-06-10 12:00", "Soil is balanced")
    val reading3 = soilReading(7.5, "2025-06-10 14:00", "Add sulfur to decrease")
    //listing and printin
val readings = listOf(reading1, reading2, reading3)

for (reading in readings) {
    println("Timestamp: ${reading.timestamp}, pH: ${reading.ph}, Recommendation: ${reading.recommendation} ")

  
}

}