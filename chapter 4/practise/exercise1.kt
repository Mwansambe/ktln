class SoilSample {
    var ph: Double = 0.0
    var location: String = ""
    var timestamp: String = ""
    fun display() {
        println("Soil at $location: pH $ph on $timestamp")
    }
}



fun main() {
    val sample = SoilSample() //create an object

    sample.ph = 6.2
    sample.location = "Farm A - Mbeya"
    sample.timestamp = "2024-06-01 10:00:00"
    sample.display()

}